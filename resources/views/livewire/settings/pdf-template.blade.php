<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Repositories\PanelSettingRepository;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Cache;
use App\Services\MinioService;

new #[Layout('components.layouts.app', ['title' => 'Teklif Şablonu'])] class extends Component {
    use Toast, WithFileUploads;

    // Header Settings
    public $group = 'header';
    public $pdf_logo;
    public ?string $current_pdf_logo_path = null;
    public int $pdf_logo_height = 50;
    public string $pdf_header_bg_color = '#4f46e5';
    public string $pdf_header_text_color = '#ffffff';

    // Content Settings
    public string $pdf_font_family = 'Segoe UI';
    public string $pdf_primary_color = '#4f46e5';
    public string $pdf_secondary_color = '#6b7280';
    public string $pdf_table_header_bg_color = '#f9fafb';
    public string $pdf_table_header_text_color = '#6b7280';

    // Specific Colors
    public string $pdf_discount_color = '#16a34a';
    public string $pdf_total_color = '#4f46e5';

    // Footer Settings
    public ?string $pdf_footer_text = null;

    // Download Page Settings
    public string $dl_header_bg_color = '#4f46e5';
    public string $dl_header_text_color = '#ffffff';
    public int $dl_logo_height = 50;
    public $dl_logo;
    public ?string $current_dl_logo_path = null;

    // Introduction Files
    public array $introduction_files = [];
    public $new_intro_file;
    public string $new_intro_name = '';
    public bool $is_uploading_intro = false;

    public function mount(PanelSettingRepository $repository): void
    {
        $setting = $repository->getActiveSetting();
        if ($setting) {
            $this->current_pdf_logo_path = $setting->pdf_logo_path;
            $this->pdf_logo_height = $setting->pdf_logo_height ?? 50;
            $this->pdf_header_bg_color = $setting->pdf_header_bg_color ?? '#4f46e5';
            $this->pdf_header_text_color = $setting->pdf_header_text_color ?? '#ffffff';

            $this->pdf_font_family = $setting->pdf_font_family ?? 'Segoe UI';
            $this->pdf_primary_color = $setting->pdf_primary_color ?? '#4f46e5';
            $this->pdf_secondary_color = $setting->pdf_secondary_color ?? '#6b7280';
            $this->pdf_table_header_bg_color = $setting->pdf_table_header_bg_color ?? '#f9fafb';
            $this->pdf_table_header_text_color = $setting->pdf_table_header_text_color ?? '#6b7280';

            $this->pdf_discount_color = $setting->pdf_discount_color ?? '#16a34a';
            $this->pdf_total_color = $setting->pdf_total_color ?? '#4f46e5';

            $this->pdf_footer_text = $setting->pdf_footer_text;

            // Download Page Init
            $this->dl_header_bg_color = $setting->dl_header_bg_color ?? '#4f46e5';
            $this->dl_header_text_color = $setting->dl_header_text_color ?? '#ffffff';
            $this->dl_logo_height = $setting->dl_logo_height ?? 50;
            $this->current_dl_logo_path = $setting->dl_logo_path;
            $this->introduction_files = $setting->introduction_files ?? [];
        }
    }

    public function save(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'pdf_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
            'pdf_logo_height' => 'required|integer|min:10|max:500',
            'pdf_header_bg_color' => 'required|string',
            'pdf_header_text_color' => 'required|string',
            'pdf_font_family' => 'required|string',
            'pdf_primary_color' => 'required|string',
            'pdf_secondary_color' => 'required|string',
            'pdf_table_header_bg_color' => 'required|string',
            'pdf_table_header_text_color' => 'required|string',
            'pdf_discount_color' => 'required|string',
            'pdf_total_color' => 'required|string',
            'pdf_footer_text' => 'nullable|string',
        ]);

        // Handle logo upload with Minio
        if ($this->pdf_logo) {
            $minioService = app(MinioService::class);

            // Delete old logo if exists
            if ($this->current_pdf_logo_path) {
                try {
                    $minioService->deleteFile($this->current_pdf_logo_path);
                } catch (\Exception $e) {
                    // Ignore delete errors if file missing
                }
            }

            // Upload to 'template' folder
            try {
                $uploadResult = $minioService->uploadFile($this->pdf_logo, 'template');
                $data['pdf_logo_path'] = $uploadResult['path'];
            } catch (\Exception $e) {
                $this->error('Logo yüklenemedi: ' . $e->getMessage());
                return;
            }
        }

        unset($data['pdf_logo']); // Remove file object from data array

        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        // Update current path for immediate feedback without reload
        if (isset($data['pdf_logo_path'])) {
            $this->current_pdf_logo_path = $data['pdf_logo_path'];
        }

        $this->success('PDF Şablon Ayarları Kaydedildi');
    }

    public function removeLogo(): void
    {
        $repository = app(PanelSettingRepository::class);
        $minioService = app(MinioService::class);

        if ($this->current_pdf_logo_path) {
            try {
                $minioService->deleteFile($this->current_pdf_logo_path);
            } catch (\Exception $e) {
                $this->error('Logo silinirken hata oluştu: ' . $e->getMessage());
                return;
            }
        }

        $repository->saveSettings(['pdf_logo_path' => null]);
        $this->current_pdf_logo_path = null;
        $this->success('Logo kaldırıldı');
    }

    public function getLogoUrlProperty()
    {
        if (!$this->current_pdf_logo_path)
            return null;
        
        try {
            return app(MinioService::class)->getFileUrl($this->current_pdf_logo_path);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Download Page Methods
    public function saveDownloadSettings(): void
    {
        $repository = app(PanelSettingRepository::class);

        $data = $this->validate([
            'dl_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
            'dl_logo_height' => 'required|integer|min:10|max:500',
            'dl_header_bg_color' => 'required|string',
            'dl_header_text_color' => 'required|string',
        ]);

        if ($this->dl_logo) {
            $minioService = app(MinioService::class);
            if ($this->current_dl_logo_path) {
                try {
                    $minioService->deleteFile($this->current_dl_logo_path);
                } catch (\Exception $e) {}
            }
            try {
                $uploadResult = $minioService->uploadFile($this->dl_logo, 'template');
                $data['dl_logo_path'] = $uploadResult['path'];
            } catch (\Exception $e) {
                $this->error('Logo yüklenemedi: ' . $e->getMessage());
                return;
            }
        }
        
        unset($data['dl_logo']);
        $repository->saveSettings($data);
        Cache::forget('theme_settings');

        if (isset($data['dl_logo_path'])) {
            $this->current_dl_logo_path = $data['dl_logo_path'];
            $this->reset('dl_logo');
        }

        $this->success('İndirme Sayfası Ayarları Kaydedildi');
    }

    public function removeDownloadLogo(): void
    {
        $repository = app(PanelSettingRepository::class);
        $minioService = app(MinioService::class);

        if ($this->current_dl_logo_path) {
            try {
                $minioService->deleteFile($this->current_dl_logo_path);
            } catch (\Exception $e) {
                 $this->error('Logo silinirken hata oluştu: ' . $e->getMessage());
                 return;
            }
        }

        $repository->saveSettings(['dl_logo_path' => null]);
        $this->current_dl_logo_path = null;
        $this->success('Logo kaldırıldı');
    }

    public function uploadIntroductionFile(): void
    {
        $this->validate([
            'new_intro_file' => 'required|file|mimes:pdf|max:25600', // 25MB
            'new_intro_name' => 'required|string|max:255',
        ]);

        $this->is_uploading_intro = true;
        try {
            $minioService = app(MinioService::class);
            $uploadResult = $minioService->uploadFile($this->new_intro_file, 'teklif-tanitim');
            
            $fileData = [
                'name' => $this->new_intro_name,
                'path' => $uploadResult['path'],
                'size' => $this->new_intro_file->getSize(),
                'uploaded_at' => now()->toIso8601String(),
            ];

            $this->introduction_files[] = $fileData;
            
            app(PanelSettingRepository::class)->saveSettings(['introduction_files' => $this->introduction_files]);
            Cache::forget('theme_settings');

            $this->reset(['new_intro_file', 'new_intro_name']);
            $this->success('Dosya yüklendi');
        } catch (\Exception $e) {
            $this->error('Dosya yüklenirken hata: ' . $e->getMessage());
        } finally {
            $this->is_uploading_intro = false;
        }
    }

    public function deleteIntroductionFile(int $index): void
    {
        if (!isset($this->introduction_files[$index])) return;

        $file = $this->introduction_files[$index];
        $minioService = app(MinioService::class);

        try {
            $minioService->deleteFile($file['path']);
        } catch (\Exception $e) {
            // Continue even if delete fails (maybe file missing)
        }

        unset($this->introduction_files[$index]);
        $this->introduction_files = array_values($this->introduction_files); // Reindex

        app(PanelSettingRepository::class)->saveSettings(['introduction_files' => $this->introduction_files]);
        Cache::forget('theme_settings');
        
        $this->success('Dosya silindi');
    }

    public function getDownloadLogoUrlProperty()
    {
        if (!$this->current_dl_logo_path) return null;
        try {
            return app(MinioService::class)->getFileUrl($this->current_dl_logo_path);
        } catch (\Exception $e) {
            return null;
        }
    }
}; ?>

<div class="p-6 min-h-screen" style="background-color: var(--page-bg);">
    <div class="max-w-5xl lg:w-4/5 mx-auto pb-20">
    {{-- Back Button & Page Title --}}
    {{-- Back Button --}}
    <a href="{{ route('settings.index') }}"
        class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="text-sm font-medium">Geri</span>
    </a>

    {{-- Page Title --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Teklif Şablonu</h1>
        <p class="text-sm text-slate-500 mt-1">Teklif PDF şablonunu ve ayarlarını özelleştirin.</p>
    </div>

    {{-- Main Settings Card --}}
    <div
        class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)]">

        {{-- Card Header --}}
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <h2 class="text-lg font-medium text-skin-heading flex items-center gap-2">
                <x-mary-icon name="o-document-text" class="w-5 h-5 opacity-70" />
                PDF Görünüm Ayarları
            </h2>
            <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
                style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
                wire:click="save" spinner="save" />
        </div>

        {{-- Accordions --}}
        <x-mary-accordion wire:model="group" separator>
            {{-- 1. Header Ayarları --}}
            <x-mary-collapse name="header" icon="o-document-duplicate">
                <x-slot:heading>
                    <span class="font-medium text-sm">Header Ayarları</span>
                </x-slot:heading>
                <x-slot:content>
                    <div class="space-y-4 pt-4">
                        {{-- Logo --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-xs font-semibold uppercase opacity-70">PDF Logo</span>
                            </label>
                            <div class="p-4 border border-dashed border-gray-200 rounded-lg bg-gray-50/50">
                                @if($pdf_logo)
                                    {{-- Temporary Preview --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="p-2 bg-white rounded border border-gray-100 relative">
                                                <img src="{{ $pdf_logo->temporaryUrl() }}" style="height: {{ $pdf_logo_height }}px"
                                                    class="object-contain" />
                                                <span class="absolute -top-2 -right-2 bg-green-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-sm">YENİ</span>
                                            </div>
                                            <div class="text-xs opacity-60">
                                                Yeni logo seçildi (Kaydetmeniz gerekiyor)
                                            </div>
                                        </div>
                                        <x-mary-button icon="o-x-mark" class="btn-ghost btn-sm text-gray-500"
                                            wire:click="$set('pdf_logo', null)" tooltip="İptal" />
                                    </div>
                                    <div class="flex gap-2 mt-4">
                                        <button type="button" wire:click="$set('pdf_logo_height', 50)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $pdf_logo_height == 50 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $pdf_logo_height == 50 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1x
                                        </button>
                                        <button type="button" wire:click="$set('pdf_logo_height', 75)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $pdf_logo_height == 75 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $pdf_logo_height == 75 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1.5x
                                        </button>
                                        <button type="button" wire:click="$set('pdf_logo_height', 100)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $pdf_logo_height == 100 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $pdf_logo_height == 100 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            2x
                                        </button>
                                    </div>

                                @elseif($current_pdf_logo_path)
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="p-2 bg-white rounded border border-gray-100">
                                                <img src="{{ $this->logoUrl }}" style="height: {{ $pdf_logo_height }}px"
                                                    class="object-contain" />
                                            </div>
                                            <div class="text-xs opacity-60">
                                                Mevcut logo yüklü ({{ $pdf_logo_height }}px)
                                            </div>
                                        </div>
                                        <x-mary-button icon="o-trash" class="btn-ghost btn-sm text-red-500"
                                            wire:click="removeLogo" tooltip="Logoyu Kaldır" />
                                    </div>
                                    <div class="flex gap-2 mt-4">
                                        <button type="button" wire:click="$set('pdf_logo_height', 50)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $pdf_logo_height == 50 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $pdf_logo_height == 50 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1x
                                        </button>
                                        <button type="button" wire:click="$set('pdf_logo_height', 75)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $pdf_logo_height == 75 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $pdf_logo_height == 75 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1.5x
                                        </button>
                                        <button type="button" wire:click="$set('pdf_logo_height', 100)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $pdf_logo_height == 100 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $pdf_logo_height == 100 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            2x
                                        </button>
                                    </div>
                                @else
                                    <x-mary-file wire:model="pdf_logo" accept="image/png, image/jpeg, image/svg+xml"
                                        class="max-w-md">
                                        <div class="text-center text-gray-400 text-sm py-2">
                                            Logo yüklemek için tıklayın
                                        </div>
                                    </x-mary-file>
                                @endif
                            </div>
                        </div>

                        {{-- Colors --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Arka Plan
                                        Rengi</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_header_bg_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span
                                        class="text-xs font-mono opacity-80 uppercase">{{ $pdf_header_bg_color }}</span>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Yazı
                                        Rengi</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_header_text_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span
                                        class="text-xs font-mono opacity-80 uppercase">{{ $pdf_header_text_color }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot:content>
            </x-mary-collapse>

            {{-- 2. İçerik & Renkler --}}
            <x-mary-collapse name="content" icon="o-paint-brush">
                <x-slot:heading>
                    <span class="font-medium text-sm">İçerik & Renkler</span>
                </x-slot:heading>
                <x-slot:content>
                    <div class="space-y-6 pt-4">
                        <div class="max-w-md">
                            <x-mary-select label="Yazı Tipi Ailesi" :options="[['id' => 'Segoe UI', 'name' => 'Segoe UI'], ['id' => 'Roboto', 'name' => 'Roboto'], ['id' => 'Open Sans', 'name' => 'Open Sans']]"
                                wire:model="pdf_font_family" icon="o-identification" class="!bg-white" />
                        </div>

                        {{-- Color Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {{-- Primary --}}
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Ana Renk
                                        (Primary)</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_primary_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_primary_color }}</span>
                                </div>
                            </div>
                            {{-- Secondary --}}
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">İkincil
                                        Renk</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_secondary_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span
                                        class="text-xs font-mono opacity-80 uppercase">{{ $pdf_secondary_color }}</span>
                                </div>
                            </div>
                            {{-- Discount --}}
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">İndirim
                                        Rengi</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_discount_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span
                                        class="text-xs font-mono opacity-80 uppercase">{{ $pdf_discount_color }}</span>
                                </div>
                            </div>
                            {{-- Total --}}
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Toplam Tutar
                                        Rengi</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_total_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_total_color }}</span>
                                </div>
                            </div>
                            {{-- Table Header BG --}}
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Tablo Başlık Arka
                                        Plan</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_table_header_bg_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span
                                        class="text-xs font-mono opacity-80 uppercase">{{ $pdf_table_header_bg_color }}</span>
                                </div>
                            </div>
                            {{-- Table Header Text --}}
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Tablo Başlık
                                        Yazı</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="pdf_table_header_text_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span
                                        class="text-xs font-mono opacity-80 uppercase">{{ $pdf_table_header_text_color }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot:content>
            </x-mary-collapse>

            {{-- 3. Footer Ayarları --}}
            <x-mary-collapse name="footer" icon="o-pencil-square">
                <x-slot:heading>
                    <span class="font-medium text-sm">Footer Ayarları</span>
                </x-slot:heading>
                <x-slot:content>
                    <div class="pt-4">
                        <x-mary-textarea label="Varsayılan Footer Notu" wire:model="pdf_footer_text"
                            placeholder="Şirket bilgileri, IBAN vb. (Teklif açıklamasının altında görünür)" rows="4"
                            hint="Bu metin tüm PDF tekliflerin en altında varsayılan olarak görünecektir."
                            class="bg-white" />
                    </div>
                </x-slot:content>
            </x-mary-collapse>
        </x-mary-accordion>

    </div>

    {{-- Download Page Settings Card --}}
    <div class="theme-card p-6 shadow-sm border border-[var(--card-border)] rounded-[var(--card-radius)] bg-[var(--card-bg)] mt-8">
        
        {{-- Card Header --}}
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-[var(--card-border)]">
            <h2 class="text-lg font-medium text-skin-heading flex items-center gap-2">
                <x-mary-icon name="o-arrow-down-tray" class="w-5 h-5 opacity-70" />
                İndirme Sayfası Ayarları
            </h2>
            <x-mary-button label="Kaydet" icon="o-check" class="btn-sm"
                style="background-color: var(--btn-save-bg) !important; color: var(--btn-save-text) !important; border-color: var(--btn-save-border) !important;"
                wire:click="saveDownloadSettings" spinner="saveDownloadSettings" />
        </div>

        {{-- Accordions --}}
        <x-mary-accordion wire:model="group" separator>
            {{-- 1. Header Ayarları --}}
            <x-mary-collapse name="dl_header" icon="o-document-duplicate">
                <x-slot:heading>
                    <span class="font-medium text-sm">Header Ayarları</span>
                </x-slot:heading>
                <x-slot:content>
                    <div class="space-y-4 pt-4">
                        {{-- Logo --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-xs font-semibold uppercase opacity-70">Sayfa Logosu</span>
                            </label>
                            <div class="p-4 border border-dashed border-gray-200 rounded-lg bg-gray-50/50">
                                @if($dl_logo)
                                    {{-- Temporary Preview --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="p-2 bg-white rounded border border-gray-100 relative">
                                                <img src="{{ $dl_logo->temporaryUrl() }}" style="height: {{ $dl_logo_height }}px"
                                                    class="object-contain" />
                                                <span class="absolute -top-2 -right-2 bg-green-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-sm">YENİ</span>
                                            </div>
                                            <div class="text-xs opacity-60">
                                                Yeni logo seçildi (Kaydetmeniz gerekiyor)
                                            </div>
                                        </div>
                                        <x-mary-button icon="o-x-mark" class="btn-ghost btn-sm text-gray-500"
                                            wire:click="$set('dl_logo', null)" tooltip="İptal" />
                                    </div>
                                    
                                     <div class="flex gap-2 mt-4">
                                        <button type="button" wire:click="$set('dl_logo_height', 50)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $dl_logo_height == 50 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $dl_logo_height == 50 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1x
                                        </button>
                                        <button type="button" wire:click="$set('dl_logo_height', 75)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $dl_logo_height == 75 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $dl_logo_height == 75 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1.5x
                                        </button>
                                        <button type="button" wire:click="$set('dl_logo_height', 100)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $dl_logo_height == 100 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $dl_logo_height == 100 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            2x
                                        </button>
                                    </div>

                                @elseif($current_dl_logo_path)
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="p-2 bg-white rounded border border-gray-100">
                                                <img src="{{ $this->downloadLogoUrl }}" style="height: {{ $dl_logo_height }}px"
                                                    class="object-contain" />
                                            </div>
                                            <div class="text-xs opacity-60">
                                                Mevcut logo yüklü ({{ $dl_logo_height }}px)
                                            </div>
                                        </div>
                                        <x-mary-button icon="o-trash" class="btn-ghost btn-sm text-red-500"
                                            wire:click="removeDownloadLogo" tooltip="Logoyu Kaldır" />
                                    </div>
                                    <div class="flex gap-2 mt-4">
                                        <button type="button" wire:click="$set('dl_logo_height', 50)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $dl_logo_height == 50 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $dl_logo_height == 50 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1x
                                        </button>
                                        <button type="button" wire:click="$set('dl_logo_height', 75)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $dl_logo_height == 75 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $dl_logo_height == 75 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            1.5x
                                        </button>
                                        <button type="button" wire:click="$set('dl_logo_height', 100)"
                                            class="px-2 py-1 text-xs font-medium rounded transition-colors {{ $dl_logo_height == 100 ? 'text-white' : 'bg-white border border-gray-200 text-slate-700 hover:bg-gray-50' }}"
                                            style="{{ $dl_logo_height == 100 ? 'background-color: var(--btn-create-bg); border-color: var(--btn-create-bg);' : '' }}">
                                            2x
                                        </button>
                                    </div>
                                @else
                                    <x-mary-file wire:model="dl_logo" accept="image/png, image/jpeg, image/svg+xml"
                                        class="max-w-md">
                                        <div class="text-center text-gray-400 text-sm py-2">
                                            Logo yüklemek için tıklayın
                                        </div>
                                    </x-mary-file>
                                @endif
                            </div>
                        </div>

                        {{-- Colors --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Arka Plan Rengi</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="dl_header_bg_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span class="text-xs font-mono opacity-80 uppercase">{{ $dl_header_bg_color }}</span>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="label"><span
                                        class="label-text text-xs font-semibold uppercase opacity-70">Yazı Rengi</span></label>
                                <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                                    <input type="color" wire:model="dl_header_text_color"
                                        class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                                    <span class="text-xs font-mono opacity-80 uppercase">{{ $dl_header_text_color }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot:content>
            </x-mary-collapse>

            {{-- 2. Tanıtım Dosyaları --}}
            <x-mary-collapse name="intro_files" icon="o-paper-clip">
                <x-slot:heading>
                    <span class="font-medium text-sm">Tanıtım Dosyaları</span>
                </x-slot:heading>
                <x-slot:content>
                    <div class="pt-4 space-y-6">
                        {{-- Upload Form --}}
                        <div class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3">Yeni Dosya Ekle</h4>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-5">
                                    <x-mary-input label="Dosya Adı" wire:model="new_intro_name" placeholder="Örn: Şirket Tanıtımı 2024" class="bg-white btn-sm h-10" />
                                </div>
                                <div class="md:col-span-5">
                                    <x-mary-file wire:model="new_intro_file" accept="application/pdf" class="max-w-full">
                                        <div class="text-left text-gray-400 text-xs py-2 px-1">PDF Seçin (Max: 25MB)</div>
                                    </x-mary-file>
                                </div>
                                <div class="md:col-span-2">
                                    <button class="theme-btn-edit w-full h-10 flex items-center justify-center gap-2"
                                        wire:click="uploadIntroductionFile" wire:listening="uploadIntroductionFile" 
                                        {{ ($is_uploading_intro || !$new_intro_file || !$new_intro_name) ? 'disabled' : '' }}>
                                        <x-mary-icon name="o-arrow-up-tray" class="w-4 h-4" />
                                        <span>Yükle</span>
                                        <span wire:loading wire:target="uploadIntroductionFile" class="loading loading-spinner loading-xs"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- File List --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 uppercase mb-3 flex items-center justify-between">
                                <span>Yüklü Dosyalar</span>
                                <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded-full">{{ count($introduction_files) }}</span>
                            </h4>
                            
                            @if(count($introduction_files) > 0)
                                <div class="space-y-2">
                                    @foreach($introduction_files as $index => $file)
                                        <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg group hover:border-gray-300 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-red-50 text-red-500 rounded-lg">
                                                    <x-mary-icon name="o-document-text" class="w-5 h-5" />
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-800">{{ $file['name'] }}</div>
                                                    <div class="text-[10px] text-gray-400 flex gap-2">
                                                        <span>{{ number_format($file['size'] / 1024 / 1024, 2) }} MB</span>
                                                        <span>•</span>
                                                        <span>{{ \Carbon\Carbon::parse($file['uploaded_at'])->format('d.m.Y H:i') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="{{ app(\App\Services\MinioService::class)->getFileUrl($file['path']) }}" target="_blank" 
                                                   class="btn btn-ghost btn-sm btn-square text-gray-500" title="Görüntüle">
                                                    <x-mary-icon name="o-eye" class="w-4 h-4" />
                                                </a>
                                                <x-mary-button icon="o-trash" class="btn-ghost btn-sm btn-square text-red-500"
                                                    wire:click="deleteIntroductionFile({{ $index }})" 
                                                    wire:confirm="Bu dosyayı silmek istediğinize emin misiniz?" title="Sil" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400 text-sm border border-dashed border-gray-100 rounded-xl bg-gray-50/30">
                                    Henüz dosya yüklenmemiş.
                                </div>
                            @endif
                        </div>
                    </div>
                </x-slot:content>
            </x-mary-collapse>
        </x-mary-accordion>
    </div>
</div>
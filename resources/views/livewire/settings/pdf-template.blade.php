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
                                @if($current_pdf_logo_path)
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
</div>
</div>
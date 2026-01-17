{{--
    🛡️ ZIRHLI BELGELEME KARTI (V12.2)
    -------------------------------------------------------------------------
    PARTIAL    : İndirme Sayfası Ayarları (_download-settings.blade.php)
    SORUMLULUK : Public indirme sayfasının görsel ve içerik ayarlarını yönetir.
    
    BAĞIMLILIKLAR (Variables):
    - $dl_logo, $dl_logo_height, $current_dl_logo_path (Logo Yönetimi)
    - $dl_header_bg_color, $dl_header_text_color (Renkler)
    - $introduction_files, $new_intro_name, $new_intro_file (Dosya Yükleme)
    
    METODLAR (Actions):
    - saveDownloadSettings()
    - removeDownloadLogo()
    - uploadIntroductionFile()
    - deleteIntroductionFile()
    -------------------------------------------------------------------------
--}}
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

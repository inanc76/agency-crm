{{--
🏗️ PARTIAL: PDF Header Ayarları
---------------------------------------------------------------------
Bu dosya PDF'in üst kısmı (Header) ile ilgili ayarları içerir.
Logo yükleme, önizleme ve header renk konfigurasyonları burada yapılır.

BAĞIMLILIKLAR (Variables):
- $pdf_logo (File|Null)
- $pdf_logo_height (Int)
- $current_pdf_logo_path (String|Null)
- $pdf_header_bg_color (String/Hex)
- $pdf_header_text_color (String/Hex)
---------------------------------------------------------------------
--}}
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
                                    <span
                                        class="absolute -top-2 -right-2 bg-green-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-sm">YENİ</span>
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
                            <x-mary-button icon="o-trash" class="btn-ghost btn-sm text-red-500" wire:click="removeLogo"
                                tooltip="Logoyu Kaldır" />
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
                        <x-mary-file wire:model="pdf_logo" accept="image/png, image/jpeg, image/svg+xml" class="max-w-md">
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
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">Arka Plan
                            Rengi</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_header_bg_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_header_bg_color }}</span>
                    </div>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs font-semibold uppercase opacity-70">Yazı
                            Rengi</span></label>
                    <div class="flex items-center gap-3 p-2 border border-gray-200 rounded bg-white">
                        <input type="color" wire:model="pdf_header_text_color"
                            class="w-8 h-8 rounded cursor-pointer border-none bg-transparent" />
                        <span class="text-xs font-mono opacity-80 uppercase">{{ $pdf_header_text_color }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
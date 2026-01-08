<div class="theme-card p-6 shadow-sm"
    style="background-color: {{ $card_bg_color }}; border-color: {{ $card_border_color }}; border-radius: {{ $card_border_radius }};">
    {{-- Card Header --}}
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
        <h2 class="text-sm font-medium text-slate-700">Header Görünüm Ayarları</h2>
    </div>

    {{-- Accordion Sections --}}
    <div class="flex flex-col gap-2">
        {{-- Accordion 1: Logo --}}
        <x-mary-collapse name="group1" group="settings" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-photo" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Logo Ayarları</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start py-2">
                    {{-- Site Name --}}
                    <div class="lg:col-span-3">
                        <x-mary-input label="Site Adı" wire:model="site_name" hint="Logo yüklenmediğinde gözükür" />
                    </div>

                    {{-- Logo --}}
                    <div class="lg:col-span-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Logo</label>
                        <div class="flex items-center gap-2">
                            @if($current_logo_path)
                                <img src="{{ asset('storage/' . $current_logo_path) }}" alt="Current Logo"
                                    class="h-10 object-contain border border-slate-200 rounded p-1 bg-white">
                            @endif
                            <div class="flex-1">
                                <x-mary-file wire:model="logo" accept=".png,.jpg,.jpeg,.svg"
                                    hint="PNG, JPG veya SVG, max 2MB" />
                            </div>
                            <div class="flex gap-1">
                                <button type="button" wire:click="$set('logo_scale', 1)"
                                    class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 1 ? 'text-white' : 'bg-slate-200 text-slate-700' }}"
                                    style="{{ $logo_scale == 1 ? 'background-color: var(--btn-primary-bg)' : '' }}">
                                    1x
                                </button>
                                <button type="button" wire:click="$set('logo_scale', 1.5)"
                                    class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 1.5 ? 'text-white' : 'bg-slate-200 text-slate-700' }}"
                                    style="{{ $logo_scale == 1.5 ? 'background-color: var(--btn-primary-bg)' : '' }}">
                                    1.5x
                                </button>
                                <button type="button" wire:click="$set('logo_scale', 2)"
                                    class="px-2 py-1 text-xs font-medium rounded {{ $logo_scale == 2 ? 'text-white' : 'bg-slate-200 text-slate-700' }}"
                                    style="{{ $logo_scale == 2 ? 'background-color: var(--btn-primary-bg)' : '' }}">
                                    2x
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Favicon --}}
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Favicon</label>
                        <div class="flex items-center gap-2">
                            @if($current_favicon_path)
                                <img src="{{ asset('storage/' . $current_favicon_path) }}" alt="Current Favicon"
                                    class="w-8 h-8 object-contain border border-slate-200 rounded p-1">
                            @endif
                            <div class="flex-1">
                                <x-mary-file wire:model="favicon" accept=".ico,.png" hint="ICO veya PNG, max 512KB" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 2: Menü --}}
        <x-mary-collapse name="group2" group="settings" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-bars-3" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Menü Renk Ayarları</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-2">
                    {{-- Header Background Color --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Header Arka Plan
                            Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="header_bg_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="header_bg_color" placeholder="#3D3373" class="flex-1" />
                        </div>
                    </div>

                    {{-- Menu Background Color --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Menü Arka Plan
                            Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="menu_bg_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="menu_bg_color" placeholder="#3D3373" class="flex-1" />
                        </div>
                    </div>

                    {{-- Menu Text Color --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Menü Yazı Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="menu_text_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="menu_text_color" placeholder="#ffffff" class="flex-1" />
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>

        {{-- Accordion 3: Kenarlık ve Yazı Rengi --}}
        <x-mary-collapse name="group3" group="settings" separator
            class="bg-white border border-slate-200 shadow-sm rounded-lg">
            <x-slot:heading>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-swatch" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Kenarlık ve Yazı Rengi</span>
                </div>
            </x-slot:heading>
            <x-slot:content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-2">
                    {{-- Header Icon Color --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Header İkon ve Yazı
                            Rengi</label>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="header_icon_color"
                                class="w-12 h-10 rounded border border-slate-200 cursor-pointer">
                            <x-mary-input wire:model.live="header_icon_color" placeholder="#ffffff" class="flex-1"
                                hint="Sağ taraftaki bildirim zili ve kullanıcı adı rengi" />
                        </div>
                    </div>

                    {{-- Header Border --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Header Alt Kenarlık</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Renk</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="header_border_color"
                                        class="w-10 h-10 rounded border border-slate-200 cursor-pointer">
                                    <x-mary-input wire:model.live="header_border_color" placeholder="#000000"
                                        class="flex-1" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Kalınlık (px)</label>
                                <x-mary-input type="number" wire:model.live="header_border_width" placeholder="0"
                                    min="0" max="20" />
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot:content>
        </x-mary-collapse>
    </div>

    {{-- Card Footer --}}
    <div class="flex justify-end pt-6 mt-6 border-t border-slate-200">
        <button type="button" wire:click="save" wire:loading.attr="disabled" class="theme-btn-save">
            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Ayarları Kaydet</span>
        </button>
    </div>
</div>
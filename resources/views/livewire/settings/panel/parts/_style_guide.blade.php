<div class="mt-6 flex flex-col gap-2">
    {{-- 1. Logo Ayarları Önizleme --}}
    <x-mary-collapse name="preview1" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-photo" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Logo Ayarları Önizleme</span>
                </div>
                <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--logo-scale:
                    {{ $logo_scale }}</span>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div
                class="p-6 bg-slate-100 rounded-xl flex items-center justify-center border border-dashed border-slate-300">
                <div class="flex items-center gap-4 bg-white p-4 rounded-lg shadow-sm w-full max-w-md">
                    @if($current_logo_path)
                        <img src="{{ asset('storage/' . $current_logo_path) }}" alt="Logo" class="object-contain"
                            style="height: {{ 2.5 * $logo_scale }}rem">
                    @else
                        <span class="text-xl font-bold tracking-tight text-slate-800">{{ $site_name }}</span>
                    @endif
                    <div class="h-4 w-px bg-slate-200 mx-2"></div>
                    <span class="text-sm font-medium text-slate-500">Uygulama Önizleme</span>
                </div>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- 2. Layout Önizleme (Sidebar & Header) --}}
    <x-mary-collapse name="preview2" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-bars-3" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Layout Önizleme (Sidebar & Header)</span>
                </div>
                <div class="flex gap-2">
                    <span
                        class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--sidebar-bg</span>
                    <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--header-bg</span>
                </div>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="flex h-32 border border-slate-200 rounded-xl overflow-hidden">
                {{-- Sidebar Preview --}}
                <div class="w-1/4 h-full flex flex-col p-3 gap-2"
                    style="background-color: {{ $sidebar_bg_color }}; color: {{ $sidebar_text_color }}">
                    <span class="text-[8px] opacity-70 mb-2">SIDEBAR</span>
                    {{-- Active Item --}}
                    <div class="h-6 rounded w-3/4" style="background-color: {{ $sidebar_active_item_bg_color }}"></div>
                    {{-- Normal Item --}}
                    <div class="h-6 rounded w-full opacity-50 bg-white/10"></div>
                </div>

                {{-- Header Preview --}}
                <div class="flex-1 flex flex-col">
                    <div class="h-12 w-full flex items-center justify-between px-4"
                        style="background-color: {{ $header_bg_color }}; border-bottom: {{ $header_border_width }}px solid {{ $header_border_color }}">
                        <span class="text-[8px] px-2 py-1 rounded"
                            style="background-color: white; color: black; opacity: 0.5;">HEADER</span>
                        {{-- Header Active Item --}}
                        <div class="h-6 w-16 rounded-full flex items-center justify-center text-[8px]"
                            style="background-color: {{ $header_active_item_bg_color }}; color: {{ $header_active_item_text_color }}">
                            Active
                        </div>
                    </div>
                    <div class="flex-1 bg-slate-50 p-4">
                        <span class="text-[10px] text-slate-400">Content Area</span>
                    </div>
                </div>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- Previews 3 to 12 follow the same pattern... --}}
    {{-- I will consolidate them all here to atomize the main file --}}

    {{-- 3. Kenarlık ve Yazı Rengi Önizleme --}}
    <x-mary-collapse name="preview3" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-swatch" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Kenarlık ve Yazı Rengi Önizleme</span>
                </div>
                <span
                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--header-icon-color</span>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="p-6 rounded-xl border-t-0"
                style="background-color: {{ $header_bg_color }}; border-bottom: {{ $header_border_width }}px solid {{ $header_border_color }}">
                <div class="flex items-center justify-end gap-6" style="color: {{ $header_icon_color }}">
                    <div class="flex flex-col items-center gap-1">
                        <x-mary-icon name="o-bell" class="w-6 h-6" />
                        <span class="text-[8px] font-mono opacity-50">--header-icon-color</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold">John Doe</span>
                        <x-mary-icon name="o-chevron-down" class="w-4 h-4" />
                    </div>
                </div>
            </div>
            <div class="mt-2 flex justify-between items-center">
                <p class="text-xs text-slate-500 italic">* Alt Kenarlık: {{ $header_border_width }}px
                </p>
                <span class="text-[9px] font-mono text-slate-400">border-bottom:
                    var(--header-border-width) solid var(--header-border-color)</span>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- 4. Global Tipografi Önizleme --}}
    <x-mary-collapse name="preview4" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-language" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Global Tipografi Önizleme</span>
                </div>
                <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">font-family:
                    {{ $font_family }}</span>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="p-6 rounded-xl border border-slate-100 bg-white" style="font-family: {{ $font_family }}">
                <div class="flex items-baseline gap-4 mb-2">
                    <h1 class="text-3xl font-bold" style="color: {{ $heading_color }}">H1: Başlık Seviye
                        1</h1>
                    <span class="text-[10px] font-mono text-slate-400">--color-text-heading</span>
                </div>
                <h2 class="text-2xl font-semibold mb-2" style="color: {{ $heading_color }}">H2: Başlık
                    Seviye 2</h2>
                <h3 class="text-xl font-medium mb-4" style="color: {{ $heading_color }}">H3: Başlık
                    Seviye 3</h3>
                <div class="relative pt-4 border-t border-slate-50">
                    <span
                        class="absolute -top-2.5 left-4 bg-white px-2 text-[10px] font-mono text-slate-400">--color-text-base</span>
                    <p class="leading-relaxed" style="color: {{ $base_text_color }}">
                        Bu paragraf metni, ayarlar sayfasından seçilen ana metin (base text) rengini ve
                        seçilen font ailesini kullanmaktadır.
                        Sistemdeki tüm uzun metinler ve açıklamalar bu biçimde görünecektir.
                    </p>
                </div>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- 5. Input & Validation Önizleme --}}
    <x-mary-collapse name="preview5" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-pencil-square" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Input & Validation Önizleme</span>
                </div>
                <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--input-radius:
                    {{ $input_border_radius }}</span>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-slate-700">Normal & Focus
                            State</label>
                        <span class="text-[9px] font-mono text-slate-400">--input-border</span>
                    </div>
                    <input type="text" placeholder="Focus durumunu test edin..."
                        class="w-full px-4 py-2 border transition-all duration-200 outline-none" style="border-color: {{ $input_border_color }}; 
                                  border-radius: {{ $input_border_radius }}; 
                                  padding-top: {{ $input_vertical_padding }}; 
                                  padding-bottom: {{ $input_vertical_padding }};">
                    <p class="text-[10px] font-mono text-slate-500 mt-2">Focus ring: <span
                            class="inline-block w-3 h-3 rounded-full align-middle"
                            style="background-color: {{ $input_focus_ring_color }}"></span> <span
                            class="text-slate-400">--input-focus-ring</span></p>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium" style="color: {{ $input_error_text_color }}">Error
                            State</label>
                        <span class="text-[9px] font-mono text-slate-400">--error-color</span>
                    </div>
                    <input type="text" value="Hatalı veri girişi"
                        class="w-full px-4 py-2 border transition-all duration-200 outline-none" style="border-color: {{ $input_error_border_color }}; 
                                  border-radius: {{ $input_border_radius }}; 
                                  padding-top: {{ $input_vertical_padding }}; 
                                  padding-bottom: {{ $input_vertical_padding }};
                                  box-shadow: 0 0 0 2px {{ $input_error_ring_color }}40;">
                    <p class="text-[10px] font-mono mt-1" style="color: {{ $input_error_text_color }}">
                        --input-error-text</p>
                </div>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- 6. Buton & Aksiyon Parametreleri Önizleme --}}
    <x-mary-collapse name="preview6" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-cursor-arrow-rays" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Buton & Aksiyon Parametreleri
                        Önizleme</span>
                </div>
                <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">Isolated
                    Design System</span>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="p-6 bg-white rounded-xl border border-slate-100 grid grid-cols-2 md:grid-cols-3 gap-6">
                <div class="flex flex-col items-center gap-2">
                    <button class="theme-btn-save w-full justify-center">
                        <x-mary-icon name="o-check" class="w-4 h-4" /> <span>Kaydet</span>
                    </button>
                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-save</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <button class="theme-btn-action w-full justify-center">
                        <x-mary-icon name="o-plus" class="w-4 h-4" /> <span>Yeni Ekle</span>
                    </button>
                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-action</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <button class="theme-btn-edit w-full justify-center">
                        <x-mary-icon name="o-pencil-square" class="w-4 h-4" /> <span>Düzenle</span>
                    </button>
                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-edit</span>
                </div>
                <div class="flex flex-col items-center gap-2 text-center">
                    <button class="theme-btn-delete w-full justify-center">
                        <x-mary-icon name="o-trash" class="w-4 h-4" /> <span>Sil</span>
                    </button>
                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-delete</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <button class="theme-btn-cancel w-full justify-center">
                        <span>İptal</span>
                    </button>
                    <span class="text-[9px] font-mono text-slate-400">.theme-btn-cancel</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <div class="h-10 flex items-center">
                        <a href="#" class="text-sm font-semibold underline"
                            style="color: {{ $action_link_color }}">Detayları Gör</a>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400">--action-link-color</span>
                </div>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- 7. Kart & Konteyner Önizleme --}}
    <x-mary-collapse name="preview7" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-rectangle-group" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Kart & Konteyner Önizleme</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">.theme-card</span>
                    <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--card-radius:
                        {{ $card_border_radius }}</span>
                </div>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="p-10 bg-slate-200/50 rounded-2xl border border-dashed border-slate-300">
                <div class="transition-all duration-300 shadow-xl relative" style="background-color: {{ $card_bg_color }}; 
                            border: 1px solid {{ $card_border_color }}; 
                            border-radius: {{ $card_border_radius }};">
                    <span
                        class="absolute -top-3 -right-3 bg-white border border-slate-200 text-[9px] font-mono px-2 py-1 rounded shadow-sm">.theme-card</span>
                    {{-- Card Header Preview --}}
                    <div class="px-6 py-4 border-b flex justify-between items-center"
                        style="border-color: {{ $card_border_color }}">
                        <h4 class="font-bold text-lg" style="color: {{ $heading_color }}">Kart Başlığı
                        </h4>
                        <span class="text-[8px] font-mono text-slate-400">--card-border</span>
                    </div>

                    {{-- Card Body Preview --}}
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-sm leading-relaxed max-w-[70%]" style="color: {{ $base_text_color }}">
                                Bu alan, seçtiğiniz <strong>Kart Arka Planı</strong> ve <strong>Kenarlık
                                    Rengi</strong> ile şekillenir.
                            </p>
                            <span class="text-[8px] font-mono text-slate-400">--card-bg</span>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <div class="h-8 w-24 rounded shadow-sm opacity-50"
                                style="background-color: var(--btn-save-bg);"></div>
                            <div class="h-8 w-24 rounded shadow-sm opacity-50"
                                style="background-color: var(--btn-cancel-bg);"></div>
                        </div>
                    </div>

                    {{-- Card Footer Preview --}}
                    <div class="px-6 py-3 bg-black/5 rounded-b-[inherit]"
                        style="border-top: 1px solid {{ $card_border_color }}">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] uppercase tracking-wider font-bold opacity-40"
                                style="color: {{ $base_text_color }}">Önizleme Modu</span>
                            <div class="flex -space-x-2">
                                <div class="w-6 h-6 rounded-full border-2 border-white bg-indigo-400">
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 border-white bg-emerald-400">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div
                    class="p-3 bg-white rounded-lg border border-slate-100 shadow-sm group hover:border-indigo-200 transition-colors">
                    <span class="text-[10px] text-slate-400 block mb-1 font-mono">--card-bg</span>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded border border-slate-200"
                            style="background-color: {{ $card_bg_color }}"></div>
                        <code class="text-xs font-mono text-slate-600">{{ $card_bg_color }}</code>
                    </div>
                </div>
                <div
                    class="p-3 bg-white rounded-lg border border-slate-100 shadow-sm group hover:border-indigo-200 transition-colors">
                    <span class="text-[10px] text-slate-400 block mb-1 font-mono">--card-border</span>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded border border-slate-200"
                            style="background-color: {{ $card_border_color }}"></div>
                        <code class="text-xs font-mono text-slate-600">{{ $card_border_color }}</code>
                    </div>
                </div>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- Previews 8 to 12 extracted similarly --}}
    {{-- 8. Tablo Hover Önizleme --}}
    <x-mary-collapse name="preview8" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-table-cells" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Tablo Hover Önizleme</span>
                </div>
                <div class="flex gap-2">
                    <span
                        class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--table-hover-bg</span>
                </div>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="p-4 bg-white rounded-xl border border-slate-100">
                <table class="table w-full">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400">
                            <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">
                                Örnek Sütun 1</th>
                            <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">
                                Örnek Sütun 2</th>
                            <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">
                                Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-50 transition-colors duration-200">
                            <td class="py-3 px-4 text-sm">Satır Verisi A-1</td>
                            <td class="py-3 px-4 text-sm">Satır Verisi A-2</td>
                            <td class="py-3 px-4 text-sm"><span
                                    class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold">AKTİF</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div
                    class="mt-4 p-3 bg-slate-50 rounded-lg flex items-center justify-between border border-dashed border-slate-200">
                    <span class="text-xs text-slate-500 italic">* Satır hover efektini test edin.</span>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded border border-slate-200"
                                style="background-color: {{ $table_hover_bg_color }}"></div>
                            <span class="text-[10px] font-mono text-slate-400">BG</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded border border-slate-200"
                                style="background-color: {{ $table_hover_text_color }}"></div>
                            <span class="text-[10px] font-mono text-slate-400">TEXT</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:content>
    </x-mary-collapse>

    {{-- Previews 9 to 12 simplified for brevity but keeping core logic --}}
    {{-- (Internal note: Keeping the structure intact as requested) --}}
    @include('livewire.settings.panel.parts._style_guide_more')
</div>
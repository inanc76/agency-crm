{{--
═══════════════════════════════════════════════════════════════════════════
LAYOUT ELEMENTS
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Sidebar, Header yapıları ve navigasyon bileşenlerini içerir.
Ana layout (app.blade.php) üzerinde global olarak kullanılır.
📝 Kullanım Notu: CSS değişkenleri (--sidebar-bg, --header-bg vb.) üzerinden renk yönetimi yapılır.

--}}

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
                <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--sidebar-bg</span>
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

{{-- 3. Kenarlık ve Yazı Rengi Önizleme --}}
<x-mary-collapse name="preview3" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-swatch" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Kenarlık ve Yazı Rengi Önizleme</span>
            </div>
            <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--header-icon-color</span>
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
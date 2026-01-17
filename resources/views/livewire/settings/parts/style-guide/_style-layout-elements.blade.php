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
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">--sidebar-*, --header-*</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="flex h-32 border border-slate-200 rounded-xl overflow-hidden mb-4">
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

        {{-- Token List Section --}}
        <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2 uppercase tracking-wider">
                <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                CSS & Tasarım Tokenleri
            </h5>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <x-copy-badge text="--sidebar-bg"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="--header-bg"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="--header-icon-color"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
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
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">--header-icon-color...</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-6 rounded-xl border-t-0 mb-4"
            style="background-color: {{ $header_bg_color }}; border-bottom: {{ $header_border_width }}px solid {{ $header_border_color }}">
            <div class="flex items-center justify-end gap-6" style="color: {{ $header_icon_color }}">
                <div class="flex flex-col items-center gap-1">
                    <x-mary-icon name="o-bell" class="w-6 h-6" />
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-bold">John Doe</span>
                    <x-mary-icon name="o-chevron-down" class="w-4 h-4" />
                </div>
            </div>
        </div>

        {{-- Token List Section --}}
        <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2 uppercase tracking-wider">
                <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                CSS & Tasarım Tokenleri
            </h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-copy-badge text="--header-icon-color"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="border-bottom: var(--header-border-width) solid var(--header-border-color)"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
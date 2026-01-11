{{-- 9. Combo Box Önizleme --}}
<x-mary-collapse name="preview9" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-chevron-up-down" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Combo Box Önizleme</span>
            </div>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4">
            <div>
                <select class="select select-sm bg-white border-slate-200 text-xs w-full">
                    <option>Örnek Seçim</option>
                </select>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>

{{-- 10. Table Avatar Styling --}}
<x-mary-collapse name="preview10" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-user-circle" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Tablo Avatar Stili</span>
            </div>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-6 bg-white rounded-xl border border-slate-100">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm"
                style="background-color: {{ $table_avatar_bg_color }}; color: {{ $table_avatar_text_color }}; border: 1px solid {{ $table_avatar_border_color }};">
                VB
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>

{{-- 11. Dashboard Elemanları Önizleme --}}
<x-mary-collapse name="preview11" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-presentation-chart-line" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Dashboard Elemanları Önizleme</span>
            </div>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl shadow-sm" style="background-color: {{ $dashboard_card_bg_color }}">
                <h4 class="text-lg font-bold" style="color: {{ $dashboard_card_text_color }}">7</h4>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>

{{-- 12. User Menu & Dropdown Önizleme --}}
<x-mary-collapse name="preview12" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-user" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">User Menu & Dropdown Önizleme</span>
            </div>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-6 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="relative">
                <x-mary-icon name="o-bell" class="w-6 h-6 text-slate-400" />
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full"
                    style="background-color: {{ $notification_badge_color }}"></span>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
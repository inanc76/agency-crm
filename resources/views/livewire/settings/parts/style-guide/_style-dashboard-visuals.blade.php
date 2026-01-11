{{--
═══════════════════════════════════════════════════════════════════════════
DASHBOARD VISUALS
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: Avatar bileşenleri, dropdown menüler ve dashboard widget örneklerini içerir.
Dashboard sayfası, Header user menu ve Tablo satırlarında global olarak kullanılır.
📝 Kullanım Notu: Gradient avatarlar için inline linear-gradient stilleri kullanılır. Dashboard stats için color-mix
fonksiyonu statik renkler üzerinde varyasyon oluşturur.

--}}

{{-- 10. Table Avatar Styling --}}
<x-mary-collapse name="preview10" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-user-circle" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Tablo Avatar Stili</span>
            </div>
            <div class="flex gap-2">
                <span
                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--table-avatar-bg</span>
            </div>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-6 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center">
            <div class="flex items-center gap-12">
                {{-- Generic Avatar --}}
                <div class="flex flex-col items-center gap-2">
                    <span class="text-xs text-slate-500">Varsayılan</span>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-sm"
                        style="background-color: {{ $table_avatar_bg_color }}; color: {{ $table_avatar_text_color }}; border: 1px solid {{ $table_avatar_border_color }};">
                        JD
                    </div>
                </div>
                {{-- With Gradient --}}
                <div class="flex flex-col items-center gap-2">
                    <span class="text-xs text-slate-500">Gradient (User)</span>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm"
                        style="background: linear-gradient(135deg, {{ $avatar_gradient_start_color }}, {{ $avatar_gradient_end_color }})">
                        AK
                    </div>
                </div>
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
            <div class="flex gap-2">
                <span
                    class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">--dashboard-stats-*</span>
            </div>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Stats 1 --}}
            <div class="p-4 rounded-xl shadow-sm" style="background-color: {{ $dashboard_card_bg_color }}">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: color-mix(in srgb, {{ $dashboard_stats_1_color }}, white 90%); color: {{ $dashboard_stats_1_color }};">
                        <x-mary-icon name="o-users" class="w-4 h-4" />
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full"
                        style="background-color: color-mix(in srgb, {{ $dashboard_stats_1_color }}, white 90%); color: {{ $dashboard_stats_1_color }};">Aktif</span>
                </div>
                <h4 class="text-lg font-bold" style="color: {{ $dashboard_card_text_color }}">7</h4>
            </div>
            {{-- Stats 2 --}}
            <div class="p-4 rounded-xl shadow-sm" style="background-color: {{ $dashboard_card_bg_color }}">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: color-mix(in srgb, {{ $dashboard_stats_2_color }}, white 90%); color: {{ $dashboard_stats_2_color }};">
                        <x-mary-icon name="o-cube" class="w-4 h-4" />
                    </div>
                </div>
                <h4 class="text-lg font-bold" style="color: {{ $dashboard_card_text_color }}">9</h4>
            </div>
            {{-- Stats 3 --}}
            <div class="p-4 rounded-xl shadow-sm" style="background-color: {{ $dashboard_card_bg_color }}">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: color-mix(in srgb, {{ $dashboard_stats_3_color }}, white 90%); color: {{ $dashboard_stats_3_color }};">
                        <x-mary-icon name="o-archive-box" class="w-4 h-4" />
                    </div>
                </div>
                <h4 class="text-lg font-bold" style="color: {{ $dashboard_card_text_color }}">5</h4>
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
        <div
            class="flex flex-col md:flex-row gap-8 items-center justify-center p-6 bg-slate-50 border border-slate-200 rounded-xl">
            {{-- User Icon & Badge --}}
            <div class="flex items-center gap-4">
                <div class="relative">
                    <x-mary-icon name="o-bell" class="w-6 h-6 text-slate-400" />
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full"
                        style="background-color: {{ $notification_badge_color }}"></span>
                </div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                    style="background: linear-gradient(to right, {{ $avatar_gradient_start_color }}, {{ $avatar_gradient_end_color }})">
                    U
                </div>
            </div>

            {{-- Dropdown Preview --}}
            <div class="w-64 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-50"
                    style="background: linear-gradient(to right, {{ $dropdown_header_bg_start_color }}, {{ $dropdown_header_bg_end_color }})">
                    <p class="text-sm font-medium text-slate-800">Kullanıcı Adı</p>
                    <p class="text-xs text-slate-500">user@example.com</p>
                </div>
                <div class="p-2">
                    <div class="px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded">Profil
                    </div>
                    <div class="dropdown-item-danger">Çıkış</div>
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
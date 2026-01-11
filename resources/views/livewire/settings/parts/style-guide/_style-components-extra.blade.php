{{-- 
═══════════════════════════════════════════════════════════════════════════
🧩 STYLE GUIDE PART 4: COMPONENTS & EXTRA ELEMENTS
═══════════════════════════════════════════════════════════════════════════

📦 PACKAGE: resources/views/livewire/settings/parts/style-guide
📄 FILE: _style-components-extra.blade.php
🏗️ CONSTITUTION: V10

┌─────────────────────────────────────────────────────────────────────────┐
│ 💼 İŞ MANTIĞI ŞERHI (Business Logic)                                   │
└─────────────────────────────────────────────────────────────────────────┘

Bu partial, sistemin GELİŞMİŞ BİLEŞENLERİNİ sergiler:

1. **Logo & Branding:**
   - Logo scale ayarları
   - Site name fallback
   - Kullanım: Header, Login sayfası

2. **Layout Components:**
   - Sidebar: Menü yapısı, active states
   - Header: Border, icon colors, active items
   - Kullanım: Ana layout (app.blade.php)

3. **Card Components:**
   - .theme-card: Standart kart bileşeni
   - Card header, body, footer yapıları
   - Kullanım: Customer Detail, Offer Detail, Dashboard

4. **Table Components:**
   - Table hover states
   - Badge stilleri (aktif, beklemede, iptal)
   - Kullanım: Customer List, Offer List, Service List

5. **Dashboard Components:**
   - Stats kartları (3 renk varyasyonu)
   - Dashboard card bg/text colors
   - Kullanım: Dashboard sayfası

6. **Avatar Components:**
   - Table avatar: Tablo satırlarında kullanıcı avatarları
   - Gradient avatar: User menu dropdown
   - Notification badge
   - Kullanım: Header, Tablo satırları

7. **User Menu & Dropdown:**
   - Avatar gradient
   - Dropdown header gradient
   - Notification badge color
   - Kullanım: Header user menu

═══════════════════════════════════════════════════════════════════════════
--}}

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
                        <tr class="border-b border-slate-50 transition-colors duration-200">
                            <td class="py-3 px-4 text-sm">Satır Verisi B-1</td>
                            <td class="py-3 px-4 text-sm">Satır Verisi B-2</td>
                            <td class="py-3 px-4 text-sm"><span
                                    class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold">BEKLEMEDE</span>
                            </td>
                        </tr>
                        <tr class="transition-colors duration-200">
                            <td class="py-3 px-4 text-sm">Satır Verisi C-1</td>
                            <td class="py-3 px-4 text-sm">Satır Verisi C-2</td>
                            <td class="py-3 px-4 text-sm"><span class="badge-danger">İPTAL</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div
                    class="mt-4 p-3 bg-slate-50 rounded-lg flex items-center justify-between border border-dashed border-slate-200">
                    <span class="text-xs text-slate-500 italic">* Satırların üzerine gelerek hover
                        efektini test edebilirsiniz.</span>
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

    {{-- 9. Combo Box Önizleme --}}
    <x-mary-collapse name="preview9" group="previews" separator
        class="bg-white border border-slate-200 shadow-sm rounded-lg">
        <x-slot:heading>
            <div class="flex items-center justify-between w-full pr-4">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-chevron-up-down" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold text-slate-700">Combo Box Önizleme</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">.select</span>
                    <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">.select-sm</span>
                </div>
            </div>
        </x-slot:heading>
        <x-slot:content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4">
                {{-- Filtre Combo Box --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <x-mary-icon name="o-funnel" class="w-4 h-4 text-blue-500" />
                        <h4 class="text-sm font-bold text-slate-800">Filtre Combo Box</h4>
                        <span class="text-[10px] font-mono text-slate-400">(Liste Sayfaları)</span>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-3 flex-wrap">
                            <select class="select select-sm bg-white border-slate-200 text-xs">
                                <option>Tüm Kategoriler</option>
                                <option>Web Hosting</option>
                                <option>Domain</option>
                                <option>E-posta</option>
                            </select>
                            <select class="select select-sm bg-white border-slate-200 text-xs">
                                <option>Tüm Durumlar</option>
                                <option>Aktif</option>
                                <option>Pasif</option>
                            </select>
                        </div>
                        <p class="mt-3 text-[10px] text-slate-400">
                            Kullanım: <code
                                class="bg-white px-1 rounded border border-slate-200">select select-sm bg-white border-slate-200 text-xs</code>
                        </p>
                    </div>
                </div>

                {{-- Tab Inline Filtre --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <x-mary-icon name="o-adjustments-horizontal" class="w-4 h-4 text-emerald-500" />
                        <h4 class="text-sm font-bold text-slate-800">Tab Inline Filtre</h4>
                        <span
                            class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded">XS</span>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-700">Hizmetler</span>
                            <select class="select select-xs bg-white border-slate-200">
                                <option>Tüm Durumlar</option>
                                <option>Aktif</option>
                                <option>Pasif</option>
                            </select>
                        </div>
                        <p class="mt-3 text-[10px] text-slate-400">
                            Kullanım: <code
                                class="bg-white px-1 rounded border border-slate-200">select select-xs bg-white border-slate-200</code>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4 pt-0">

                {{-- Form Combo Box --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <x-mary-icon name="o-document-plus" class="w-4 h-4 text-purple-500" />
                        <h4 class="text-sm font-bold text-slate-800">Form Combo Box</h4>
                        <span class="text-[10px] font-mono text-slate-400">(Yeni Ekle / Düzenle)</span>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-3">
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">Müşteri Seçimi
                                *</label>
                            <select class="select w-full bg-white">
                                <option value="">Müşteri Seçin</option>
                                <option>Örnek Müşteri A.Ş.</option>
                                <option>Demo Ltd. Şti.</option>
                                <option>Test Ticaret ve San. A.Ş.</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 opacity-60">Kategori</label>
                            <select class="select w-full bg-white">
                                <option value="">Kategori Seçin</option>
                                <option>Web Hosting</option>
                                <option>Domain</option>
                            </select>
                        </div>
                        <p class="text-[10px] text-slate-400">
                            Kullanım: <code
                                class="bg-white px-1 rounded border border-slate-200">select w-full bg-white</code>
                        </p>
                    </div>
                </div>
            </div>

            {{-- CSS Sınıfları Tablosu --}}
            <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
                <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                    <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                    Combo Box CSS Sınıfları
                </h5>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <code class="bg-white px-2 py-1 rounded border border-slate-200">select</code>
                        <span class="text-slate-500">Temel stil</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <code class="bg-white px-2 py-1 rounded border border-slate-200">select-sm</code>
                        <span class="text-slate-500">Küçük boyut</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <code
                            class="bg-emerald-50 text-emerald-700 px-2 py-1 rounded border border-emerald-200">select-xs</code>
                        <span class="text-slate-500 font-bold">Ekstra Küçük</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <code class="bg-white px-2 py-1 rounded border border-slate-200">w-full</code>
                        <span class="text-slate-500">Tam genişlik</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <code class="bg-white px-2 py-1 rounded border border-slate-200">bg-white</code>
                        <span class="text-slate-500">Beyaz arka plan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <code class="bg-white px-2 py-1 rounded border border-slate-200">border-slate-200</code>
                        <span class="text-slate-500">Kenarlık</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <code class="bg-white px-2 py-1 rounded border border-slate-200">text-xs</code>
                        <span class="text-slate-500">Küçük font</span>
                    </div>
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
</div>
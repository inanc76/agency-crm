{{--
═══════════════════════════════════════════════════════════════════════════
CARDS & TABLES
═══════════════════════════════════════════════════════════════════════════

💼 İş Mantığı Şerhi: .theme-card standartları, veri tabloları ve form elemanları (Combo Box) görsel şablonlarını içerir.
Offer Detail, Customer Detail ve Liste sayfalarında yoğun olarak kullanılır.
📝 Kullanım Notu: .theme-card sınıfı ile radius ve shadow ayarları yapılır. .select-sm ve .select-xs sınıfları dinamik
boyutlandırma sağlar.

--}}

{{-- 7. Kart & Konteyner Önizleme --}}
<x-mary-collapse name="preview7" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-rectangle-group" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Kart & Konteyner Önizleme</span>
            </div>
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">.theme-card, --card-*</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-10 bg-slate-200/50 rounded-2xl border border-dashed border-slate-300 mb-4">
            <div class="transition-all duration-300 shadow-xl relative" style="background-color: {{ $card_bg_color }}; 
                            border: 1px solid {{ $card_border_color }}; 
                            border-radius: {{ $card_border_radius }};">
                {{-- Card Header Preview --}}
                <div class="px-6 py-4 border-b flex justify-between items-center"
                    style="border-color: {{ $card_border_color }}">
                    <h4 class="font-bold text-lg" style="color: {{ $heading_color }}">Kart Başlığı</h4>
                </div>

                {{-- Card Body Preview --}}
                <div class="p-6">
                    <p class="text-sm leading-relaxed mb-4" style="color: {{ $base_text_color }}">
                        Bu alan, seçtiğiniz <strong>Kart Arka Planı</strong> ve <strong>Kenarlık Rengi</strong> ile
                        şekillenir.
                    </p>
                </div>
            </div>
        </div>

        {{-- Token List Section --}}
        <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2 uppercase tracking-wider">
                <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                CSS & Tasarım Tokenleri
            </h5>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <x-copy-badge text=".theme-card"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="--card-bg"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="--card-border"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge :text="$card_border_radius"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between">
                    --card-radius: {{ $card_border_radius }}
                </x-copy-badge>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>

{{-- 8. Standart Tablo Tasarımı (Agency Table) --}}
<x-mary-collapse name="preview8" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-table-cells" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Agency Standart Tablo Tasarımı</span>
            </div>
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">.agency-table, .item-name</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="space-y-6">
            {{-- Table Preview Area --}}
            <div class="p-6 bg-slate-100 rounded-xl border border-slate-200">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
                    <table class="agency-table">
                        <thead>
                            <tr
                                style="background-color: {{ $table_header_bg_color }}; border-bottom: 2px solid {{ $table_divide_color }};">
                                <th class="w-10">
                                    <input type="checkbox" class="checkbox checkbox-xs rounded border-slate-300">
                                </th>
                                <th style="color: {{ $table_header_text_color }};">Müşteri</th>
                                <th style="color: {{ $table_header_text_color }};">Şehir</th>
                                <th style="color: {{ $table_header_text_color }}; text-align: center;">Kişiler</th>
                                <th style="color: {{ $table_header_text_color }}; text-align: center;">Varlıklar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="group transition-all duration-200"
                                onmouseover="this.style.backgroundColor='{{ $table_hover_bg_color }}'; this.style.color='{{ $table_hover_text_color }}';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit';"
                                style="border-bottom: 1px solid {{ $table_divide_color }};">
                                <td class="px-6 py-4"><input type="checkbox"
                                        class="checkbox checkbox-xs rounded border-slate-300"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar-circle"
                                            style="background-color: {{ $table_avatar_bg_color }}; color: {{ $table_avatar_text_color }}; border: 1px solid {{ $table_avatar_border_color }};">
                                            D
                                        </div>
                                        <div>
                                            <div class="item-name"
                                                style="font-size: {{ $table_item_name_size }}; font-weight: {{ $table_item_name_weight }}; color: {{ $list_card_link_color }};">
                                                Deneme Firması
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span
                                        class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold border border-blue-100 uppercase">İstanbul</span>
                                </td>
                                <td class="text-center font-bold text-slate-400">1</td>
                                <td class="text-center">
                                    <span class="count-badge"
                                        style="background-color: {{ $card_bg_color }}; border: 1px solid {{ $table_divide_color }}; color: {{ $heading_color }};">
                                        4
                                    </span>
                                </td>
                            </tr>
                            <tr class="group transition-all duration-200"
                                onmouseover="this.style.backgroundColor='{{ $table_hover_bg_color }}'; this.style.color='{{ $table_hover_text_color }}';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit';"
                                style="border-bottom: 1px solid {{ $table_divide_color }};">
                                <td class="px-6 py-4"><input type="checkbox"
                                        class="checkbox checkbox-xs rounded border-slate-300"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar-circle"
                                            style="background-color: {{ $table_avatar_bg_color }}; color: {{ $table_avatar_text_color }}; border: 1px solid {{ $table_avatar_border_color }};">
                                            G
                                        </div>
                                        <div>
                                            <div class="item-name"
                                                style="font-size: {{ $table_item_name_size }}; font-weight: {{ $table_item_name_weight }}; color: {{ $list_card_link_color }};">
                                                Gca Medya
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span
                                        class="px-2 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] uppercase font-bold">-</span>
                                </td>
                                <td class="text-center font-bold text-slate-400">2</td>
                                <td class="text-center">
                                    <span class="count-badge"
                                        style="background-color: {{ $card_bg_color }}; border: 1px solid {{ $table_divide_color }}; color: {{ $heading_color }};">
                                        0
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Copyable CSS Block --}}
            <div x-data="{ showCode: false, copied: false, cssCode: `/* Agency Standart Tablo Sınıfı */
.agency-table {
    width: 100%;
    text-align: left;
    font-size: 0.875rem; /* text-sm */
    border-collapse: collapse;
}

.agency-table thead {
    background-color: {{ $table_header_bg_color }};
    border-bottom: 2px solid {{ $table_divide_color }};
}

.agency-table th {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    color: {{ $table_header_text_color }};
}

.agency-table tbody tr {
    border-bottom: 1px solid {{ $table_divide_color }};
    transition: all 0.2s ease;
    cursor: pointer;
}

.agency-table tbody tr:hover {
    background-color: {{ $table_hover_bg_color }} !important;
    color: {{ $table_hover_text_color }} !important;
}

.agency-table td {
    padding: 1rem 1.5rem;
    vertical-align: middle;
}

/* Tablo İsim & Link Stili */
.agency-table .item-name {
    font-size: {{ $table_item_name_size }};
    font-weight: {{ $table_item_name_weight }};
    color: {{ $list_card_link_color }};
}

/* Avatar Çemberi */
.agency-table .avatar-circle {
    width: 2.25rem;
    height: 2.25rem;
    background-color: {{ $table_avatar_bg_color }};
    color: {{ $table_avatar_text_color }};
    border: 1px solid {{ $table_avatar_border_color }};
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}` }" class="space-y-3">
                <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="flex items-center gap-2">
                        <x-mary-icon name="o-code-bracket-square" class="w-5 h-5 text-indigo-500" />
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kopyalanabilir CSS
                            Sınıfları</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <button @click="showCode = !showCode"
                            class="text-[10px] font-bold text-slate-400 hover:text-indigo-500 transition-colors uppercase tracking-tight">
                            <span x-text="showCode ? 'Kodu Gizle' : 'Kodu Göster'"></span>
                        </button>
                        <div class="h-4 w-[1px] bg-slate-200"></div>
                        <button
                            @click="navigator.clipboard.writeText(cssCode); copied = true; setTimeout(() => copied = false, 2000)"
                            class="flex items-center gap-2 group cursor-pointer" title="Kodu Kopyala">
                            <span x-show="!copied"
                                class="text-[10px] font-bold text-slate-500 group-hover:text-indigo-600 uppercase tracking-tight transition-colors">Kodu
                                Kopyala</span>
                            <span x-show="copied"
                                class="text-[10px] font-bold text-green-600 uppercase tracking-tight animate-bounce">Kopyalandı!</span>

                            <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-200"
                                :class="copied ? 'bg-green-50' : 'bg-white border border-slate-200 shadow-sm group-hover:border-indigo-300 group-hover:shadow-md'">
                                <x-mary-icon x-show="!copied" name="o-document-duplicate"
                                    class="w-4 h-4 text-slate-400 group-hover:text-indigo-500" />
                                <x-mary-icon x-show="copied" name="o-check" class="w-4 h-4 text-green-500" />
                            </div>
                        </button>
                    </div>
                </div>

                <div x-show="showCode" x-collapse x-cloak class="relative group">
                    <pre class="bg-slate-900 text-slate-300 p-4 rounded-xl text-xs overflow-x-auto leading-relaxed border border-slate-800"
                        x-text="cssCode"></pre>
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
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">.select, .select-sm...</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-4 mb-4">
            {{-- Filtre Combo Box --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <x-mary-icon name="o-funnel" class="w-4 h-4 text-blue-500" />
                    <h4 class="text-sm font-bold text-slate-800">Filtre Combo Box</h4>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <select class="select select-sm bg-white border-slate-200 text-xs">
                        <option>Tüm Kategoriler</option>
                        <option>Web Hosting</option>
                    </select>
                </div>
            </div>

            {{-- Form Combo Box --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <x-mary-icon name="o-document-plus" class="w-4 h-4 text-purple-500" />
                    <h4 class="text-sm font-bold text-slate-800">Form Combo Box</h4>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <select class="select w-full bg-white">
                        <option value="">Seçim Yapın...</option>
                        <option>Örnek Seçenek</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Token List Section --}}
        <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2 uppercase tracking-wider">
                <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                CSS & Tasarım Tokenleri
            </h5>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <x-copy-badge text="select"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="select-sm"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="select-xs"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between font-bold" />
                <x-copy-badge text="w-full bg-white"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
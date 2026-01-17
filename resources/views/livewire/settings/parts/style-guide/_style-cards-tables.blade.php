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

{{-- 8. Tablo Hover Önizleme --}}
<x-mary-collapse name="preview8" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-table-cells" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Tablo Hover Önizleme</span>
            </div>
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">--table-hover-*</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        <div class="p-4 bg-white rounded-xl border border-slate-100 mb-4">
            <table class="table w-full">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400">
                        <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">Sütun 1</th>
                        <th class="text-left py-3 px-4 text-xs font-bold uppercase tracking-wider">Sütun 2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-50 transition-colors duration-200">
                        <td class="py-3 px-4 text-sm">Satır Verisi A-1</td>
                        <td class="py-3 px-4 text-sm"><span
                                class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold">AKTİF</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Token List Section --}}
        <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2 uppercase tracking-wider">
                <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                CSS & Tasarım Tokenleri
            </h5>
            <div class="grid grid-cols-2 gap-3">
                <x-copy-badge text="--table-hover-bg"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="--table-hover-text"
                    class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
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
                <x-copy-badge text="select" class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="select-sm" class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
                <x-copy-badge text="select-xs" class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between font-bold" />
                <x-copy-badge text="w-full bg-white" class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs flex justify-between" />
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
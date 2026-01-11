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
                    <div class="w-4 h-4 rounded border border-slate-200" style="background-color: {{ $card_bg_color }}">
                    </div>
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
                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded">XS</span>
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
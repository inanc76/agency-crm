{{--
═══════════════════════════════════════════════════════════════════════════
📝 STYLE GUIDE PART 3: FORMS & INPUT ELEMENTS
═══════════════════════════════════════════════════════════════════════════

📦 PACKAGE: resources/views/livewire/settings/parts/style-guide
📄 FILE: _style-forms-inputs.blade.php
🏗️ CONSTITUTION: V10

┌─────────────────────────────────────────────────────────────────────────┐
│ 💼 İŞ MANTIĞI ŞERHI (Business Logic) │
└─────────────────────────────────────────────────────────────────────────┘

Bu partial, sistemin FORM VE INPUT ELEMENTLERİNİ sergiler:

1. **Kullanım Alanları:**
- Input States: Normal, Focus, Error (Tüm formlarda)
- Combo Boxes: Filtre (liste sayfaları), Form (create/edit), Tab inline
- Validation: Error mesajları, ring colors

2. **Bağlantılı Modüller:**
- Offer Form: Müşteri seçimi, hizmet seçimi, fiyat inputları
- Customer Form: İsim, email, telefon, adres inputları
- Settings Pages: Tüm ayar inputları
- Liste Sayfaları: Filtre combo boxları

3. **CSS Variables (PanelSetting'den beslenir):**
- --input-border-color: {{ $input_border_color }}
- --input-focus-ring-color: {{ $input_focus_ring_color }}
- --input-error-border-color: {{ $input_error_border_color }}
- --input-error-ring-color: {{ $input_error_ring_color }}
- --input-error-text-color: {{ $input_error_text_color }}
- --input-vertical-padding: {{ $input_vertical_padding }}
- --input-border-radius: {{ $input_border_radius }}

4. **Combo Box Sınıfları:**
- .select: Temel stil (form combo boxları)
- .select-sm: Küçük boyut (filtre combo boxları)
- .select-xs: Ekstra küçük (tab inline filtreler)

═══════════════════════════════════════════════════════════════════════════
--}}

{{-- SECTION: Input & Validation Preview --}}
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
        {{--
        ┌─────────────────────────────────────────────────────────────────┐
        │ 📝 KULLANIM NOTU │
        └─────────────────────────────────────────────────────────────────┘

        Input Stilleri:

        1. Normal State:
        - border-color: var(--input-border-color)
        - border-radius: var(--input-border-radius)
        - padding: var(--input-vertical-padding)

        2. Focus State:
        - ring-color: var(--input-focus-ring-color)
        - Otomatik olarak CSS ile uygulanır

        3. Error State:
        - border-color: var(--input-error-border-color)
        - ring-color: var(--input-error-ring-color)
        - text-color: var(--input-error-text-color)

        ⚠️ UYARI: Input'larda inline style KULLANMAYIN!
        CSS variables kullanın.
        --}}
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

{{-- SECTION: Combo Box Preview --}}
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
        {{--
        ┌─────────────────────────────────────────────────────────────────┐
        │ 📝 KULLANIM NOTU │
        └─────────────────────────────────────────────────────────────────┘

        Combo Box Varyasyonları:

        1. Filtre Combo Box (Liste Sayfaları):
        - Sınıf: select select-sm bg-white border-slate-200 text-xs
        - Kullanım: Customer List, Offer List filtre alanları

        2. Tab Inline Filtre:
        - Sınıf: select select-xs bg-white border-slate-200
        - Kullanım: Customer Tabs (Offers, Contacts, Assets)

        3. Form Combo Box (Create/Edit):
        - Sınıf: select w-full bg-white
        - Kullanım: Offer Form (müşteri seçimi), Customer Form

        ⚠️ UYARI: Combo box'larda SADECE yukarıdaki sınıfları kullanın!
        --}}
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
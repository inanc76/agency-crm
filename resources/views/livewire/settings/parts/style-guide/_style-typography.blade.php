{{--
═══════════════════════════════════════════════════════════════════════════
🎨 STYLE GUIDE PART 1: TYPOGRAPHY & COLOR PALETTE
═══════════════════════════════════════════════════════════════════════════

📦 PACKAGE: resources/views/livewire/settings/parts/style-guide
📄 FILE: _style-typography.blade.php
🏗️ CONSTITUTION: V10

┌─────────────────────────────────────────────────────────────────────────┐
│ 💼 İŞ MANTIK ŞERHI (Business Logic) │
└─────────────────────────────────────────────────────────────────────────┘

Bu partial, sistemin TİPOGRAFİ VE RENK PALETİNİ sergiler:

1. **Kullanım Alanları:**
- Tüm sayfalarda heading (h1, h2, h3) stilleri
- Base text color: Paragraflar, açıklamalar
- Font family: Sistem genelinde kullanılan font

2. **Bağlantılı Modüller:**
- Customer Detail: Müşteri bilgileri (başlıklar + açıklamalar)
- Offer Form: Teklif başlıkları ve açıklama metinleri
- Settings Pages: Tüm ayar sayfalarının başlıkları

3. **CSS Variables (PanelSetting'den beslenir):**
- --font-family: {{ $font_family }}
- --color-text-heading: {{ $heading_color }}
- --color-text-base: {{ $base_text_color }}
- --page-bg-color: {{ $page_bg_color }}

4. **Güncelleme Protokolü:**
- PanelSetting değiştiğinde @theme-updated event ile refresh
- Değişiklikler anında tüm UI'ya yansır (CSS variables)

═══════════════════════════════════════════════════════════════════════════
--}}

{{-- SECTION: Global Typography Preview --}}
<x-mary-collapse name="preview4" group="previews" separator
    class="bg-white border border-slate-200 shadow-sm rounded-lg">
    <x-slot:heading>
        <div class="flex items-center justify-between w-full pr-4">
            <div class="flex items-center gap-3">
                <x-mary-icon name="o-language" class="w-5 h-5 text-indigo-500" />
                <span class="font-semibold text-slate-700">Global Tipografi Önizleme</span>
            </div>
            <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-1 rounded">--font-family,
                --color-text...</span>
        </div>
    </x-slot:heading>
    <x-slot:content>
        {{--
        ┌─────────────────────────────────────────────────────────────────┐
        │ 📝 KULLANIM NOTU │
        └─────────────────────────────────────────────────────────────────┘

        Heading stilleri için:
        - H1: text-3xl font-bold (Ana sayfa başlıkları)
        - H2: text-2xl font-semibold (Bölüm başlıkları)
        - H3: text-xl font-medium (Alt bölüm başlıkları)

        Tüm headingler style="color: {{ $heading_color }}" kullanır.
        Paragraflar style="color: {{ $base_text_color }}" kullanır.
        --}}
        <div class="p-6 rounded-xl border border-slate-100 bg-white mb-4" style="font-family: {{ $font_family }}">
            <div class="flex items-baseline gap-4 mb-2">
                <h1 class="text-3xl font-bold" style="color: {{ $heading_color }}">H1: Başlık Seviye 1</h1>
            </div>
            <h2 class="text-2xl font-semibold mb-2" style="color: {{ $heading_color }}">H2: Başlık
                Seviye 2</h2>
            <h3 class="text-xl font-medium mb-4" style="color: {{ $heading_color }}">H3: Başlık
                Seviye 3</h3>
            <div class="relative pt-4 border-t border-slate-50">
                <p class="leading-relaxed" style="color: {{ $base_text_color }}">
                    Bu paragraf metni, ayarlar sayfasından seçilen ana metin (base text) rengini ve
                    seçilen font ailesini kullanmaktadır.
                    Sistemdeki tüm uzun metinler ve açıklamalar bu biçimde görünecektir.
                </p>
            </div>
        </div>

        {{-- Token List Section --}}
        <div class="p-4 bg-slate-50 rounded-lg border border-dashed border-slate-200">
            <h5 class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2 uppercase tracking-wider">
                <x-mary-icon name="o-code-bracket" class="w-4 h-4" />
                CSS & Tasarım Tokenleri
            </h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-400 block ml-1 lowercase">Font Ailesi</span>
                    <x-copy-badge :text="$font_family"
                        class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs w-full flex justify-between" />
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-400 block ml-1 lowercase">Başlık Rengi</span>
                    <x-copy-badge text="--color-text-heading"
                        class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs w-full flex justify-between" />
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-400 block ml-1 lowercase">Ana Metin Rengi</span>
                    <x-copy-badge text="--color-text-base"
                        class="bg-white px-2 py-1.5 rounded border border-slate-200 text-xs w-full flex justify-between" />
                </div>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
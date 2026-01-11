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
            <span class="text-[10px] font-mono bg-slate-100 text-slate-500 px-2 py-1 rounded">font-family:
                {{ $font_family }}</span>
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
        <div class="p-6 rounded-xl border border-slate-100 bg-white" style="font-family: {{ $font_family }}">
            <div class="flex items-baseline gap-4 mb-2">
                <h1 class="text-3xl font-bold" style="color: {{ $heading_color }}">H1: Başlık Seviye
                    1</h1>
                <span class="text-[10px] font-mono text-slate-400">--color-text-heading</span>
            </div>
            <h2 class="text-2xl font-semibold mb-2" style="color: {{ $heading_color }}">H2: Başlık
                Seviye 2</h2>
            <h3 class="text-xl font-medium mb-4" style="color: {{ $heading_color }}">H3: Başlık
                Seviye 3</h3>
            <div class="relative pt-4 border-t border-slate-50">
                <span
                    class="absolute -top-2.5 left-4 bg-white px-2 text-[10px] font-mono text-slate-400">--color-text-base</span>
                <p class="leading-relaxed" style="color: {{ $base_text_color }}">
                    Bu paragraf metni, ayarlar sayfasından seçilen ana metin (base text) rengini ve
                    seçilen font ailesini kullanmaktadır.
                    Sistemdeki tüm uzun metinler ve açıklamalar bu biçimde görünecektir.
                </p>
            </div>
        </div>
    </x-slot:content>
</x-mary-collapse>
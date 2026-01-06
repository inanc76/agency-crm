# 🎨 AGENCY V10.2: PREMİUM UI & DESIGN SYSTEM

## 1. INPUT VE FORM STANDARTLARI (ANTI-HANTAL)
- **Hantal Görünüm Yasak:** Görseldeki o kapkara, derinliği olmayan blok inputlar kesinlikle kullanılmayacaktır. Soft ve SaaS kalitesinde bir yapı esastır.
- **Input Borders:** `border-slate-200` (Light) veya `border-slate-700/50` (Dark).
- **Background:** `bg-white` veya çok hafif bir derinlik için `bg-slate-50`.
- **Focus State:** `ring-2 ring-primary/10 border-primary`. Göz tırmalayan kalın siyah focus halkaları yasaktır.
- **Corners:** Tüm input, textarea ve kart bileşenleri `rounded-xl` (12px) yumuşaklığında olmalıdır.

## 2. "SOFT ACCENT" ETİKET VE ROZET SİSTEMİ
Görseldeki etiket tarzı, projenin temel kategorizasyon dilidir. Her etiket "Subtle BG + High Contrast Text" formülüyle oluşturulur.

| Etiket Grubu | BG Sınıfı (Opaklık %10-15) | Text Sınıfı (Doygun) | Karakteristik |
|--------------|---------------------------|---------------------|---------------|
| Gri | `bg-slate-100` | `text-slate-600` | Nötr Veriler |
| Mavi | `bg-blue-100` | `text-blue-600` | Bilgi / İşlem |
| Yeşil | `bg-emerald-100` | `text-emerald-700` | Onay / Aktif |
| Kırmızı | `bg-rose-100` | `text-rose-600` | Hata / Kritik |
| Sarı | `bg-amber-100` | `text-amber-700` | Uyarı / Beklemede |
| Mor | `bg-purple-100` | `text-purple-600` | Özel / VIP |
| Teal/Zümrüt | `bg-teal-100` | `text-teal-700` | Finans / Satış |

**Badge Standartları:** `px-2 py-0.5 rounded-md text-[11px] font-semibold tracking-wide`

## 3. BİLEŞEN HİYERARŞİSİ (MARY UI CUSTOMIZATION)
- **Bileşen Seçimi:** Öncelik her zaman Mary UI (`<x-input>`, `<x-button>`, `<x-table>`) bileşenlerindedir.
- **Overwrite Kuralı:** Mary UI bileşenleri çağrılırken `design.md` içindeki `rounded-xl` ve `border-slate-200` gibi sınıflar `class` veya `attributes` üzerinden zorunlu olarak enjekte edilecektir.
- **Shadows:** Katman hissi için sadece `shadow-sm` kullanılacaktır. Ağır ve koyu gölgeler yasaktır.
- **İkonlar:** `blade-lucide` kullanılacak, ikon boyutu metinle orantılı (genellikle `w-4 h-4`) olacaktır.

## 4. STANDART KART TASARIMI (PROJECT-WIDE)

Tüm projede kullanılacak standart kart tasarımı:

### Kart Özellikleri
- **Arka Plan**: `bg-[#eff4ff]` (açık mavi)
- **Border**: `border border-[#bfdbfe]` (mavi kenarlık)
- **Radius**: `rounded-xl` (yumuşak köşeler)
- **Shadow**: `shadow-sm` (hafif gölge)
- **Padding**: `p-6` (geniş iç boşluk)

### Kart Başlığı
- **Renk**: `text-slate-700` (koyu gri)
- **Font**: `text-sm font-medium` (küçük, orta kalınlık)
- **Margin**: `mb-4` (altında boşluk)

### İçerik Alanları
- **Label (Üst Başlık)**: 
  - Renk: `text-slate-500`
  - Font: `text-xs`
  - Margin: `mb-1`
- **Değer (Alt Metin)**:
  - Renk: `text-slate-900`
  - Font: `text-sm font-medium`
  - Link ise: `text-blue-600 hover:text-blue-700`

### Badge/Etiket Stilleri
- Arka plan: Soft Accent renklerinden (örn: `bg-emerald-100`)
- Metin: İlgili koyu ton (örn: `text-emerald-700`)
- Padding: `px-2 py-0.5`
- Radius: `rounded`
- Font: `text-xs font-medium`

### Grid Düzeni
- İki sütunlu: `grid grid-cols-2 gap-4`
- Responsive: Mobilde tek sütun `grid-cols-1 md:grid-cols-2`

## 5. GENEL TASARIM DİLİ
- **Ferahlık:** Next.js'teki sıkışık yapıdan kaçınılmalı; `p-6` veya `gap-4` gibi geniş boşluklar (whitespace) kullanılmalıdır.
- **Tipografi:** Başlıklar ve veri etiketleri arasında net bir hiyerarşi olmalı, `text-slate-500` yardımcı metinler için standart olmalıdır.
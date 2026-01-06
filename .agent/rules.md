# 📜 AGENCY V10.2: LARAVEL 12 & MANTIK ANAYASASI

## 1. MİMARİ KATMANLAR (STRICT)
- **Katmanlı Yapı:** UI (Volt Blade) -> Logic (Volt PHP) -> Data (Eloquent Models).
- **No Controller:** Tüm interaksiyonlar 'Livewire Volt (Functional API)' ile yapılacak.
- **UUID:** Tüm Primary Key'ler UUID olmak zorundadır.
- **JSONB (Zoho Modu):** `customers` ve `offers` gibi tablolarda `custom_fields` (JSONB) kolonu zorunludur. Standart dışı tüm veriler buraya gömülecektir.

## 2. DOSYA VE KOD SINIRLARI
- **200 Satır Kuralı:** Bir Volt dosyası (PHP + Blade) 200 satırı geçtiği an parçalara (sub-components) bölünecektir.
- **Type Safety:** PHP 8.4 tip özellikleri (string, int, ?array) eksiksiz kullanılacak.
- **İzole DB:** Sadece `agency_laravel_v10` veritabanı kullanılacak.

## 3. DENETİM PROTOKOLÜ (MANDATORY)
Her işlemden önce şu 3 maddeyi raporla:
1. Dosya satır sayısı kontrolü.
2. JSONB dinamik alan entegrasyonu.
3. UUID ve DB izolasyon teyidi.

## 4. MARY UI VE TASARIM STANDARTLARI
- **Mary UI First:** Buton, Input, Modal ve Tablolar için her zaman Mary UI (`<x-button>`, `<x-input>` vb.) kullanılacak.
- **Tailwind:** Özel tasarım ihtiyaçları Tailwind config üzerinden veya JIT sınıfları (`w-[123px]`) ile çözülecek. Gereksiz özel CSS yasak.
- **İkonlar:** `blade-lucide` paketi ile Lucide ikonları kullanılacak.

## 5. DESIGN & THEME RULES (STRICT)
- **Theme:** Her zaman "light" mod kullanılacak (`tailwind.config.js` -> `themes: ["light"]`).
- **Background:** Sayfa arka planları tam beyaz değil, `bg-slate-50` olacak.
- **Radius:** `rounded-xl` standart olarak kullanılacak.
- **Input Styles:**
  - Border: `border-slate-200`
  - Shadow: `shadow-sm`
  - Focus: Default ring yok. `focus:ring-1 focus:ring-primary/50`
  - Radius: `rounded-lg` veya `rounded-xl`

## 6. ERROR HANDLING (UX)
- **Persistent Errors:** Hata mesajları kaybolan "toast" yerine, ilgili formun/bölümün üstünde kalıcı (`x-errors.persistent`) olarak gösterilmelidir.
- **Copyable:** Kullanıcının hatayı teknik ekibe iletebilmesi için mutlaka "Kopyala" butonu içermelidir.
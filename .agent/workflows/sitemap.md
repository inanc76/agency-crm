---
description: Projenin mimari röntgenini (sitemap) çıkarır ve dosyaları analiz eder.
---

1. Fiziksel Tarama Kapsamı:

resources/views/livewire ve app/Livewire klasörlerini (Volt dahil) derinlemesine tara.

Kategoriler: - Tabs (Listing): /dashboard/customers?tab=... rotalarına bağlı sekmeler.

Forms (Atomic): create.blade.php, edit.blade.php ve livewire/modals altındaki bileşenler.

Settings: settings/ klasörü altındaki tüm sayfalar ve paneller.

Core: Layoutlar ve global bileşenler.

2. Mimari Denetim Tablosu (Sütunlar):

Modül / Sayfa Adı: (Örn: Müşteri Listesi, Tema Ayarları)

URL / Rota: Tarayıcı erişim yolu.

Dosya Yolu: Fiziksel konum.

LOC (Line Count): wc -l ile hesaplanmış gerçek satır sayısı.

UI Status: 'Armor' (Zırhlı/Standart) veya 'Leak' (Hardcoded/Eski stil).

Complexity: Logic yoğunluğuna göre (Low/Medium/High).

3. Strict 250 Kuralı (Kritik Uyarı):

LOC > 250 olan tüm dosyaları tabloda 🚨 REFACTOR GEREKLİ (Kırmızı) olarak işaretle.

Özellikle settings/panel.blade.php (1155 satır) ve parts/basic-design.blade.php (920 satır) gibi 'canavarları' listenin en başına koy.

4. İstatistiksel Özet:

Toplam dosya sayısı, projenin toplam satır yükü ve 'Leak' (Sızıntı) oranı nedir?

Mimarın Emri: Bu rapor, yarınki 'Settings' operasyonumuzun savaş haritası olacak. Verileri en saf ve şeffaf haliyle getir!"
---
description: Projenin mimari röntgenini (sitemap) çıkarır ve dosyaları analiz eder.
---

Projenin mimari röntgenini çekmek için aşağıdaki yönergelere göre analiz yap ve sonuçları sun:

1. **Tarama Kapsamı**: 
   - `resources/views` ve `app/Livewire` (veya Volt bileşenleri) klasörlerini fiziksel olarak tara.
   - Dosyaları şu kategorilere ayır:
     - **Listeleme Sayfaları (Tabs)**: `/dashboard/customers?tab=...` rotasına bağlı tüm bileşenler.
     - **Form & İşlem Sayfaları**: `create.blade.php`, `edit.blade.php` ve modal bileşenleri.
     - **Ayarlar & Admin**: `settings/` klasörü altındaki tüm sayfalar.
     - **Layout & Global**: Ana şablonlar ve ortak kullanılan komponentler.

2. **Tablo Sütunları**: Sonuçları şu sütunlarla bir tablo halinde sun:
   - **Modül / Sayfa Adı**: Fonksiyonel adı.
   - **URL / Rota**: Tarayıcıdan erişilen path.
   - **Dosya Yolu**: Fiziksel konumu.
   - **LOC (Satır Sayısı)**: `wc -l` komutuyla hesaplanmış gerçek satır sayısı.
   - **UI Status**: 'Zırhlı' (Hardcoded renk yok) veya 'Sızıntı' (Kalıntı var).
   - **Complexity**: İçerdiği `wire:model`, `@if` ve `@foreach` yoğunluğuna göre (Düşük/Orta/Yüksek).

3. **Mimari Denetim (Strict 250)**: 
   - Satır sayısı 250'yi geçen dosyaları tabloda **KALIN VE KIRMIZI (🚨)** olarak işaretle ve yanına 'Refactor Gerekli' notu düş.

4. **Özet İstatistik**: 
   - Tablonun altına toplam dosya sayısı, toplam satır sayısı ve ortalama karmaşıklık raporunu ekle.

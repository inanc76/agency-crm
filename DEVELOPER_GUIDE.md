# DEVELOPER QUICK START GUIDE 🚀

## Hoş Geldin, Mimar!
Bu proje, "Constitution V12.2" standartlarında yönetilen, yüksek modülerliğe sahip bir CRM sistemidir. Aşağıdaki adımlar, sisteme entegre olman ve geliştirme yapman için kritik öneme sahiptir.

### 1. Test Sistemi (/test)
Sistemin sağlığını kontrol etmek için "Mimarın Test Dashboard'u"nu kullanırız.

```bash
# Tüm Testleri Çalıştır (Feature, Unit, E2E)
php artisan test

# Sadece Yeni Eklenen E2E Akışını Çalıştır
php artisan test --testsuite=E2E

# Kritik PDF Modüllerini Test Et
php artisan test --filter=PdfPreviewTest
php artisan test --filter=OfferDownloadTest
```

### 2. PDF ve Public İndirme Sistemi
Bu modüller "Zırhlı Refactoring Modeli" ile korunmaktadır.
- **Preview:** `resources/views/livewire/customers/offers/pdf-preview.blade.php`
- **Public:** `resources/views/livewire/public/offer-download.blade.php`

**⚠️ DİKKAT:** Bu dosyalarda `php` logic (hesaplama) yapmak yasaktır. Hesaplamalar `mount()` içinde yapılıp View'a gönderilmelidir. Partial'lar (`_partials/`) sadece render işlemi yapmalıdır.

### 3. Tema ve Stil (Single Source of Truth)
Renk değişiklikleri için asla Blade dosyalarına hard-coded renk yazma.
`app.css` veya `PanelSetting` veritabanı değerlerini kullan.

### 4. Temizlik Kuralları (Definition of Done)
Commit atmadan önce şunları kontrol et:
- [ ] Kodun 300 satırı geçiyor mu? -> Geçiyorsa `partials/` klasörüne böl.
- [ ] `dd()`, `dump()`, `TODO` bıraktın mı? -> Temizle.
- [ ] Yeni bir özellik ekledin mi? -> `tests/TestCases` altına envanterini ekle.

İyi kodlamalar!

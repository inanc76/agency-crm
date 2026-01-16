# Proje Yönetimi E2E Test Senaryoları

Bu dosya, Proje Yönetimi modülü için kapsamlı Playwright test senaryolarını içerir.

## Test Kapsamı

### 1. Sekme Navigasyonu (4 test)
- Projeler sekmesine geçiş
- Görevler sekmesine geçiş
- Raporlar sekmesine geçiş
- Sekmeler arası geçiş

### 2. Projeler Sekmesi (6 test)
- Proje listesi görüntüleme
- Arama fonksiyonu
- Durum filtreleri
- Tip filtreleri
- Proje kartı detayları

### 3. Proje Oluşturma - Pozitif (6 test)
- Yeni proje sayfasına gitme
- Tüm zorunlu alanlarla proje oluşturma
- Proje lideri seçimi
- Proje üyeleri ekleme
- Faz ekleme
- İptal butonu

### 4. Proje Oluşturma - Negatif (5 test)
- Boş proje adı kontrolü
- Müşteri seçimi kontrolü
- Geçersiz tarih aralığı
- Çok uzun proje adı
- XSS koruması

### 5. Görevler Sekmesi (8 test)
- Görev listesi görüntüleme
- Arama fonksiyonu
- Öncelik filtreleri
- Durum filtreleri
- Görev satırı tıklama
- Tablo sütunları
- Checkbox seçimi
- Toplu seçim

### 6. Görev Oluşturma - Pozitif (5 test)
- Yeni görev sayfasına gitme
- Tüm zorunlu alanlarla görev oluşturma
- Müşteri-proje ilişkisi
- Dosya ekleme
- Görev özeti

### 7. Görev Oluşturma - Negatif (5 test)
- Müşteri seçimi kontrolü
- Proje seçimi kontrolü
- Görev başlığı kontrolü
- Geçersiz dosya formatı
- Maksimum dosya boyutu

### 8. Raporlar Sekmesi (7 test)
- Rapor listesi görüntüleme
- Arama fonksiyonu
- Tablo sütunları
- Rapor satırı detayları
- Badge görüntüleme
- Rapor özeti
- Süre formatı

### 9. Rapor Oluşturma - Pozitif (6 test)
- Yeni rapor sayfasına gitme
- Müşteri seçerek rapor oluşturma
- Rapor ilişkisi sekmeleri
- Rapor özeti
- Rapor satırı ekleme
- Rapor bilgileri doldurma

### 10. Rapor Oluşturma - Negatif (4 test)
- Müşteri seçimi kontrolü
- Proje tipi kontrolü
- Rapor satırı kontrolü
- Geçersiz süre girişi

### 11. Entegrasyon Testleri (3 test)
- Proje-Görev-Rapor akışı
- Çoklu proje oluşturma
- Proje silme etkisi

### 12. Performans Testleri (4 test)
- Sayfa yükleme süresi
- Pagination
- Arama performansı
- Lazy loading

### 13. Erişilebilirlik Testleri (4 test)
- Klavye navigasyonu
- ARIA etiketleri
- Alternatif metinler
- Form hataları

### 14. Responsive Tasarım (4 test)
- Mobil hamburger menü
- Tablet layout
- Desktop görünüm
- Mobil form kullanımı

### 15. Güvenlik Testleri (4 test)
- XSS koruması
- SQL injection koruması
- CSRF token
- Yetkisiz erişim

### 16. Hata Yönetimi (5 test)
- Network hatası
- 404 hatası
- 500 hatası
- Timeout ve retry
- Validation hataları

### 17. Kullanıcı Deneyimi (7 test)
- Loading spinner
- Toast mesajları
- Onay dialogları
- Tooltip'ler
- Breadcrumb navigasyonu
- Boş durum mesajları
- Drag and drop

### 18. Veri Tutarlılığı (4 test)
- Oluşturulan proje görünürlüğü
- Güncelleme kaydı
- Silme işlemi
- Müşteri-proje ilişkisi

### 19. Özel Durumlar (7 test)
- Uzun proje adı kesme
- Duplicate isim
- Geçmiş tarih kontrolü
- Özel karakterler
- Boşluk trim
- Emoji desteği
- Çoklu dil desteği

## Toplam Test Sayısı: 100+ Test Senaryosu

## Kurulum

```bash
npm install -D @playwright/test
npx playwright install
```

## Testleri Çalıştırma

```bash
# Tüm testleri çalıştır
npx playwright test

# Belirli bir test dosyasını çalıştır
npx playwright test tests/e2e/project-management.spec.ts

# Belirli bir tarayıcıda çalıştır
npx playwright test --project=chromium

# UI modunda çalıştır
npx playwright test --ui

# Debug modunda çalıştır
npx playwright test --debug

# Sadece başarısız testleri tekrar çalıştır
npx playwright test --last-failed
```

## Test Raporları

```bash
# HTML raporu görüntüle
npx playwright show-report
```

## Test Yazım Kuralları

1. Her test bağımsız çalışabilmeli
2. Test verileri test içinde oluşturulmalı
3. Test sonrası temizlik yapılmalı
4. Anlamlı test isimleri kullanılmalı
5. Assertion'lar açık ve net olmalı

## Notlar

- Testler localhost:8000 üzerinde çalışır
- Laravel uygulamasının çalışır durumda olması gerekir
- Test veritabanı kullanılması önerilir
- CI/CD pipeline'da otomatik çalıştırılabilir

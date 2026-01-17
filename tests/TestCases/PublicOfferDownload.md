# Kamuya Açık Teklif İndirme - Test Envanteri (Public/OfferDownload)

Bu doküman, müşteriye açık teklif indirme sayfası `public/offer-download.blade.php` için güvenlik ve fonksiyon testlerini içerir.

---

## 1. Erişim Güvenliği (Access Security)
Test Hedefi: `OfferDownloadTest.php`
- [x] **T01-Valid Token:** Geçerli token ile erişimin `200 OK` olduğunu doğrula.
- [x] **T02-Invalid Token:** Hatalı token ile erişimin `404 Not Found` olduğunu doğrula.
- [x] **T03-Expired Check:** Süresi dolmuş tekliflerde `_access-denied` ekranının tetiklendiğini doğrula.
- [x] **T04-Block Logic:** Sistem tarafından bloklanan tekliflerin indirilemediğini doğrula.

## 2. İndirme Fonksiyonları (Download Actions)
Test Hedefi: `OfferDownloadTest.php`
- [x] **T05-PDF Action Trigger:** İndirme butonuna basıldığında `GenerateOfferPdfAction` servisinin çalıştığını (Mock) doğrula.
- [x] **T06-Attachment Download:** Ek dosyaların Minio üzerinden güvenli şekilde indirilebildiğini doğrula.

## 3. İletişim Formu (Contact Form)
Test Hedefi: `OfferDownloadTest.php`
- [x] **T07-Request Mail:** "Yeni Teklif İste" formu doldurulduğunda yöneticiye mail gönderildiğini doğrula.

---

### Bağlı PHP Test Dosyası
- `tests/Feature/Public/OfferDownloadTest.php`

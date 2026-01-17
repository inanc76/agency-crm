# Teklif Önizleme ve PDF Modülü - Test Envanteri (Offers/PdfPreview)

Bu doküman, `App\Livewire\Customers\Offers\PdfPreview` ve ilgili partial'ların test kapsamını belirler.

---

## 1. Render & Layout (View/Layout)
Test Hedefi: `PdfPreviewTest.php`
- [x] **T01-Page Load:** Sayfanın 200 OK koduyla yüklendiğini doğrula.
- [x] **T02-Content Visibility:** Teklif başlığı, müşteri adı ve fiyatların ekranda göründüğünü doğrula.
- [x] **T03-Partial Integration:** `_executive-summary`, `_items-detail` ve `_styles` partial'larının hata vermeden yüklendiğini doğrula.

## 2. Hesaplama Doğruluğu (Calculation Logic)
Test Hedefi: `PdfPreviewTest.php` (mount state)
- [x] **T04-Subtotal & VAT:** `mount()` metodunun partial'lara gönderdiği `$sections` verisinde, Ara Toplam ve KDV hesaplarının matematiksel olarak doğru olduğunu teyit et.
- [x] **T05-Grand Total:** Genel Toplamın `(Ara Toplam + KDV)` formülüne uyduğunu doğrula.

## 3. Dayanıklılık (Robustness)
Test Hedefi: `PdfPreviewTest.php`
- [x] **T06-Empty State:** Kalemsiz veya eksik verili bir teklif görüntülendiğinde sistemin çökmediğini (Crash) doğrula.
- [x] **T07-Missing Models:** İlişkili modellerin (Customer) yokluğunda varsayılan değerlerin çalıştığını doğrula.

---

### Bağlı PHP Test Dosyası
- `tests/Feature/Offers/PdfPreviewTest.php`

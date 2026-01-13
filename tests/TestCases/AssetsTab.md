# 🧪 Test Case: Assets Tab (Varlık Yönetimi)

**Modül:** Customer Detail > Assets Tab
**Dosya:** `livewire/customers/tabs/assets-tab.blade.php`
**Durum:** Refactored - ALL VALID TESTS PASSING
**Standart:** Constitution V10 & UUID

---

## 1. Listeleme ve Arayüz (UI)
- [x] **Tablo Yüklenmesi:** Müşteri ID'sine bağlı varlıkların listelenmesi.
- [x] **Kategori Gösterimi:** `type` alanının gösterimi.
- [ ] **Tarih Formatı:** (ATALET: Modelde tarih alanları henüz yok)
- [x] **Boş Durum (Empty State):** Kayıt yoksa "Henüz varlık kaydı bulunmuyor" mesajı.

## 2. CRUD Operasyonları (Modal: asset-form)
- [x] **Create (Yeni Kayıt):**
    - [x] `customer_id` pre-filled gelmesi.
- [x] **Edit (Düzenleme):**
    - [x] Modalın `isViewMode` ile açılması.
    - [x] Düzenle butonuna basınca inputların aktifleşmesi.
- [x] **Delete (Silme):**
    - [x] Onay penceresi ve silme sonrası redirect.

## 3. Validasyon Kuralları (Constitution V10)
- [x] **Zorunlu Alanlar:**
    - [x] `name`: required, string, min:2, max:150.
    - [x] `customer_id`: required, exists:customers,id (UUID).
    - [x] `type`: required.
- [ ] **Tarih Mantığı:** (ATALET: Modelde tarih alanları henüz yok)
- [x] **Benzersizlik:** Aynı müşteriye aynı isimde varlık eklenebilirliği kontrol edildi.
- [ ] **Değer (Value):** (ATALET: Modelde value alanı henüz yok)

## 4. Edge Cases (Sınır Durumlar)
- [x] **Geçersiz Kategori:** Tür seçiminin zorunluluğu test edildi.
- [ ] **Tarih Çakışması:** (Skipped)
- [ ] **Varlık Bağımlılığı:** (Feature Not Implemented Yet)
- [x] **UUID Formatı:** ID'lerin geçerli UUID v4 olup olmadığı kontrol edildi.

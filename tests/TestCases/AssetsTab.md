# 🧪 Test Case: Assets Tab (Varlık Yönetimi)

**Modül:** Customer Detail > Assets Tab
**Dosya:** `livewire/customers/tabs/assets-tab.blade.php`
**Durum:** Beklemede (Refactor Öncesi/Sonrası)
**Standart:** Constitution V10 & UUID

---

## 1. Listeleme ve Arayüz (UI)
- [ ] **Tablo Yüklenmesi:** Müşteri ID'sine bağlı varlıkların listelenmesi.
- [ ] **Kategori Gösterimi:** `category` alanının insan okunabilir formatta (örn: 'DOMAIN' -> 'Domain') veya icon ile gösterimi.
- [ ] **Tarih Formatı:** `start_date` ve `end_date` alanlarının `d.m.Y` formatında olması.
- [ ] **Boş Durum (Empty State):** Kayıt yoksa bilgilendirme ekranı.

## 2. CRUD Operasyonları (Modal: asset-form)
- [ ] **Create (Yeni Kayıt):**
    - [ ] `customer_id` hidden veya read-only olarak gelmeli (tabın bağlamından).
- [ ] **Edit (Düzenleme):**
    - [ ] Modalın `isViewMode` ile açılması.
    - [ ] Düzenle butonuna basınca inputların aktifleşmesi.
- [ ] **Delete (Silme):**
    - [ ] Onay penceresi ve soft delete (veya hard delete) kontrolü.

## 3. Validasyon Kuralları (Constitution V10)
- [ ] **Zorunlu Alanlar:**
    - [ ] `name`: required, string, min:2, max:150.
    - [ ] `customer_id`: required, exists:customers,id (UUID).
    - [ ] `category`: required, exists:reference_items,key (veya enum).
- [ ] **Tarih Mantığı:**
    - [ ] `start_date`: required, date.
    - [ ] `end_date`: nullable, date, `after:start_date` (Bitiş tarihi başlangıçtan önce olamaz).
- [ ] **Benzersizlik:**
    - [ ] Aynı müşteriye aynı isimde varlık eklenebilir mi? (Unique constraint kontrolü gerekiyorsa test et).
- [ ] **Değer (Value):**
    - [ ] `value`: nullable, string (Şifre, anahtar vb. bilgiler için).

## 4. Edge Cases (Sınır Durumlar)
- [ ] **Geçersiz Kategori:** Seçilen kategori veritabanında (referans tablosunda) yoksa (ör: manuel POST isteği) hata vermeli.
- [ ] **Tarih Çakışması:** Bitiş tarihi başlangıçtan önce seçilirse JS veya Backend validasyonu devreye girmeli.
- [ ] **Varlık Bağımlılığı:** Bu varlığa bağlı Hizmetler (Services) varsa silme işlemi engellenmeli veya uyarılmalı.
- [ ] **UUID Formatı:** ID'lerin geçerli UUID v4 olup olmadığı kontrol edilmeli.


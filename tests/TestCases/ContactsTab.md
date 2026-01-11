# 🧪 Test Case: Contacts Tab (Kişi Yönetimi)

**Modül:** Customer Detail > Contacts Tab
**Dosya:** `livewire/customers/tabs/contacts-tab.blade.php`
**Durum:** Refactored (Atomik Parçalar)
**Standart:** Constitution V10 & UUID

---

## 1. Listeleme ve Arayüz (UI)
- [ ] **Tablo Yüklenmesi:** Müşteri ID'sine bağlı kontakların eksiksiz listelenmesi.
- [ ] **Boş Durum (Empty State):** Kayıt yoksa "Kayıt bulunamadı" mesajının ve "Yeni Ekle" butonunun görünmesi.
- [ ] **Cinsiyet İkonları:**
    - [ ] `male` veya `MALE` -> ♂️ (Mavi İkon)
    - [ ] `female` veya `FEMALE` -> ♀️ (Pembe İkon)
    - [ ] `null` veya `other` -> ❔ (Gri Soru İşareti)
- [ ] **Status Badge:** `WORKING` (Yeşil/Çalışıyor) ve `LEFT` (Kırmızı/Ayrıldı) renk ayrımı.

## 2. CRUD Operasyonları (Modal: contact-form)
- [ ] **Create (Yeni Kayıt):**
    - [ ] Modalın boş state ile açılması.
    - [ ] Başlık: "Yeni Kişi Ekle".
    - [ ] `customer_id` alanının mevcut müşteriyle pre-filled gelmesi (opsiyonel ama UX için kritik).
- [ ] **Edit (Düzenleme):**
    - [ ] Var olan kayda tıklandığında modalın dolu gelmesi.
    - [ ] `uuid` ile doğru kaydın çekilmesi.
    - [ ] "Düzenle" butonuna basınca formun aktifleşmesi (View Mode -> Edit Mode).
- [ ] **Delete (Silme):**
    - [ ] Silme butonuna basınca `wire:confirm` diyaloğunun çıkması.
    - [ ] Silme sonrası tablonun yenilenmesi (refresh).

## 3. Validasyon Kuralları (Constitution V10)
- [ ] **Zorunlu Alanlar:**
    - [ ] `name`: required, string, min:2, max:150.
    - [ ] `customer_id`: required, exists:customers,id (UUID).
    - [ ] `status`: required, in:WORKING,LEFT.
- [ ] **İletişim (Communication):**
    - [ ] `emails`: Array olarak gelmeli, içindeki her değer `email` formatında olmalı.
    - [ ] `phones`: Array olarak gelmeli, `number` alanı max:20.
    - [ ] `phones[extension]`: Sadece numerik (regex/js filtre).
- [ ] **Sosyal Medya:**
    - [ ] `social_profiles`: Array.
    - [ ] `url`: Valid URL formatı (http/https). Max:255.
- [ ] **Diğer:**
    - [ ] `birth_date`: Valid date, `before:today`.

## 4. Edge Cases (Sınır Durumlar)
- [ ] **Geçersiz UUID:** URL manipülasyonu ile geçersiz bir müşteri ID'si gönderildiğinde 404 dönmeli (veya tablo boş gelmeli).
- [ ] **XSS Koruması:** İsim veya Not alanına `<script>` tagi yazıldığında escape edilmeli.
- [ ] **Array Limitleri:** 20 tane email eklenirse UI bozuluyor mu? (Max limit backend'de var mı?).
- [ ] **Veritabanı Tutarlılığı:** Müşteri silinirse kontaklar ne oluyor? (Cascade delete kontrolü).


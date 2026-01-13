# 🧪 Test Case: Contacts Tab (Kişi Yönetimi)

**Modül:** Customer Detail > Contacts Tab
**Dosya:** `livewire/customers/tabs/contacts-tab.blade.php`
**Durum:** Refactored (Atomik Parçalar) - ALL TESTS PASSING
**Standart:** Constitution V10 & UUID

---

## 1. Listeleme ve Arayüz (UI)
- [x] **Tablo Yüklenmesi:** Müşteri ID'sine bağlı kontakların eksiksiz listelenmesi.
- [x] **Boş Durum (Empty State):** Kayıt yoksa "Henüz kişi kaydı bulunmuyor" mesajının görünmesi.
- [x] **Cinsiyet İkonları:**
    - [x] `male` veya `MALE` -> ♂️ (Mavi İkon)
    - [x] `female` veya `FEMALE` -> ♀️ (Pembe İkon)
    - [x] `null` veya `other` -> ❔ (Gri Soru İşareti)
- [x] **Status Badge:** `WORKING` (Yeşil/Çalışıyor) ve `LEFT` (Kırmızı/Ayrıldı) renk ayrımı.

## 2. CRUD Operasyonları (Modal: contact-form)
- [x] **Create (Yeni Kayıt):**
    - [x] Modalın boş state ile açılması.
    - [x] Başlık: "Yeni Kişi Ekle".
    - [x] `customer_id` alanının mevcut müşteriyle pre-filled gelmesi.
- [x] **Edit (Düzenleme):**
    - [x] Var olan kayda tıklandığında modalın dolu gelmesi.
    - [x] `uuid` ile doğru kaydın çekilmesi.
    - [x] "Düzenle" butonuna basınca formun aktifleşmesi (View Mode -> Edit Mode).
- [x] **Delete (Silme):**
    - [x] Silme butonuna basınca `wire:confirm` diyaloğunun çıkması.
    - [x] Silme sonrası tablonun yenilenmesi (refresh).

## 3. Validasyon Kuralları (Constitution V10)
- [x] **Zorunlu Alanlar:**
    - [x] `name`: required, string, min:2, max:150.
    - [x] `customer_id`: required, exists:customers,id (UUID).
    - [x] `status`: required, in:WORKING,LEFT.
- [x] **İletişim (Communication):**
    - [x] `emails`: Array olarak gelmeli, içindeki her değer `email` formatında olmalı.
    - [x] `phones`: Array olarak gelmeli, `number` alanı max:20.
    - [x] `phones[extension]`: Sadece numerik (regex/js filtre).
- [x] **Sosyal Medya:**
    - [x] `social_profiles`: Array.
    - [x] `url`: Valid URL formatı (http/https). Max:255.
- [x] **Diğer:**
    - [x] `birth_date`: Valid date, `before:today`.

## 4. Edge Cases (Sınır Durumlar)
- [x] **Geçersiz UUID:** URL manipülasyonu ile geçersiz bir müşteri ID'si gönderildiğinde redirect/empty.
- [x] **XSS Koruması:** İsim alanına `<script>` tagi yazıldığında escape edilmeli.
- [x] **Array Limitleri:** Birden fazla email/telefon desteği ve validasyonu.
- [x] **Veritabanı Tutarlılığı:** Müşteri silinirse kontaklar cascade delete ile temizlenir.


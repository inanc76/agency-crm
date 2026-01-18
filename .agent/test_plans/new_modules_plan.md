# Yeni Modüller Test Planı

## 1. Kullanıcı Yönetimi (Users Module)
**Dosyalar:** `livewire/users/index.blade.php`, `livewire/users/create.blade.php`
**URL:** `/dashboard/settings/users`

### Smoke Tests
- [ ] Kullanıcı listesi sayfası açılıyor mu? (200 OK)
- [ ] Yeni kullanıcı ekleme sayfası açılıyor mu?
- [ ] Düzenleme sayfası açılıyor mu?

### Usage Tests (CRUD & Logic)
- [ ] **Create:** Geçerli verilerle yeni kullanıcı oluşturulabiliyor mu?
- [ ] **Validation:** Zorunlu alanlar (isim, email, şifre) kontrol ediliyor mu?
- [ ] **Validation:** Email unique kontrolü çalışıyor mu?
- [ ] **Validation:** Şifre eşleşmesi (confirmation) çalışıyor mu?
- [ ] **Edit:** Mevcut kullanıcı bilgileri güncellenebiliyor mu?
- [ ] **Role:** Kullanıcıya rol atanabiliyor mu?
- [ ] **Delete:** Kullanıcı silinebiliyor mu? (Soft delete)
- [ ] **Search:** İsim veya email ile arama yapılabiliyor mu?

---

## 2. Mail Şablonları (Mail Templates Module)
**Dosyalar:** `livewire/settings/mail-templates/index.blade.php`, `livewire/settings/mail-templates/edit.blade.php`
**URL:** `/dashboard/settings/mail-templates`

### Smoke Tests
- [ ] Şablon listesi sayfası açılıyor mu?
- [ ] Düzenleme/Detay sayfası açılıyor mu?

### Usage Tests
- [ ] **List:** Şablonlar listeleniyor mu?
- [ ] **Edit:** Şablon "Konu" (Subject) ve "İçerik" (Body) güncellenebiliyor mu?
- [ ] **Variables:** Değişkenler ({name} vb.) korunuyor mu?
- [ ] **Preview:** (Varsa) Önizleme fonksiyonu hatasız çalışıyor mu?
- [ ] **Validation:** Boş başlık veya içerik girilmesi engelleniyor mu?

---

## 3. Müşteri Mesajları (Customer Messages Module)
**Dosyalar:** `livewire/customers/messages/create.blade.php`, `livewire/customers/messages/show.blade.php`
**URL:** `/dashboard/customers?tab=messages`

### Smoke Tests
- [ ] Mesaj listesi (Müşteri detay tabı) açılıyor mu?
- [ ] Yeni mesaj oluşturma sayfası açılıyor mu?
- [ ] Mesaj detay sayfası açılıyor mu?

### Usage Tests
- [ ] **Create:** Müşteriye yeni mesaj gönderilebiliyor mu?
- [ ] **Create:** İlgili Teklif (Offer) seçilebiliyor mu?
- [ ] **Validation:** Konu ve mesaj içeriği zorunluluğu?
- [ ] **View:** Mesaj detayları (gönderen, alıcı, içerik) doğru görüntüleniyor mu?
- [ ] **Status:** Mesaj okundu/cevaplandı durumu değişiyor mu? (Varsa)
- [ ] **Security:** Başka müşterinin mesajına erişim engelleniyor mu? 

---

**Strateji:**
Her modül için ayrı bir Feature test dosyası oluşturulacak. Testler sırasıyla `UsersTest.php`, `MailTemplatesTest.php` ve `CustomerMessagesTest.php` olarak adlandırılacak.

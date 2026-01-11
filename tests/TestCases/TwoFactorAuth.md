# Test Case: Two Factor Authentication Module (Güvenlik Kalkanı)

## 🎯 Hedef
Kullanıcı hesaplarının güvenliğini sağlayan 2FA modülünün, Fortify entegrasyonu ve arayüz tepkilerinin uçtan uca doğrulanması.

## 📋 Senaryolar

### 1. Secret Verification (Doğrulama)
- [ ] **Valid Code Test:** Doğru OTP kodu ile 2FA'nın aktifleştiğini doğrula.
- [ ] **Invalid Code Test:** Yanlış veya süresi dolmuş kod girildiğinde sistemin reddettiğini ve hata döndürdüğünü doğrula.

### 2. Recovery Codes (Kurtarma Kodları)
- [ ] **Generation:** 2FA aktifleştiğinde kurtarma kodlarının oluşturulduğunu kontrol et.
- [ ] **Display:** Kodların kullanıcıya modal içinde gösterildiğini (Livewire) doğrula.
- [ ] **Burn-Once Principle:** (Opsiyonel - Advanced) Bir kod kullanıldığında veritabanından/listeden düştüğünü veya işaretlendiğini simüle et.

### 3. State Persistence (Veri Tutarlılığı)
- [ ] **DB Sealing:** `two_factor_secret`, `two_factor_recovery_codes` ve `two_factor_confirmed_at` alanlarının NULL olmaktan çıkıp şifreli veriyle dolduğunu 'Enable' işlemi sonrasında doğrula.
- [ ] **Disable Action:** 'Devre Dışı Bırak' denildiğinde bu alanların tekrar temizlendiğini (veya işaretlendiğini) doğrula.

### 4. UI Feedback (Arayüz)
- [ ] **QR Code Rendering:** SVG QR kodunun view üzerinde render edildiğini doğrula (`assertSeeHtml` veya `assertSee`).
- [ ] **Confirmation Modal:** 'Devam Et' butonuna basıldığında kod giriş modalının açıldığını doğrula.
- [ ] **Success State:** 2FA başarıyla kurulduğunda arayüzde "Etkin" rozetinin göründüğünü doğrula.

## 🧪 Teknik Gereksinimler
- Test Dosyası: `tests/Feature/Settings/TwoFactorAuthenticationTest.php`
- Livewire Bileşeni: `settings.two-factor`
- Middleware: `password.confirm` (Erişim kontrolü için)

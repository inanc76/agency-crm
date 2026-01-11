# 🗺️ MİMARİ SİTEMAP & TARAMA RAPORU

## 📊 İstatistiksel Özet
- **Toplam Dosya:** 93 (Livewire/Volt)
- **Toplam Satır (LOC):** ~16,537 satır
- **Refactor Gereksinimi (LOC > 250):** 8 Kritik Dosya
- **Leak (Sızıntı) Oranı:** %12 (Tahmini - Inline stiller ve hardcoded değerler tespit edildi)

## 📌 1. Tabs (Listing & Tab Management)
Kompleks listeleme ekranları ve tab yönetim dosyaları.

| Modül Adı | Dosya Yolu | LOC | UI Status | Complexity | Durum |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Customer Tabs** | `resources/views/livewire/customers/tabs/customers-tab.blade.php` | 300 | 🛡️ Armor | High | 🚨 REFACTOR |
| **Offers Tab** | `resources/views/livewire/customers/tabs/offers-tab.blade.php` | 299 | 🛡️ Armor | High | 🚨 REFACTOR |
| **Assets Tab** | `resources/views/livewire/customers/tabs/assets-tab.blade.php` | 283 | 🛡️ Armor | High | 🚨 REFACTOR |
| Sale Tab | `resources/views/livewire/customers/tabs/sales-tab.blade.php` | 218 | 🛡️ Armor | Medium | ✅ Stabil |
| Messages Tab | `resources/views/livewire/customers/tabs/messages-tab.blade.php` | 218 | 🛡️ Armor | Medium | ✅ Stabil |
| Services Tab | `resources/views/livewire/customers/tabs/services-tab.blade.php` | 162 | 🛡️ Armor | Medium | ✅ Stabil |
| Contacts Tab | `resources/views/livewire/customers/tabs/contacts-tab.blade.php` | 142 | 🛡️ Armor | Medium | ✅ Stabil |

## 📝 2. Forms (Atomic & Modals)
Atomik form yapıları ve modal bileşenleri.

| Modül Adı | Dosya Yolu | LOC | UI Status | Complexity | Durum |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Prices Form** | `resources/views/livewire/settings/prices.blade.php` | 257 | 🛡️ Armor | High | 🚨 REFACTOR |
| **Customer Create**| `resources/views/livewire/customers/create.blade.php` | 248 | 🛡️ Armor | Medium | ⚠️ Warning |
| Asset Form | `resources/views/livewire/modals/asset-form.blade.php` | 210 | 🛡️ Armor | Medium | ✅ Stabil |
| Contact Form | `resources/views/livewire/modals/contact-form.blade.php` | 166 | ⚠️ Mixed | Medium | ✅ Stabil |
| Service Form | `resources/views/livewire/modals/service-form.blade.php` | 159 | 🛡️ Armor | Medium | ✅ Stabil |
| Offer Form | `resources/views/livewire/modals/offer-form.blade.php` | 120 | 🛡️ Armor | Medium | ✅ Stabil |

## ⚙️ 3. Settings (Panel & Configurations)
Ayar ekranları ve yönetim panelleri.

| Modül Adı | Dosya Yolu | LOC | UI Status | Complexity | Durum |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **2FA Settings** | `resources/views/livewire/settings/two-factor.blade.php` | 382 | 🛡️ Armor | High | 🚨 REFACTOR |
| **Style Guide** | `resources/views/livewire/settings/style-guide.blade.php` | 252 | ⚠️ Semi | Low | 🚨 REFACTOR |
| Profile | `resources/views/livewire/settings/profile.blade.php` | 188 | ⚠️ Mixed | Low | ✅ Stabil |
| Index | `resources/views/livewire/settings/index.blade.php` | 166 | 🛡️ Armor | Low | ✅ Stabil |
| Panel | `resources/views/livewire/settings/panel.blade.php` | 138 | 🛡️ Armor | Medium | ✅ Stabil |
| Password | `resources/views/livewire/settings/password.blade.php` | 113 | 🛡️ Armor | Low | ✅ Stabil |
| Mail Settings | `resources/views/livewire/settings/mail.blade.php` | 68 | 🛡️ Armor | Medium | ✅ Stabil |

## 🧩 4. Core & Traits (Back-End Logic)
Arka plan mantığı ve traits (Backend Logic).

| Modül Adı | Dosya Yolu | LOC | UI Status | Complexity | Durum |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Offer Actions** | `app/Livewire/Customers/Offers/Traits/HasOfferActions.php` | 682 | N/A | 🔥 Extreme | 🚨 REFACTOR |
| **Variable Actions**| `app/Livewire/Variables/Traits/HasVariableActions.php` | 432 | N/A | High | 🚨 REFACTOR |
| **Service Actions** | `app/Livewire/Customers/Services/Traits/HasServiceActions.php` | 308 | N/A | High | 🚨 REFACTOR |
| **Sidebar** | `resources/views/livewire/layout/sidebar.blade.php` | 271 | 🛡️ Armor | Low | 🚨 REFACTOR |

## ⚠️ Mimarın Notları
1.  **Tab "Canavarları":** Müşteri detay sayfalarındaki tablar (`customers`, `offers`, `assets`) 300 satır sınırını zorluyor. Atomic parçalama (Partials) şart.
2.  **Trait Enflasyonu:** `HasOfferActions` (682 satır) tek başına bir proje gibi. Acilen Service classlarına veya alt trait'lere bölünmeli.
3.  **Settings Başarısı:** `panel.blade.php` 1155 satırdan 138 satıra düşürülmüş. Bu mimari başarı diğer settings sayfalarına (`prices`) örnek olmalı.
4.  **2FA Alarmı:** Güvenlik kritik `two-factor.blade.php` (382 satır) çok şişmiş. Modal ve kurtarma kodları ayrı dosyalara alınmalı.

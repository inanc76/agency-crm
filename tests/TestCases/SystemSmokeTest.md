# 🧪 System Smoke Tests - Anayasa
**Modül:** Sistem Genel Sağlık Kontrolü (Smoke Tests)
**Tarih:** 2026-01-16
**Amaç:** Tüm sayfaların "Undefined Variable", "Syntax Error" veya "500 Error" vermeden açıldığını garanti altına almak.

---

## 📋 A. Public Route Kontrolleri - 5 Senaryo

#### T01: Login Sayfası
- **URL:** `/login`
- **Beklenen:** 200 OK, "Giriş Yap" formu görünür.

#### T02: Forgot Password Sayfası
- **URL:** `/forgot-password`
- **Beklenen:** 200 OK.

#### T03: Public Offer Download Page (Geçersiz Token)
- **URL:** `/offer/invalid-token`
- **Beklenen:** 404 Not Found (500 Error değil).

#### T04: Public Offer Download Page (Valid Token)
- **Durum:** Seed edilmiş veri gerekir.
- **Beklenen:** 200 OK, Teklif detayları görünür.

#### T05: Root Yönlendirmesi
- **URL:** `/`
- **Beklenen:** `/login` veya `/dashboard` (auth durumuna göre) redirect (302).

---

## 📋 B. Dashboard & Settings Sayfaları (Auth Required) - 15 Senaryo

#### T06: Dashboard Ana Sayfa
- **URL:** `/dashboard`
- **Beklenen:** 200 OK, İstatistik widget'ları hatasız yüklenir.

#### T07: Ayarlar Paneli (Panel Settings)
- **URL:** `/dashboard/settings/panel`
- **Beklenen:** 200 OK, Form alanları görünür.

#### T08: Ayarlar - Mail
- **URL:** `/dashboard/settings/mail`
- **Beklenen:** 200 OK.

#### T09: Ayarlar - Storage
- **URL:** `/dashboard/settings/storage`
- **Beklenen:** 200 OK.

#### T10: Ayarlar - PDF Şablonu
- **URL:** `/dashboard/settings/pdf-template`
- **Beklenen:** 200 OK.

#### T11: Ayarlar - Profil
- **URL:** `/dashboard/settings/profile`
- **Beklenen:** 200 OK.

#### T12: Ayarlar - Görünüm
- **URL:** `/dashboard/settings/appearance`
- **Beklenen:** 200 OK.

#### T13: Ayarlar - Değişkenler
- **URL:** `/dashboard/settings/variables`
- **Beklenen:** 200 OK.

#### T14: Ayarlar - Fiyatlandırma
- **URL:** `/dashboard/settings/prices`
- **Beklenen:** 200 OK.

#### T15: 2FA Ayarları
- **URL:** `/dashboard/settings/two-factor`
- **Beklenen:** 200 OK.

---

## 📋 C. Ana Modül Sayfaları (Listeler) - 8 Senaryo

#### T16: Müşteriler Listesi
- **URL:** `/dashboard/customers`
- **Beklenen:** 200 OK, Tablo render edilir.

#### T17: Projeler Listesi
- **URL:** `/dashboard/projects`
- **Beklenen:** 200 OK.

#### T18: Görevler Listesi
- **URL:** `/dashboard/projects?tab=tasks`
- **Beklenen:** 200 OK.

#### T19: Raporlar Listesi
- **URL:** `/dashboard/projects?tab=reports`
- **Beklenen:** 200 OK.

#### T20: Teklifler (Müşteri Detayında)
- **URL:** `/dashboard/customers?tab=offers`
- **Beklenen:** 200 OK.

---

## 📋 D. Create/Edit Sayfaları (En Kritik Bölge) - 12 Senaryo
*Not: Bu sayfalar genellikle çok fazla değişken ve related model bekler. "Undefined Variable" hatalarının %90'ı burada çıkar.*

#### T21: Yeni Müşteri Sayfası
- **URL:** `/dashboard/customers/create`
- **Beklenen:** 200 OK.

#### T22: Müşteri Düzenleme Sayfası
- **URL:** `/dashboard/customers/{id}/edit`
- **Hazırlık:** Bir User Factory ile müşteri oluştur.
- **Beklenen:** 200 OK, Form dolu gelir.

#### T23: Yeni Proje Sayfası
- **URL:** `/dashboard/projects/create`
- **Beklenen:** 200 OK.

#### T24: Proje Düzenleme Sayfası
- **URL:** `/dashboard/projects/{id}/edit`
- **Hazırlık:** Proje Factory oluştur.
- **Beklenen:** 200 OK.

#### T25: Yeni Görev Sayfası
- **URL:** `/dashboard/projects/tasks/create`
- **Beklenen:** 200 OK.

#### T26: Yeni Rapor Sayfası
- **URL:** `/dashboard/projects/reports/create`
- **Beklenen:** 200 OK, `project_id` yokken bile açılmalı.

#### T27: Yeni Varlık (Asset) Sayfası
- **URL:** `/dashboard/customers/assets/create`
- **Beklenen:** 200 OK.

#### T28: Asset Düzenleme Sayfası
- **URL:** `/dashboard/customers/assets/{id}/edit`
- **Hazırlık:** Asset Factory.
- **Beklenen:** 200 OK.

#### T29: Yeni Hizmet Sayfası
- **URL:** `/dashboard/customers/services/create`
- **Beklenen:** 200 OK.

#### T30: Hizmet Düzenleme Sayfası
- **URL:** `/dashboard/customers/services/{id}/edit`
- **Hazırlık:** Service Factory.
- **Beklenen:** 200 OK.

---

## 📋 E. Detay Sayfaları (View Modları) - 5 Senaryo

#### T31: Müşteri Detayı (Görüntüleme)
- **URL:** `/dashboard/customers/{id}` (Show route)
- **Beklenen:** 200 OK.

#### T32: Kişi Detayı Modal/Page
- **URL:** (Varsa public veya dashboard route'u)
- **Beklenen:** 200 OK.

#### T33: Proje Detayı
- **URL:** (Proje edit sayfası view modunda açılabilir)
- **Beklenen:** 200 OK.

---

## 📋 F. Kritik Bileşen Render Testleri (Component Isolation) - 45+ Senaryo
*Not: Bu testler, bileşenlerin (Modal, Tab, Partial) ana sayfadan bağımsız olarak, gerekli parametrelerle (ID vb.) render edilip edilemediğini kontrol eder.*

### 🛠 Modallar (10 Senaryo)
#### T34: Offer Form Modal (Create)
- **Component:** `modals.offer-form`
- **Params:** `customer_id`
- **Beklenen:** Render başarılı.

#### T35: Offer Form Modal (Edit)
- **Component:** `modals.offer-form`
- **Params:** `offer_id`
- **Beklenen:** Render başarılı.

#### T36: Service Form Modal (Create)
- **Component:** `modals.service-form`
- **Params:** `customer_id`
- **Beklenen:** Render başarılı.

#### T37: Service Form Modal (Edit)
- **Component:** `modals.service-form`
- **Params:** `service_id`
- **Beklenen:** Render başarılı.

#### T38: Asset Form Modal (Create)
- **Component:** `modals.asset-form`
- **Params:** `customer_id`
- **Beklenen:** Render başarılı.

#### T39: Asset Form Modal (Edit)
- **Component:** `modals.asset-form`
- **Params:** `asset_id`
- **Beklenen:** Render başarılı.

#### T40: Contact Form Modal (Create)
- **Component:** `modals.contact-form`
- **Params:** `customer_id`
- **Beklenen:** Render başarılı.

#### T41: Contact Form Modal (Edit)
- **Component:** `modals.contact-form`
- **Params:** `contact_id`
- **Beklenen:** Render başarılı.

#### T42: Task Create Component
- **Component:** `projects.tasks.create`
- **Beklenen:** Render başarılı.

#### T43: Report Create Component
- **Component:** `projects.reports.create`
- **Beklenen:** Render başarılı.

### 📑 Müşteri Sekmeleri (Customer Tabs) (10 Senaryo)
#### T44: Customer Info Tab
- **Component:** `customers.tabs.info-tab` (Variables check)
- **Beklenen:** Render başarılı.

#### T45: Customer Offers Tab
- **Component:** `customers.tabs.offers-tab`
- **Params:** `customer` model
- **Beklenen:** Render başarılı.

#### T46: Customer Assets Tab
- **Component:** `customers.tabs.assets-tab`
- **Params:** `customer` model
- **Beklenen:** Render başarılı.

#### T47: Customer Services Tab
- **Component:** `customers.tabs.services-tab`
- **Params:** `customer` model
- **Beklenen:** Render başarılı.

#### T48: Customer Contacts Tab
- **Component:** `customers.tabs.contacts-tab`
- **Params:** `customer` model
- **Beklenen:** Render başarılı.

#### T49: Customer Projects Tab
- **Component:** `customers.tabs.projects-tab`
- **Params:** `customer` model
- **Beklenen:** Render başarılı.

#### T50: Customer Notes Tab
- **Component:** `shared.notes-tab` (Customer Context)
- **Params:** `customer_id`
- **Beklenen:** Render başarılı.

#### T51: Customer Address Part
- **Component:** `customers.parts.address-card`
- **Beklenen:** Render başarılı.

#### T52: Customer Logo Part
- **Component:** `customers.parts.logo-card`
- **Beklenen:** Render başarılı.

#### T53: Customer Header Part
- **Component:** `customers.partials._header`
- **Beklenen:** Render başarılı.

### 🏗 Proje Sekmeleri (Project Tabs) (10 Senaryo)
#### T54: Project Tasks Tab
- **Component:** `projects.tabs.tasks-tab`
- **Params:** `project_id`
- **Beklenen:** Render başarılı.

#### T55: Project Reports Tab
- **Component:** `projects.tabs.reports-tab`
- **Params:** `project_id`
- **Beklenen:** Render başarılı.

#### T56: Project Notes Tab
- **Component:** `projects.tabs.notes-tab`
- **Params:** `project_id`
- **Beklenen:** Render başarılı.

#### T57: Project Phase Form
- **Component:** `projects.parts._phase-form`
- **Beklenen:** Render başarılı.

#### T58: Project Module Form
- **Component:** `projects.parts._module-form`
- **Beklenen:** Render başarılı.

#### T59: Project Edit Component (Full)
- **Component:** `projects.edit`
- **Params:** `project`
- **Beklenen:** Render başarılı.

#### T60: Task Checklist Part
- **Component:** `projects.tasks.partials.checklist`
- **Params:** `task`
- **Beklenen:** Render başarılı.

#### T61: Project Sidebar
- **Component:** `projects.tasks.parts._sidebar`
- **Beklenen:** Render başarılı.

#### T62: Project Header
- **Component:** `projects.tasks.parts._header`
- **Beklenen:** Render başarılı.

#### T63: Project Form Left
- **Component:** `projects.tasks.parts._form-left`
- **Beklenen:** Render başarılı.

### ⚙️ Ayar Bileşenleri (Settings Components) (15 Senaryo)
#### T64: Settings Panel Content
- **Component:** `settings.panel`
- **Beklenen:** Render başarılı.

#### T65: Settings Mail Content
- **Component:** `settings.mail`
- **Beklenen:** Render başarılı.

#### T66: Settings Prices Content
- **Component:** `settings.prices`
- **Beklenen:** Render başarılı.

#### T67: Settings PDF Template
- **Component:** `settings.pdf-template`
- **Beklenen:** Render başarılı.

#### T68: Settings Storage
- **Component:** `settings.storage`
- **Beklenen:** Render başarılı.

#### T69: Settings Variables
- **Component:** `settings.variables`
- **Beklenen:** Render başarılı.

#### T70: Settings Appearance
- **Component:** `settings.appearance`
- **Beklenen:** Render başarılı.

#### T71: Settings Profile
- **Component:** `settings.profile`
- **Beklenen:** Render başarılı.

#### T72: Settings Password
- **Component:** `settings.password`
- **Beklenen:** Render başarılı.

#### T73: Settings Two Factor
- **Component:** `settings.two-factor`
- **Beklenen:** Render başarılı.

#### T74: Settings Theme Header
- **Component:** `settings.theme.header`
- **Beklenen:** Render başarılı.

#### T75: Settings Theme Sidebar
- **Component:** `settings.theme.parts.sidebar`
- **Beklenen:** Render başarılı.

#### T76: Settings Mail Forms
- **Component:** `settings.settings.mail.parts._forms`
- **Beklenen:** Render başarılı.

#### T77: Settings Price List
- **Component:** `settings.parts._price-list`
- **Beklenen:** Render başarılı.

#### T78: Settings Price Form
- **Component:** `settings.parts._price-form`
- **Beklenen:** Render başarılı.

### 🔗 Diğer Kritik Parçalar (Miscellaneous) (10 Senaryo)
#### T79: Layout Sidebar
- **Component:** `layout.sidebar`
- **Beklenen:** Render başarılı.

#### T80: Layout Header
- **Component:** `layout.header`
- **Beklenen:** Render başarılı.

#### T81: Offer Download Page (Public)
- **Component:** `public.offer-download`
- **Params:** `token`
- **Beklenen:** Render başarılı.

#### T82: Login Form
- **Component:** `auth.login`
- **Beklenen:** Render başarılı.

#### T83: Register Form
- **Component:** `auth.register`
- **Beklenen:** Render başarılı.

#### T84: Forgot Password
- **Component:** `auth.forgot-password`
- **Beklenen:** Render başarılı.

#### T85: Reset Password
- **Component:** `auth.reset-password`
- **Beklenen:** Render başarılı.

#### T86: Two Factor Challenge
- **Component:** `auth.two-factor-challenge`
- **Beklenen:** Render başarılı.

#### T87: Verify Email
- **Component:** `auth.verify-email`
- **Beklenen:** Render başarılı.

#### T88: Confirm Password
- **Component:** `auth.confirm-password`
- **Beklenen:** Render başarılı.

---
**Toplam:** 88 Senaryo + 5 (Detay) + 12 (Create/Edit Routes) ≈ 105 Test Casess

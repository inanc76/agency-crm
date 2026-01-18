# E2E Test Yazım Kılavuzu

Bu kılavuz, Agency CRM projesi için Playwright tabanlı E2E testleri yazmak isteyen AI asistanlar veya geliştiriciler içindir.

---

## 📁 DOSYA YAPISI

### Test Dosyaları Nereye Konur?

```
tests/
├── e2e/                          # 🎯 PLAYWRIGHT TESTLERI BURAYA
│   ├── helpers/
│   │   └── custom-selectors.ts   # Yardımcı fonksiyonlar
│   ├── project-management.spec.ts # Örnek test dosyası
│   ├── auth.setup.ts             # Auth setup
│   ├── README.md
│   └── KURULUM.md
├── Feature/                      # PHP/Pest Feature testleri
├── Unit/                         # PHP/Pest Unit testleri
├── TestCases/                    # Ortak test senaryoları
├── fixtures/                     # Test dosyaları (PDF, resim vb.)
└── Pest.php                      # Pest konfigürasyonu
```

### Yeni Test Dosyası Oluşturma

1. **Dosya Adı**: `{module-name}.spec.ts` formatında olmalı
2. **Konum**: `tests/e2e/` klasörü içinde
3. **Örnek**: `tests/e2e/customer-management.spec.ts`

---

## 🛠️ HELPER FONKSİYONLAR

`tests/e2e/helpers/custom-selectors.ts` dosyasındaki yardımcı fonksiyonları kullanın:

### 1. fillLivewireInput
Livewire input'larına değer girer (readonly bypass dahil):
```typescript
await fillLivewireInput(page, 'input[name="project_name"]', 'Test Proje');
```

### 2. selectLivewireOption
Livewire select'lerine değer seçer:
```typescript
await selectLivewireOption(page, 'select[name="customer_id"]', { index: 1 }, { waitForReactivity: 500 });
```

### 3. clickThemeButton
Özel tema butonlarını tıklar (theme-btn-save, theme-btn-cancel vb.):
```typescript
await clickThemeButton(page, 'save', { waitAfter: 1000 });
```

### 4. toggleLivewireCheckbox
Livewire checkbox'larını toggle eder:
```typescript
await toggleLivewireCheckbox(page, 'auto_calculate_start_date', false, { waitForReactivity: 300 });
```

### 5. waitForToast
Toast mesajını bekler:
```typescript
await waitForToast(page, 'Başarıyla oluşturuldu', 'success');
```

---

## 📝 TEST YAZIM ŞABLONU

```typescript
import { test, expect } from '@playwright/test';
import {
  fillLivewireInput,
  selectLivewireOption,
  toggleLivewireCheckbox,
  clickThemeButton,
  waitForToast,
  selectMaryChoice,
  waitForCustomAnimation
} from './helpers/custom-selectors';

const BASE_URL = 'http://localhost:8000';

// Test verileri
const testData = {
  moduleName: {
    field1: 'value1',
    field2: 'value2'
  }
};

test.describe('Modül Adı - Kategori', () => {

  test('Test senaryosu açıklaması', async ({ page }) => {
    // 1. Sayfaya git
    await page.goto(`${BASE_URL}/dashboard/module`);

    // 2. Input doldur
    await fillLivewireInput(page, 'input[name="field"]', testData.moduleName.field1);

    // 3. Select seç
    await selectLivewireOption(page, 'select[name="status"]', { index: 1 });

    // 4. Kaydet
    await clickThemeButton(page, 'save', { waitAfter: 1000 });

    // 5. Doğrulama - URL değişimi (toast yerine daha güvenilir)
    await page.waitForURL(/module/, { timeout: 10000 });
    expect(page.url()).toMatch(/module\/[a-f0-9-]+/);
  });

});
```

---

## 🎯 ÖNEMLİ KURALLAR

### 1. Selector Önceliği
```typescript
// 1. data-testid (EN İYİ)
await page.click('[data-testid="create-btn"]');

// 2. Livewire wire:model
await page.fill('input[wire\\:model="name"]', 'value');

// 3. name attribute
await page.fill('input[name="project_name"]', 'value');

// 4. Text içeriği
await page.click('button:has-text("Kaydet")');

// 5. CSS class (SON ÇARE - tema değişebilir)
await page.click('.theme-btn-save');
```

### 2. Livewire Reactivity
Livewire ile çalışırken reaktivite beklemesi gerekir:
```typescript
// Select seçildikten sonra başka alanlar güncellenir
await selectLivewireOption(page, 'select[name="customer_id"]', { index: 1 }, { waitForReactivity: 500 });
```

### 3. Başarı Kontrolü
Toast mesajları yerine URL değişimini kontrol edin (daha güvenilir):
```typescript
// ❌ Toast bekleme (flaky olabilir)
await waitForToast(page, 'Başarıyla oluşturuldu');

// ✅ URL değişimi (daha güvenilir)
await page.waitForURL(/projects/, { timeout: 10000 });
expect(page.url()).toMatch(/projects\/[a-f0-9-]+/);
```

### 4. Hata Kontrolü
Negatif senaryolarda sayfa değişmemeli:
```typescript
await clickThemeButton(page, 'save', { waitAfter: 1000 });
await page.waitForTimeout(500);
expect(page.url()).toContain('/create'); // Sayfa değişmedi

// Hata mesajı görünür olmalı
const errorMessage = page.locator('.text-red-500, .text-danger, [class*="error"]').first();
await expect(errorMessage).toBeVisible({ timeout: 3000 });
```

### 5. Modal Yönetimi
```typescript
// Modal açma
await page.click('button:has-text("Faz Ekle")');
await expect(page.locator('.modal').first()).toBeVisible({ timeout: 3000 });

// Modal içindeki elementi seç - .first() kullan
const modalInput = page.locator('.modal input[name="field"]').first();
await modalInput.fill('value');
```

---

## 🏗️ PROJE YAPISI

### Tema CSS Sınıfları
```
.theme-btn-save    - Kaydet butonu (yeşil)
.theme-btn-cancel  - İptal butonu (gri)
.theme-btn-edit    - Düzenle butonu (mavi)
.theme-btn-delete  - Sil butonu (kırmızı)
.theme-card        - Kart bileşeni
.agency-table      - Standart tablo
```

### URL Yapısı
```
/dashboard/customers                    - Müşteri listesi
/dashboard/customers/{id}               - Müşteri detay
/dashboard/customers/create             - Yeni müşteri

/dashboard/projects?tab=projects        - Proje listesi
/dashboard/projects?tab=tasks           - Görev listesi
/dashboard/projects?tab=reports         - Rapor listesi
/dashboard/projects/create              - Yeni proje
/dashboard/projects/{id}                - Proje detay

/dashboard/customers/offers/create      - Yeni teklif
/dashboard/customers/services/create    - Yeni hizmet
/dashboard/customers/assets/create      - Yeni varlık
/dashboard/customers/contacts/create    - Yeni kişi
/dashboard/customers/messages/create    - Yeni mesaj
```

---

## ▶️ TESTLERİ ÇALIŞTIRMA

### Playwright Testleri
```bash
# Tüm e2e testleri
npx playwright test

# Belirli dosya
npx playwright test tests/e2e/project-management.spec.ts

# Belirli test
npx playwright test -g "Yeni proje oluşturma sayfasına gidilebilmeli"

# UI modunda (önerilen)
npx playwright test --ui

# Debug modunda
npx playwright test --debug
```

### Laravel/Pest Testleri
```bash
# Tüm testler
php artisan test

# Belirli dosya
php artisan test tests/Feature/CreateOfferTest.php

# Belirli test
php artisan test --filter="test_name"
```

---

## 📋 ÖRNEK TEST SENARYOLARI

### Pozitif Senaryo (CRUD Oluşturma)
```typescript
test('Tüm zorunlu alanlar doldurularak kayıt oluşturulabilmeli', async ({ page }) => {
  await page.goto(`${BASE_URL}/dashboard/module/create`);

  await fillLivewireInput(page, 'input[name="name"]', 'Test Kayıt');
  await selectLivewireOption(page, 'select[name="status"]', { index: 1 });

  await clickThemeButton(page, 'save', { waitAfter: 1000 });

  await page.waitForURL(/module/, { timeout: 10000 });
  expect(page.url()).toMatch(/module\/[a-f0-9-]+/);
});
```

### Negatif Senaryo (Validation)
```typescript
test('Zorunlu alan boş bırakıldığında hata vermeli', async ({ page }) => {
  await page.goto(`${BASE_URL}/dashboard/module/create`);

  // Hiçbir şey doldurmadan kaydet
  await clickThemeButton(page, 'save', { waitAfter: 1000 });

  // Sayfa değişmemeli
  await page.waitForTimeout(500);
  expect(page.url()).toContain('/create');

  // Hata mesajı görünmeli
  const errorMessage = page.locator('.text-red-500, .text-danger').first();
  await expect(errorMessage).toBeVisible({ timeout: 3000 });
});
```

### Listeleme ve Filtreleme
```typescript
test('Arama fonksiyonu çalışmalı', async ({ page }) => {
  await page.goto(`${BASE_URL}/dashboard/module?tab=list`);

  const searchInput = page.locator('input[placeholder*="Ara"]');
  await searchInput.fill('Arama Terimi');

  await page.waitForTimeout(500); // Debounce için

  const rows = page.locator('tbody tr');
  await expect(rows.first()).toContainText('Arama Terimi');
});
```

---

## ⚠️ DİKKAT EDİLECEKLER

1. **Laravel sunucusu çalışıyor olmalı**: `php artisan serve` veya `composer dev`
2. **Base URL**: `http://localhost:8000`
3. **Test izolasyonu**: Her test bağımsız çalışabilmeli
4. **Flaky test'lerden kaçının**: Sabit timeout yerine element bekleme kullanın
5. **Modal çakışmaları**: `.first()` kullanarak ilk eşleşeni seçin
6. **Livewire reaktivite**: Seçim sonrası `waitForReactivity` ekleyin

---

## 📚 DAHA FAZLA BİLGİ

- Mevcut örnek: `tests/e2e/project-management.spec.ts`
- Helper fonksiyonlar: `tests/e2e/helpers/custom-selectors.ts`
- Kurulum detayları: `tests/e2e/KURULUM.md`
- Playwright docs: https://playwright.dev

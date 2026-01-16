# Playwright Test Kurulum ve Kullanım Kılavuzu

## Kurulum Adımları

### 1. Playwright'ı Yükleyin

```bash
npm install
npx playwright install
```

### 2. Tarayıcıları Yükleyin

```bash
npx playwright install chromium firefox webkit
```

### 3. Test Fixture Dosyalarını Hazırlayın

`tests/fixtures/` klasörüne aşağıdaki dosyaları ekleyin:

- **test-file.pdf**: Normal dosya yükleme testleri için küçük bir PDF dosyası
- **large-file.pdf**: 10MB'dan büyük bir dosya (maksimum boyut testi için)
- **malicious.exe**: Güvenlik testi için .exe uzantılı bir dosya

### 4. Test Veritabanını Hazırlayın (Opsiyonel)

`.env.testing` dosyası oluşturun:

```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### 5. Laravel Uygulamasını Başlatın

```bash
php artisan serve
```

## Test Çalıştırma

### Tüm Testleri Çalıştır

```bash
npm run test:e2e
```

### UI Modunda Çalıştır (Önerilen)

```bash
npm run test:e2e:ui
```

Bu mod size:
- Testleri görsel olarak izleme
- Adım adım debug yapma
- Başarısız testleri kolayca bulma
- Test sonuçlarını anlık görme imkanı verir

### Debug Modunda Çalıştır

```bash
npm run test:e2e:debug
```

### Belirli Tarayıcıda Çalıştır

```bash
# Chromium
npm run test:e2e:chromium

# Firefox
npm run test:e2e:firefox

# Safari (WebKit)
npm run test:e2e:webkit

# Mobil Chrome
npm run test:e2e:mobile
```

### Belirli Bir Test Dosyasını Çalıştır

```bash
npx playwright test tests/e2e/project-management.spec.ts
```

### Belirli Bir Test Senaryosunu Çalıştır

```bash
npx playwright test -g "Yeni proje oluşturma sayfasına gidilebilmeli"
```

### Sadece Başarısız Testleri Tekrar Çalıştır

```bash
npx playwright test --last-failed
```

## Test Raporlarını Görüntüleme

```bash
npm run test:report
```

Bu komut HTML raporunu tarayıcıda açar ve size:
- Test sonuçları özeti
- Başarısız testlerin ekran görüntüleri
- Video kayıtları
- Trace dosyaları
- Detaylı hata mesajları sunar

## Test Yazma İpuçları

### 1. Test İzolasyonu

Her test bağımsız çalışmalı:

```typescript
test('Test senaryosu', async ({ page }) => {
  // Her test kendi setup'ını yapmalı
  await page.goto('/dashboard/projects/create');
  
  // Test işlemleri...
  
  // Cleanup gerekirse afterEach'de yapılmalı
});
```

### 2. Selector Stratejileri

Öncelik sırası:
1. `data-testid` attribute'ları (en stabil)
2. ARIA rolleri ve etiketleri
3. Text içeriği
4. CSS sınıfları (en az tercih edilen)

```typescript
// İyi
await page.click('[data-testid="create-project-btn"]');

// Kabul edilebilir
await page.click('button:has-text("Yeni Proje")');

// Kaçınılmalı
await page.click('.btn-primary.create-btn');
```

### 3. Bekleme Stratejileri

```typescript
// Otomatik bekleme (önerilen)
await expect(page.locator('.success-message')).toBeVisible();

// Manuel bekleme (gerekirse)
await page.waitForSelector('.data-loaded', { timeout: 5000 });

// Timeout kullanmaktan kaçının
await page.waitForTimeout(1000); // Sadece gerekirse
```

### 4. Test Verileri

```typescript
// Test verilerini değişken olarak tanımlayın
const testProject = {
  name: `Test Proje ${Date.now()}`, // Unique isim
  customer: 'Volkan İnanç',
  status: 'Aktif'
};

// Testlerde kullanın
await page.fill('input[name="project_name"]', testProject.name);
```

## CI/CD Entegrasyonu

### GitHub Actions Örneği

`.github/workflows/playwright.yml`:

```yaml
name: Playwright Tests
on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]
jobs:
  test:
    timeout-minutes: 60
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - uses: actions/setup-node@v3
      with:
        node-version: 18
    - name: Install dependencies
      run: npm ci
    - name: Install Playwright Browsers
      run: npx playwright install --with-deps
    - name: Run Playwright tests
      run: npm run test:e2e
    - uses: actions/upload-artifact@v3
      if: always()
      with:
        name: playwright-report
        path: playwright-report/
        retention-days: 30
```

## Sorun Giderme

### Test Başarısız Oluyor

1. **Ekran görüntüsünü kontrol edin**: `test-results/` klasöründe
2. **Video kaydını izleyin**: Başarısız testlerin videosu otomatik kaydedilir
3. **Trace dosyasını açın**: `npx playwright show-trace trace.zip`

### Timeout Hataları

```typescript
// Timeout süresini artırın
test.setTimeout(60000); // 60 saniye

// Veya belirli bir işlem için
await page.click('button', { timeout: 30000 });
```

### Element Bulunamıyor

```typescript
// Element'in yüklendiğinden emin olun
await page.waitForLoadState('networkidle');

// Veya belirli bir selector için bekleyin
await page.waitForSelector('[data-testid="project-card"]');
```

## Best Practices

1. **Test isimleri açıklayıcı olmalı**: "Test 1" yerine "Yeni proje oluşturma sayfasına gidilebilmeli"
2. **Her test bir şeyi test etmeli**: Çok fazla assertion tek testte olmamalı
3. **Test verileri temizlenmeli**: Test sonrası oluşturulan veriler silinmeli
4. **Flaky testlerden kaçının**: Random timeout'lar yerine deterministic bekleme kullanın
5. **Page Object Model kullanın**: Büyük projelerde kod tekrarını azaltır

## Daha Fazla Bilgi

- [Playwright Dokümantasyonu](https://playwright.dev)
- [Best Practices](https://playwright.dev/docs/best-practices)
- [API Reference](https://playwright.dev/docs/api/class-playwright)

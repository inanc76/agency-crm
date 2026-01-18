import { test, expect } from '@playwright/test';

/**
 * PUBLIC OFFER DOWNLOAD - KAPSAMLI TEST SENARYOLARI
 * 
 * Test Kapsamı:
 * 1. Geçerli Token ile Erişim
 * 2. Geçersiz Token ile Erişim
 * 3. PDF İndirme Fonksiyonu
 * 4. Sayfa İçeriği Görüntüleme
 * 5. Responsive Tasarım
 * 6. Güvenlik Kontrolleri
 * 7. Hata Durumları
 */

const BASE_URL = 'http://localhost:8000';

// Test verileri - Bu tokenlar test ortamında oluşturulacak
const testData = {
  validToken: 'valid-test-token-12345',
  invalidToken: 'invalid-token-67890',
  expiredToken: 'expired-token-abcde'
};

test.describe('Public Offer Download - Geçerli Token Erişimi', () => {

  test('Geçerli token ile sayfa açılabilmeli', async ({ page }) => {
    // Geçerli token ile public offer sayfasına git
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // Sayfa başarıyla yüklenmeli
    await expect(page).toHaveURL(new RegExp(`/offer/${testData.validToken}`));

    // Sayfa başlığı görünmeli
    const pageTitle = page.locator('h1, .offer-title, .page-title').first();
    await expect(pageTitle).toBeVisible({ timeout: 5000 });
    await expect(pageTitle).toContainText('Teklif');
  });

  test('Teklif detayları görüntülenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // Müşteri bilgileri görünmeli
    const customerInfo = page.locator('.customer-info, .offer-customer').first();
    await expect(customerInfo).toBeVisible();

    // Teklif tarihi görünmeli
    const offerDate = page.locator('.offer-date, .date').first();
    await expect(offerDate).toBeVisible();

    // Teklif tutarı görünmeli
    const offerAmount = page.locator('.offer-amount, .total-amount, .price').first();
    await expect(offerAmount).toBeVisible();

    // Teklif açıklaması görünmeli
    const offerDescription = page.locator('.offer-description, .description').first();
    await expect(offerDescription).toBeVisible();
  });

  test('Şirket logosu ve bilgileri görüntülenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // Şirket logosu görünmeli
    const companyLogo = page.locator('.company-logo, .logo img').first();
    
    if (await companyLogo.isVisible()) {
      await expect(companyLogo).toBeVisible();
      
      // Logo src attribute'u olmalı
      const src = await companyLogo.getAttribute('src');
      expect(src).toBeTruthy();
    }

    // Şirket adı görünmeli
    const companyName = page.locator('.company-name, .company-title').first();
    await expect(companyName).toBeVisible();

    // İletişim bilgileri görünmeli
    const contactInfo = page.locator('.contact-info, .company-contact').first();
    await expect(contactInfo).toBeVisible();
  });
});

test.describe('Public Offer Download - PDF İndirme', () => {

  test('PDF indirme butonu çalışmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // PDF indirme butonunu bul
    const downloadButton = page.locator('button:has-text("PDF İndir"), a:has-text("İndir"), .download-btn, [data-testid="download-pdf"]').first();
    await expect(downloadButton).toBeVisible({ timeout: 5000 });

    // İndirme işlemini başlat
    const downloadPromise = page.waitForDownload();
    await downloadButton.click();

    // İndirme tamamlanmalı
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toContain('.pdf');
    expect(download.suggestedFilename()).toContain('teklif');
  });

  test('PDF direkt link ile indirilebilmeli', async ({ page }) => {
    // PDF direkt linkine git
    const response = await page.goto(`${BASE_URL}/offer/${testData.validToken}/pdf`);

    // PDF response olmalı
    expect(response?.status()).toBe(200);
    expect(response?.headers()['content-type']).toContain('application/pdf');
  });

  test('PDF içeriği doğru olmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // PDF önizleme varsa kontrol et
    const pdfPreview = page.locator('iframe[src*=".pdf"], embed[type="application/pdf"]').first();
    
    if (await pdfPreview.isVisible()) {
      await expect(pdfPreview).toBeVisible();
      
      const src = await pdfPreview.getAttribute('src');
      expect(src).toContain(testData.validToken);
    }
  });
});

test.describe('Public Offer Download - Geçersiz Token Erişimi', () => {

  test('Geçersiz token ile 404 hatası alınmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/${testData.invalidToken}`);

    // 404 sayfası görünmeli
    await expect(page.locator('text=404, text=Bulunamadı, text=Geçersiz')).toBeVisible({ timeout: 3000 });
  });

  test('Boş token ile hata alınmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/`);

    // 404 veya hata sayfası görünmeli
    const errorMessage = page.locator('text=404, text=Sayfa bulunamadı, text=Geçersiz').first();
    await expect(errorMessage).toBeVisible({ timeout: 3000 });
  });

  test('Süresi dolmuş token ile hata alınmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/${testData.expiredToken}`);

    // Süre dolmuş mesajı görünmeli
    const expiredMessage = page.locator('text=süresi dolmuş, text=geçersiz, text=erişim süresi').first();
    
    if (await expiredMessage.isVisible()) {
      await expect(expiredMessage).toBeVisible();
    } else {
      // Alternatif olarak 404 sayfası görünebilir
      await expect(page.locator('text=404, text=Bulunamadı')).toBeVisible();
    }
  });
});

test.describe('Public Offer Download - Responsive Tasarım', () => {

  test('Mobil görünümde sayfa düzgün görüntülenmeli', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // Sayfa mobilde görünür olmalı
    const pageContent = page.locator('.offer-content, .container, main').first();
    await expect(pageContent).toBeVisible();

    // İndirme butonu mobilde görünür olmalı
    const downloadButton = page.locator('button:has-text("PDF İndir"), .download-btn').first();
    await expect(downloadButton).toBeVisible();

    // Metin okunabilir boyutta olmalı
    const offerTitle = page.locator('h1, .offer-title').first();
    
    if (await offerTitle.isVisible()) {
      const fontSize = await offerTitle.evaluate(el => getComputedStyle(el).fontSize);
      const fontSizeNum = parseInt(fontSize);
      expect(fontSizeNum).toBeGreaterThan(16); // En az 16px
    }
  });

  test('Tablet görünümde sayfa düzgün görüntülenmeli', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // Sayfa tablet boyutunda uygun olmalı
    const pageContent = page.locator('.offer-content, .container').first();
    await expect(pageContent).toBeVisible();

    // Layout tablet için optimize olmalı
    const contentWidth = await pageContent.evaluate(el => el.offsetWidth);
    expect(contentWidth).toBeLessThan(768);
  });

  test('Desktop görünümde tam özellikler görüntülenmeli', async ({ page }) => {
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // Tüm öğeler desktop'ta görünür olmalı
    await expect(page.locator('.company-logo, .logo')).toBeVisible();
    await expect(page.locator('.offer-title, h1')).toBeVisible();
    await expect(page.locator('.download-btn, button:has-text("İndir")')).toBeVisible();
    await expect(page.locator('.contact-info, .company-contact')).toBeVisible();
  });
});

test.describe('Public Offer Download - Güvenlik Kontrolleri', () => {

  test('Token güvenlik kontrolü çalışmalı', async ({ page }) => {
    // Manipüle edilmiş token ile erişim dene
    const manipulatedToken = testData.validToken + 'xxx';
    
    await page.goto(`${BASE_URL}/offer/${manipulatedToken}`);

    // Erişim reddedilmeli
    await expect(page.locator('text=404, text=Geçersiz, text=Bulunamadı')).toBeVisible({ timeout: 3000 });
  });

  test('SQL injection koruması çalışmalı', async ({ page }) => {
    const sqlInjection = "'; DROP TABLE offers; --";
    
    await page.goto(`${BASE_URL}/offer/${encodeURIComponent(sqlInjection)}`);

    // Güvenlik koruması çalışmalı, 404 dönmeli
    await expect(page.locator('text=404, text=Geçersiz')).toBeVisible({ timeout: 3000 });
  });

  test('XSS koruması çalışmalı', async ({ page }) => {
    const xssPayload = '<script>alert("XSS")</script>';
    
    await page.goto(`${BASE_URL}/offer/${encodeURIComponent(xssPayload)}`);

    // Script çalışmamalı
    const alerts = [];
    page.on('dialog', dialog => {
      alerts.push(dialog.message());
      dialog.dismiss();
    });

    await page.waitForTimeout(1000);
    expect(alerts.length).toBe(0);
  });
});

test.describe('Public Offer Download - Hata Durumları', () => {

  test('Network hatası durumunda uygun mesaj gösterilmeli', async ({ page }) => {
    // Network'ü offline yap
    await page.context().setOffline(true);

    try {
      await page.goto(`${BASE_URL}/offer/${testData.validToken}`, { timeout: 5000 });
    } catch (error) {
      // Network hatası bekleniyor
      expect(error.message).toContain('net::ERR_INTERNET_DISCONNECTED');
    }

    // Network'ü tekrar online yap
    await page.context().setOffline(false);
  });

  test('Sunucu hatası durumunda uygun mesaj gösterilmeli', async ({ page }) => {
    // 500 hatası simüle et
    await page.route(`**/offer/${testData.validToken}`, route => {
      route.fulfill({ status: 500, body: 'Internal Server Error' });
    });

    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // Hata sayfası görünmeli
    const errorMessage = page.locator('text=500, text=Sunucu Hatası, text=Bir hata oluştu').first();
    await expect(errorMessage).toBeVisible({ timeout: 3000 });
  });

  test('PDF oluşturulamadığında hata gösterilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/offer/${testData.validToken}`);

    // PDF endpoint'ini hata döndürecek şekilde mock'la
    await page.route(`**/offer/${testData.validToken}/pdf`, route => {
      route.fulfill({ status: 500, body: 'PDF generation failed' });
    });

    const downloadButton = page.locator('button:has-text("PDF İndir"), .download-btn').first();
    await downloadButton.click();

    // Hata mesajı görünmeli
    const errorMessage = page.locator('.error, .alert-danger, text=PDF oluşturulamadı').first();
    await expect(errorMessage).toBeVisible({ timeout: 5000 });
  });
});
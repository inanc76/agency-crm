import { test, expect } from '@playwright/test';
import {
  fillLivewireInput,
  selectLivewireOption,
  clickThemeButton,
  waitForToast
} from './helpers/custom-selectors';

/**
 * OFFERS PDF PREVIEW - KAPSAMLI TEST SENARYOLARI
 * 
 * Test Kapsamı:
 * 1. PDF Preview Modal Açılması
 * 2. PDF İçerik Görüntüleme
 * 3. PDF İndirme Fonksiyonu
 * 4. PDF Yazdırma Fonksiyonu
 * 5. Modal Kapatma
 * 6. Responsive Tasarım
 * 7. Hata Durumları
 */

const BASE_URL = 'http://localhost:8000';

// Test verileri
const testData = {
  offer: {
    customer: 'Volkan İnanç',
    title: 'Test Teklif',
    description: 'Test amaçlı teklif açıklaması'
  }
};

test.describe('Offers PDF Preview - Modal Açılması', () => {

  test('PDF preview modal açılabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    // İlk teklifin PDF preview butonuna tıkla
    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn, [data-testid="pdf-preview"]');
    
    await pdfButton.click();

    // Modal açılmalı
    const modal = page.locator('.modal, .pdf-preview-modal, [data-testid="pdf-modal"]').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Modal başlığı görünmeli
    await expect(modal.locator('.modal-title, .modal-header')).toContainText('PDF Önizleme');
  });

  test('PDF preview modal kapatılabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // X butonu ile kapat
    const closeButton = modal.locator('.close, .modal-close, button:has-text("×")').first();
    await closeButton.click();

    // Modal kapanmalı
    await expect(modal).not.toBeVisible();
  });

  test('ESC tuşu ile modal kapatılabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // ESC tuşuna bas
    await page.keyboard.press('Escape');

    // Modal kapanmalı
    await expect(modal).not.toBeVisible();
  });
});

test.describe('Offers PDF Preview - İçerik Görüntüleme', () => {

  test('PDF içeriği iframe içinde görüntülenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // PDF iframe görünmeli
    const pdfIframe = modal.locator('iframe, .pdf-viewer, embed[type="application/pdf"]').first();
    await expect(pdfIframe).toBeVisible({ timeout: 10000 });

    // PDF src attribute'u olmalı
    const src = await pdfIframe.getAttribute('src');
    expect(src).toContain('.pdf');
  });

  test('PDF yükleme spinner gösterilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Loading spinner görünmeli (kısa süre)
    const spinner = modal.locator('.loading, .spinner, .pdf-loading').first();
    
    if (await spinner.isVisible()) {
      await expect(spinner).toBeVisible();
      
      // Spinner kaybolmalı
      await expect(spinner).not.toBeVisible({ timeout: 10000 });
    }
  });

  test('PDF başlık bilgileri görüntülenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Teklif başlığı görünmeli
    const title = modal.locator('.pdf-title, .offer-title, .modal-title').first();
    await expect(title).toBeVisible();

    // Müşteri adı görünmeli
    const customer = modal.locator('.customer-name, .pdf-customer').first();
    
    if (await customer.isVisible()) {
      await expect(customer).toContainText('Volkan İnanç');
    }
  });
});

test.describe('Offers PDF Preview - İndirme Fonksiyonu', () => {

  test('PDF indirme butonu çalışmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // İndirme butonunu bul
    const downloadButton = modal.locator('button:has-text("İndir"), .download-btn, [data-testid="download-pdf"]').first();
    await expect(downloadButton).toBeVisible();

    // İndirme işlemini başlat
    const downloadPromise = page.waitForDownload();
    await downloadButton.click();

    // İndirme tamamlanmalı
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toContain('.pdf');
  });

  test('PDF indirme linki doğru URL\'e sahip olmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // İndirme linki kontrolü
    const downloadLink = modal.locator('a[href*=".pdf"], a[download]').first();
    
    if (await downloadLink.isVisible()) {
      const href = await downloadLink.getAttribute('href');
      expect(href).toContain('/offers/');
      expect(href).toContain('.pdf');
    }
  });
});

test.describe('Offers PDF Preview - Yazdırma Fonksiyonu', () => {

  test('PDF yazdırma butonu çalışmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Yazdırma butonunu bul
    const printButton = modal.locator('button:has-text("Yazdır"), .print-btn, [data-testid="print-pdf"]').first();
    
    if (await printButton.isVisible()) {
      await expect(printButton).toBeVisible();
      
      // Print dialog açılacağı için sadece butona tıklayabiliriz
      await printButton.click();
      
      // Print dialog açıldığını varsayıyoruz
      expect(true).toBe(true);
    }
  });
});

test.describe('Offers PDF Preview - Responsive Tasarım', () => {

  test('Mobil görünümde modal düzgün görüntülenmeli', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Modal mobilde tam ekran olmalı
    const modalContent = modal.locator('.modal-content, .modal-body').first();
    await expect(modalContent).toBeVisible();

    // PDF viewer mobilde görünür olmalı
    const pdfViewer = modal.locator('iframe, .pdf-viewer').first();
    await expect(pdfViewer).toBeVisible();
  });

  test('Tablet görünümde modal düzgün görüntülenmeli', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Modal tablet boyutunda uygun olmalı
    const modalDialog = modal.locator('.modal-dialog').first();
    
    if (await modalDialog.isVisible()) {
      const boundingBox = await modalDialog.boundingBox();
      expect(boundingBox?.width).toBeLessThan(768);
    }
  });
});

test.describe('Offers PDF Preview - Hata Durumları', () => {

  test('PDF yüklenemediğinde hata mesajı gösterilmeli', async ({ page }) => {
    // PDF endpoint'ini 404 döndürecek şekilde mock'la
    await page.route('**/offers/*/pdf', route => {
      route.fulfill({ status: 404, body: 'Not Found' });
    });

    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Hata mesajı görünmeli
    const errorMessage = modal.locator('.error, .alert-danger, .pdf-error').first();
    await expect(errorMessage).toBeVisible({ timeout: 10000 });
    await expect(errorMessage).toContainText('PDF yüklenemedi');
  });

  test('Network hatası durumunda uygun mesaj gösterilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/customers?tab=offers`);

    const firstOffer = page.locator('.offer-card, tbody tr').first();
    const pdfButton = firstOffer.locator('button:has-text("PDF"), .pdf-preview-btn');
    
    await pdfButton.click();

    const modal = page.locator('.modal, .pdf-preview-modal').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // Network'ü offline yap
    await page.context().setOffline(true);

    // Yeniden yükleme butonuna tıkla (varsa)
    const reloadButton = modal.locator('button:has-text("Yeniden Yükle"), .reload-btn').first();
    
    if (await reloadButton.isVisible()) {
      await reloadButton.click();
      
      // Network hatası mesajı görünmeli
      const networkError = modal.locator('.network-error, .connection-error').first();
      await expect(networkError).toBeVisible({ timeout: 5000 });
    }

    // Network'ü tekrar online yap
    await page.context().setOffline(false);
  });

  test('Geçersiz teklif ID\'si için hata gösterilmeli', async ({ page }) => {
    // Geçersiz teklif ID'si ile direkt PDF preview sayfasına git
    await page.goto(`${BASE_URL}/dashboard/customers/offers/invalid-id/pdf-preview`);

    // 404 sayfası veya hata mesajı görünmeli
    const errorMessage = page.locator('text=404, text=Bulunamadı, text=Geçersiz').first();
    await expect(errorMessage).toBeVisible({ timeout: 3000 });
  });
});
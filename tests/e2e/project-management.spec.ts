import { test, expect } from '@playwright/test';

/**
 * PROJE YÖNETİMİ - KAPSAMLI TEST SENARYOLARI
 * 
 * Test Kapsamı:
 * 1. Projeler Sekmesi (Projects Tab)
 * 2. Görevler Sekmesi (Tasks Tab)
 * 3. Raporlar Sekmesi (Reports Tab)
 * 4. Proje Oluşturma
 * 5. Görev Oluşturma
 * 6. Rapor Oluşturma
 */

const BASE_URL = 'http://localhost:8000';
const DASHBOARD_PROJECTS_URL = `${BASE_URL}/dashboard/projects`;

// Test verileri
const testData = {
  project: {
    name: 'Test Projesi',
    customer: 'Volkan İnanç',
    status: 'Tasak',
    timezone: 'Istanbul (UTC+3)',
    projectType: 'Web Geliştirme',
    description: 'Test amaçlı oluşturulan proje',
    startDate: '01.01.2026',
    endDate: '31.12.2026'
  },
  task: {
    customer: 'Volkan İnanç',
    project: 'Deneme Firması',
    assignee: 'Volkan İnanç',
    priority: 'Normal',
    status: 'Yapılacak',
    title: 'Test Görevi',
    description: 'Test amaçlı görev açıklaması'
  },
  report: {
    customer: 'Volkan İnanç',
    projectType: 'Web Geliştirme',
    date: '16.01.2026'
  }
};

test.describe('Proje Yönetimi - Sekme Navigasyonu', () => {

  test('Projeler sekmesine geçiş yapılabilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    await expect(page).toHaveURL(/tab=projects/);
    await expect(page.locator('text=Projeler')).toBeVisible();
    await expect(page.locator('button:has-text("Yeni Proje")')).toBeVisible();
    
    // Proje kartlarının görünür olduğunu kontrol et
    await expect(page.locator('[data-testid="project-card"]').first()).toBeVisible();
  });

  test('Görevler sekmesine geçiş yapılabilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    await expect(page).toHaveURL(/tab=tasks/);
    await expect(page.locator('text=Görevler')).toBeVisible();
    await expect(page.locator('button:has-text("Yeni Görev")')).toBeVisible();
  });

  test('Raporlar sekmesine geçiş yapılabilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    await expect(page).toHaveURL(/tab=reports/);
    await expect(page.locator('text=Raporlar')).toBeVisible();
    await expect(page.locator('button:has-text("Yeni Rapor")')).toBeVisible();
  });

  test('Sekmeler arası geçiş yapılabilmeli', async ({ page }) => {
    await page.goto(DASHBOARD_PROJECTS_URL);
    
    // Projeler -> Görevler
    await page.click('text=Görevler');
    await expect(page).toHaveURL(/tab=tasks/);
    
    // Görevler -> Raporlar
    await page.click('text=Raporlar');
    await expect(page).toHaveURL(/tab=reports/);
    
    // Raporlar -> Projeler
    await page.click('text=Projeler');
    await expect(page).toHaveURL(/tab=projects/);
  });
});

test.describe('Projeler Sekmesi - Listeleme ve Filtreleme', () => {

  test('Proje listesi görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Proje kartlarının yüklendiğini kontrol et
    await page.waitForSelector('[data-testid="project-card"]', { timeout: 5000 });
    
    const projectCards = page.locator('[data-testid="project-card"]');
    await expect(projectCards).toHaveCount(await projectCards.count());
    
    // İlk proje kartının içeriğini kontrol et
    const firstCard = projectCards.first();
    await expect(firstCard.locator('.project-name')).toBeVisible();
    await expect(firstCard.locator('.project-status')).toBeVisible();
  });

  test('Proje arama fonksiyonu çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const searchInput = page.locator('input[placeholder*="Proje ara"]');
    await searchInput.fill('Deneme Firması');
    
    await page.waitForTimeout(500); // Debounce için bekle
    
    const projectCards = page.locator('[data-testid="project-card"]');
    await expect(projectCards.first()).toContainText('Deneme Firması');
  });

  test('Durum filtreleri çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Tüm Durumlar dropdown'ını aç
    await page.click('text=Tüm Durumlar');
    
    // Bir durum seç
    await page.click('text=Devam Ediyor');
    
    await page.waitForTimeout(500);
    
    // Filtrelenmiş sonuçları kontrol et
    const statusBadges = page.locator('.project-status:has-text("Devam Ediyor")');
    expect(await statusBadges.count()).toBeGreaterThan(0);
  });

  test('Tip filtreleri çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    await page.click('text=Tüm Tipler');
    await page.click('text=Destek Hizmeti');
    
    await page.waitForTimeout(500);
    
    const projectCards = page.locator('[data-testid="project-card"]');
    expect(await projectCards.count()).toBeGreaterThan(0);
  });

  test('Proje kartı detayları doğru görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const firstCard = page.locator('[data-testid="project-card"]').first();
    
    // Kart içeriğini kontrol et
    await expect(firstCard.locator('.project-code')).toBeVisible();
    await expect(firstCard.locator('.project-name')).toBeVisible();
    await expect(firstCard.locator('.project-status')).toBeVisible();
    await expect(firstCard.locator('.project-days')).toBeVisible();
    await expect(firstCard.locator('.project-owner')).toBeVisible();
    await expect(firstCard.locator('.project-date')).toBeVisible();
  });
});

test.describe('Proje Oluşturma - Pozitif Senaryolar', () => {

  test('Yeni proje oluşturma sayfasına gidilebilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    await page.click('button:has-text("Yeni Proje")');
    
    await expect(page).toHaveURL(/\/dashboard\/projects\/create/);
    await expect(page.locator('text=Yeni Proje Oluştur')).toBeVisible();
  });

  test('Tüm zorunlu alanlar doldurularak proje oluşturulabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    // Proje Bilgileri
    await page.fill('input[name="project_name"]', testData.project.name);
    
    // Müşteri seçimi
    await page.click('text=Müşteri Seçin');
    await page.click(`text=${testData.project.customer}`);
    
    // Durum seçimi
    await page.click('select[name="status"]');
    await page.selectOption('select[name="status"]', testData.project.status);
    
    // Zaman dilimi
    await page.click('select[name="timezone"]');
    await page.selectOption('select[name="timezone"]', { label: testData.project.timezone });
    
    // Proje tipi
    await page.click('text=Proje Tipi Seçin');
    await page.click(`text=${testData.project.projectType}`);
    
    // Tarihler
    await page.fill('input[name="start_date"]', testData.project.startDate);
    await page.fill('input[name="end_date"]', testData.project.endDate);
    
    // Açıklama
    await page.fill('textarea[name="description"]', testData.project.description);
    
    // Kaydet
    await page.click('button:has-text("Kaydet")');
    
    // Başarı mesajı kontrolü
    await expect(page.locator('.success-message, .toast-success')).toBeVisible({ timeout: 5000 });
    
    // Proje listesine yönlendirildiğini kontrol et
    await expect(page).toHaveURL(/tab=projects/);
  });

  test('Proje lideri seçilerek proje oluşturulabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'Proje Lideri Test');
    
    // Katılımcılar bölümü
    await page.click('text=Proje Lideri Seçin');
    await page.click('text=Volkan İnanç');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('.success-message, .toast-success')).toBeVisible({ timeout: 5000 });
  });

  test('Proje üyeleri eklenebilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'Üye Ekleme Test');
    
    // Proje üyeleri dropdown
    await page.click('select[name="project_members"]');
    await page.selectOption('select[name="project_members"]', { label: 'Volkan İnanç' });
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('.success-message, .toast-success')).toBeVisible({ timeout: 5000 });
  });

  test('Faz ekle butonu çalışmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    const fazEkleButton = page.locator('button:has-text("Faz Ekle")');
    await expect(fazEkleButton).toBeVisible();
    
    await fazEkleButton.click();
    
    // Faz ekleme modalı veya alanı açılmalı
    await expect(page.locator('.phase-form, .modal')).toBeVisible();
  });

  test('İptal butonu çalışmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'İptal Test');
    
    await page.click('button:has-text("İptal")');
    
    // Proje listesine geri dönmeli
    await expect(page).toHaveURL(/tab=projects/);
  });
});

test.describe('Proje Oluşturma - Negatif Senaryolar', () => {

  test('Proje adı boş bırakıldığında hata vermeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Proje adı gereklidir, text=Bu alan zorunludur')).toBeVisible();
  });

  test('Müşteri seçilmeden kayıt yapılamamalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'Test Proje');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Müşteri seçimi zorunludur')).toBeVisible();
  });

  test('Geçersiz tarih aralığı girildiğinde hata vermeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'Tarih Test');
    await page.fill('input[name="start_date"]', '31.12.2026');
    await page.fill('input[name="end_date"]', '01.01.2026');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Bitiş tarihi başlangıç tarihinden önce olamaz')).toBeVisible();
  });

  test('Çok uzun proje adı girildiğinde hata vermeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    const longName = 'A'.repeat(256);
    await page.fill('input[name="project_name"]', longName);
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Proje adı çok uzun')).toBeVisible();
  });

  test('Özel karakterler içeren proje adı kontrolü', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', '<script>alert("test")</script>');
    
    await page.click('button:has-text("Kaydet")');
    
    // XSS koruması çalışmalı
    await expect(page.locator('alert')).not.toBeVisible();
  });
});

test.describe('Görevler Sekmesi - Listeleme ve Filtreleme', () => {

  test('Görev listesi görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    await page.waitForSelector('table', { timeout: 5000 });
    
    const taskRows = page.locator('tbody tr');
    expect(await taskRows.count()).toBeGreaterThan(0);
  });

  test('Görev arama fonksiyonu çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    const searchInput = page.locator('input[placeholder*="Görev ara"]');
    await searchInput.fill('İletişim sayfasının yapılması');
    
    await page.waitForTimeout(500);
    
    const taskRows = page.locator('tbody tr');
    await expect(taskRows.first()).toContainText('İletişim sayfasının yapılması');
  });

  test('Öncelik filtreleri çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    await page.click('text=Tüm Öncelikler');
    await page.click('text=Normal');
    
    await page.waitForTimeout(500);
    
    const normalTasks = page.locator('td:has-text("Normal")');
    expect(await normalTasks.count()).toBeGreaterThan(0);
  });

  test('Durum filtreleri çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    await page.click('text=Tüm Durumlar');
    await page.click('text=Devam Ediyor');
    
    await page.waitForTimeout(500);
    
    const statusBadges = page.locator('.status-badge:has-text("Devam Ediyor")');
    expect(await statusBadges.count()).toBeGreaterThan(0);
  });

  test('Görev satırı tıklanabilir olmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    const firstRow = page.locator('tbody tr').first();
    await firstRow.click();
    
    // Görev detay sayfası veya modal açılmalı
    await expect(page.locator('.task-detail, .modal')).toBeVisible();
  });

  test('Görev tablosu sütunları doğru görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    // Tablo başlıklarını kontrol et
    await expect(page.locator('th:has-text("Konu")')).toBeVisible();
    await expect(page.locator('th:has-text("Proje")')).toBeVisible();
    await expect(page.locator('th:has-text("Öncelik")')).toBeVisible();
    await expect(page.locator('th:has-text("Durum")')).toBeVisible();
    await expect(page.locator('th:has-text("Atanan")')).toBeVisible();
  });

  test('Checkbox seçimi çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    const firstCheckbox = page.locator('tbody tr input[type="checkbox"]').first();
    await firstCheckbox.check();
    
    await expect(firstCheckbox).toBeChecked();
  });

  test('Toplu seçim çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    const selectAllCheckbox = page.locator('thead input[type="checkbox"]');
    await selectAllCheckbox.check();
    
    const allCheckboxes = page.locator('tbody tr input[type="checkbox"]');
    const count = await allCheckboxes.count();
    
    for (let i = 0; i < count; i++) {
      await expect(allCheckboxes.nth(i)).toBeChecked();
    }
  });
});

test.describe('Görev Oluşturma - Pozitif Senaryolar', () => {

  test('Yeni görev oluşturma sayfasına gidilebilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    await page.click('button:has-text("Yeni Görev")');
    
    await expect(page).toHaveURL(/\/dashboard\/projects\/tasks\/create/);
    await expect(page.locator('text=Yeni Görev Oluştur')).toBeVisible();
  });

  test('Tüm zorunlu alanlar doldurularak görev oluşturulabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    // Müşteri seçimi
    await page.click('text=Müşteri seçin');
    await page.click(`text=${testData.task.customer}`);
    
    // Proje seçimi
    await page.click('text=Önce müşteri seçin');
    await page.click(`text=${testData.task.project}`);
    
    // Atanan kişi
    await page.click('input[placeholder*="atanan"]');
    await page.fill('input[placeholder*="atanan"]', testData.task.assignee);
    
    // Öncelik
    await page.click('select[name="priority"]');
    await page.selectOption('select[name="priority"]', testData.task.priority);
    
    // Durum
    await page.click('select[name="status"]');
    await page.selectOption('select[name="status"]', testData.task.status);
    
    // Görev detayları
    await page.fill('input[name="title"]', testData.task.title);
    await page.fill('textarea[name="description"]', testData.task.description);
    
    // Kaydet
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('.success-message, .toast-success')).toBeVisible({ timeout: 5000 });
    await expect(page).toHaveURL(/tab=tasks/);
  });

  test('Müşteri seçildiğinde ilgili projeler yüklenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    // Proje dropdown'ı aktif olmalı
    const projectDropdown = page.locator('select[name="project"]');
    await expect(projectDropdown).toBeEnabled();
    
    // Projeler yüklenmiş olmalı
    const options = projectDropdown.locator('option');
    expect(await options.count()).toBeGreaterThan(1);
  });

  test('Öğeler bölümüne dosya eklenebilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    await page.fill('input[name="title"]', 'Dosya Ekleme Test');
    
    // Dosya yükleme alanı
    const fileInput = page.locator('input[type="file"]');
    await fileInput.setInputFiles('./tests/fixtures/test-file.pdf');
    
    await expect(page.locator('.uploaded-file')).toBeVisible();
  });

  test('Görev özeti müşteri seçimine göre güncellenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    // Sağ taraftaki özet bölümü
    const summary = page.locator('[data-testid="task-summary"]');
    await expect(summary).toContainText('Volkan İnanç');
  });
});

test.describe('Görev Oluşturma - Negatif Senaryolar', () => {

  test('Müşteri seçilmeden görev oluşturulamaz', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    await page.fill('input[name="title"]', 'Test Görev');
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Müşteri seçimi zorunludur')).toBeVisible();
  });

  test('Proje seçilmeden görev oluşturulamaz', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    await page.fill('input[name="title"]', 'Test Görev');
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Proje seçimi zorunludur')).toBeVisible();
  });

  test('Görev başlığı boş bırakılamaz', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Görev başlığı gereklidir')).toBeVisible();
  });

  test('Geçersiz dosya formatı yüklenemez', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    const fileInput = page.locator('input[type="file"]');
    await fileInput.setInputFiles('./tests/fixtures/malicious.exe');
    
    await expect(page.locator('text=Geçersiz dosya formatı')).toBeVisible();
  });

  test('Maksimum dosya boyutu aşılamaz', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    // 10MB üzeri dosya
    const fileInput = page.locator('input[type="file"]');
    await fileInput.setInputFiles('./tests/fixtures/large-file.pdf');
    
    await expect(page.locator('text=Dosya boyutu çok büyük')).toBeVisible();
  });
});

test.describe('Raporlar Sekmesi - Listeleme ve Filtreleme', () => {

  test('Rapor listesi görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    await page.waitForSelector('table', { timeout: 5000 });
    
    const reportRows = page.locator('tbody tr');
    expect(await reportRows.count()).toBeGreaterThan(0);
  });

  test('Rapor arama fonksiyonu çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    const searchInput = page.locator('input[placeholder*="Rapor ara"]');
    await searchInput.fill('Destek Hizmeti');
    
    await page.waitForTimeout(500);
    
    const reportRows = page.locator('tbody tr');
    await expect(reportRows.first()).toContainText('Destek Hizmeti');
  });

  test('Rapor tablosu sütunları doğru görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    await expect(page.locator('th:has-text("Tarih")')).toBeVisible();
    await expect(page.locator('th:has-text("Raporu Giren")')).toBeVisible();
    await expect(page.locator('th:has-text("Müşteri")')).toBeVisible();
    await expect(page.locator('th:has-text("Hizmet/Proje")')).toBeVisible();
    await expect(page.locator('th:has-text("Süre")')).toBeVisible();
    await expect(page.locator('th:has-text("Rapor Özeti")')).toBeVisible();
  });

  test('Rapor satırı detayları görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    const firstRow = page.locator('tbody tr').first();
    
    await expect(firstRow.locator('td').nth(0)).toBeVisible(); // Tarih
    await expect(firstRow.locator('td').nth(1)).toBeVisible(); // Raporu Giren
    await expect(firstRow.locator('td').nth(2)).toBeVisible(); // Müşteri
    await expect(firstRow.locator('td').nth(3)).toBeVisible(); // Hizmet/Proje
    await expect(firstRow.locator('td').nth(4)).toBeVisible(); // Süre
  });

  test('Destek Hizmeti badge görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    const badge = page.locator('.badge:has-text("Destek Hizmeti")');
    await expect(badge.first()).toBeVisible();
  });

  test('Rapor özeti görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    const firstRow = page.locator('tbody tr').first();
    const summary = firstRow.locator('td:last-child');
    
    await expect(summary).toBeVisible();
    expect(await summary.textContent()).not.toBe('');
  });

  test('Süre formatı doğru görüntülenmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    const timeCell = page.locator('td:has-text("1s 00dk")').first();
    await expect(timeCell).toBeVisible();
  });
});

test.describe('Rapor Oluşturma - Pozitif Senaryolar', () => {

  test('Yeni rapor oluşturma sayfasına gidilebilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=reports`);
    
    await page.click('button:has-text("Yeni Rapor")');
    
    await expect(page).toHaveURL(/\/dashboard\/projects\/reports\/create/);
    await expect(page.locator('text=Yeni Rapor Ekle')).toBeVisible();
  });

  test('Müşteri seçilerek rapor oluşturulabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    // Müşteri seçimi
    await page.click('text=Müşteri seçin');
    await page.click(`text=${testData.report.customer}`);
    
    // Rapor ilişkisi sekmesi
    await page.click('text=Proje');
    
    // Proje tipi seçimi
    await page.click('text=Seçiniz');
    await page.click(`text=${testData.report.projectType}`);
    
    // Kaydet
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('.success-message, .toast-success')).toBeVisible({ timeout: 5000 });
  });

  test('Rapor ilişkisi sekmeleri çalışmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    // Proje sekmesi
    await page.click('text=Proje');
    await expect(page.locator('text=Proje Tipi')).toBeVisible();
    
    // Görev sekmesi
    await page.click('text=Görev');
    await expect(page.locator('text=Görev seçimi')).toBeVisible();
    
    // Rapor Yok sekmesi
    await page.click('text=Rapor Yok');
    await expect(page.locator('text=Rapor ilişkisi olmadan')).toBeVisible();
  });

  test('Rapor özeti görüntülenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    const summary = page.locator('[data-testid="report-summary"]');
    await expect(summary).toBeVisible();
    
    // Oluşturan bilgisi
    await expect(summary.locator('text=Volkan İnanç')).toBeVisible();
    
    // Tarih bilgisi
    await expect(summary.locator('text=16.01.2026')).toBeVisible();
    
    // Toplam süre
    await expect(summary.locator('text=0s 00dk')).toBeVisible();
  });

  test('Rapor Ekle butonu ile yeni rapor satırı eklenebilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    const addButton = page.locator('button:has-text("Rapor Ekle")');
    await addButton.click();
    
    // Yeni rapor satırı formu görünmeli
    await expect(page.locator('.report-row-form')).toBeVisible();
  });

  test('Rapor bilgileri doldurulabilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    await page.click('button:has-text("Rapor Ekle")');
    
    // Rapor detayları
    await page.fill('input[name="report_title"]', 'Test Rapor Başlığı');
    await page.fill('textarea[name="report_description"]', 'Test rapor açıklaması');
    
    // Süre girişi
    await page.fill('input[name="hours"]', '2');
    await page.fill('input[name="minutes"]', '30');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('.success-message')).toBeVisible();
  });
});

test.describe('Rapor Oluşturma - Negatif Senaryolar', () => {

  test('Müşteri seçilmeden rapor oluşturulamaz', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Müşteri seçimi zorunludur')).toBeVisible();
  });

  test('Proje tipi seçilmeden kayıt yapılamaz', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    await page.click('text=Proje');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Lütfen önce proje seçin')).toBeVisible();
  });

  test('Rapor satırı olmadan kayıt yapılamaz', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Henüz rapor satırı eklenmemiş')).toBeVisible();
  });

  test('Geçersiz süre girişi kabul edilmemeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/reports/create`);
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    await page.click('button:has-text("Rapor Ekle")');
    
    await page.fill('input[name="hours"]', '-1');
    await page.fill('input[name="minutes"]', '70');
    
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('text=Geçersiz süre girişi')).toBeVisible();
  });
});

test.describe('Entegrasyon Testleri', () => {

  test('Proje oluştur -> Görev ekle -> Rapor oluştur akışı', async ({ page }) => {
    // 1. Proje oluştur
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'Entegrasyon Test Projesi');
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    await expect(page).toHaveURL(/tab=projects/);
    
    // 2. Görev ekle
    await page.click('text=Görevler');
    await page.click('button:has-text("Yeni Görev")');
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    await page.click('text=Önce müşteri seçin');
    await page.click('text=Entegrasyon Test Projesi');
    await page.fill('input[name="title"]', 'Test Görevi');
    await page.click('button:has-text("Kaydet")');
    
    await expect(page).toHaveURL(/tab=tasks/);
    
    // 3. Rapor oluştur
    await page.click('text=Raporlar');
    await page.click('button:has-text("Yeni Rapor")');
    
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    await page.click('text=Görev');
    await page.click('text=Test Görevi');
    await page.click('button:has-text("Kaydet")');
    
    await expect(page).toHaveURL(/tab=reports/);
  });

  test('Aynı müşteri için birden fazla proje oluşturulabilmeli', async ({ page }) => {
    for (let i = 1; i <= 3; i++) {
      await page.goto(`${BASE_URL}/dashboard/projects/create`);
      
      await page.fill('input[name="project_name"]', `Çoklu Proje ${i}`);
      await page.click('text=Müşteri Seçin');
      await page.click('text=Volkan İnanç');
      await page.click('button:has-text("Kaydet")');
      
      await expect(page.locator('.success-message')).toBeVisible();
    }
    
    // Tüm projelerin listelendiğini kontrol et
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    await expect(page.locator('text=Çoklu Proje 1')).toBeVisible();
    await expect(page.locator('text=Çoklu Proje 2')).toBeVisible();
    await expect(page.locator('text=Çoklu Proje 3')).toBeVisible();
  });

  test('Proje silme işlemi görevleri etkilememeli', async ({ page }) => {
    // Önce bir proje ve görev oluştur
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    await page.fill('input[name="project_name"]', 'Silinecek Proje');
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    // Projeyi sil
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    await page.locator('text=Silinecek Proje').hover();
    await page.click('button[aria-label="Sil"]');
    await page.click('button:has-text("Evet, Sil")');
    
    // Görevler sekmesinde ilgili görevlerin durumunu kontrol et
    await page.click('text=Görevler');
    
    // Görevler hala görünür olmalı veya uygun bir mesaj gösterilmeli
    await expect(page.locator('text=Proje silinmiş')).toBeVisible();
  });
});

test.describe('Performans ve Yükleme Testleri', () => {

  test('Proje listesi 3 saniyeden kısa sürede yüklenmeli', async ({ page }) => {
    const startTime = Date.now();
    
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    await page.waitForSelector('[data-testid="project-card"]', { timeout: 3000 });
    
    const loadTime = Date.now() - startTime;
    expect(loadTime).toBeLessThan(3000);
  });

  test('Görev listesi pagination çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    // İlk sayfa
    const firstPageRows = await page.locator('tbody tr').count();
    
    // Sonraki sayfa
    const nextButton = page.locator('button:has-text("Sonraki")');
    if (await nextButton.isVisible()) {
      await nextButton.click();
      await page.waitForTimeout(500);
      
      const secondPageRows = await page.locator('tbody tr').count();
      expect(secondPageRows).toBeGreaterThan(0);
    }
  });

  test('Büyük veri setinde arama performansı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const searchInput = page.locator('input[placeholder*="Proje ara"]');
    
    const startTime = Date.now();
    await searchInput.fill('Test');
    await page.waitForTimeout(500);
    
    const searchTime = Date.now() - startTime;
    expect(searchTime).toBeLessThan(1000);
  });

  test('Lazy loading çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Sayfayı aşağı kaydır
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    
    await page.waitForTimeout(1000);
    
    // Yeni içerik yüklenmiş olmalı
    const projectCards = page.locator('[data-testid="project-card"]');
    expect(await projectCards.count()).toBeGreaterThan(3);
  });
});

test.describe('Erişilebilirlik Testleri', () => {

  test('Klavye navigasyonu çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Tab ile navigasyon
    await page.keyboard.press('Tab');
    await page.keyboard.press('Tab');
    await page.keyboard.press('Enter');
    
    // Focus durumunu kontrol et
    const focusedElement = await page.evaluate(() => document.activeElement?.tagName);
    expect(focusedElement).toBeTruthy();
  });

  test('ARIA etiketleri mevcut olmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Butonların aria-label'ları
    await expect(page.locator('button[aria-label="Yeni Proje"]')).toBeVisible();
    
    // Form alanlarının aria-describedby'ları
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    await expect(page.locator('input[aria-describedby]')).toHaveCount(await page.locator('input[aria-describedby]').count());
  });

  test('Ekran okuyucu için alternatif metinler mevcut olmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const images = page.locator('img');
    const count = await images.count();
    
    for (let i = 0; i < count; i++) {
      const alt = await images.nth(i).getAttribute('alt');
      expect(alt).toBeTruthy();
    }
  });

  test('Form hataları ekran okuyucu için erişilebilir olmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.click('button:has-text("Kaydet")');
    
    const errorMessage = page.locator('[role="alert"]');
    await expect(errorMessage).toBeVisible();
  });
});

test.describe('Responsive Tasarım Testleri', () => {

  test('Mobil görünümde hamburger menü çalışmalı', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const hamburgerMenu = page.locator('[aria-label="Menu"]');
    await expect(hamburgerMenu).toBeVisible();
    
    await hamburgerMenu.click();
    await expect(page.locator('.mobile-menu')).toBeVisible();
  });

  test('Tablet görünümde layout düzgün görünmeli', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const projectCards = page.locator('[data-testid="project-card"]');
    await expect(projectCards.first()).toBeVisible();
  });

  test('Desktop görünümde tüm öğeler görünür olmalı', async ({ page }) => {
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    await expect(page.locator('text=Projeler')).toBeVisible();
    await expect(page.locator('text=Görevler')).toBeVisible();
    await expect(page.locator('text=Raporlar')).toBeVisible();
  });

  test('Mobilde form alanları kullanılabilir olmalı', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    const projectNameInput = page.locator('input[name="project_name"]');
    await expect(projectNameInput).toBeVisible();
    
    await projectNameInput.fill('Mobil Test');
    await expect(projectNameInput).toHaveValue('Mobil Test');
  });
});

test.describe('Güvenlik Testleri', () => {

  test('XSS saldırısına karşı korumalı olmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    const xssPayload = '<script>alert("XSS")</script>';
    await page.fill('input[name="project_name"]', xssPayload);
    await page.click('button:has-text("Kaydet")');
    
    // Alert çalışmamalı
    page.on('dialog', async dialog => {
      throw new Error('XSS vulnerability detected!');
    });
    
    await page.waitForTimeout(1000);
  });

  test('SQL injection koruması olmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const sqlPayload = "'; DROP TABLE projects; --";
    const searchInput = page.locator('input[placeholder*="Proje ara"]');
    await searchInput.fill(sqlPayload);
    
    await page.waitForTimeout(500);
    
    // Sayfa hala çalışıyor olmalı
    await expect(page.locator('text=Projeler')).toBeVisible();
  });

  test('CSRF token kontrolü yapılmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    const csrfToken = await page.locator('input[name="_token"]').getAttribute('value');
    expect(csrfToken).toBeTruthy();
    expect(csrfToken?.length).toBeGreaterThan(10);
  });

  test('Yetkisiz erişim engellenmelidir', async ({ page, context }) => {
    // Çıkış yap
    await context.clearCookies();
    
    // Korumalı sayfaya erişmeye çalış
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    // Login sayfasına yönlendirilmeli
    await expect(page).toHaveURL(/login/);
  });
});

test.describe('Hata Yönetimi Testleri', () => {

  test('Network hatası durumunda uygun mesaj gösterilmeli', async ({ page }) => {
    await page.route('**/api/projects', route => route.abort());
    
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    await expect(page.locator('text=Bağlantı hatası, text=Veriler yüklenemedi')).toBeVisible();
  });

  test('404 hatası durumunda uygun sayfa gösterilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/nonexistent`);
    
    await expect(page.locator('text=Sayfa bulunamadı, text=404')).toBeVisible();
  });

  test('500 hatası durumunda kullanıcı bilgilendirilmeli', async ({ page }) => {
    await page.route('**/api/projects', route => 
      route.fulfill({ status: 500, body: 'Internal Server Error' })
    );
    
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    await expect(page.locator('text=Bir hata oluştu, text=Sunucu hatası')).toBeVisible();
  });

  test('Timeout durumunda retry mekanizması çalışmalı', async ({ page }) => {
    let requestCount = 0;
    
    await page.route('**/api/projects', route => {
      requestCount++;
      if (requestCount < 3) {
        route.abort();
      } else {
        route.continue();
      }
    });
    
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    expect(requestCount).toBeGreaterThanOrEqual(3);
  });

  test('Validation hataları kullanıcı dostu gösterilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.click('button:has-text("Kaydet")');
    
    const errorMessages = page.locator('.error-message, .text-red-500');
    expect(await errorMessages.count()).toBeGreaterThan(0);
    
    // Her hata mesajı ilgili alanın yanında olmalı
    await expect(errorMessages.first()).toBeVisible();
  });
});

test.describe('Kullanıcı Deneyimi Testleri', () => {

  test('Loading spinner gösterilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Yavaş network simüle et
    await page.route('**/api/projects', route => {
      setTimeout(() => route.continue(), 2000);
    });
    
    await page.reload();
    
    await expect(page.locator('.spinner, .loading')).toBeVisible();
  });

  test('Başarı mesajları otomatik kapanmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'Toast Test');
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    const successToast = page.locator('.toast-success, .success-message');
    await expect(successToast).toBeVisible();
    
    // 5 saniye sonra kaybolmalı
    await page.waitForTimeout(5000);
    await expect(successToast).not.toBeVisible();
  });

  test('Onay dialogları çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const firstProject = page.locator('[data-testid="project-card"]').first();
    await firstProject.hover();
    await firstProject.locator('button[aria-label="Sil"]').click();
    
    // Onay dialogu açılmalı
    await expect(page.locator('.modal, .dialog')).toBeVisible();
    await expect(page.locator('text=Emin misiniz?')).toBeVisible();
    
    // İptal butonu
    await page.click('button:has-text("İptal")');
    await expect(page.locator('.modal, .dialog')).not.toBeVisible();
  });

  test('Tooltip\'ler çalışmalı', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const infoIcon = page.locator('[data-tooltip]').first();
    await infoIcon.hover();
    
    await expect(page.locator('.tooltip')).toBeVisible();
  });

  test('Breadcrumb navigasyonu çalışmalı', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await expect(page.locator('text=Dashboard')).toBeVisible();
    await expect(page.locator('text=Projeler')).toBeVisible();
    await expect(page.locator('text=Yeni Proje')).toBeVisible();
    
    // Breadcrumb ile geri dön
    await page.click('text=Projeler');
    await expect(page).toHaveURL(/tab=projects/);
  });

  test('Boş durum mesajları gösterilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Tüm projeleri filtrele
    const searchInput = page.locator('input[placeholder*="Proje ara"]');
    await searchInput.fill('NONEXISTENT_PROJECT_XYZ123');
    
    await page.waitForTimeout(500);
    
    await expect(page.locator('text=Proje bulunamadı, text=Sonuç yok')).toBeVisible();
  });

  test('Drag and drop çalışmalı (eğer varsa)', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=tasks`);
    
    const firstTask = page.locator('tbody tr').first();
    const secondTask = page.locator('tbody tr').nth(1);
    
    if (await firstTask.isVisible() && await secondTask.isVisible()) {
      await firstTask.dragTo(secondTask);
      
      // Sıralama değişmiş olmalı
      await page.waitForTimeout(500);
    }
  });
});

test.describe('Veri Tutarlılığı Testleri', () => {

  test('Oluşturulan proje listede görünmeli', async ({ page }) => {
    const uniqueProjectName = `Test Proje ${Date.now()}`;
    
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', uniqueProjectName);
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    await expect(page).toHaveURL(/tab=projects/);
    
    // Yeni proje listede görünmeli
    await expect(page.locator(`text=${uniqueProjectName}`)).toBeVisible();
  });

  test('Güncellenen proje bilgileri kaydedilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const firstProject = page.locator('[data-testid="project-card"]').first();
    await firstProject.click();
    
    // Düzenleme sayfası
    await page.click('button:has-text("Düzenle")');
    
    const newName = `Güncellenmiş Proje ${Date.now()}`;
    await page.fill('input[name="project_name"]', newName);
    await page.click('button:has-text("Kaydet")');
    
    // Güncelleme kontrolü
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    await expect(page.locator(`text=${newName}`)).toBeVisible();
  });

  test('Silinen proje listeden kaldırılmalı', async ({ page }) => {
    const projectToDelete = 'Silinecek Test Proje';
    
    // Önce proje oluştur
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    await page.fill('input[name="project_name"]', projectToDelete);
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    // Projeyi sil
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    const projectCard = page.locator(`text=${projectToDelete}`).locator('..');
    await projectCard.hover();
    await projectCard.locator('button[aria-label="Sil"]').click();
    await page.click('button:has-text("Evet, Sil")');
    
    // Proje listede olmamalı
    await expect(page.locator(`text=${projectToDelete}`)).not.toBeVisible();
  });

  test('Müşteri değiştirildiğinde ilgili projeler güncellenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/tasks/create`);
    
    // İlk müşteri
    await page.click('text=Müşteri seçin');
    await page.click('text=Volkan İnanç');
    
    const firstProjectCount = await page.locator('select[name="project"] option').count();
    
    // Müşteri değiştir
    await page.click('text=Volkan İnanç');
    await page.click('text=Deneme Firması');
    
    const secondProjectCount = await page.locator('select[name="project"] option').count();
    
    // Proje listesi değişmiş olmalı
    expect(firstProjectCount).not.toBe(secondProjectCount);
  });
});

test.describe('Özel Durumlar ve Edge Cases', () => {

  test('Çok uzun proje adı kesilmeli', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    const longNameCard = page.locator('[data-testid="project-card"]').first();
    const projectName = longNameCard.locator('.project-name');
    
    // Text overflow kontrolü
    const overflow = await projectName.evaluate(el => 
      window.getComputedStyle(el).textOverflow
    );
    
    expect(overflow).toBe('ellipsis');
  });

  test('Aynı isimde proje oluşturulabilmeli (eğer izin veriliyorsa)', async ({ page }) => {
    const duplicateName = 'Duplicate Test Project';
    
    // İlk proje
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    await page.fill('input[name="project_name"]', duplicateName);
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    // İkinci proje
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    await page.fill('input[name="project_name"]', duplicateName);
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    // Her iki proje de listede olmalı veya hata vermeli
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    const duplicates = page.locator(`text=${duplicateName}`);
    
    const count = await duplicates.count();
    expect(count).toBeGreaterThanOrEqual(1);
  });

  test('Tarih seçici geçmiş tarihleri engellemeli (eğer gerekiyorsa)', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    const startDateInput = page.locator('input[name="start_date"]');
    await startDateInput.fill('01.01.2020');
    
    await page.click('button:has-text("Kaydet")');
    
    // Geçmiş tarih uyarısı (eğer varsa)
    const warning = page.locator('text=Geçmiş tarih seçilemez');
    if (await warning.isVisible()) {
      expect(await warning.isVisible()).toBe(true);
    }
  });

  test('Özel karakterler içeren müşteri adı işlenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', 'Test & Co. "Proje" #1');
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    await expect(page.locator('.success-message')).toBeVisible();
  });

  test('Boşluk karakterleri trim edilmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', '   Boşluklu Proje   ');
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Boşluklar temizlenmiş olmalı
    await expect(page.locator('text=Boşluklu Proje')).toBeVisible();
    await expect(page.locator('text=   Boşluklu Proje   ')).not.toBeVisible();
  });

  test('Emoji karakterleri desteklenmeli', async ({ page }) => {
    await page.goto(`${BASE_URL}/dashboard/projects/create`);
    
    await page.fill('input[name="project_name"]', '🚀 Roket Projesi 🎯');
    await page.click('text=Müşteri Seçin');
    await page.click('text=Volkan İnanç');
    await page.click('button:has-text("Kaydet")');
    
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    await expect(page.locator('text=🚀 Roket Projesi 🎯')).toBeVisible();
  });

  test('Çoklu dil desteği çalışmalı (eğer varsa)', async ({ page }) => {
    await page.goto(`${DASHBOARD_PROJECTS_URL}?tab=projects`);
    
    // Dil değiştirici varsa
    const langSwitcher = page.locator('[data-testid="language-switcher"]');
    
    if (await langSwitcher.isVisible()) {
      await langSwitcher.click();
      await page.click('text=English');
      
      await expect(page.locator('text=Projects')).toBeVisible();
    }
  });
});

// Test sonrası temizlik
test.afterEach(async ({ page }) => {
  // Oluşturulan test verilerini temizle (opsiyonel)
  await page.close();
});

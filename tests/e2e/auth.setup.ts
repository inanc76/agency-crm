
import { test as setup, expect } from '@playwright/test';

const authFile = 'playwright/.auth/user.json';

setup('authenticate', async ({ page }) => {
    // Perform authentication steps.
    await page.goto('http://localhost:8000/login');

    // Use robust selectors based on input names
    await page.fill('input[name="email"]', 'admin@mediaclick.com.tr');
    await page.fill('input[name="password"]', 'admin');

    // Click the login button
    await page.click('button[type="submit"]');

    // Wait until the page receives the cookies.
    await page.waitForURL('http://localhost:8000/dashboard');

    // Save storage state
    await page.context().storageState({ path: authFile });
});

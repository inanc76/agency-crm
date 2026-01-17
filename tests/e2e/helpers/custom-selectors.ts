/**
 * 🎯 CUSTOM SELECTOR HELPERS
 * Özel CSS sınıfları ve Livewire reactivity için yardımcı fonksiyonlar
 */

import { Page, Locator } from '@playwright/test';

/**
 * Livewire wire:model ile bağlı bir input'a değer gir
 * Readonly bypass ve Livewire event trigger içerir
 */
export async function fillLivewireInput(
    page: Page,
    selector: string,
    value: string,
    options: { waitForReactivity?: number } = {}
) {
    // İlk eşleşen elementi al (modal vs. ana form ayrımı için)
    const input = page.locator(selector).first();

    // Input'u bekle
    await input.waitFor({ state: 'visible', timeout: 5000 });

    // Readonly ise force fill kullan
    const isReadonly = await input.getAttribute('readonly');

    if (isReadonly !== null) {
        // JavaScript ile değer ata (readonly bypass)
        await input.evaluate((el: HTMLInputElement, val: string) => {
            el.value = val;
            // Livewire event'lerini tetikle
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }, value);
    } else {
        // Normal fill
        await input.fill(value);
    }

    // Livewire reactivity için bekle
    if (options.waitForReactivity) {
        await page.waitForTimeout(options.waitForReactivity);
    }
}

/**
 * Livewire wire:model ile bağlı bir select'e değer seç
 */
export async function selectLivewireOption(
    page: Page,
    selector: string,
    option: { index?: number; label?: string; value?: string },
    options: { waitForReactivity?: number } = {}
) {
    const select = page.locator(selector);

    // Select'i bekle
    await select.waitFor({ state: 'visible', timeout: 5000 });

    // Seçim yap
    if (option.index !== undefined) {
        await select.selectOption({ index: option.index });
    } else if (option.label) {
        await select.selectOption({ label: option.label });
    } else if (option.value) {
        await select.selectOption({ value: option.value });
    }

    // Livewire reactivity için bekle
    if (options.waitForReactivity) {
        await page.waitForTimeout(options.waitForReactivity);
    }
}

/**
 * Özel tema butonu tıkla (theme-btn-* sınıfları)
 */
export async function clickThemeButton(
    page: Page,
    buttonType: 'save' | 'cancel' | 'edit' | 'delete',
    options: { waitAfter?: number } = {}
) {
    // İlk eşleşen butonu al (modal vs. ana form ayrımı için)
    const button = page.locator(`.theme-btn-${buttonType}`).first();

    await button.waitFor({ state: 'visible', timeout: 5000 });
    await button.click();

    if (options.waitAfter) {
        await page.waitForTimeout(options.waitAfter);
    }
}

/**
 * Toggle checkbox (Livewire wire:model.live ile)
 */
export async function toggleLivewireCheckbox(
    page: Page,
    wireModel: string,
    targetState: boolean,
    options: { waitForReactivity?: number } = {}
) {
    const checkbox = page.locator(`input[wire\\:model\\.live="${wireModel}"]`);

    await checkbox.waitFor({ state: 'visible', timeout: 5000 });

    const currentState = await checkbox.isChecked();

    if (currentState !== targetState) {
        await checkbox.click();

        // Livewire reactivity için bekle
        if (options.waitForReactivity) {
            await page.waitForTimeout(options.waitForReactivity);
        }
    }
}

/**
 * Özel CSS animasyonlarının bitmesini bekle
 */
export async function waitForCustomAnimation(
    page: Page,
    selector: string,
    animationClass?: string
) {
    const element = page.locator(selector);

    // Element görünür olana kadar bekle
    await element.waitFor({ state: 'visible', timeout: 5000 });

    // Animasyon sınıfı varsa, animasyonun bitmesini bekle
    if (animationClass) {
        await page.waitForFunction(
            ({ sel, animClass }) => {
                const el = document.querySelector(sel);
                if (!el) return false;
                const style = window.getComputedStyle(el);
                return style.animationName === 'none' || !el.classList.contains(animClass);
            },
            { sel: selector, animClass: animationClass },
            { timeout: 3000 }
        );
    }

    // Ekstra güvenlik için kısa bir bekleme
    await page.waitForTimeout(200);
}

/**
 * Mary UI choices component için seçim yap
 */
export async function selectMaryChoice(
    page: Page,
    wireModel: string,
    searchText: string,
    options: { waitForReactivity?: number } = {}
) {
    // Choices container'ı bul
    const choicesContainer = page.locator(`[wire\\:model="${wireModel}"]`).locator('..');

    // Search input'u bul ve tıkla
    const searchInput = choicesContainer.locator('input[type="text"]');
    await searchInput.click();

    // Arama yap
    await searchInput.fill(searchText);
    await page.waitForTimeout(300);

    // İlk sonucu seç
    const firstOption = page.locator('.choices__list--dropdown .choices__item').first();
    await firstOption.click();

    if (options.waitForReactivity) {
        await page.waitForTimeout(options.waitForReactivity);
    }
}

/**
 * Toast mesajını bekle ve doğrula
 */
export async function waitForToast(
    page: Page,
    expectedText?: string,
    type: 'success' | 'error' | 'warning' | 'info' = 'success'
) {
    // Toast mesajını bekle - Birden fazla selector dene
    try {
        // Önce CSS class'ları dene
        const toast = page.locator('.toast-success, .success-message, [data-toast-type="success"]').first();
        await toast.waitFor({ state: 'visible', timeout: 3000 });
        return toast;
    } catch {
        // CSS bulamazsa text-based selector dene
        const textToast = page.locator('text=başarıyla, text=oluşturuldu, text=güncellendi').first();
        await textToast.waitFor({ state: 'visible', timeout: 7000 });

        if (expectedText) {
            await page.locator(`text=${expectedText}`).waitFor({ state: 'visible', timeout: 2000 });
        }

        return textToast;
    }
}

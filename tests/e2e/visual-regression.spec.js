import { expect, test } from '@playwright/test';
import { adminCredentials, signIn } from './helpers.js';

const viewports = [
    { name: 'mobile', width: 320, height: 740 },
    { name: 'desktop', width: 1440, height: 900 },
];

for (const viewport of viewports) {
    test(`customer admin screens match the ${viewport.name} visual baseline`, async ({ page, browserName }) => {
        test.skip(browserName !== 'chromium', 'Visual baselines are maintained in Chromium; responsive behavior is checked in all engines.');
        await page.setViewportSize({ width: viewport.width, height: viewport.height });
        await signIn(page, adminCredentials);
        await page.goto('/admin/customers');
        await expect(page.getByRole('heading', { name: 'Customer accounts', level: 1 })).toBeVisible();
        await expect(page).toHaveScreenshot(`customer-directory-${viewport.name}.png`, {
            fullPage: true,
            animations: 'disabled',
        });

        await page.getByRole('link', { name: 'View customer' }).first().click();
        await expect(page.getByRole('heading', { name: 'Aino Korhonen', level: 1 })).toBeVisible();
        await expect(page).toHaveScreenshot(`customer-details-${viewport.name}.png`, {
            fullPage: true,
            animations: 'disabled',
            mask: [page.getByText(/^Joined /)],
        });
    });
}

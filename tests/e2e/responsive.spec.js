import { expect, test } from '@playwright/test';
import { adminCredentials, customerCredentials, openBooking, signIn, bookingCode } from './helpers.js';

const viewports = [
    { name: 'small mobile', width: 320, height: 740 },
    { name: 'tablet', width: 768, height: 1024 },
    { name: 'desktop', width: 1440, height: 900 },
];

for (const viewport of viewports) {
    test(`customer and admin screens fit ${viewport.name} viewport`, async ({ browser, page }, testInfo) => {
        await page.setViewportSize({ width: viewport.width, height: viewport.height });

        await signIn(page, customerCredentials);
        await page.getByRole('heading', { name: 'Your bookings' }).waitFor();
        await expectNoHorizontalOverflow(page, 'customer dashboard');

        await openBooking(page, bookingCode(testInfo, 'CANCEL'));
        await expectNoHorizontalOverflow(page, 'customer booking details');

        const adminContext = await browser.newContext({ viewport: { width: viewport.width, height: viewport.height } });
        try {
            const adminPage = await adminContext.newPage();
            await signIn(adminPage, adminCredentials);
            await expect(adminPage.getByRole('complementary', { name: 'Admin navigation' })).toBeVisible();
            await expectNoHorizontalOverflow(adminPage, 'admin dashboard');

            for (const path of ['/admin/bookings', '/admin/locations', '/admin/reviews', '/admin/reports', '/admin/customers']) {
                await adminPage.goto(path);
                await expect(adminPage.locator('#main-content')).toBeVisible();
                await expectNoHorizontalOverflow(adminPage, path);
            }

            await adminPage.getByRole('link', { name: 'View customer' }).first().click();
            await expect(adminPage.getByRole('heading', { name: 'Aino Korhonen' })).toBeVisible();
            await expectNoHorizontalOverflow(adminPage, 'customer account details');
        } finally {
            await adminContext.close();
        }
    });
}

async function expectNoHorizontalOverflow(page, screen) {
    const hasOverflow = await page.evaluate(() =>
        document.documentElement.scrollWidth > document.documentElement.clientWidth
    );
    expect(hasOverflow, `Unexpected horizontal overflow on ${screen}`).toBe(false);
}

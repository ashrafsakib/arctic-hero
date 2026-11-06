import { expect, test } from '@playwright/test';
import { adminCredentials, bookingCode, customerCredentials, openBooking, signIn } from './helpers.js';

test('customer cancellation request is reviewed by an admin and persists', async ({ browser, page }, testInfo) => {
    const code = bookingCode(testInfo, 'CANCEL');
    await signIn(page, customerCredentials);
    await openBooking(page, code);

    await page.getByLabel('Reason (optional)').fill('Our plans changed; please cancel this ride.');
    await page.getByRole('button', { name: 'Send cancellation request' }).click();
    await expect(page.getByText('Your cancellation request has been sent for review.')).toBeVisible();
    await expect(page.getByText('Your cancellation request is waiting for review.')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Send cancellation request' })).toHaveCount(0);
    await page.reload();
    await expect(page.getByText('Your cancellation request is waiting for review.')).toBeVisible();

    const adminContext = await browser.newContext();
    try {
        const adminPage = await adminContext.newPage();
        await signIn(adminPage, adminCredentials);
        await adminPage.goto(`/admin/bookings?search=${encodeURIComponent(code)}`);
        await adminPage.getByRole('link', { name: code }).click();
        await adminPage.getByLabel('Status').selectOption('cancelled');
        await adminPage.getByLabel('Admin note').fill('Cancellation approved during E2E verification.');
        await adminPage.getByRole('button', { name: 'Save update' }).click();
        await expect(adminPage.getByRole('status')).toContainText('Booking status updated.');
        await expect(adminPage.getByLabel('Status')).toHaveValue('cancelled');

        await page.reload();
        await expect(page.locator('.dashboard-status[data-status="cancelled"]')).toHaveText('Cancelled');
        await expect(page.getByText('Cancellation approved during E2E verification.')).toHaveCount(0);
    } finally {
        await adminContext.close();
    }
});

test('guests are redirected to sign in before accessing a booking action', async ({ page }) => {
    await page.goto('/my/bookings/1');
    await expect(page).toHaveURL(/\/login/);
    await expect(page.getByRole('button', { name: 'Log in' })).toBeVisible();
});

import { expect, test } from '@playwright/test';
import { adminCredentials, bookingCode, customerCredentials, openBooking, signIn } from './helpers.js';

test('customer submits a completed-ride review and an admin response is visible', async ({ browser, page }, testInfo) => {
    const code = bookingCode(testInfo, 'REVIEW');
    const comment = `Excellent ride from ${testInfo.project.name}.`;
    const response = 'Thank you for sharing your feedback.';

    await signIn(page, customerCredentials);
    await openBooking(page, code);
    await page.getByLabel('Rating').selectOption('5');
    await page.getByLabel('Comment (optional)').fill(comment);
    await page.getByRole('button', { name: 'Submit review' }).click();
    await expect(page.getByRole('status')).toContainText('Thank you for sharing your feedback.');
    await expect(page.getByRole('heading', { name: 'Your review' })).toBeVisible();
    await expect(page.getByText(comment)).toBeVisible();
    await expect(page.getByRole('button', { name: 'Submit review' })).toHaveCount(0);
    await page.reload();
    await expect(page.getByText(comment)).toBeVisible();

    const adminContext = await browser.newContext();
    try {
        const adminPage = await adminContext.newPage();
        await signIn(adminPage, adminCredentials);
        await adminPage.goto('/admin/reviews');
        const review = adminPage.getByRole('article').filter({ hasText: code });
        await expect(review).toBeVisible();
        await review.getByLabel('Visibility').selectOption('hidden');
        await review.getByLabel('Response to customer').fill(response);
        await review.getByRole('button', { name: 'Save review' }).click();
        await expect(adminPage.getByRole('status')).toContainText('Review moderation updated.');

        await page.reload();
        await expect(page.getByText(comment)).toBeVisible();
        await expect(page.getByText(response)).toBeVisible();
    } finally {
        await adminContext.close();
    }
});

import { expect, test } from '@playwright/test';
import { adminCredentials, signIn } from './helpers.js';

test('admin can find a customer, review the account and booking history, then manage access', async ({ page }) => {
    await signIn(page, adminCredentials);
    await page.goto('/admin/customers');

    await expect(page.getByRole('heading', { name: 'Customer accounts', level: 1 })).toBeVisible();
    await expect(page.getByRole('status')).toContainText('matching customers');
    await page.getByRole('searchbox', { name: 'Search name, email, or phone' }).fill('aino.korhonen@example.com');
    await page.getByRole('button', { name: 'Search' }).click();
    await expect(page.getByRole('link', { name: 'View customer' })).toBeVisible();
    await expect(page.getByText('Other Customer')).toHaveCount(0);

    await page.getByRole('link', { name: 'View customer' }).click();
    await expect(page.getByRole('heading', { name: 'Aino Korhonen' })).toBeVisible();
    await expect(page.getByText('No bookings for this customer.')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Deactivate account' })).toBeVisible();

    await page.getByRole('button', { name: 'Deactivate account' }).click();
    await expect(page.getByRole('status')).toContainText('Customer account status updated.');
    await expect(page.getByRole('button', { name: 'Reactivate account' })).toBeVisible();

    await page.getByRole('button', { name: 'Reactivate account' }).click();
    await expect(page.getByRole('status')).toContainText('Customer account status updated.');
    await expect(page.getByRole('button', { name: 'Deactivate account' })).toBeVisible();
});

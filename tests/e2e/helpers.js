import { expect } from '@playwright/test';

export const customerCredentials = {
    email: 'e2e-customer@example.test',
    password: 'password',
};

export const adminCredentials = {
    email: 'admin@example.com',
    password: 'ChangeMe123!',
};

export async function signIn(page, credentials) {
    await page.goto('/login');
    await page.getByLabel('Email').fill(credentials.email);
    await page.getByLabel('Password', { exact: true }).fill(credentials.password);
    await page.getByRole('button', { name: 'Log in' }).click();
    await expect(page).toHaveURL(/\/(dashboard|admin)(?:\/|$)/);
}

export function bookingCode(testInfo, scenario) {
    return `AH-E2E-${scenario}-${testInfo.project.name.toUpperCase()}`;
}

export async function openBooking(page, code) {
    await page.getByRole('link', { name: new RegExp(`View details · ${code}`) }).click();
    await expect(page.getByRole('heading', { name: 'Ride details' })).toBeVisible();
}

import { defineConfig, devices } from '@playwright/test';
import { randomUUID } from 'node:crypto';

const viewportTests = '**/responsive.spec.js';
const visualTests = '**/visual-regression.spec.js';
const journeyTests = ['**/cancellation-flow.spec.js', '**/customer-management.spec.js', '**/review-flow.spec.js'];
const runId = randomUUID();
process.env.ARCTIC_HERO_E2E_RUN_ID = runId;
const browsers = [
    { name: 'chromium', device: 'Desktop Chrome' },
    { name: 'firefox', device: 'Desktop Firefox' },
    { name: 'webkit', device: 'Desktop Safari' },
];

export default defineConfig({
    testDir: '.',
    testMatch: [viewportTests, visualTests, ...journeyTests],
    globalTeardown: './cleanup.cjs',
    fullyParallel: false,
    workers: 1,
    retries: 0,
    reporter: [['list'], ['html', { outputFolder: '../../playwright-report', open: 'never' }]],
    use: {
        baseURL: 'http://127.0.0.1:4173',
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
    },
    projects: browsers.map(({ name, device }) => ({
        name,
        testMatch: [viewportTests, visualTests, ...journeyTests],
        use: { ...devices[device] },
    })),
    webServer: {
        command: 'node start-server.cjs',
        url: 'http://127.0.0.1:4173/login',
        timeout: 120_000,
        reuseExistingServer: false,
        env: { ARCTIC_HERO_E2E_RUN_ID: runId },
    },
});

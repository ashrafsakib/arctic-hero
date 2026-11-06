const { existsSync, rmSync } = require('node:fs');
const { tmpdir } = require('node:os');
const { join } = require('node:path');

module.exports = async function globalTeardown() {
    const runId = process.env.ARCTIC_HERO_E2E_RUN_ID;
    if (!runId) throw new Error('Playwright did not provide an E2E run identifier for cleanup.');

    const database = join(tmpdir(), `arctic-hero-e2e-${runId}.sqlite`);
    for (const suffix of ['', '-shm', '-wal']) {
        const path = `${database}${suffix}`;
        if (existsSync(path)) rmSync(path, { force: true });
    }

    rmSync(join(tmpdir(), `arctic-hero-e2e-sessions-${runId}`), { recursive: true, force: true });
};

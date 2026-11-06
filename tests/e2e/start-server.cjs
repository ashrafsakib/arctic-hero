const { spawn, spawnSync } = require('node:child_process');
const { randomBytes } = require('node:crypto');
const { existsSync, mkdirSync, rmSync, writeFileSync } = require('node:fs');
const { tmpdir } = require('node:os');
const { join } = require('node:path');

const root = join(__dirname, '..', '..');
const runId = process.env.ARCTIC_HERO_E2E_RUN_ID;
if (!runId) throw new Error('Playwright did not provide an E2E run identifier.');

const database = join(tmpdir(), `arctic-hero-e2e-${runId}.sqlite`);
const sessionDirectory = join(tmpdir(), `arctic-hero-e2e-sessions-${runId}`);
const env = {
    ...process.env,
    APP_ENV: 'testing',
    APP_KEY: `base64:${randomBytes(32).toString('base64')}`,
    CACHE_STORE: 'array',
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: database,
    DB_URL: '',
    MAIL_MAILER: 'array',
    QUEUE_CONNECTION: 'sync',
    SESSION_DRIVER: 'file',
    SESSION_FILES: sessionDirectory,
};

function run(command, args) {
    const result = spawnSync(command, args, { cwd: root, env, stdio: 'inherit' });
    if (result.error) throw result.error;
    if (result.status !== 0) {
        throw new Error(`${command} ${args.join(' ')} exited with ${result.status}`);
    }
}

writeFileSync(database, '');
mkdirSync(sessionDirectory, { recursive: true });

try {
    run('php', ['artisan', 'migrate:fresh', '--force', '--seed']);
    run('php', ['tests/e2e/seed.php']);
} catch (error) {
    console.error(error);
    rmSync(database, { force: true });
    rmSync(sessionDirectory, { recursive: true, force: true });
    process.exit(1);
}

const server = spawn('php', ['artisan', 'serve', '--host=127.0.0.1', '--port=4173'], {
    cwd: root,
    env,
    stdio: 'inherit',
});

function cleanup() {
    for (const suffix of ['', '-shm', '-wal']) {
        const path = `${database}${suffix}`;
        if (existsSync(path)) rmSync(path, { force: true });
    }
    rmSync(sessionDirectory, { recursive: true, force: true });
}

server.on('error', (error) => {
    console.error(error);
    cleanup();
    process.exitCode = 1;
});
server.on('exit', (code) => {
    cleanup();
    process.exitCode = code ?? 1;
});

for (const signal of ['SIGINT', 'SIGTERM']) {
    process.on(signal, () => server.kill(signal));
}

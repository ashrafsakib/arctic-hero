# Operations and Deployment

## Production prerequisites

- PHP 8.2 or newer with the PDO MySQL extension
- Composer, Node.js, and npm
- MySQL 8 with a dedicated application database/user
- HTTPS termination and a mail provider with valid sender credentials

Do not deploy the local demo administrator credentials. Create a unique administrator password and keep database, mail, and routing credentials in the deployment secret store, not in source control.

## First deployment

1. Install PHP and Node dependencies and generate production assets:

   ```bash
   composer install --no-dev --prefer-dist --optimize-autoloader
   npm ci
   npm run build
   ```

2. Set `APP_ENV=production`, `APP_DEBUG=false`, the public `APP_URL`, a generated `APP_KEY`, and production MySQL connection values. Configure `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME`.
3. Optionally set `ROUTING_PROVIDER_URL` to an OSRM-compatible routing service. Leave it empty to use the local distance/time estimate. Do not expose provider credentials in this URL or client-side code.
4. Run schema changes and create the public storage link:

   ```bash
   php artisan migrate --force
   php artisan storage:link
   ```

5. Create or securely provision the production admin. Do not run demo seeders in production unless their credentials have been explicitly replaced.
6. Cache production configuration and routes, then restart PHP workers:

   ```bash
   php artisan optimize
   ```

Before deploying migrations, take a database backup and verify that the release's `database/migrations` set is included. Do not use `migrate:fresh` against production.

## Release and rollback

1. Back up the database and current release.
2. Deploy the application release and production environment settings.
3. Run `php artisan migrate --force`.
4. Run `npm run build` as part of the release pipeline and `php artisan optimize`.
5. Smoke-check `/up`, login, customer booking, the admin dashboard, and a public locations/routes page.
6. If application code needs rollback, redeploy the previous compatible release. Roll back database migrations only after assessing data loss and migration dependencies; prefer a forward corrective migration for schema changes containing user data.

## Access and account operations

- Admin routes require an authenticated account with the admin role and active status.
- The customer directory searches customer name, email, and phone. It does not expose admin accounts or provide role editing.
- Marking a customer inactive blocks new logins and ends access on their next authenticated request. Reactivating the account restores login eligibility.
- Keep at least one active administrator available. Admin accounts must be managed through an authorized provisioning process.

## Mail and notification monitoring

Booking and contact notifications are synchronous. A mail failure does not roll back a saved booking or contact submission:

- A failed booking notification is reported in the confirmation flow and logged. Verify the customer booking and check the Laravel log before arranging confirmation manually.
- Contact submissions are retained in the admin inbox even when mail delivery fails. Their email status is marked `failed`; use the recorded submission and contact details to follow up.
- Check `storage/logs/laravel.log` for delivery warnings and the underlying transport exception. Verify sender address, provider credentials, network access, and provider logs.
- Use a local mail catcher in development. Do not use real customer addresses for test messages.

Notifications are currently synchronous. If they are changed to queued notifications, configure and monitor a production queue backend and worker, and verify retries/failure handling before rollout.

## Routing and maps

- An unavailable, timed-out, or invalid OSRM-compatible response falls back to the local route estimate; check the application log if this occurs unexpectedly.
- Booking coordinates and managed locations are restricted to the configured Oulu service bounds. Check address and pin data when a location cannot be saved.
- Admin pin forms and public maps load Leaflet/OpenStreetMap assets; allow outbound access to the configured tile host and follow the tile provider's usage policy.

## Routine checks and troubleshooting

| Symptom | Checks |
|---|---|
| `/up` or pages return 500 | Check PHP/web-server logs, `storage/logs/laravel.log`, DB connectivity, file permissions on `storage` and `bootstrap/cache`, then run `php artisan optimize:clear` and rebuild caches. |
| Login is rejected | Confirm the account exists, is active, and has the expected password; check rate limiting and mail configuration if password reset is needed. |
| Admin page returns 403 | Confirm the signed-in user has the admin role and active status. Customer accounts cannot access admin routes. |
| Booking is saved but no email arrives | Check the confirmation warning, mail transport variables, provider/network logs, and Laravel logs; the booking remains persisted for manual follow-up. |
| Contact email is missing | Check the admin contact inbox and `email_status`; failed submissions remain available there for follow-up. |
| Route estimate looks unusual | Check `ROUTING_PROVIDER_URL` response format/availability and application logs; a fallback estimate may have been used. |
| Map is blank | Verify Leaflet asset loading, browser console/network errors, tile host access, and saved location coordinates. |

After an incident, capture the timestamp, affected booking/contact reference, relevant sanitized log context, and recovery action. Do not copy secrets, session tokens, or full customer records into issue reports.

## Validation commands

Run the application checks before release:

```bash
php artisan test
vendor/bin/pint --test
npm run build
php artisan route:list
```

In PowerShell, use `vendor\bin\pint --test`. Feature tests use the configured test database and do not send real mail.

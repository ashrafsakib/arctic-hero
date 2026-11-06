# Arctic Hero

Arctic Hero is a Laravel 12 web application for a company-operated taxi booking workflow in Oulu, Finland. Customers can begin a booking as guests; the company receives a pending request and manually contacts and confirms it.

## Company

- Name: Arctic Hero
- Phone: +358417296611
- Email: ashrafulsakib740@gmail.com
- Company number: 3481963-9
- VAT number: FI34819639
- Website: not configured

## Current Scope

- Laravel 12, PHP 8.2+, Blade, Tailwind CSS, Alpine.js, and Breeze authentication
- Customer and admin roles with active-status authorization
- Database-backed vehicle types, vehicles, pricing rules, bookings, status history, reviews, locations, and notifications
- Fare snapshots and payment placeholders on bookings
- Realistic Oulu seed data
- No driver portal, automatic assignment, live tracking, online payments, or AI features
- Admin workbenches for bookings, vehicles, customers, pricing, locations/map pins, reviews, company settings, reports, and contact submissions
- Customer cancellation requests, completed-trip reviews, and account status enforcement

## Requirements

- PHP 8.2 or newer with `pdo_mysql` for MySQL
- Composer
- Node.js and npm
- MySQL 8 for normal development and production

## Installation

```bash
composer install
copy .env.example .env
php artisan key:generate
npm install
npm run build
```

Configure the database in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taxi_booking
DB_USERNAME=root
DB_PASSWORD=
```

Create the database, then run:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

The seeder creates a local demo admin (`admin@example.com` / `ChangeMe123!`). Only use this account locally; create a unique production administrator and change or remove demo credentials before exposing the application.

## Useful Commands

```bash
php artisan migrate:fresh --seed
php artisan test
npm run dev
php artisan route:list
```

The checked-in local `.env` uses SQLite so the project can be started without a MySQL server. Use MySQL through `.env` for the intended deployment configuration.

## Architecture

- `app/Models`: Eloquent entities and relationships
- `app/Http/Controllers`: thin HTTP controllers, with admin controllers under `Admin`
- `app/Http/Requests`: request validation
- `app/Services`: fare, route-estimation, notification, and company-settings workflows
- `app/Http/Middleware`: admin and active-account access checks
- `database/migrations`: schema and indexes
- `database/seeders`: Oulu-oriented demo data
- `resources/views`: Blade pages and Breeze layouts

The booking flow creates pending requests and records status changes in `booking_status_histories`. Admins manage the booking lifecycle, customer access, active pricing rules, location pins, review responses, and company configuration. See the operations guide for production rollout and troubleshooting.

## Database Design

- `users`: customer/admin identity, phone, role, and active status
- `vehicle_types`: configurable capacity and fare rates
- `vehicles`: company-owned vehicles with no driver relationship
- `bookings`: journey data and immutable estimated-fare breakdown
- `booking_status_histories`: auditable status changes and notes
- `reviews`: one review per completed booking via a unique booking constraint
- `locations`: public Oulu tourist and transport locations
- `pricing_rules`: extensible extra-charge rules
- `notifications`: Laravel database notifications

See [project-planning/task-breakdown.md](project-planning/task-breakdown.md) for the Git-reconciled task breakdown and per-member 200-hour plan. The completion-hour allocation there is an estimate; use individual time logs for actual hours.

## Maps and Routing

The public route explorer uses Leaflet and OpenStreetMap with active location records. Booking fare estimates use the optional OSRM-compatible route provider when configured; if it is unavailable or returns invalid data, the application records a warning and uses its local estimate.

## Production Notes

- Set `APP_ENV=production`, `APP_DEBUG=false`, a strong `APP_KEY`, and secure session/cookie settings.
- Use MySQL 8 and configure a real mail transport. Booking and contact mail are sent synchronously; configure a queue worker if queued work is added.
- Validate and store uploaded images through Laravel’s public/private filesystem configuration.
- Run `php artisan optimize` during deployment and never use demo credentials in production.

See [docs/operations.md](docs/operations.md) for environment setup, release steps, backups, account management, mail monitoring, and common troubleshooting.

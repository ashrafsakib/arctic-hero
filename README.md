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

Phase 1 and Phase 2 are implemented:

- Laravel 12, PHP 8.3+, Blade, Tailwind CSS, Alpine.js, and Breeze authentication
- Customer and admin roles with active-status authorization
- Database-backed vehicle types, vehicles, pricing rules, bookings, status history, reviews, locations, and notifications
- Fare snapshots and payment placeholders on bookings
- Realistic Oulu seed data
- No driver portal, automatic assignment, live tracking, online payments, or AI features

## Requirements

- PHP 8.3 or newer with `pdo_mysql` for MySQL
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

The local demo admin is `admin@example.com` with password `ChangeMe123!`. Change this password immediately outside local development.

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
- `app/Services`: business workflows such as fare, routing, and booking services (next implementation phase)
- `app/Policies`: resource authorization (next implementation phase)
- `database/migrations`: schema and indexes
- `database/seeders`: Oulu-oriented demo data
- `resources/views`: Blade pages and Breeze layouts

The core booking lifecycle is `pending -> contacted -> confirmed -> completed`. Rejection and cancellation are controlled terminal paths. Every transition will be written to `booking_status_histories` by `BookingService` in the booking-engine phase.

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

## Planned Next Phase

The next increment is the guest multi-step booking flow: session preservation through login/registration, modular route calculation with a fallback, `FareCalculationService`, `BookingService`, status-transition validation, and feature tests for those workflows.

## Maps and Routing

The planned map layer uses Leaflet and OpenStreetMap. Geocoding/routing providers will be isolated behind `RouteService` and configured through environment variables, with a deterministic fallback for development when external services are unavailable.

## Production Notes

- Set `APP_ENV=production`, `APP_DEBUG=false`, a strong `APP_KEY`, and secure session/cookie settings.
- Use MySQL 8, a queue worker, scheduled tasks, and a real mail transport.
- Validate and store uploaded images through Laravel’s public/private filesystem configuration.
- Run `php artisan optimize` during deployment and never use demo credentials in production.

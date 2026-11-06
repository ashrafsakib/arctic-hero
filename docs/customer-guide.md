# Customer Guide and Local Demo

## Booking a ride

1. Open the home page and choose **Book a ride**.
2. Set the pickup and destination by choosing a known Oulu place or selecting points on the map. Enter the pickup date and time, passenger count, luggage count, and any optional note.
3. Continue to the vehicle step, choose a vehicle that fits the passengers and luggage, and review the estimated fare.
4. Sign in or create a customer account when prompted, then submit the booking request.
5. Check the confirmation page and your dashboard for the booking code and current status.

The fare is an estimate, and a submitted booking starts as **Pending**. Arctic Hero staff contact the customer to confirm the ride; submitting a request does not mean a driver or payment has been assigned.

## Managing your account and rides

- Sign in to view bookings, ride status, and account details on the dashboard.
- Open a booking to review its route, schedule, estimate, and status history.
- For a pending, contacted, or confirmed ride, submit a cancellation request from its details page. Staff review the request; it is not an immediate cancellation.
- After a ride is marked completed, submit one review for that booking. An administrator response appears with the review.
- Use **Manage your profile** to update profile information or password. Contact Arctic Hero using the phone or email shown on the site if you need help.

## Local demonstration setup

Use a local development environment only. The following commands recreate the local database and its sample data; **do not run `migrate:fresh` against a shared or production database**:

```powershell
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

In another terminal, run `npm run dev` instead of `npm run build` if you want Vite's development server while presenting the site.

The local seeder provides these demo accounts:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `ChangeMe123!` |
| Customer | `aino.korhonen@example.com` | `password` |

These are predictable local-only credentials. Never use them on a public deployment; provision unique accounts and passwords instead. Demo emails use the configured mail transport, so use a local mail catcher or explain that email delivery is not part of the walkthrough.

## Suggested five-minute demo

1. As a customer, sign in and review the dashboard, then open the fleet, routes, and booking form.
2. Submit a booking for an Oulu pickup and destination, selecting a date, passengers, luggage, and a suitable vehicle. Point out that the fare is an estimate and the request needs staff confirmation.
3. Log out, sign in with the local admin account, and open the customer directory. Search for the customer by name, email, or phone; open the profile and inspect the booking history.
4. Show the account-access control. Deactivate the sample customer, then reactivate it so the demo account remains usable. Inactive customers cannot sign in.
5. Open the booking from the customer history and show its status and staff workflow. If demonstrating cancellation or reviews, change the booking to a supported state using the admin booking screen first: cancellation requests are available before completion, and reviews are available after completion.

The application does not provide online payments, driver assignment, or live vehicle tracking; avoid presenting those as available features.

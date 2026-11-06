<?php

use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$customer = User::factory()->create([
    'name' => 'E2E Customer',
    'email' => 'e2e-customer@example.test',
]);
$vehicleType = VehicleType::query()->create([
    'name' => 'E2E Standard Taxi',
    'passenger_capacity' => 4,
    'luggage_capacity' => 2,
    'base_fare' => 10,
    'per_km_rate' => 1.5,
    'per_minute_rate' => 0.2,
    'status' => 'active',
]);

foreach (['chromium', 'firefox', 'webkit'] as $browser) {
    foreach ([
        ['code' => 'CANCEL', 'status' => Booking::CONFIRMED],
        ['code' => 'REVIEW', 'status' => Booking::COMPLETED],
    ] as $scenario) {
        $booking = Booking::query()->create([
            'booking_code' => "AH-E2E-{$scenario['code']}-".strtoupper($browser),
            'customer_id' => $customer->id,
            'pickup_address' => 'Oulu Market Square',
            'pickup_lat' => 65.0141,
            'pickup_lng' => 25.4698,
            'destination_address' => 'Oulu Cathedral',
            'destination_lat' => 65.0124,
            'destination_lng' => 25.4682,
            'booking_date' => today()->addDays(2)->toDateString(),
            'booking_time' => '10:30:00',
            'passengers' => 2,
            'luggage_count' => 1,
            'vehicle_type_id' => $vehicleType->id,
            'distance_km' => 2.3,
            'estimated_duration_minutes' => 8,
            'base_fare' => 10,
            'distance_fare' => 3.45,
            'time_fare' => 1.6,
            'extra_charge' => 0,
            'total_fare' => 15.05,
            'status' => $scenario['status'],
        ]);

        $booking->statusHistories()->create([
            'status' => $scenario['status'],
            'changed_by' => $customer->id,
            'notes' => 'E2E test booking created.',
            'created_at' => now(),
        ]);
    }
}

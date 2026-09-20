<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VehicleType;
use App\Models\Vehicle;
use App\Models\Location;
use App\Models\PricingRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Arctic Hero Admin',
            'email' => 'admin@example.com',
            'phone' => '+358 40 123 4567',
            'password' => Hash::make('ChangeMe123!'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Aino Korhonen',
            'email' => 'aino.korhonen@example.com',
            'phone' => '+358 44 555 1200',
        ]);

        $standard = VehicleType::create(['name' => 'Standard Taxi', 'description' => 'Comfortable taxi for everyday journeys around Oulu.', 'passenger_capacity' => 4, 'luggage_capacity' => 2, 'base_fare' => 10, 'per_km_rate' => 1.50, 'per_minute_rate' => 0.20]);
        $premium = VehicleType::create(['name' => 'Premium Taxi', 'description' => 'A spacious and quiet vehicle for business and airport journeys.', 'passenger_capacity' => 4, 'luggage_capacity' => 3, 'base_fare' => 14, 'per_km_rate' => 1.85, 'per_minute_rate' => 0.25]);
        $minivan = VehicleType::create(['name' => 'Minivan', 'description' => 'Practical group transportation for up to seven passengers.', 'passenger_capacity' => 7, 'luggage_capacity' => 5, 'base_fare' => 16, 'per_km_rate' => 2.10, 'per_minute_rate' => 0.30]);

        Vehicle::insert([
            ['vehicle_type_id' => $standard->id, 'name' => 'Aurora 01', 'brand' => 'Toyota', 'model' => 'Corolla', 'registration_number' => 'OUX-101', 'color' => 'White', 'year' => 2023, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type_id' => $premium->id, 'name' => 'Aurora 02', 'brand' => 'Mercedes-Benz', 'model' => 'E-Class', 'registration_number' => 'OUX-202', 'color' => 'Black', 'year' => 2024, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type_id' => $minivan->id, 'name' => 'Aurora 03', 'brand' => 'Volkswagen', 'model' => 'Caravelle', 'registration_number' => 'OUX-303', 'color' => 'Silver', 'year' => 2022, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        PricingRule::create(['name' => 'Standard daytime pricing', 'description' => 'Default daytime fare profile for active vehicle types.', 'status' => 'active']);

        Location::insert([
            ['name' => 'Oulu Cathedral', 'description' => 'A historic cathedral and landmark in the heart of Oulu.', 'address' => 'Kirkkokatu 3 A, Oulu', 'latitude' => 65.0124, 'longitude' => 25.4682, 'category' => 'tourist_attraction', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nallikari Beach', 'description' => 'Oulu\'s popular seaside destination with views over the Gulf of Bothnia.', 'address' => 'Nallikarinranta, Oulu', 'latitude' => 65.0173, 'longitude' => 25.4352, 'category' => 'tourist_attraction', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oulu Airport', 'description' => 'The main airport serving the Oulu region.', 'address' => 'Lentokentäntie 720, Oulunsalo', 'latitude' => 64.9301, 'longitude' => 25.3546, 'category' => 'airport', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oulu Market Square', 'description' => 'A lively waterfront square beside the market hall and City Theatre.', 'address' => 'Kauppatori, Oulu', 'latitude' => 65.0141, 'longitude' => 25.4698, 'category' => 'tourist_attraction', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

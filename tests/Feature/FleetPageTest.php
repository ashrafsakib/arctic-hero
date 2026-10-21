<?php

namespace Tests\Feature;

use App\Models\VehicleType;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FleetPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_fleet_page_lists_active_vehicles_with_details_and_booking_links(): void
    {
        Storage::fake('public');
        $vehicleType = $this->createVehicleType('Standard Taxi', 'active');
        $image = UploadedFile::fake()->image('aurora.jpg')->store('fleet', 'public');
        Vehicle::query()->create([
            'vehicle_type_id' => $vehicleType->id,
            'name' => 'Aurora 05',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'registration_number' => 'OUX-105',
            'status' => 'active',
            'image' => $image,
        ]);

        $inactiveType = $this->createVehicleType('Retired Taxi', 'inactive');
        Vehicle::query()->create([
            'vehicle_type_id' => $inactiveType->id,
            'name' => 'Retired Car',
            'registration_number' => 'OUX-106',
            'status' => 'active',
        ]);

        $this->get(route('fleet.index'))
            ->assertOk()
            ->assertSeeText('Aurora 05')
            ->assertSeeText('Toyota Corolla')
            ->assertSeeText('Up to 4')
            ->assertSeeText('€10.00')
            ->assertDontSeeText('Retired Car')
            ->assertSee(route('booking.create', ['vehicle_type_id' => $vehicleType->id]), false)
            ->assertSee(asset('storage/'.$image), false);
    }

    private function createVehicleType(string $name, string $status): VehicleType
    {
        return VehicleType::query()->create([
            'name' => $name,
            'description' => 'Comfortable vehicle for city trips.',
            'passenger_capacity' => 4,
            'luggage_capacity' => 2,
            'base_fare' => 10,
            'per_km_rate' => 1.5,
            'per_minute_rate' => 0.2,
            'status' => $status,
        ]);
    }
}
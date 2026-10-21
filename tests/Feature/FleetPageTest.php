<?php

namespace Tests\Feature;

use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FleetPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_fleet_page_lists_active_vehicles_with_details_and_booking_links(): void
    {
        $activeVehicle = $this->createVehicleType('Comfort Car', 'active');
        $this->createVehicleType('Retired Car', 'inactive');

        $this->get(route('fleet.index'))
            ->assertOk()
            ->assertSeeText('Comfort Car')
            ->assertSeeText('Up to 4')
            ->assertSeeText('€10.00')
            ->assertDontSeeText('Retired Car')
            ->assertSee(route('booking.create', ['vehicle_type_id' => $activeVehicle->id]), false);
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
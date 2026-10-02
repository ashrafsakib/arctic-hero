<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_only_the_signed_in_customers_upcoming_rides(): void
    {
        $customer = User::factory()->create(['name' => 'Aino Korhonen']);
        $otherCustomer = User::factory()->create(['name' => 'Mika Example']);
        $vehicleType = VehicleType::create([
            'name' => 'Standard Taxi',
            'description' => 'Everyday trips around Oulu.',
            'passenger_capacity' => 4,
            'luggage_capacity' => 2,
            'base_fare' => 10,
            'per_km_rate' => 1.5,
            'per_minute_rate' => 0.2,
            'status' => 'active',
        ]);

        $this->createBooking($customer, $vehicleType, 'OWN-1001', 'Oulu Airport', 'Oulu Market Square');
        $this->createBooking($otherCustomer, $vehicleType, 'OTHER-1001', 'Private customer pickup', 'Private destination');

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Welcome back, Aino')
            ->assertSeeText('Oulu Airport')
            ->assertSeeText('Oulu Market Square')
            ->assertSeeText('Standard Taxi')
            ->assertDontSeeText('Private customer pickup')
            ->assertDontSeeText('Private destination');
    }

    public function test_dashboard_shows_a_booking_prompt_when_there_are_no_upcoming_rides(): void
    {
        $customer = User::factory()->create(['name' => 'Aino Korhonen']);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('No upcoming rides')
            ->assertSeeText('Plan your first ride');
    }

    private function createBooking(User $customer, VehicleType $vehicleType, string $code, string $pickup, string $destination): void
    {
        Booking::create([
            'booking_code' => $code,
            'customer_id' => $customer->id,
            'pickup_address' => $pickup,
            'destination_address' => $destination,
            'booking_date' => today()->addDay()->toDateString(),
            'booking_time' => '10:30:00',
            'passengers' => 2,
            'luggage_count' => 1,
            'vehicle_type_id' => $vehicleType->id,
            'base_fare' => 10,
            'distance_fare' => 0,
            'time_fare' => 0,
            'extra_charge' => 0,
            'total_fare' => 10,
            'status' => Booking::CONFIRMED,
        ]);
    }
}
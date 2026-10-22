<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_a_booking_and_see_the_saved_confirmation(): void
    {
        $customer = User::factory()->create();
        $vehicleType = $this->createVehicleType();

        $response = $this->actingAs($customer)->post(route('booking.store'), $this->bookingData($vehicleType));
        $response->assertValid();
        $response->assertRedirect(route('booking.complete'));

        $bookingResponse = $this->get(route('booking.complete'));
        $booking = Booking::query()->firstOrFail();

        $bookingResponse->assertRedirect(route('booking.confirmation', $booking));
        $this->assertSame($customer->id, $booking->customer_id);
        $this->assertSame(Booking::PENDING, $booking->status);
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'status' => Booking::PENDING,
            'changed_by' => $customer->id,
        ]);
        $this->assertSame(10.0, (float) $booking->base_fare);
        $this->assertGreaterThan(0, (float) $booking->distance_km);
        $this->assertEqualsWithDelta(
            (float) $booking->base_fare + (float) $booking->distance_fare + (float) $booking->time_fare,
            (float) $booking->total_fare,
            0.01,
        );

        $this->get(route('booking.confirmation', $booking))
            ->assertOk()
            ->assertSeeText($booking->booking_code)
            ->assertSeeText('Oulu Market Square')
            ->assertSeeText('Standard Taxi')
            ->assertSee(route('bookings.show', $booking), false);

        $this->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Booking status')
            ->assertSeeText('Pending')
            ->assertSeeText($booking->booking_code);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText($booking->booking_code)
            ->assertSee('href="'.route('bookings.show', $booking).'"', false);
    }

    public function test_guest_booking_is_preserved_until_registration_and_confirmation(): void
    {
        $vehicleType = $this->createVehicleType();

        $this->post(route('booking.store'), $this->bookingData($vehicleType))
            ->assertValid()
            ->assertRedirect(route('booking.complete'));

        $this->get(route('booking.complete'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('booking_draft');

        $this->assertDatabaseCount('bookings', 0);

        $this->post(route('register'), [
            'name' => 'Aino Korhonen',
            'phone' => '+358 40 123 4567',
            'email' => 'aino@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect(route('booking.complete'));

        $completionResponse = $this->get(route('booking.complete'));
        $booking = Booking::query()->firstOrFail();
        $completionResponse->assertRedirect(route('booking.confirmation', $booking));

        $this->assertSame('Aino Korhonen', $booking->customer->name);
        $this->assertSame('Oulu Cathedral', $booking->destination_address);
        $this->get(route('booking.confirmation', $booking))
            ->assertOk()
            ->assertSeeText($booking->booking_code);
    }

    public function test_booking_rejects_a_vehicle_without_enough_passenger_capacity(): void
    {
        $customer = User::factory()->create();
        $vehicleType = $this->createVehicleType();

        $this->actingAs($customer)
            ->from(route('booking.create'))
            ->post(route('booking.store'), array_merge($this->bookingData($vehicleType), ['passengers' => 5]))
            ->assertSessionHasErrors('vehicle_type_id');

        $this->assertDatabaseCount('bookings', 0);
    }

    private function createVehicleType(): VehicleType
    {
        return VehicleType::query()->create([
            'name' => 'Standard Taxi',
            'passenger_capacity' => 4,
            'luggage_capacity' => 2,
            'base_fare' => 10,
            'per_km_rate' => 1.5,
            'per_minute_rate' => 0.2,
            'status' => 'active',
        ]);
    }

    private function bookingData(VehicleType $vehicleType): array
    {
        return [
            'pickup_address' => 'Oulu Market Square',
            'pickup_lat' => 65.0141,
            'pickup_lng' => 25.4698,
            'destination_address' => 'Oulu Cathedral',
            'destination_lat' => 65.0124,
            'destination_lng' => 25.4682,
            'booking_date' => today()->addDay()->toDateString(),
            'booking_time' => '10:30',
            'passengers' => 2,
            'luggage_count' => 1,
            'vehicle_type_id' => $vehicleType->id,
            'customer_notes' => 'Call on arrival',
        ];
    }
}
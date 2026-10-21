<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_review_and_update_a_booking_seen_by_the_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create(['name' => 'Aino Korhonen']);
        $vehicleType = $this->createVehicleType();
        $booking = $this->createBooking($customer, $vehicleType);

        $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['search' => $booking->booking_code, 'status' => 'pending']))
            ->assertOk()
            ->assertSeeText($booking->booking_code)
            ->assertSeeText('Aino Korhonen');

        $this->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Oulu Market Square')
            ->assertSeeText('Oulu Cathedral')
            ->assertSeeText('Fare estimate');

        $this->patch(route('admin.bookings.update', $booking), [
            'status' => Booking::CONTACTED,
            'admin_notes' => 'We called the customer about the pickup.',
        ])->assertRedirect(route('admin.bookings.show', $booking));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => Booking::CONTACTED,
            'admin_notes' => 'We called the customer about the pickup.',
        ]);
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'status' => Booking::CONTACTED,
            'changed_by' => $admin->id,
            'notes' => 'We called the customer about the pickup.',
        ]);

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Contacted')
            ->assertDontSeeText('We called the customer about the pickup.');
    }

    public function test_invalid_status_transition_is_rejected_without_history_changes(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create();
        $booking = $this->createBooking($customer, $this->createVehicleType());

        $this->actingAs($admin)
            ->from(route('admin.bookings.show', $booking))
            ->patch(route('admin.bookings.update', $booking), ['status' => Booking::COMPLETED])
            ->assertSessionHasErrors('status');

        $this->assertSame(Booking::PENDING, $booking->fresh()->status);
        $this->assertDatabaseCount('booking_status_histories', 1);
    }

    public function test_booking_routes_are_admin_only_and_customer_detail_is_owner_only(): void
    {
        $this->get(route('admin.bookings.index'))->assertRedirect(route('login'));

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $booking = $this->createBooking($customer, $this->createVehicleType());

        $this->actingAs($customer)->get(route('admin.bookings.index'))->assertForbidden();
        $this->get(route('bookings.show', $booking))->assertOk();

        $anotherCustomer = User::factory()->create();
        $this->actingAs($anotherCustomer)->get(route('bookings.show', $booking))->assertNotFound();
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

    private function createBooking(User $customer, VehicleType $vehicleType): Booking
    {
        $booking = Booking::query()->create([
            'booking_code' => 'AH-'.strtoupper(fake()->unique()->bothify('????####')),
            'customer_id' => $customer->id,
            'pickup_address' => 'Oulu Market Square',
            'pickup_lat' => 65.0141,
            'pickup_lng' => 25.4698,
            'destination_address' => 'Oulu Cathedral',
            'destination_lat' => 65.0124,
            'destination_lng' => 25.4682,
            'booking_date' => today()->addDay()->toDateString(),
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
            'status' => Booking::PENDING,
            'customer_notes' => 'Call on arrival',
        ]);

        $booking->statusHistories()->create([
            'status' => Booking::PENDING,
            'changed_by' => $customer->id,
            'notes' => 'Booking request submitted.',
            'created_at' => now(),
        ]);

        return $booking;
    }
}
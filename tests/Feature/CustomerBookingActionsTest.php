<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBookingActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_cancellation_and_admin_can_approve_it(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $booking = $this->createBooking($customer, Booking::CONFIRMED);

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Request cancellation');

        $this->actingAs($customer)
            ->post(route('bookings.cancellation.store', $booking), ['reason' => 'Plans changed'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(Booking::CANCELLATION_REQUESTED, $booking->fresh()->status);
        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Your cancellation request is waiting for review.')
            ->assertDontSeeText('Send cancellation request');

        $this->actingAs($customer)
            ->from(route('bookings.show', $booking))
            ->post(route('bookings.cancellation.store', $booking))
            ->assertSessionHasErrors('cancellation');

        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'status' => Booking::CANCELLATION_REQUESTED,
            'changed_by' => $customer->id,
            'notes' => 'Plans changed',
        ]);
        $this->assertDatabaseCount('booking_status_histories', 2);

        $this->actingAs($admin)
            ->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSee(Booking::CANCELLED);

        $this->patch(route('admin.bookings.update', $booking), ['status' => Booking::CANCELLED])
            ->assertRedirect(route('admin.bookings.show', $booking));
        $this->assertSame(Booking::CANCELLED, $booking->fresh()->status);
    }

    public function test_admin_can_decline_a_cancellation_request_to_the_previous_status(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $booking = $this->createBooking($customer, Booking::CONFIRMED);

        $this->actingAs($customer)
            ->post(route('bookings.cancellation.store', $booking))
            ->assertRedirect();

        $this->actingAs($admin)
            ->patch(route('admin.bookings.update', $booking), ['status' => Booking::CONFIRMED])
            ->assertRedirect(route('admin.bookings.show', $booking));

        $this->assertSame(Booking::CONFIRMED, $booking->fresh()->status);
    }

    public function test_customer_cannot_request_cancellation_for_a_completed_booking(): void
    {
        $customer = User::factory()->create();
        $booking = $this->createBooking($customer, Booking::COMPLETED);

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Submit review');

        $this->actingAs($customer)
            ->from(route('bookings.show', $booking))
            ->post(route('bookings.cancellation.store', $booking))
            ->assertSessionHasErrors('cancellation');

        $this->assertSame(Booking::COMPLETED, $booking->fresh()->status);
        $this->assertDatabaseCount('booking_status_histories', 1);
    }

    public function test_customer_can_review_a_completed_booking_once(): void
    {
        $customer = User::factory()->create();
        $booking = $this->createBooking($customer, Booking::COMPLETED);

        $this->actingAs($customer)
            ->post(route('bookings.review.store', $booking), [
                'rating' => 5,
                'comment' => 'A smooth and comfortable ride.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'A smooth and comfortable ride.',
            'status' => 'visible',
        ]);

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Your review')
            ->assertSeeText('A smooth and comfortable ride.');

        $this->actingAs($customer)
            ->from(route('bookings.show', $booking))
            ->post(route('bookings.review.store', $booking), ['rating' => 4])
            ->assertSessionHasErrors('review');
        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_only_the_owner_can_review_their_completed_booking(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $booking = $this->createBooking($customer, Booking::COMPLETED);

        $this->actingAs($otherCustomer)
            ->post(route('bookings.review.store', $booking), ['rating' => 5])
            ->assertNotFound();

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_incomplete_booking_cannot_be_reviewed(): void
    {
        $customer = User::factory()->create();
        $booking = $this->createBooking($customer, Booking::CONFIRMED);

        $this->actingAs($customer)
            ->from(route('bookings.show', $booking))
            ->post(route('bookings.review.store', $booking), ['rating' => 5])
            ->assertSessionHasErrors('review');

        $this->assertDatabaseCount('reviews', 0);
    }

    private function createBooking(User $customer, string $status): Booking
    {
        $vehicleType = VehicleType::query()->create([
            'name' => 'Standard Taxi',
            'passenger_capacity' => 4,
            'luggage_capacity' => 2,
            'base_fare' => 10,
            'per_km_rate' => 1.5,
            'per_minute_rate' => 0.2,
            'status' => 'active',
        ]);

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
            'status' => $status,
        ]);

        $booking->statusHistories()->create([
            'status' => $status,
            'changed_by' => $customer->id,
            'notes' => 'Booking request submitted.',
            'created_at' => now(),
        ]);

        return $booking;
    }
}

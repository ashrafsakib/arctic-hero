<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\PricingRule;
use App\Models\Review;
use App\Models\User;
use App\Models\VehicleType;
use App\Notifications\BookingSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CrossWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_pricing_admin_completion_review_and_response_work_as_one_customer_journey(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $vehicleType = $this->createVehicleType();
        $scheduledAt = today()->addDays(2)->setTime(10, 30);
        PricingRule::query()->create([
            'name' => 'Airport pickup surcharge',
            'extra_charge' => 6.25,
            'starts_at' => $scheduledAt->copy()->subHour(),
            'ends_at' => $scheduledAt->copy()->addHour(),
            'status' => 'active',
        ]);
        Notification::fake();

        $this->actingAs($customer)
            ->post(route('booking.store'), $this->bookingData($vehicleType, $scheduledAt))
            ->assertRedirect(route('booking.complete'));
        $this->get(route('booking.complete'))->assertRedirect();
        $booking = Booking::query()->firstOrFail();
        $this->assertSame(6.25, (float) $booking->extra_charge);
        $this->assertEqualsWithDelta(
            (float) $booking->base_fare + (float) $booking->distance_fare + (float) $booking->time_fare + 6.25,
            (float) $booking->total_fare,
            0.01,
        );
        Notification::assertSentTo($customer, BookingSubmitted::class);
        Notification::assertSentOnDemand(BookingSubmitted::class);

        $this->actingAs($admin)
            ->patch(route('admin.bookings.update', $booking), ['status' => Booking::CONFIRMED])
            ->assertRedirect(route('admin.bookings.show', $booking));
        $this->patch(route('admin.bookings.update', $booking), ['status' => Booking::COMPLETED])
            ->assertRedirect(route('admin.bookings.show', $booking));

        $this->actingAs($customer)
            ->post(route('bookings.review.store', $booking), [
                'rating' => 5,
                'comment' => 'A smooth and punctual airport ride.',
            ])
            ->assertRedirect(route('bookings.show', $booking));
        $review = Review::query()->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('admin.reviews.update', $review), [
                'status' => 'visible',
                'admin_response' => 'Thank you for choosing us.',
            ])
            ->assertRedirect(route('admin.reviews.index'));

        $this->actingAs($customer)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('A smooth and punctual airport ride.')
            ->assertSeeText('Thank you for choosing us.');
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

    private function bookingData(VehicleType $vehicleType, $scheduledAt): array
    {
        return [
            'pickup_address' => 'Oulu Airport',
            'pickup_lat' => 64.9301,
            'pickup_lng' => 25.3546,
            'destination_address' => 'Oulu Market Square',
            'destination_lat' => 65.0141,
            'destination_lng' => 25.4698,
            'booking_date' => $scheduledAt->toDateString(),
            'booking_time' => $scheduledAt->format('H:i'),
            'passengers' => 2,
            'luggage_count' => 1,
            'vehicle_type_id' => $vehicleType->id,
        ];
    }
}

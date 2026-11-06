<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Location;
use App\Models\PricingRule;
use App\Models\Review;
use App\Models\User;
use App\Models\VehicleType;
use App\Services\CompanySettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_pricing_rule(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->actingAs($admin);

        $this->get(route('admin.pricing-rules.index'))
            ->assertOk()
            ->assertSeeText('Pricing rules');

        $this->post(route('admin.pricing-rules.store'), [
            'name' => 'Airport surcharge',
            'description' => 'Airport transfer fee',
            'extra_charge' => '7.50',
            'starts_at' => now()->toDateTimeLocalString(),
            'ends_at' => now()->addDays(30)->toDateTimeLocalString(),
            'status' => 'active',
        ])->assertRedirect(route('admin.pricing-rules.index'));

        $rule = PricingRule::query()->firstOrFail();
        $this->assertSame('7.50', $rule->extra_charge);

        $this->patch(route('admin.pricing-rules.update', $rule), [
            'name' => 'Airport transfer surcharge',
            'description' => 'Updated fee',
            'extra_charge' => '9.25',
            'starts_at' => now()->toDateTimeLocalString(),
            'ends_at' => now()->addDays(60)->toDateTimeLocalString(),
            'status' => 'inactive',
        ])->assertRedirect(route('admin.pricing-rules.index'));

        $this->assertSame('9.25', $rule->fresh()->extra_charge);
        $this->assertSame('inactive', $rule->fresh()->status);

        $this->delete(route('admin.pricing-rules.destroy', $rule))
            ->assertRedirect(route('admin.pricing-rules.index'));
        $this->assertDatabaseMissing('pricing_rules', ['id' => $rule->id]);
    }

    public function test_booking_fare_uses_only_active_rules_valid_at_the_scheduled_pickup(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $vehicleType = $this->createVehicleType();
        $scheduledAt = today()->addDays(2)->setTime(10, 30);

        PricingRule::query()->create([
            'name' => 'Scheduled surcharge',
            'extra_charge' => 7.5,
            'starts_at' => $scheduledAt->copy()->subHour(),
            'ends_at' => $scheduledAt->copy()->addHour(),
            'status' => 'active',
        ]);
        PricingRule::query()->create([
            'name' => 'Inactive surcharge',
            'extra_charge' => 20,
            'status' => 'inactive',
        ]);
        PricingRule::query()->create([
            'name' => 'Expired surcharge',
            'extra_charge' => 15,
            'starts_at' => $scheduledAt->copy()->subDays(3),
            'ends_at' => $scheduledAt->copy()->subDay(),
            'status' => 'active',
        ]);

        $this->actingAs($customer)
            ->post(route('booking.store'), $this->bookingData($vehicleType, $scheduledAt))
            ->assertRedirect(route('booking.complete'));
        $this->get(route('booking.complete'))->assertRedirect();

        $booking = Booking::query()->firstOrFail();
        $this->assertSame(7.5, (float) $booking->extra_charge);
        $this->assertEqualsWithDelta(
            (float) $booking->base_fare + (float) $booking->distance_fare + (float) $booking->time_fare + 7.5,
            (float) $booking->total_fare,
            0.01,
        );

        $this->actingAs($admin)
            ->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSeeText('Extra charge')
            ->assertSeeText('€7.50');
    }

    public function test_admin_can_manage_validated_location_map_pins(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->actingAs($admin);

        $this->get(route('admin.locations.index'))
            ->assertOk()
            ->assertSeeText('Location map pins')
            ->assertSeeText('Add location');

        $this->get(route('admin.locations.create'))
            ->assertOk()
            ->assertSee('id="location-pin-map"', false);

        $this->post(route('admin.locations.store'), [
            'name' => 'Oulu Station',
            'description' => 'Railway station pickup point',
            'address' => 'Rautatienkatu 11, Oulu',
            'latitude' => '65.0120000',
            'longitude' => '25.4800000',
            'category' => 'transport_hub',
            'status' => 'active',
        ])->assertRedirect(route('admin.locations.index'));

        $location = Location::query()->where('name', 'Oulu Station')->firstOrFail();
        $this->assertSame(65.012, (float) $location->latitude);
        $this->assertSame(25.48, (float) $location->longitude);

        $this->patch(route('admin.locations.update', $location), [
            'name' => 'Oulu Central Station',
            'description' => 'Updated pickup point',
            'address' => 'Rautatienkatu 11, Oulu',
            'latitude' => '65.0130000',
            'longitude' => '25.4810000',
            'category' => 'transport_hub',
            'status' => 'active',
        ])->assertRedirect(route('admin.locations.index'));

        $this->assertSame('65.0130000', $location->fresh()->latitude);
        $this->assertSame('25.4810000', $location->fresh()->longitude);

        $this->delete(route('admin.locations.destroy', $location))
            ->assertRedirect(route('admin.locations.index'));
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_location_coordinates_must_be_a_valid_ouluservice_pin_pair(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->from(route('admin.locations.index'))
            ->post(route('admin.locations.store'), [
                'name' => 'Invalid location',
                'address' => 'Somewhere',
                'latitude' => '66.0',
                'longitude' => '25.4',
                'category' => 'tourist_attraction',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('latitude');

        $this->assertDatabaseMissing('locations', ['name' => 'Invalid location']);

        $this->from(route('admin.locations.index'))
            ->post(route('admin.locations.store'), [
                'name' => 'Missing longitude',
                'address' => 'Somewhere',
                'latitude' => '65.01',
                'longitude' => null,
                'category' => 'tourist_attraction',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('longitude');
    }

    public function test_admin_can_moderate_a_review_and_publish_a_response_to_its_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create();
        $review = $this->createReview($customer);

        $this->actingAs($admin)
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertSeeText($review->booking->booking_code)
            ->assertSeeText('A smooth ride');

        $this->patch(route('admin.reviews.update', $review), [
            'status' => 'hidden',
            'admin_response' => 'Thank you for the feedback.',
        ])->assertRedirect(route('admin.reviews.index'));

        $review->refresh();
        $this->assertSame('hidden', $review->status);
        $this->assertSame('Thank you for the feedback.', $review->admin_response);
        $this->assertNotNull($review->admin_responded_at);

        $this->actingAs($customer)
            ->get(route('bookings.show', $review->booking))
            ->assertOk()
            ->assertSeeText('Thank you for the feedback.');
    }

    public function test_admin_can_update_company_service_settings_used_by_public_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->actingAs($admin);

        $this->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSeeText('Company and service settings');

        $this->patch(route('admin.settings.update'), [
            'name' => 'North Oulu Taxis',
            'phone' => '040 123 4567',
            'email' => 'service@example.com',
            'website' => 'https://service.example.com',
            'company_number' => '1234567-8',
            'vat_number' => 'FI12345678',
            'service_area' => 'Oulu and nearby towns',
            'booking_advance_days' => 90,
        ])->assertRedirect(route('admin.settings.edit'));

        $this->assertDatabaseHas('company_settings', [
            'key' => 'name',
            'value' => 'North Oulu Taxis',
        ]);
        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSeeText('+358401234567')
            ->assertSeeText('service@example.com')
            ->assertSeeText('Oulu and nearby towns');
        $this->get(route('booking.create'))
            ->assertOk()
            ->assertSee('max="'.today()->addDays(90)->toDateString().'"', false);
        $this->assertSame(90, app(CompanySettings::class)->bookingAdvanceDays());
    }

    public function test_company_settings_reject_invalid_contact_and_service_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->from(route('admin.settings.edit'))
            ->patch(route('admin.settings.update'), [
                'name' => 'North Oulu Taxis',
                'phone' => 'not a phone',
                'email' => 'not-an-email',
                'website' => 'javascript:alert(1)',
                'company_number' => '1234567-8',
                'vat_number' => 'FI12345678',
                'service_area' => 'Oulu',
                'booking_advance_days' => 0,
            ])
            ->assertSessionHasErrors(['phone', 'email', 'website', 'booking_advance_days']);

        $this->assertDatabaseCount('company_settings', 0);
    }

    public function test_admin_report_filters_booking_volume_status_and_completed_fare_revenue(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create();
        $vehicleType = $this->createVehicleType();
        $from = today()->subDays(2)->toDateString();
        $to = today()->toDateString();
        $this->createBooking($customer, $vehicleType, Booking::COMPLETED, today()->subDay(), 42.5);
        $this->createBooking($customer, $vehicleType, Booking::CANCELLED, today(), 50);
        $this->createBooking($customer, $vehicleType, Booking::COMPLETED, today()->subDays(10), 80);
        $this->assertDatabaseCount('bookings', 3);
        $this->assertSame(2, Booking::query()->whereDate('booking_date', '>=', $from)->whereDate('booking_date', '<=', $to)->count());

        $this->actingAs($admin)
            ->get(route('admin.reports.index', ['date_from' => $from, 'date_to' => $to]))
            ->assertOk()
            ->assertSeeText('Operations report')
            ->assertSeeText('€42.50')
            ->assertSeeText('Total bookings2')
            ->assertSeeText('Completed')
            ->assertSeeText('Cancelled');
    }

    public function test_admin_operations_are_not_available_to_customers_or_guests(): void
    {
        $this->get(route('admin.pricing-rules.index'))->assertRedirect(route('login'));
        $customer = User::factory()->create();
        $this->actingAs($customer)
            ->get(route('admin.locations.index'))
            ->assertForbidden();
        $this->get(route('admin.reviews.index'))->assertForbidden();
        $this->get(route('admin.settings.edit'))->assertForbidden();
        $this->get(route('admin.reports.index'))->assertForbidden();
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
            'pickup_address' => 'Oulu Market Square',
            'pickup_lat' => 65.0141,
            'pickup_lng' => 25.4698,
            'destination_address' => 'Oulu Cathedral',
            'destination_lat' => 65.0124,
            'destination_lng' => 25.4682,
            'booking_date' => $scheduledAt->toDateString(),
            'booking_time' => $scheduledAt->format('H:i'),
            'passengers' => 2,
            'luggage_count' => 1,
            'vehicle_type_id' => $vehicleType->id,
        ];
    }

    private function createReview(User $customer): Review
    {
        $vehicleType = $this->createVehicleType();
        $booking = $this->createBooking($customer, $vehicleType, Booking::COMPLETED, today()->subDay(), 28.5);

        return Review::query()->create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'A smooth ride',
            'status' => 'visible',
        ]);
    }

    private function createBooking(
        User $customer,
        VehicleType $vehicleType,
        string $status,
        $date,
        float $fare,
    ): Booking {
        return Booking::query()->create([
            'booking_code' => 'AH-'.strtoupper(fake()->unique()->bothify('????####')),
            'customer_id' => $customer->id,
            'pickup_address' => 'Oulu Market Square',
            'destination_address' => 'Oulu Cathedral',
            'booking_date' => $date->toDateString(),
            'booking_time' => '10:30:00',
            'passengers' => 2,
            'luggage_count' => 1,
            'vehicle_type_id' => $vehicleType->id,
            'base_fare' => $fare,
            'distance_fare' => 0,
            'time_fare' => 0,
            'extra_charge' => 0,
            'total_fare' => $fare,
            'status' => $status,
        ]);
    }
}

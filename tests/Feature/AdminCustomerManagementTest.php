<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_customers_view_booking_history_and_change_customer_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create([
            'name' => 'Aino Korhonen',
            'email' => 'aino@example.com',
            'phone' => '+358401234567',
        ]);
        $other = User::factory()->create(['name' => 'Other Customer', 'email' => 'other@example.com']);
        $vehicleType = $this->createVehicleType();
        $booking = $this->createBooking($customer, $vehicleType);
        $this->createBooking($other, $vehicleType, 'AH-OTHER01');

        $this->actingAs($admin)
            ->get(route('admin.customers.index', ['search' => 'aino@example.com']))
            ->assertOk()
            ->assertSeeText('Aino Korhonen')
            ->assertSeeText('1')
            ->assertDontSeeText('Other Customer');

        $this->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSeeText('aino@example.com')
            ->assertSeeText('+358401234567')
            ->assertSeeText($booking->booking_code)
            ->assertSee(route('admin.bookings.show', $booking), false);

        $this->patch(route('admin.customers.status.update', $customer), ['status' => 'inactive'])
            ->assertRedirect(route('admin.customers.show', $customer));
        $this->assertSame('inactive', $customer->fresh()->status);
        $this->actingAs($customer)->get(route('bookings.show', $booking))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->actingAs($admin);
        $this->get(route('admin.customers.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSeeText('Aino Korhonen')
            ->assertDontSeeText('Other Customer');

        $this->patch(route('admin.customers.status.update', $customer), ['status' => 'active'])
            ->assertRedirect(route('admin.customers.show', $customer));
        $this->assertSame('active', $customer->fresh()->status);
    }

    public function test_customer_management_cannot_change_an_admin_account_or_account_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->get(route('admin.customers.show', $admin))
            ->assertNotFound();

        $this->patch(route('admin.customers.status.update', $admin), ['status' => 'inactive'])
            ->assertNotFound();

        $this->assertSame('admin', $admin->fresh()->role);
        $this->assertSame('active', $admin->fresh()->status);
    }

    public function test_customer_directory_searches_by_name_and_phone_and_clears_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create([
            'name' => 'Aino Korhonen',
            'email' => 'aino@example.com',
            'phone' => '+358401234567',
        ]);
        User::factory()->create(['name' => 'Other Customer', 'email' => 'other@example.com']);

        $this->actingAs($admin)
            ->get(route('admin.customers.index', ['search' => '+358401234567', 'status' => 'active']))
            ->assertOk()
            ->assertSeeText('Aino Korhonen')
            ->assertSeeText('Showing 1–1 of 1 matching customers')
            ->assertSeeText('Clear filters')
            ->assertDontSeeText('Other Customer');

        $this->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSeeText('Showing 1–2 of 2 matching customers');
    }

    public function test_invalid_customer_status_is_rejected_without_changing_the_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $customer = User::factory()->create(['status' => 'active']);

        $this->actingAs($admin)
            ->from(route('admin.customers.show', $customer))
            ->patch(route('admin.customers.status.update', $customer), ['status' => 'suspended'])
            ->assertRedirect(route('admin.customers.show', $customer))
            ->assertSessionHasErrors('status');

        $this->assertSame('active', $customer->fresh()->status);
    }

    public function test_only_active_admins_can_access_customer_management(): void
    {
        $this->get(route('admin.customers.index'))->assertRedirect(route('login'));

        $customer = User::factory()->create();
        $this->actingAs($customer)->get(route('admin.customers.index'))->assertForbidden();

        $inactiveAdmin = User::factory()->create(['role' => 'admin', 'status' => 'inactive']);
        $this->actingAs($inactiveAdmin)->get(route('admin.customers.index'))->assertForbidden();
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

    private function createBooking(User $customer, VehicleType $vehicleType, string $code = 'AH-CUST001'): Booking
    {
        return Booking::query()->create([
            'booking_code' => $code,
            'customer_id' => $customer->id,
            'pickup_address' => 'Oulu Market Square',
            'destination_address' => 'Oulu Cathedral',
            'booking_date' => today()->addDay(),
            'booking_time' => '10:30:00',
            'passengers' => 2,
            'luggage_count' => 1,
            'vehicle_type_id' => $vehicleType->id,
            'base_fare' => 10,
            'distance_fare' => 5,
            'time_fare' => 2,
            'extra_charge' => 0,
            'total_fare' => 17,
            'status' => Booking::PENDING,
        ]);
    }
}

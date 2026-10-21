<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminFleetManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_view_update_and_delete_a_vehicle(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $vehicleType = $this->createVehicleType();

        $this->actingAs($admin)
            ->get(route('admin.vehicles.index'))
            ->assertOk()
            ->assertSeeText('Fleet management');

        $createResponse = $this->get(route('admin.vehicles.create'));
        $createResponse->assertOk()->assertSeeText('Add a vehicle');

        $this->post(route('admin.vehicles.store'), $this->vehicleData($vehicleType, [
            'image' => UploadedFile::fake()->image('aurora.jpg'),
        ]))->assertRedirect();

        $vehicle = Vehicle::query()->firstOrFail();
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'registration_number' => 'OUX-901',
            'vehicle_type_id' => $vehicleType->id,
        ]);
        Storage::disk('public')->assertExists($vehicle->image);
        $this->get(route('admin.vehicles.index'))
            ->assertOk()
            ->assertSee('src="'.$vehicle->display_image_url.'"', false);

        $this->get(route('admin.vehicles.show', $vehicle))
            ->assertOk()
            ->assertSeeText('Aurora 09')
            ->assertSeeText('OUX-901');
        $this->get(route('admin.vehicles.edit', $vehicle))
            ->assertOk()
            ->assertSeeText('Edit Aurora 09');

        $this->put(route('admin.vehicles.update', $vehicle), $this->vehicleData($vehicleType, [
            'name' => 'Aurora 10',
            'registration_number' => 'OUX-902',
            'status' => 'maintenance',
        ]))->assertRedirect(route('admin.vehicles.show', $vehicle));

        $vehicle->refresh();
        $this->assertSame('Aurora 10', $vehicle->name);
        $this->assertSame('OUX-902', $vehicle->registration_number);
        $this->assertSame('maintenance', $vehicle->status);
        Storage::disk('public')->assertExists($vehicle->image);

        $image = $vehicle->image;
        $this->delete(route('admin.vehicles.destroy', $vehicle))
            ->assertRedirect(route('admin.vehicles.index'));

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_vehicle_listing_filters_by_search_and_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $vehicleType = $this->createVehicleType();
        $premiumType = $this->createVehicleType('Premium Taxi');
        Vehicle::query()->create($this->vehicleData($vehicleType));
        Vehicle::query()->create($this->vehicleData($vehicleType, [
            'name' => 'Night Runner',
            'registration_number' => 'OUX-903',
            'status' => 'inactive',
        ]));
        Vehicle::query()->create($this->vehicleData($premiumType, [
            'name' => 'Aurora Premium',
            'brand' => 'Mercedes-Benz',
            'registration_number' => 'OUX-904',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.vehicles.index', [
                'search' => 'Aurora',
                'status' => 'active',
                'vehicle_type_id' => $vehicleType->id,
                'brand' => 'Toyota',
            ]))
            ->assertOk()
            ->assertSeeText('Aurora 09')
            ->assertDontSeeText('Night Runner')
            ->assertDontSeeText('Aurora Premium');
    }

    public function test_vehicle_registration_number_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $vehicleType = $this->createVehicleType();
        Vehicle::query()->create($this->vehicleData($vehicleType));

        $this->actingAs($admin)
            ->from(route('admin.vehicles.create'))
            ->post(route('admin.vehicles.store'), $this->vehicleData($vehicleType, ['name' => 'Duplicate Plate']))
            ->assertSessionHasErrors('registration_number');

        $this->assertDatabaseCount('vehicles', 1);
    }

    public function test_only_active_admins_can_access_fleet_management(): void
    {
        $this->get(route('admin.vehicles.index'))->assertRedirect(route('login'));

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $this->actingAs($customer)->get(route('admin.vehicles.index'))->assertForbidden();

        $disabledAdmin = User::factory()->create(['role' => 'admin', 'status' => 'inactive']);
        $this->actingAs($disabledAdmin)->get(route('admin.vehicles.index'))->assertForbidden();
    }

    private function createVehicleType(string $name = 'Standard Taxi'): VehicleType
    {
        return VehicleType::query()->create([
            'name' => $name,
            'description' => 'Comfortable taxi for city trips.',
            'passenger_capacity' => 4,
            'luggage_capacity' => 2,
            'base_fare' => 10,
            'per_km_rate' => 1.5,
            'per_minute_rate' => 0.2,
            'status' => 'active',
        ]);
    }

    /** @return array<string, mixed> */
    private function vehicleData(VehicleType $vehicleType, array $overrides = []): array
    {
        return array_merge([
            'vehicle_type_id' => $vehicleType->id,
            'name' => 'Aurora 09',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'registration_number' => 'OUX-901',
            'color' => 'White',
            'year' => 2025,
            'status' => 'active',
        ], $overrides);
    }
}
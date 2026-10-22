<?php

namespace Tests\Feature;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_routes_page_lists_active_locations_and_prefills_booking_endpoints(): void
    {
        $origin = $this->createLocation('Oulu Market Square', 'Kauppatori, Oulu', 65.0141, 25.4698, 'tourist_attraction');
        $airport = $this->createLocation('Oulu Airport', 'Lentokentäntie 720, Oulunsalo', 64.9301, 25.3546, 'airport');
        $this->createLocation('Closed stop', 'Closed address', 65.01, 25.46, 'tourist_attraction', 'inactive');

        $this->get(route('routes.index'))
            ->assertOk()
            ->assertSeeText('Oulu is closer')
            ->assertSeeText('Oulu Market Square')
            ->assertSeeText('Oulu Airport')
            ->assertDontSeeText('Closed stop')
            ->assertSee('data-route-locations', false)
            ->assertSee('pickup_address='.rawurlencode($origin->address), false)
            ->assertSee('destination_address='.rawurlencode($airport->address), false);
    }

    public function test_routes_page_shows_an_empty_state_without_active_locations(): void
    {
        $this->get(route('routes.index'))
            ->assertOk()
            ->assertSeeText('Local stops are being updated.')
            ->assertDontSee('id="routes-map"', false);
    }

    private function createLocation(
        string $name,
        string $address,
        float $latitude,
        float $longitude,
        string $category,
        string $status = 'active',
    ): Location {
        return Location::query()->create([
            'name' => $name,
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'category' => $category,
            'status' => $status,
        ]);
    }
}
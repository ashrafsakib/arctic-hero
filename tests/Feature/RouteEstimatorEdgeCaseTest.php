<?php

namespace Tests\Feature;

use App\Services\Booking\RouteEstimator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RouteEstimatorEdgeCaseTest extends TestCase
{
    public function test_unusable_zero_route_response_falls_back_to_positive_local_estimate(): void
    {
        config(['services.routing.url' => 'https://router.example.test']);
        Http::fake([
            'router.example.test/*' => Http::response([
                'code' => 'Ok',
                'routes' => [['distance' => 0, 'duration' => 0]],
            ]),
        ]);

        $route = app(RouteEstimator::class)->estimate(65.0141, 25.4698, 65.0141, 25.4698);

        $this->assertEqualsWithDelta(1.0, $route['distance_km'], 0.001);
        $this->assertSame(2, $route['duration_minutes']);
    }

    public function test_connection_failure_falls_back_to_local_estimate(): void
    {
        config(['services.routing.url' => 'https://router.example.test']);
        Http::fake(function (): never {
            throw new ConnectionException('Routing provider unavailable.');
        });

        $route = app(RouteEstimator::class)->estimate(65.0141, 25.4698, 65.0124, 25.4682);

        $this->assertGreaterThan(0, $route['distance_km']);
        $this->assertGreaterThan(0, $route['duration_minutes']);
    }
}

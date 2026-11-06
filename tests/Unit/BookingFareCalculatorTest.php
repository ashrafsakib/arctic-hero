<?php

namespace Tests\Unit;

use App\Models\VehicleType;
use App\Services\Booking\FareCalculator;
use PHPUnit\Framework\TestCase;

class BookingFareCalculatorTest extends TestCase
{
    public function test_fares_round_each_component_and_clamp_negative_extra_charge(): void
    {
        $vehicleType = new VehicleType([
            'base_fare' => '10.00',
            'per_km_rate' => '1.333',
            'per_minute_rate' => '0.125',
        ]);

        $fare = (new FareCalculator)->calculate($vehicleType, 2.345, 3, -5);

        $this->assertSame(10.0, $fare['base_fare']);
        $this->assertSame(3.12, $fare['distance_fare']);
        $this->assertSame(0.39, $fare['time_fare']);
        $this->assertSame(0.0, $fare['extra_charge']);
        $this->assertSame(13.51, $fare['total_fare']);
    }

    public function test_zero_trip_components_preserve_base_fare_and_round_surcharge(): void
    {
        $vehicleType = new VehicleType([
            'base_fare' => '0.00',
            'per_km_rate' => '2.00',
            'per_minute_rate' => '1.00',
        ]);

        $fare = (new FareCalculator)->calculate($vehicleType, 0, 0, 1.235);

        $this->assertSame(0.0, $fare['base_fare']);
        $this->assertSame(0.0, $fare['distance_fare']);
        $this->assertSame(0.0, $fare['time_fare']);
        $this->assertSame(1.24, $fare['extra_charge']);
        $this->assertSame(1.24, $fare['total_fare']);
    }
}

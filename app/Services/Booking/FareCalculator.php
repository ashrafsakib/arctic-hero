<?php

namespace App\Services\Booking;

use App\Models\VehicleType;

class FareCalculator
{
    /**
     * @return array{base_fare: float, distance_fare: float, time_fare: float, total_fare: float}
     */
    public function calculate(VehicleType $vehicleType, float $distanceKm, int $durationMinutes): array
    {
        $baseFare = (float) $vehicleType->base_fare;
        $distanceFare = round($distanceKm * (float) $vehicleType->per_km_rate, 2);
        $timeFare = round($durationMinutes * (float) $vehicleType->per_minute_rate, 2);

        return [
            'base_fare' => $baseFare,
            'distance_fare' => $distanceFare,
            'time_fare' => $timeFare,
            'total_fare' => round($baseFare + $distanceFare + $timeFare, 2),
        ];
    }
}

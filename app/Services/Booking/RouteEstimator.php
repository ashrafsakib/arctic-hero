<?php

namespace App\Services\Booking;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RouteEstimator
{
    /**
     * @return array{distance_km: float, duration_minutes: int}
     */
    public function estimate(float $startLat, float $startLng, float $endLat, float $endLng): array
    {
        $providerUrl = config('services.routing.url');

        if (is_string($providerUrl) && $providerUrl !== '') {
            try {
                $response = Http::timeout(3)->get(
                    rtrim($providerUrl, '/').'/route/v1/driving/'
                        .$startLng.','.$startLat.';'.$endLng.','.$endLat,
                    ['overview' => 'false', 'steps' => 'false'],
                );

                $distance = $response->json('routes.0.distance');
                $duration = $response->json('routes.0.duration');

                if ($response->successful() && is_numeric($distance) && $distance > 0 && is_numeric($duration) && $duration > 0) {
                    return [
                        'distance_km' => max(0.01, round((float) $distance / 1000, 2)),
                        'duration_minutes' => max(1, (int) ceil((float) $duration / 60)),
                    ];
                }

                Log::warning('Routing provider returned an unusable route estimate.', [
                    'status' => $response->status(),
                ]);
            } catch (ConnectionException $exception) {
                report($exception);
                Log::warning('Routing provider could not be reached; using the local route estimate.');
            }
        }

        return $this->estimateLocally($startLat, $startLng, $endLat, $endLng);
    }

    /**
     * @return array{distance_km: float, duration_minutes: int}
     */
    private function estimateLocally(float $startLat, float $startLng, float $endLat, float $endLng): array
    {
        $toRadians = static fn (float $degrees): float => deg2rad($degrees);
        $latitudeDelta = $toRadians($endLat - $startLat);
        $longitudeDelta = $toRadians($endLng - $startLng);
        $value = sin($latitudeDelta / 2) ** 2
            + cos($toRadians($startLat)) * cos($toRadians($endLat)) * sin($longitudeDelta / 2) ** 2;
        $value = min(1, max(0, $value));
        $straightLineDistance = 6371 * 2 * atan2(sqrt($value), sqrt(1 - $value));
        $distance = max(1, round($straightLineDistance * 1.25, 2));

        return [
            'distance_km' => $distance,
            'duration_minutes' => max(1, (int) ceil($distance / 35 * 60)),
        ];
    }
}

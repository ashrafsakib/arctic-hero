<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\View\View;

class RoutesController extends Controller
{
    public function index(): View
    {
        $locations = Location::query()
            ->where('status', 'active')
            ->whereNotNull('address')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get();

        $origin = $locations->firstWhere('name', 'Oulu Market Square') ?? $locations->first();
        $popularRoutes = $origin
            ? $locations->reject(fn ($location) => (int) $location->id === (int) $origin->id)
                ->map(fn ($destination): array => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'distance_km' => $this->estimateRoadDistance($origin, $destination),
                ])
                ->values()
            : collect();

        $mapLocations = $locations->map(fn ($location): array => [
            'id' => $location->id,
            'name' => $location->name,
            'address' => $location->address,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'category' => $location->category,
        ])->values();

        return view('routes.index', compact('locations', 'popularRoutes', 'mapLocations'));
    }

    private function estimateRoadDistance($origin, $destination): float
    {
        $toRadians = static fn (float $degrees): float => deg2rad($degrees);
        $startLatitude = $toRadians((float) $origin->latitude);
        $endLatitude = $toRadians((float) $destination->latitude);
        $latitudeDelta = $endLatitude - $startLatitude;
        $longitudeDelta = $toRadians((float) $destination->longitude - (float) $origin->longitude);
        $value = sin($latitudeDelta / 2) ** 2
            + cos($startLatitude) * cos($endLatitude) * sin($longitudeDelta / 2) ** 2;
        $straightLineDistance = 6371 * 2 * atan2(sqrt($value), sqrt(1 - $value));

        return max(1, round($straightLineDistance * 1.25, 1));
    }
}
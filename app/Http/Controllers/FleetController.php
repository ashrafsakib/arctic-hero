<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FleetController extends Controller
{
    public function index(): View
    {
        $vehicleTypes = VehicleType::query()
            ->where('status', 'active')
            ->orderBy('base_fare')
            ->get();

        $vehicleTypes->each(function (VehicleType $vehicleType): void {
            $name = Str::lower($vehicleType->name);
            $fallbackImage = match (true) {
                Str::contains($name, 'premium') => 'taxi-driver.jpg',
                Str::contains($name, ['mini', 'van']) => 'northern-road.jpg',
                default => 'taxi-city.jpg',
            };

            $vehicleType->setAttribute(
                'display_image',
                $vehicleType->image
                    ? asset('storage/'.$vehicleType->image)
                    : asset('images/'.$fallbackImage),
            );
        });

        return view('fleet.index', compact('vehicleTypes'));
    }
}
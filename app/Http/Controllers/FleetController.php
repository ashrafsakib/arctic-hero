<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\View\View;

class FleetController extends Controller
{
    public function index(): View
    {
        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->where('status', 'active')
            ->whereHas('vehicleType', fn ($query) => $query->where('status', 'active'))
            ->orderBy('name')
            ->get();

        $maximumPassengers = $vehicles->max(fn (Vehicle $vehicle) => $vehicle->vehicleType->passenger_capacity) ?? 0;

        return view('fleet.index', compact('vehicles', 'maximumPassengers'));
    }
}
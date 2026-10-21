<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'vehicleCount' => Vehicle::count(),
            'activeVehicleCount' => Vehicle::where('status', 'active')->count(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ContactSubmission;
use App\Models\Location;
use App\Models\PricingRule;
use App\Models\Review;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'vehicleCount' => Vehicle::count(),
            'activeVehicleCount' => Vehicle::where('status', 'active')->count(),
            'newContactCount' => ContactSubmission::where('status', ContactSubmission::NEW)->count(),
            'pendingBookingCount' => Booking::where('status', Booking::PENDING)->count(),
            'cancellationRequestCount' => Booking::where('status', Booking::CANCELLATION_REQUESTED)->count(),
            'visibleReviewCount' => Review::where('status', 'visible')->count(),
            'locationCount' => Location::count(),
            'activePricingRuleCount' => PricingRule::where('status', 'active')->count(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $defaults = [
            'date_from' => today()->subDays(29)->toDateString(),
            'date_to' => today()->toDateString(),
        ];
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $dateFrom = $filters['date_from'] ?? $defaults['date_from'];
        $dateTo = $filters['date_to'] ?? $defaults['date_to'];

        if (Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) > 365) {
            throw ValidationException::withMessages([
                'date_to' => 'Choose a reporting period of one year or less.',
            ]);
        }

        $period = Booking::query()
            ->whereDate('booking_date', '>=', $dateFrom)
            ->whereDate('booking_date', '<=', $dateTo);
        $statusCounts = (clone $period)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->orderBy('status')
            ->pluck('aggregate', 'status');
        $completedPeriod = (clone $period)->where('status', Booking::COMPLETED);

        return view('admin.reports.index', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalBookings' => (clone $period)->count(),
            'statusCounts' => $statusCounts,
            'completedCount' => $statusCounts->get(Booking::COMPLETED, 0),
            'completedRevenue' => (float) $completedPeriod->sum('total_fare'),
            'topPickups' => (clone $period)
                ->selectRaw('pickup_address, COUNT(*) as aggregate')
                ->groupBy('pickup_address')
                ->orderByDesc('aggregate')
                ->limit(5)
                ->get(),
        ]);
    }
}

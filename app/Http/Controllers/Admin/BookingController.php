<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const STATUSES = [
        Booking::PENDING,
        Booking::CONTACTED,
        Booking::CONFIRMED,
        Booking::REJECTED,
        Booking::COMPLETED,
        Booking::CANCELLED,
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:'.implode(',', self::STATUSES)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $bookings = Booking::query()
            ->with(['customer', 'vehicleType'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('booking_code', 'like', "%{$search}%")
                        ->orWhere('pickup_address', 'like', "%{$search}%")
                        ->orWhere('destination_address', 'like', "%{$search}%")
                        ->orWhereHas('customer', function (Builder $customerQuery) use ($search): void {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('booking_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('booking_date', '<=', $date))
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'totalCount' => Booking::count(),
            'pendingCount' => Booking::where('status', Booking::PENDING)->count(),
            'confirmedCount' => Booking::where('status', Booking::CONFIRMED)->count(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Booking $booking): View
    {
        return view('admin.bookings.show', [
            'booking' => $booking->load(['customer', 'vehicleType', 'statusHistories.changedBy']),
            'availableStatuses' => array_values(array_unique([
                $booking->status,
                ...$this->transitionsFor($booking->status),
            ])),
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $nextStatus = $data['status'];
        $previousStatus = $booking->status;
        if ($nextStatus !== $previousStatus && ! in_array($nextStatus, $this->transitionsFor($previousStatus), true)) {
            throw ValidationException::withMessages([
                'status' => 'That booking status transition is not allowed.',
            ]);
        }

        DB::transaction(function () use ($booking, $data, $nextStatus, $previousStatus, $request): void {
            $booking->update([
                'status' => $nextStatus,
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            if ($nextStatus !== $previousStatus) {
                $booking->statusHistories()->create([
                    'status' => $nextStatus,
                    'changed_by' => $request->user()->id,
                    'notes' => $data['admin_notes'] ?? null,
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', $nextStatus === $previousStatus ? 'Booking notes updated.' : 'Booking status updated.');
    }

    /** @return list<string> */
    private function transitionsFor(string $status): array
    {
        return match ($status) {
            Booking::PENDING => [Booking::CONTACTED, Booking::CONFIRMED, Booking::REJECTED, Booking::CANCELLED],
            Booking::CONTACTED => [Booking::CONFIRMED, Booking::REJECTED, Booking::CANCELLED],
            Booking::CONFIRMED => [Booking::COMPLETED, Booking::CANCELLED],
            default => [],
        };
    }
}
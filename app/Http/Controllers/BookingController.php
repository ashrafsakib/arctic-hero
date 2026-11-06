<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Location;
use App\Models\VehicleType;
use App\Services\Booking\BookingNotificationService;
use App\Services\Booking\FareCalculator;
use App\Services\Booking\RouteEstimator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        $vehicleTypes = VehicleType::query()
            ->where('status', 'active')
            ->orderBy('base_fare')
            ->get();
        $locations = Location::query()
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get(['name', 'address', 'latitude', 'longitude']);

        return view('booking.create', [
            'vehicleTypes' => $vehicleTypes,
            'vehicleData' => $vehicleTypes->map(fn ($vehicle): array => [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'passenger_capacity' => $vehicle->passenger_capacity,
                'luggage_capacity' => $vehicle->luggage_capacity,
                'base_fare' => (float) $vehicle->base_fare,
                'per_km_rate' => (float) $vehicle->per_km_rate,
                'per_minute_rate' => (float) $vehicle->per_minute_rate,
            ])->values(),
            'locations' => $locations,
            'locationData' => $locations->map(fn ($location): array => [
                'name' => $location->name,
                'address' => $location->address,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
            ])->values(),
            'pickup' => $request->string('pickup_address')->toString(),
            'destination' => $request->string('destination_address')->toString(),
            'bookingDate' => $request->input('booking_date', today()->toDateString()),
            'bookingTime' => $request->input('booking_time', '12:00'),
            'passengers' => (int) $request->input('passengers', 1),
            'selectedVehicleTypeId' => (int) $request->input('vehicle_type_id', $vehicleTypes->first()?->id),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pickup_address' => ['required', 'string', 'max:255'],
            'pickup_lat' => ['required', 'numeric', 'between:64.5,65.5'],
            'pickup_lng' => ['required', 'numeric', 'between:24.8,26.0'],
            'destination_address' => ['required', 'string', 'max:255'],
            'destination_lat' => ['required', 'numeric', 'between:64.5,65.5'],
            'destination_lng' => ['required', 'numeric', 'between:24.8,26.0'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'date_format:H:i'],
            'passengers' => ['required', 'integer', 'between:1,7'],
            'luggage_count' => ['required', 'integer', 'between:0,10'],
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $vehicleType = VehicleType::query()
            ->where('status', 'active')
            ->findOrFail($data['vehicle_type_id']);

        if ($data['passengers'] > $vehicleType->passenger_capacity || $data['luggage_count'] > $vehicleType->luggage_capacity) {
            throw ValidationException::withMessages([
                'vehicle_type_id' => 'Choose a vehicle with enough seats and luggage space.',
            ]);
        }

        $request->session()->put('booking_draft', $data);

        if (! $request->user()) {
            return redirect()->guest(route('booking.complete'));
        }

        return redirect()->route('booking.complete');
    }

    public function complete(
        Request $request,
        RouteEstimator $routeEstimator,
        FareCalculator $fareCalculator,
        BookingNotificationService $bookingNotifications,
    ): RedirectResponse {
        $data = $request->session()->pull('booking_draft');

        if (! is_array($data)) {
            return redirect()->route('booking.create');
        }

        $vehicleType = VehicleType::query()
            ->where('status', 'active')
            ->findOrFail($data['vehicle_type_id']);

        if ($data['passengers'] > $vehicleType->passenger_capacity || $data['luggage_count'] > $vehicleType->luggage_capacity) {
            return redirect()->route('booking.create')
                ->withErrors(['vehicle_type_id' => 'That vehicle no longer has enough seats and luggage space.']);
        }

        $route = $routeEstimator->estimate(
            (float) $data['pickup_lat'],
            (float) $data['pickup_lng'],
            (float) $data['destination_lat'],
            (float) $data['destination_lng'],
        );
        $fare = $fareCalculator->calculate($vehicleType, $route['distance_km'], $route['duration_minutes']);

        $booking = DB::transaction(function () use ($request, $data, $vehicleType, $route, $fare): Booking {
            $booking = Booking::create([
                'booking_code' => 'AH-'.Str::upper(Str::random(8)),
                'customer_id' => $request->user()->id,
                'pickup_address' => $data['pickup_address'],
                'pickup_lat' => $data['pickup_lat'],
                'pickup_lng' => $data['pickup_lng'],
                'destination_address' => $data['destination_address'],
                'destination_lat' => $data['destination_lat'],
                'destination_lng' => $data['destination_lng'],
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'passengers' => $data['passengers'],
                'luggage_count' => $data['luggage_count'],
                'vehicle_type_id' => $vehicleType->id,
                'distance_km' => $route['distance_km'],
                'estimated_duration_minutes' => $route['duration_minutes'],
                'base_fare' => $fare['base_fare'],
                'distance_fare' => $fare['distance_fare'],
                'time_fare' => $fare['time_fare'],
                'extra_charge' => 0,
                'total_fare' => $fare['total_fare'],
                'status' => Booking::PENDING,
                'customer_notes' => $data['customer_notes'] ?? null,
            ]);

            $booking->statusHistories()->create([
                'status' => Booking::PENDING,
                'changed_by' => $request->user()->id,
                'notes' => 'Booking request submitted.',
                'created_at' => now(),
            ]);

            return $booking;
        });

        $response = redirect()->route('booking.confirmation', $booking);

        if (! $bookingNotifications->send($booking)) {
            $response->with('notification_warning', 'Your booking was saved, but we could not send its email notification. Please contact us if you do not receive a confirmation.');
        }

        return $response;
    }

    public function confirmation(Request $request, Booking $booking): View
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        return view('booking.confirmation', [
            'booking' => $booking->load('vehicleType'),
        ]);
    }

    public function show(Request $request, Booking $booking): View
    {
        abort_unless($booking->customer_id === $request->user()->id, 404);

        return view('booking.show', [
            'booking' => $booking->load(['vehicleType', 'review']),
            'statusHistories' => $booking->statusHistories()
                ->orderBy('created_at')
                ->get(['id', 'booking_id', 'status', 'created_at']),
        ]);
    }

    public function requestCancellation(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 404);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $booking, $data): void {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            abort_unless($lockedBooking->customer_id === $request->user()->id, 404);

            if (! $lockedBooking->canCustomerRequestCancellation()) {
                throw ValidationException::withMessages([
                    'cancellation' => 'This booking can no longer be cancelled online.',
                ]);
            }

            $lockedBooking->update(['status' => Booking::CANCELLATION_REQUESTED]);
            $lockedBooking->statusHistories()->create([
                'status' => Booking::CANCELLATION_REQUESTED,
                'changed_by' => $request->user()->id,
                'notes' => $data['reason'] ?? 'Cancellation requested by customer.',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Your cancellation request has been sent for review.');
    }

    public function storeReview(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 404);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $booking, $data): void {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            abort_unless($lockedBooking->customer_id === $request->user()->id, 404);

            if (! $lockedBooking->canCustomerSubmitReview()) {
                throw ValidationException::withMessages([
                    'review' => 'You can review a booking after it has been completed.',
                ]);
            }

            if ($lockedBooking->review()->exists()) {
                throw ValidationException::withMessages([
                    'review' => 'A review has already been submitted for this booking.',
                ]);
            }

            $lockedBooking->review()->create([
                'customer_id' => $request->user()->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
        });

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Thank you for sharing your feedback.');
    }
}

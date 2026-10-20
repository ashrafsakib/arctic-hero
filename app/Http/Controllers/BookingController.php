<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Location;
use App\Models\VehicleType;
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
            'vehicleData' => $vehicleTypes->map(fn (VehicleType $vehicle): array => [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'passenger_capacity' => $vehicle->passenger_capacity,
                'luggage_capacity' => $vehicle->luggage_capacity,
                'base_fare' => (float) $vehicle->base_fare,
                'per_km_rate' => (float) $vehicle->per_km_rate,
                'per_minute_rate' => (float) $vehicle->per_minute_rate,
            ])->values(),
            'locations' => $locations,
            'locationData' => $locations->map(fn (Location $location): array => [
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

    public function complete(Request $request): RedirectResponse
    {
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

        $distance = $this->estimateDistance(
            (float) $data['pickup_lat'],
            (float) $data['pickup_lng'],
            (float) $data['destination_lat'],
            (float) $data['destination_lng'],
        );
        $duration = max(1, (int) ceil($distance / 35 * 60));
        $baseFare = (float) $vehicleType->base_fare;
        $distanceFare = round($distance * (float) $vehicleType->per_km_rate, 2);
        $timeFare = round($duration * (float) $vehicleType->per_minute_rate, 2);

        $booking = DB::transaction(fn () => Booking::create([
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
            'distance_km' => $distance,
            'estimated_duration_minutes' => $duration,
            'base_fare' => $baseFare,
            'distance_fare' => $distanceFare,
            'time_fare' => $timeFare,
            'extra_charge' => 0,
            'total_fare' => round($baseFare + $distanceFare + $timeFare, 2),
            'status' => Booking::PENDING,
            'customer_notes' => $data['customer_notes'] ?? null,
        ]));

        return redirect()->route('booking.confirmation', $booking);
    }

    public function confirmation(Request $request, Booking $booking): View
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        return view('booking.confirmation', [
            'booking' => $booking->load('vehicleType'),
        ]);
    }

    private function estimateDistance(float $startLat, float $startLng, float $endLat, float $endLng): float
    {
        $toRadians = static fn (float $degrees): float => deg2rad($degrees);
        $latitudeDelta = $toRadians($endLat - $startLat);
        $longitudeDelta = $toRadians($endLng - $startLng);
        $value = sin($latitudeDelta / 2) ** 2
            + cos($toRadians($startLat)) * cos($toRadians($endLat)) * sin($longitudeDelta / 2) ** 2;

        return max(1, round(6371 * 2 * atan2(sqrt($value), sqrt(1 - $value)) * 1.25, 2));
    }
}
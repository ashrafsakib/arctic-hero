<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plan your ride | {{ config('company.name', 'Arctic Hero') }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="booking-page">
    <header class="booking-header">
        <a href="{{ url('/') }}" class="booking-brand"><span class="booking-brand-mark">A</span><span>ARCTIC <b>HERO</b></span></a>
        <div class="booking-header-actions">
            <span>Oulu, Finland</span>
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Log in</a>
            @endauth
        </div>
    </header>

    <main class="booking-main">
        <div class="booking-title-row">
            <div><p class="booking-eyebrow">ARCTIC HERO · OULU</p><h1>Plan your ride.</h1><p>Choose your route, vehicle and pickup time.</p></div>
            <a class="booking-back-link" href="{{ url('/') }}">Back to home</a>
        </div>
        <ol class="booking-progress" aria-label="Booking progress">
            <li data-progress-step="1" class="is-current"><span>01</span><b>Route</b></li>
            <li data-progress-step="2"><span>02</span><b>Your ride</b></li>
            <li data-progress-step="3"><span>03</span><b>Review</b></li>
        </ol>

        @if ($errors->any())
            <div class="booking-alert" role="alert">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('booking.store') }}" method="post" data-booking-form>
            @csrf
            <input type="hidden" name="pickup_lat" value="{{ old('pickup_lat') }}">
            <input type="hidden" name="pickup_lng" value="{{ old('pickup_lng') }}">
            <input type="hidden" name="destination_lat" value="{{ old('destination_lat') }}">
            <input type="hidden" name="destination_lng" value="{{ old('destination_lng') }}">
            <section class="booking-step" data-booking-step="1">
                <div class="booking-step-heading"><span>01 / Route</span><h2>Where are we picking you up?</h2><p>Choose a known place or tap the map to set each point.</p></div>
                <div class="booking-route-layout">
                    <div class="booking-route-fields">
                        <label class="booking-input"><span><i class="pickup-dot"></i>Pickup</span><input name="pickup_address" list="known-locations" value="{{ old('pickup_address', $pickup) }}" placeholder="Address, landmark or map point" autocomplete="off"><button type="button" data-select-pickup aria-label="Set pickup point on map" aria-pressed="true">Selected for map</button></label>
                        <label class="booking-input"><span><i class="destination-dot"></i>Destination</span><input name="destination_address" list="known-locations" value="{{ old('destination_address', $destination) }}" placeholder="Where are you going?" autocomplete="off"><button type="button" data-select-destination aria-label="Set destination point on map" aria-pressed="false">Set on map</button></label>
                        <datalist id="known-locations">@foreach ($locations as $location)<option value="{{ $location->address }}">{{ $location->name }}</option>@endforeach</datalist>
                        <div class="booking-date-grid">
                            <label class="booking-input"><span>Pickup date</span><input type="date" name="booking_date" min="{{ today()->toDateString() }}" value="{{ old('booking_date', $bookingDate) }}" required></label>
                            <label class="booking-input"><span>Pickup time</span><input type="time" name="booking_time" value="{{ old('booking_time', $bookingTime) }}" required></label>
                        </div>
                        <div class="booking-date-grid">
                            <label class="booking-input"><span>Passengers</span><select name="passengers" required>@for ($count = 1; $count <= 7; $count++)<option value="{{ $count }}" @selected(old('passengers', $passengers) == $count)>{{ $count }} {{ $count === 1 ? 'passenger' : 'passengers' }}</option>@endfor</select></label>
                            <label class="booking-input"><span>Luggage bags</span><select name="luggage_count">@for ($count = 0; $count <= 10; $count++)<option value="{{ $count }}" @selected(old('luggage_count', 0) == $count)>{{ $count }} {{ $count === 1 ? 'bag' : 'bags' }}</option>@endfor</select></label>
                        </div>
                        <p class="booking-map-hint"><span>+</span> Tap the map to set the selected point. Drag a pin to fine-tune.</p>
                    </div>
                    <div class="booking-map-wrap"><div id="booking-map" aria-label="Map of Oulu for selecting pickup and destination"></div><div class="booking-map-legend"><span><i class="pickup-dot"></i>Pickup</span><span><i class="destination-dot"></i>Destination</span></div></div>
                </div>
                <p class="booking-step-error" data-step-error role="alert"></p>
                <div class="booking-step-actions"><span>Prices are estimates in EUR</span><button type="button" class="booking-next" data-next-step>Choose a vehicle <span>→</span></button></div>
            </section>

            <section class="booking-step" data-booking-step="2" hidden>
                <div class="booking-step-heading"><span>02 / Your ride</span><h2>Pick the right fit.</h2><p>Vehicle availability is matched to your passenger count.</p></div>
                <div class="booking-choice-layout">
                    <div class="booking-vehicle-list">
                        @foreach ($vehicleTypes as $vehicleType)
                            <label class="booking-vehicle" data-vehicle-option>
                                <input type="radio" name="vehicle_type_id" value="{{ $vehicleType->id }}" @checked(old('vehicle_type_id', $vehicleTypes->first()?->id) == $vehicleType->id)>
                                <span class="booking-vehicle-icon" aria-hidden="true">{{ $vehicleType->passenger_capacity > 4 ? 'VAN' : ($vehicleType->base_fare > 10 ? 'PRO' : 'TAXI') }}</span>
                                <span class="booking-vehicle-copy"><strong>{{ $vehicleType->name }}</strong><small>{{ $vehicleType->description }}</small><small>Up to {{ $vehicleType->passenger_capacity }} passengers · {{ $vehicleType->luggage_capacity }} bags</small></span>
                                <span class="booking-vehicle-rate"><strong>€{{ number_format((float) $vehicleType->base_fare, 2) }}</strong><small>base fare</small></span>
                            </label>
                        @endforeach
                    </div>
                    <aside class="booking-fare-panel" aria-live="polite">
                        <p>FARE ESTIMATE</p><h3 data-fare-total>€0.00</h3><span class="booking-fare-route" data-fare-route>Set pickup and destination on the map</span>
                        <dl><div><dt>Base fare</dt><dd data-fare-base>—</dd></div><div><dt>Distance</dt><dd data-fare-distance>—</dd></div><div><dt>Journey time</dt><dd data-fare-time>—</dd></div></dl>
                        <small>Estimate uses the selected route and vehicle rates. Final fare may vary.</small>
                    </aside>
                </div>
                <label class="booking-input booking-notes"><span>Notes for your driver <small>Optional</small></span><textarea name="customer_notes" rows="3" maxlength="2000" placeholder="Accessibility needs, child seat or other details"></textarea></label>
                <p class="booking-step-error" data-step-error role="alert"></p>
                <div class="booking-step-actions"><button type="button" class="booking-previous" data-previous-step>← Back to route</button><button type="button" class="booking-next" data-next-step>Review booking <span>→</span></button></div>
            </section>

            <section class="booking-step" data-booking-step="3" hidden>
                <div class="booking-step-heading"><span>03 / Review</span><h2>Ready for the road?</h2><p>Check your details before sending the ride request.</p></div>
                <div class="booking-review-layout">
                    <div class="booking-review-details">
                        <div><span>Pickup</span><strong data-review-pickup></strong></div>
                        <div><span>Destination</span><strong data-review-destination></strong></div>
                        <div><span>Date and time</span><strong data-review-date></strong></div>
                        <div><span>Group</span><strong data-review-passengers></strong></div>
                        <div><span>Vehicle</span><strong data-review-vehicle></strong></div>
                    </div>
                    <aside class="booking-fare-panel booking-review-fare"><p>ESTIMATED TOTAL</p><h3 data-fare-total>€0.00</h3><dl><div><dt>Base fare</dt><dd data-fare-base>—</dd></div><div><dt>Distance</dt><dd data-fare-distance>—</dd></div><div><dt>Journey time</dt><dd data-fare-time>—</dd></div></dl><small>No payment is taken now. Your booking is sent as a ride request.</small></aside>
                </div>
                <label class="booking-confirm-check"><input type="checkbox" required><span>I have checked the trip details and want to send this booking request.</span></label>
                <p class="booking-step-error" data-step-error role="alert"></p>
                <div class="booking-step-actions"><button type="button" class="booking-previous" data-previous-step>← Change details</button><button type="submit" class="booking-next booking-submit-final">Confirm ride request <span>→</span></button></div>
                @guest<p class="booking-auth-note">You’ll sign in or create an account before your booking is saved.</p>@endguest
            </section>
        </form>
    </main>

    <footer class="booking-footer"><a href="mailto:{{ config('company.email') }}">Need help? {{ config('company.email') }}</a><span>ARCTIC HERO · OULU</span></footer>
    <script type="application/json" data-vehicle-types>@json($vehicleData)</script>
    <script type="application/json" data-locations>@json($locationData)</script>
</body>
</html>
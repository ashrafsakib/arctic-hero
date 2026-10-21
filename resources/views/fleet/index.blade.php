<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Our Fleet | {{ config('company.name', 'Arctic Hero') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-shell fleet-page">
    <header class="site-header" x-data="{ open: false }">
        <div class="utility-bar"><div class="utility-inner"><div class="utility-contact"><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a></div><div class="utility-social"><span>OULU, FINLAND</span><span>CITY · AIRPORT · GROUP</span></div></div></div>
        <div class="header-inner">
            <a href="{{ url('/') }}" class="brand" aria-label="Arctic Hero home"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a>
            <nav class="desktop-nav" aria-label="Main navigation">
                <a href="{{ url('/') }}#services">Services</a>
                <a href="{{ route('fleet.index') }}" aria-current="page">Our fleet</a>
                <a href="{{ route('booking.create') }}">Book a ride</a>
                <a href="{{ url('/') }}#locations">Oulu routes</a>
                <a href="{{ url('/') }}#contact">Contact</a>
            </nav>
            <div class="header-actions">
                @auth<a href="{{ route('dashboard') }}" class="text-link">Dashboard</a>@else<a href="{{ route('login') }}" class="text-link">Log in</a>@endauth
                <a href="{{ route('booking.create') }}" class="button button-small">Book a taxi <span class="arrow">↗</span></a>
            </div>
            <button type="button" class="menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle navigation"><span></span><span></span></button>
            <nav x-cloak x-show="open" class="mobile-nav" aria-label="Mobile navigation">
                <a href="{{ url('/') }}#services" @click="open = false">Services</a>
                <a href="{{ route('fleet.index') }}" aria-current="page" @click="open = false">Our fleet</a>
                <a href="{{ route('booking.create') }}" @click="open = false">Book a ride</a>
                <a href="{{ url('/') }}#locations" @click="open = false">Oulu routes</a>
                <a href="{{ url('/') }}#contact" @click="open = false">Contact</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="fleet-hero">
            <img class="fleet-hero-image" src="{{ asset('images/taxi-city.jpg') }}" alt="Taxi ready for a city journey in Oulu">
            <div class="fleet-hero-copy">
                <p class="fleet-eyebrow">The Arctic Hero fleet · Oulu</p>
                <h1>A ride that<br><span>fits your day.</span></h1>
                <p>From everyday city trips to airport pickups and group journeys, choose the space and comfort you need.</p>
                <a href="#fleet-options" class="fleet-hero-link">Explore the vehicles <span>↓</span></a>
            </div>
            <span class="fleet-hero-caption">Local rides · Oulu and nearby</span>
        </section>

        <section class="fleet-intro" aria-label="Fleet overview">
            <p>{{ $vehicles->count() }} {{ $vehicles->count() === 1 ? 'vehicle' : 'vehicles' }} available</p>
            <span>Up to {{ $maximumPassengers }} passengers</span>
            <span>Clear fare estimates</span>
            <span>City and airport trips</span>
        </section>

        <section id="fleet-options" class="fleet-options">
            <div class="fleet-options-heading">
                <div><p class="fleet-eyebrow">Choose your vehicle</p><h2>Find your fit.</h2></div>
                <p>Every ride starts with the right amount of room. Compare the options and see an estimated fare when you plan your route.</p>
            </div>

            @if ($vehicles->isEmpty())
                <div class="fleet-empty"><h2>Fleet details are being updated.</h2><p>Contact our team to plan your ride.</p><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a></div>
            @else
                <div class="fleet-vehicle-list">
                    @foreach ($vehicles as $index => $vehicle)
                        @php($vehicleType = $vehicle->vehicleType)
                        <article class="fleet-vehicle-row">
                            <figure class="fleet-vehicle-image"><img src="{{ $vehicle->display_image_url }}" alt="{{ trim(($vehicle->brand ?? '').' '.($vehicle->model ?? '')) ?: $vehicle->name }}" loading="lazy"><figcaption>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} / {{ $vehicles->count() }}</figcaption></figure>
                            <div class="fleet-vehicle-copy">
                                <p class="fleet-vehicle-category">{{ $vehicleType->passenger_capacity > 4 ? 'Group travel' : ($vehicleType->base_fare > 10 ? 'Extra comfort' : 'Everyday ride') }}</p>
                                <h3>{{ $vehicle->name }}</h3>
                                <p class="fleet-vehicle-description">{{ trim(($vehicle->brand ?? '').' '.($vehicle->model ?? '')) }} · {{ $vehicleType->name }}. {{ $vehicleType->description }}</p>
                                <div class="fleet-capacities"><div><span>Passengers</span><strong>Up to {{ $vehicleType->passenger_capacity }}</strong></div><div><span>Luggage</span><strong>{{ $vehicleType->luggage_capacity }} {{ $vehicleType->luggage_capacity === 1 ? 'bag' : 'bags' }}</strong></div></div>
                                <div class="fleet-price"><span>Base fare</span><strong>€{{ number_format((float) $vehicleType->base_fare, 2) }}</strong><small>+ €{{ number_format((float) $vehicleType->per_km_rate, 2) }}/km · €{{ number_format((float) $vehicleType->per_minute_rate, 2) }}/min</small></div>
                                <a class="fleet-book-link" href="{{ route('booking.create', ['vehicle_type_id' => $vehicleType->id]) }}">Choose this vehicle <span>↗</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="fleet-cta">
            <div><p class="fleet-eyebrow">Your next trip</p><h2>Ready when you are.</h2><p>Pick your route and pickup time. We’ll help you take it from there.</p></div>
            <a class="button" href="{{ route('booking.create') }}">Plan a ride <span class="arrow">↗</span></a>
        </section>
    </main>

    <footer class="site-footer">
        <div class="fleet-footer-row"><a href="{{ url('/') }}" class="brand light-brand"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a><span>Oulu, Finland</span><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a><span>© {{ date('Y') }} Arctic Hero</span></div>
    </footer>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Oulu Routes | {{ config('company.name', 'Arctic Hero') }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-shell routes-page">
    <header class="site-header" x-data="{ open: false }">
        <div class="utility-bar"><div class="utility-inner"><div class="utility-contact"><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a></div><div class="utility-social"><span>OULU, FINLAND</span><span>CITY · AIRPORT · GROUP</span></div></div></div>
        <div class="header-inner">
            <a href="{{ url('/') }}" class="brand" aria-label="Arctic Hero home"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a>
            <nav class="desktop-nav" aria-label="Main navigation">
                <a href="{{ url('/') }}#services">Services</a>
                <a href="{{ route('fleet.index') }}">Our fleet</a>
                <a href="{{ route('booking.create') }}">Book a ride</a>
                <a href="{{ route('routes.index') }}" aria-current="page">Oulu routes</a>
                <a href="{{ route('contact.index') }}">Contact</a>
            </nav>
            <div class="header-actions">
                @auth<a href="{{ route('dashboard') }}" class="text-link">Dashboard</a>@else<a href="{{ route('login') }}" class="text-link">Log in</a>@endauth
                <a href="{{ route('booking.create') }}" class="button button-small">Book a taxi <span class="arrow">↗</span></a>
            </div>
            <button type="button" class="menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle navigation"><span></span><span></span></button>
            <nav x-cloak x-show="open" class="mobile-nav" aria-label="Mobile navigation">
                <a href="{{ url('/') }}#services" @click="open = false">Services</a>
                <a href="{{ route('fleet.index') }}" @click="open = false">Our fleet</a>
                <a href="{{ route('booking.create') }}" @click="open = false">Book a ride</a>
                <a href="{{ route('routes.index') }}" aria-current="page" @click="open = false">Oulu routes</a>
                <a href="{{ route('contact.index') }}" @click="open = false">Contact</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="routes-hero">
            <img src="{{ asset('images/coastal-journey.jpg') }}" alt="Oulu coastline and city seen from above">
            <div class="routes-hero-copy">
                <p class="routes-eyebrow">Local knowledge · Oulu, Finland</p>
                <h1>Oulu is closer<br><span>than you think.</span></h1>
                <p>From the city centre to the airport, waterfront, and neighbourhood stops, find your destination and plan the ride.</p>
                <a href="#route-planner" class="routes-hero-link">Explore local stops <span>↓</span></a>
            </div>
            <span class="routes-hero-caption">City · Airport · Coast</span>
        </section>

        <section class="routes-summary" aria-label="Oulu route overview">
            <strong>{{ $locations->count() }} local stops</strong><span>City centre</span><span>Airport connections</span><span>Coastal destinations</span>
        </section>

        <section class="routes-explorer" id="route-planner">
            <div class="routes-explorer-heading"><div><p class="routes-eyebrow">Navigate the city</p><h2>Find your stop.</h2></div><p>Select a location to see it on the map. Choose a suggested route to start a booking with both places filled in.</p></div>
            @if ($locations->isEmpty())
                <div class="routes-empty"><h2>Local stops are being updated.</h2><p>Contact our team and we’ll help plan your route.</p><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a></div>
            @else
                <div class="routes-map-layout">
                    <div class="routes-stop-list" aria-label="Oulu locations">
                        @foreach ($locations as $location)
                            <button class="routes-stop" type="button" data-route-location="{{ $location->id }}" aria-label="Show {{ $location->name }} on map">
                                <span class="routes-stop-index">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="routes-stop-copy"><strong>{{ $location->name }}</strong><small>{{ $location->address }}</small></span>
                                <span class="routes-stop-category">{{ \Illuminate\Support\Str::headline($location->category) }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="routes-map-panel"><div id="routes-map" role="img" aria-label="Interactive map of Oulu locations"></div><div class="routes-map-note"><span class="routes-map-dot"></span> Active Arctic Hero stops</div></div>
                </div>
            @endif
        </section>

        @if ($popularRoutes->isNotEmpty())
            <section class="popular-routes">
                <div class="popular-routes-heading"><div><p class="routes-eyebrow">Start from the centre</p><h2>Popular routes.</h2></div><p>Distance and time are estimates. Your booking will show its own fare estimate before you submit.</p></div>
                <div class="popular-route-list">
                    @foreach ($popularRoutes as $route)
                        <article class="popular-route" data-route-pair data-origin-id="{{ $route['origin']->id }}" data-destination-id="{{ $route['destination']->id }}">
                            <div class="popular-route-count">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="popular-route-main"><p>{{ $route['origin']->name }} <span>→</span></p><h3>{{ $route['destination']->name }}</h3><small>{{ $route['destination']->address }}</small></div>
                            <div class="popular-route-estimate"><strong>~{{ number_format($route['distance_km'], 1) }} km</strong><span>approx. {{ max(3, (int) ceil($route['distance_km'] / 35 * 60)) }} min</span></div>
                            <div class="popular-route-actions"><button type="button" data-show-route>Show route</button><a href="{{ route('booking.create', ['pickup_address' => $route['origin']->address, 'destination_address' => $route['destination']->address]) }}">Plan this ride <span>↗</span></a></div>
                        </article>
                    @endforeach
                </div>
                <p class="routes-estimate-note">Route distances are approximate estimates based on map coordinates; actual road distance and fare may vary.</p>
            </section>
        @endif

        <section class="routes-cta"><div><p class="routes-eyebrow">Ready when you are</p><h2>Let’s get you there.</h2><p>Set your pickup, choose a vehicle, and see your estimated fare.</p></div><a href="{{ route('booking.create') }}" class="button">Plan your ride <span class="arrow">↗</span></a></section>
    </main>

    <footer class="site-footer"><div class="fleet-footer-row"><a href="{{ url('/') }}" class="brand light-brand"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a><span>Oulu, Finland</span><a href="{{ route('contact.index') }}">Contact our team</a><span>© {{ date('Y') }} Arctic Hero</span></div></footer>
    <script type="application/json" data-route-locations>@json($mapLocations)</script>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking received | {{ config('company.name', 'Arctic Hero') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="booking-page">
    <header class="booking-header">
        <a href="{{ url('/') }}" class="booking-brand"><span class="booking-brand-mark">A</span><span>ARCTIC <b>HERO</b></span></a>
        <div class="booking-header-actions"><span>Oulu, Finland</span><a href="{{ route('dashboard') }}">Dashboard</a></div>
    </header>
    <main class="booking-confirmation">
        <div class="confirmation-mark" aria-hidden="true">✓</div>
        <p class="booking-eyebrow">REQUEST RECEIVED · {{ $booking->booking_code }}</p>
        <h1>Your ride is in motion.</h1>
        <p class="confirmation-lead">We’ve received your request. Our team will review the details and contact you to confirm the ride.</p>
        <section class="confirmation-sheet" aria-label="Booking summary">
            <div class="confirmation-route"><div><span>Pickup</span><strong>{{ $booking->pickup_address }}</strong></div><div><span>Destination</span><strong>{{ $booking->destination_address }}</strong></div></div>
            <dl><div><dt>Pickup time</dt><dd>{{ $booking->booking_date->format('D, M j, Y') }} · {{ \Illuminate\Support\Carbon::parse($booking->booking_time)->format('g:i A') }}</dd></div><div><dt>Vehicle</dt><dd>{{ $booking->vehicleType->name }} · {{ $booking->passengers }} {{ $booking->passengers === 1 ? 'passenger' : 'passengers' }}</dd></div><div><dt>Estimated fare</dt><dd>€{{ number_format((float) $booking->total_fare, 2) }}</dd></div><div><dt>Status</dt><dd><span class="confirmation-status">{{ \Illuminate\Support\Str::headline($booking->status) }}</span></dd></div></dl>
            <p class="confirmation-code">Booking reference <strong>{{ $booking->booking_code }}</strong></p>
        </section>
        <div class="confirmation-actions"><a class="booking-next" href="{{ route('dashboard') }}">View my rides <span>→</span></a><a href="{{ url('/') }}">Return to Arctic Hero</a></div>
    </main>
    <footer class="booking-footer"><a href="mailto:{{ config('company.email') }}">Questions? {{ config('company.email') }}</a><span>ARCTIC HERO · OULU</span></footer>
</body>
</html>
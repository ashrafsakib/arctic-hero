<x-app-layout>
    <div class="customer-dashboard">
        <div class="dashboard-wrap">
            <div class="booking-customer-back"><a href="{{ route('dashboard') }}">← My rides</a></div>
            <section class="booking-customer-heading">
                <div><p class="dashboard-eyebrow">Booking {{ $booking->booking_code }}</p><h1>Ride details</h1><p>{{ $booking->booking_date->format('l, M j, Y') }} · {{ \Illuminate\Support\Carbon::parse($booking->booking_time)->format('g:i A') }}</p></div>
                <span class="dashboard-status" data-status="{{ $booking->status }}">{{ \Illuminate\Support\Str::headline($booking->status) }}</span>
            </section>

            <div class="booking-customer-grid">
                <section class="booking-customer-panel">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Your journey</p><h2>Route</h2></div></div>
                    <div class="booking-customer-route"><div><span>Pickup</span><strong>{{ $booking->pickup_address }}</strong></div><div><span>Destination</span><strong>{{ $booking->destination_address }}</strong></div></div>
                    <dl class="booking-customer-facts"><div><dt>Vehicle</dt><dd>{{ $booking->vehicleType->name }}</dd></div><div><dt>Passengers</dt><dd>{{ $booking->passengers }}</dd></div><div><dt>Luggage</dt><dd>{{ $booking->luggage_count }} bags</dd></div><div><dt>Estimate</dt><dd>€{{ number_format((float) $booking->total_fare, 2) }}</dd></div></dl>
                    @if ($booking->customer_notes)<div class="booking-customer-note"><span>Your note</span><p>{{ $booking->customer_notes }}</p></div>@endif
                </section>

                <section class="booking-customer-panel">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Live updates</p><h2>Booking status</h2></div></div>
                    <ol class="booking-customer-history">@forelse ($statusHistories as $history)<li><span class="booking-history-dot"></span><div><strong>{{ \Illuminate\Support\Str::headline($history->status) }}</strong><time datetime="{{ $history->created_at->toIso8601String() }}">{{ $history->created_at->format('M j, Y · g:i A') }}</time></div></li>@empty<li><div><strong>{{ \Illuminate\Support\Str::headline($booking->status) }}</strong><time datetime="{{ $booking->created_at->toIso8601String() }}">{{ $booking->created_at->format('M j, Y · g:i A') }}</time></div></li>@endforelse</ol>
                    <div class="booking-customer-help"><span>Need help with this ride?</span><a href="mailto:{{ config('company.email') }}?subject={{ rawurlencode('Booking '.$booking->booking_code) }}">{{ config('company.email') }}</a><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a></div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
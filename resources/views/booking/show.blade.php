<x-app-layout>
    <div class="customer-dashboard">
        <div class="dashboard-wrap">
            <div class="booking-customer-back"><a href="{{ route('dashboard') }}">← My rides</a></div>
            @if (session('success'))<p class="booking-customer-panel" role="status">{{ session('success') }}</p>@endif
            @if ($errors->any())<p class="booking-customer-panel" role="alert">{{ $errors->first() }}</p>@endif
            <section class="booking-customer-heading">
                <div><p class="dashboard-eyebrow">Booking {{ $booking->booking_code }}</p><h1>Ride details</h1><p>{{ $booking->booking_date->format('l, M j, Y') }} · {{ \Illuminate\Support\Carbon::parse($booking->booking_time)->format('g:i A') }}</p></div>
                <span class="dashboard-status" data-status="{{ $booking->status }}">{{ \Illuminate\Support\Str::headline($booking->status) }}</span>
            </section>

            <div class="booking-customer-grid">
                <section class="booking-customer-panel">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Your journey</p><h2>Route</h2></div></div>
                    <div class="booking-customer-route"><div><span>Pickup</span><strong>{{ $booking->pickup_address }}</strong></div><div><span>Destination</span><strong>{{ $booking->destination_address }}</strong></div></div>
                    <dl class="booking-customer-facts"><div><dt>Vehicle</dt><dd>{{ $booking->vehicleType->name }}</dd></div><div><dt>Passengers</dt><dd>{{ $booking->passengers }}</dd></div><div><dt>Luggage</dt><dd>{{ $booking->luggage_count }} bags</dd></div><div><dt>Pricing surcharge</dt><dd>€{{ number_format((float) $booking->extra_charge, 2) }}</dd></div><div><dt>Estimate</dt><dd>€{{ number_format((float) $booking->total_fare, 2) }}</dd></div></dl>
                    @if ($booking->customer_notes)<div class="booking-customer-note"><span>Your note</span><p>{{ $booking->customer_notes }}</p></div>@endif
                </section>

                <section class="booking-customer-panel">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Live updates</p><h2>Booking status</h2></div></div>
                    <ol class="booking-customer-history">@forelse ($statusHistories as $history)<li><span class="booking-history-dot"></span><div><strong>{{ \Illuminate\Support\Str::headline($history->status) }}</strong><time datetime="{{ $history->created_at->toIso8601String() }}">{{ $history->created_at->format('M j, Y · g:i A') }}</time></div></li>@empty<li><div><strong>{{ \Illuminate\Support\Str::headline($booking->status) }}</strong><time datetime="{{ $booking->created_at->toIso8601String() }}">{{ $booking->created_at->format('M j, Y · g:i A') }}</time></div></li>@endforelse</ol>
                    <div class="booking-customer-help"><span>Need help with this ride?</span><a href="mailto:{{ config('company.email') }}?subject={{ rawurlencode('Booking '.$booking->booking_code) }}">{{ config('company.email') }}</a><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a></div>
                </section>
            </div>

            @if ($booking->canCustomerRequestCancellation())
                <section class="booking-customer-panel" aria-labelledby="cancellation-heading">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Plans changed?</p><h2 id="cancellation-heading">Request cancellation</h2></div></div>
                    <p>Our team will review your request and update this booking's status.</p>
                    <form class="mt-4 space-y-3" action="{{ route('bookings.cancellation.store', $booking) }}" method="post">
                        @csrf
                        <label class="block">Reason (optional)
                            <textarea class="mt-1 block w-full" name="reason" rows="3" maxlength="2000">{{ old('reason') }}</textarea>
                        </label>
                        <button class="booking-next" type="submit">Send cancellation request</button>
                    </form>
                </section>
            @elseif ($booking->status === \App\Models\Booking::CANCELLATION_REQUESTED)
                <p class="booking-customer-panel" role="status">Your cancellation request is waiting for review.</p>
            @endif

            @if ($booking->canCustomerSubmitReview() && ! $booking->review)
                <section class="booking-customer-panel" aria-labelledby="review-heading">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Share your experience</p><h2 id="review-heading">Review your ride</h2></div></div>
                    <form class="mt-4 space-y-3" action="{{ route('bookings.review.store', $booking) }}" method="post">
                        @csrf
                        <label class="block">Rating
                            <select class="mt-1 block w-full" name="rating" required>
                                <option value="">Select a rating</option>
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</option>
                                @endfor
                            </select>
                        </label>
                        <label class="block">Comment (optional)
                            <textarea class="mt-1 block w-full" name="comment" rows="4" maxlength="2000">{{ old('comment') }}</textarea>
                        </label>
                        <button class="booking-next" type="submit">Submit review</button>
                    </form>
                </section>
            @elseif ($booking->review)
                <section class="booking-customer-panel" aria-labelledby="review-heading">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Thanks for your feedback</p><h2 id="review-heading">Your review</h2></div></div>
                    <p>{{ $booking->review->rating }} / 5 stars</p>
                    @if ($booking->review->comment)<p>{{ $booking->review->comment }}</p>@endif
                    @if ($booking->review->admin_response)<div class="booking-customer-note"><span>Response from {{ $companySettings['name'] }}</span><p>{{ $booking->review->admin_response }}</p></div>@endif
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
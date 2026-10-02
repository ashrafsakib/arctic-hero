<x-app-layout>
    <div class="customer-dashboard">
        <div class="dashboard-wrap">
            <section class="dashboard-welcome">
                <div>
                    <p class="dashboard-eyebrow">Your Arctic Hero account <span>Oulu, Finland</span></p>
                    <h1>Welcome back, {{ $firstName }}.</h1>
                    <p>Your next ride is just a few details away.</p>
                </div>
                <a class="dashboard-primary" href="{{ url('/') }}#book">Book a ride <span>↗</span></a>
            </section>

            <section class="dashboard-metrics" aria-label="Ride summary">
                <article><span>Upcoming rides</span><strong>{{ $upcomingCount }}</strong></article>
                <article><span>Completed trips</span><strong>{{ $completedTrips }}</strong></article>
                <article><span>All bookings</span><strong>{{ $totalTrips }}</strong></article>
            </section>

            <div class="dashboard-content">
                <section class="dashboard-trips">
                    <div class="dashboard-section-heading">
                        <div><p class="dashboard-eyebrow">Your travel plans</p><h2>Upcoming rides</h2></div>
                        <a href="{{ url('/') }}#book">Book another <span>↗</span></a>
                    </div>

                    @if ($upcomingBookings->isEmpty())
                        <div class="dashboard-empty">
                            <span class="dashboard-empty-mark">A</span>
                            <h3>No upcoming rides</h3>
                            <p>When you book a ride, its pickup details will appear here.</p>
                            <a class="dashboard-primary" href="{{ url('/') }}#book">Plan your first ride <span>↗</span></a>
                        </div>
                    @else
                        <div class="dashboard-bookings">
                            @foreach ($upcomingBookings as $booking)
                                <article class="dashboard-booking">
                                    <time class="dashboard-date" datetime="{{ $booking->booking_date->toDateString() }}">
                                        <span>{{ $booking->booking_date->format('M') }}</span>
                                        <strong>{{ $booking->booking_date->format('d') }}</strong>
                                    </time>
                                    <div class="dashboard-route">
                                        <p>{{ $booking->pickup_address }} <span>→</span> {{ $booking->destination_address }}</p>
                                        <span>{{ \Illuminate\Support\Carbon::parse($booking->booking_time)->format('g:i A') }} · {{ $booking->vehicleType->name }}</span>
                                    </div>
                                    <div class="dashboard-booking-meta">
                                        <span class="dashboard-status" data-status="{{ $booking->status }}">{{ \Illuminate\Support\Str::headline($booking->status) }}</span>
                                        <strong>€{{ number_format((float) $booking->total_fare, 2) }}</strong>
                                        <small>{{ $booking->passengers }} {{ $booking->passengers === 1 ? 'passenger' : 'passengers' }}</small>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>

                <aside class="dashboard-account">
                    <div class="dashboard-section-heading"><div><p class="dashboard-eyebrow">Your details</p><h2>Account</h2></div></div>
                    <dl>
                        <div><dt>Name</dt><dd>{{ $user->name }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $user->phone ?: 'Add a phone number' }}</dd></div>
                    </dl>
                    <a class="dashboard-account-link" href="{{ route('profile.edit') }}">Manage your profile <span>↗</span></a>
                    <div class="dashboard-help"><span>Need help with a ride?</span><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a></div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>

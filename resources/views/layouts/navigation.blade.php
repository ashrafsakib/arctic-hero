<nav class="customer-nav" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="customer-nav-inner">
        <a href="{{ route('dashboard') }}" class="customer-brand" aria-label="{{ config('company.name') }} dashboard"><span class="customer-brand-mark">A</span><span>ARCTIC <b>HERO</b></span></a>
        <div class="customer-nav-links">
            @if (Auth::user()->isAdmin())
                <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Admin dashboard</a>
                <a class="{{ request()->routeIs('admin.vehicles.*') ? 'is-active' : '' }}" href="{{ route('admin.vehicles.index') }}">Fleet</a>
                <a class="{{ request()->routeIs('admin.pricing-rules.*') ? 'is-active' : '' }}" href="{{ route('admin.pricing-rules.index') }}">Pricing</a>
                <a class="{{ request()->routeIs('admin.locations.*') ? 'is-active' : '' }}" href="{{ route('admin.locations.index') }}">Locations</a>
                <a class="{{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}" href="{{ route('admin.reviews.index') }}">Reviews</a>
                <a class="{{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}" href="{{ route('admin.reports.index') }}">Reports</a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}" href="{{ route('admin.settings.edit') }}">Settings</a>
            @else
                <a class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a><a href="{{ route('booking.create') }}">Book a ride</a><a href="{{ url('/#services') }}">Services</a><a href="{{ route('fleet.index') }}">Our fleet</a><a href="{{ route('routes.index') }}">Oulu routes</a>
            @endif
        </div>
        <div class="customer-nav-actions">
            <a class="customer-profile" href="{{ route('profile.edit') }}"><span>{{ substr(Auth::user()->name, 0, 1) }}</span>{{ Auth::user()->name }}</a>
            <form method="POST" action="{{ route('logout', absolute: false) }}">@csrf<button type="submit">Log out</button></form>
        </div>
        <button type="button" class="customer-menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-controls="customer-mobile-nav" aria-label="Toggle account navigation"><span></span><span></span></button>
    </div>
    <div id="customer-mobile-nav" x-cloak x-show="open" class="customer-mobile-nav">
        @if (Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="open = false">Admin dashboard</a><a href="{{ route('admin.vehicles.index') }}" @click="open = false">Fleet management</a><a href="{{ route('admin.pricing-rules.index') }}" @click="open = false">Pricing rules</a><a href="{{ route('admin.locations.index') }}" @click="open = false">Locations</a><a href="{{ route('admin.reviews.index') }}" @click="open = false">Reviews</a><a href="{{ route('admin.reports.index') }}" @click="open = false">Reports</a><a href="{{ route('admin.settings.edit') }}" @click="open = false">Company settings</a>
        @else
            <a href="{{ route('dashboard') }}" @click="open = false">Dashboard</a><a href="{{ route('booking.create') }}" @click="open = false">Book a ride</a><a href="{{ url('/#services') }}" @click="open = false">Services</a><a href="{{ route('fleet.index') }}" @click="open = false">Our fleet</a><a href="{{ route('routes.index') }}" @click="open = false">Oulu routes</a><a href="{{ route('profile.edit') }}" @click="open = false">Manage profile</a>
        @endif
        <form method="POST" action="{{ route('logout', absolute: false) }}">@csrf<button type="submit">Log out</button></form>
    </div>
</nav>

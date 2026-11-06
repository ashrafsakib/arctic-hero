<aside class="admin-sidebar" aria-label="Admin navigation">
    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand" aria-label="Arctic Hero admin home">
        <span class="admin-sidebar-mark">A</span>
        <span>ARCTIC <b>HERO</b><small>ADMIN PORTAL</small></span>
    </a>

    <p class="admin-sidebar-label">Workspace</p>
    <nav class="admin-sidebar-nav">
        <a class="{{ request()->routeIs('admin.dashboard') ? 'is-current' : '' }}" href="{{ route('admin.dashboard') }}"><span aria-hidden="true">01</span>Overview</a>
        <a class="{{ request()->routeIs('admin.vehicles.*') ? 'is-current' : '' }}" href="{{ route('admin.vehicles.index') }}"><span aria-hidden="true">02</span>Fleet management</a>
        <a class="{{ request()->routeIs('admin.contact-submissions.*') ? 'is-current' : '' }}" href="{{ route('admin.contact-submissions.index') }}"><span aria-hidden="true">03</span>Contact inbox</a>
        <a class="{{ request()->routeIs('admin.bookings.*') ? 'is-current' : '' }}" href="{{ route('admin.bookings.index') }}"><span aria-hidden="true">04</span>Bookings</a>
        <a class="{{ request()->routeIs('admin.pricing-rules.*') ? 'is-current' : '' }}" href="{{ route('admin.pricing-rules.index') }}"><span aria-hidden="true">05</span>Pricing rules</a>
        <a class="{{ request()->routeIs('admin.locations.*') ? 'is-current' : '' }}" href="{{ route('admin.locations.index') }}"><span aria-hidden="true">06</span>Locations &amp; map pins</a>
        <a class="{{ request()->routeIs('admin.reviews.*') ? 'is-current' : '' }}" href="{{ route('admin.reviews.index') }}"><span aria-hidden="true">07</span>Review moderation</a>
        <a class="{{ request()->routeIs('admin.reports.*') ? 'is-current' : '' }}" href="{{ route('admin.reports.index') }}"><span aria-hidden="true">08</span>Reports</a>
        <a class="{{ request()->routeIs('admin.settings.*') ? 'is-current' : '' }}" href="{{ route('admin.settings.edit') }}"><span aria-hidden="true">09</span>Company settings</a>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="{{ route('profile.edit') }}" class="admin-sidebar-user"><span>{{ substr(Auth::user()->name, 0, 1) }}</span><span>{{ Auth::user()->name }}<small>Administrator</small></span></a>
        <a href="{{ url('/') }}" class="admin-sidebar-public">View public site <span aria-hidden="true">↗</span></a>
        <form method="POST" action="{{ route('logout', absolute: false) }}">
            @csrf
            <button type="submit">Log out</button>
        </form>
    </div>
</aside>
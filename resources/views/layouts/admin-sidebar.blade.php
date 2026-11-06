<aside class="admin-sidebar" aria-label="Admin navigation">
    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand" aria-label="Arctic Hero admin home">
        <span class="admin-sidebar-mark">A</span>
        <span>ARCTIC <b>HERO</b><small>ADMIN PORTAL</small></span>
    </a>

    <p id="admin-workspace-label" class="admin-sidebar-label">Workspace</p>
    <nav class="admin-sidebar-nav" aria-labelledby="admin-workspace-label">
        <a @class(['is-current' => request()->routeIs('admin.dashboard')]) @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif href="{{ route('admin.dashboard') }}"><span aria-hidden="true">01</span>Overview</a>
        <a @class(['is-current' => request()->routeIs('admin.vehicles.*')]) @if (request()->routeIs('admin.vehicles.*')) aria-current="page" @endif href="{{ route('admin.vehicles.index') }}"><span aria-hidden="true">02</span>Fleet management</a>
        <a @class(['is-current' => request()->routeIs('admin.contact-submissions.*')]) @if (request()->routeIs('admin.contact-submissions.*')) aria-current="page" @endif href="{{ route('admin.contact-submissions.index') }}"><span aria-hidden="true">03</span>Contact inbox</a>
        <a @class(['is-current' => request()->routeIs('admin.bookings.*')]) @if (request()->routeIs('admin.bookings.*')) aria-current="page" @endif href="{{ route('admin.bookings.index') }}"><span aria-hidden="true">04</span>Bookings</a>
        <a @class(['is-current' => request()->routeIs('admin.pricing-rules.*')]) @if (request()->routeIs('admin.pricing-rules.*')) aria-current="page" @endif href="{{ route('admin.pricing-rules.index') }}"><span aria-hidden="true">05</span>Pricing rules</a>
        <a @class(['is-current' => request()->routeIs('admin.locations.*')]) @if (request()->routeIs('admin.locations.*')) aria-current="page" @endif href="{{ route('admin.locations.index') }}"><span aria-hidden="true">06</span>Locations &amp; map pins</a>
        <a @class(['is-current' => request()->routeIs('admin.reviews.*')]) @if (request()->routeIs('admin.reviews.*')) aria-current="page" @endif href="{{ route('admin.reviews.index') }}"><span aria-hidden="true">07</span>Review moderation</a>
        <a @class(['is-current' => request()->routeIs('admin.reports.*')]) @if (request()->routeIs('admin.reports.*')) aria-current="page" @endif href="{{ route('admin.reports.index') }}"><span aria-hidden="true">08</span>Reports</a>
        <a @class(['is-current' => request()->routeIs('admin.settings.*')]) @if (request()->routeIs('admin.settings.*')) aria-current="page" @endif href="{{ route('admin.settings.edit') }}"><span aria-hidden="true">09</span>Company settings</a>
        <a @class(['is-current' => request()->routeIs('admin.customers.*')]) @if (request()->routeIs('admin.customers.*')) aria-current="page" @endif href="{{ route('admin.customers.index') }}"><span aria-hidden="true">10</span>Customers</a>
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
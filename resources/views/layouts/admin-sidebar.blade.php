<aside class="admin-sidebar" aria-label="Admin navigation">
    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand" aria-label="Arctic Hero admin home">
        <span class="admin-sidebar-mark">A</span>
        <span>ARCTIC <b>HERO</b><small>ADMIN PORTAL</small></span>
    </a>

    <p class="admin-sidebar-label">Workspace</p>
    <nav class="admin-sidebar-nav">
        <a class="{{ request()->routeIs('admin.dashboard') ? 'is-current' : '' }}" href="{{ route('admin.dashboard') }}"><span aria-hidden="true">01</span>Overview</a>
        <a class="{{ request()->routeIs('admin.vehicles.*') ? 'is-current' : '' }}" href="{{ route('admin.vehicles.index') }}"><span aria-hidden="true">02</span>Fleet management</a>
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
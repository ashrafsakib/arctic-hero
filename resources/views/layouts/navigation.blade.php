<nav class="customer-nav" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="customer-nav-inner">
        <a href="{{ route('dashboard') }}" class="customer-brand" aria-label="{{ config('company.name') }} dashboard"><span class="customer-brand-mark">A</span><span>ARCTIC <b>HERO</b></span></a>
        <div class="customer-nav-links"><a class="is-active" href="{{ route('dashboard') }}">Dashboard</a><a href="{{ url('/#services') }}">Services</a><a href="{{ url('/#fleet') }}">Our fleet</a><a href="{{ url('/#locations') }}">Oulu routes</a></div>
        <div class="customer-nav-actions">
            <a class="customer-profile" href="{{ route('profile.edit') }}"><span>{{ substr(Auth::user()->name, 0, 1) }}</span>{{ Auth::user()->name }}</a>
            <form method="POST" action="{{ route('logout', absolute: false) }}">@csrf<button type="submit">Log out</button></form>
        </div>
        <button type="button" class="customer-menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-controls="customer-mobile-nav" aria-label="Toggle account navigation"><span></span><span></span></button>
    </div>
    <div id="customer-mobile-nav" x-cloak x-show="open" class="customer-mobile-nav">
        <a href="{{ route('dashboard') }}" @click="open = false">Dashboard</a><a href="{{ url('/#services') }}" @click="open = false">Services</a><a href="{{ url('/#fleet') }}" @click="open = false">Our fleet</a><a href="{{ route('profile.edit') }}" @click="open = false">Manage profile</a>
        <form method="POST" action="{{ route('logout', absolute: false) }}">@csrf<button type="submit">Log out</button></form>
    </div>
</nav>

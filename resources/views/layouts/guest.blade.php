@php
    $authContent = match (true) {
        request()->routeIs('register') => ['label' => 'Join Arctic Hero', 'title' => 'Create your account', 'description' => 'Set up your profile to continue with a ride request.'],
        request()->routeIs('login') => ['label' => 'Good to see you', 'title' => 'Welcome back', 'description' => 'Sign in to continue to your Arctic Hero account.'],
        request()->routeIs('password.request') => ['label' => 'Account help', 'title' => 'Reset your password', 'description' => 'Enter your email and we’ll send instructions to reset your password.'],
        request()->routeIs('password.reset') => ['label' => 'Account help', 'title' => 'Choose a new password', 'description' => 'Create a new password for your account.'],
        request()->routeIs('password.confirm') => ['label' => 'Account security', 'title' => 'Confirm your password', 'description' => 'Confirm your password to continue.'],
        request()->routeIs('verification.notice') => ['label' => 'One last step', 'title' => 'Verify your email', 'description' => 'Check your inbox for a verification link.'],
        default => ['label' => 'Account access', 'title' => 'Your Arctic Hero account', 'description' => 'Manage your account and continue with your ride.'],
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('company.name', 'Arctic Hero') }} | {{ $authContent['title'] }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-shell">
        <header class="site-header" x-data="{ open: false }">
            <div class="utility-bar">
                <div class="utility-inner">
                    <div class="utility-contact"><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a></div>
                    <div class="utility-social"><span>OULU, FINLAND</span><span>CITY · AIRPORT · GROUP</span></div>
                </div>
            </div>
            <div class="header-inner">
                <a href="{{ url('/') }}" class="brand" aria-label="Arctic Hero home"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a>
                <nav class="desktop-nav" aria-label="Main navigation">
                    <a href="{{ url('/') }}#services">Services</a>
                    <a href="{{ url('/') }}#fleet">Our fleet</a>
                    <a href="{{ url('/') }}#locations">Oulu routes</a>
                    <a href="{{ url('/') }}#contact">Contact</a>
                </nav>
                <div class="header-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-link">Dashboard</a>
                    @else
                        @if (request()->routeIs('login'))
                            <a href="{{ route('register') }}" class="text-link">Create account</a>
                        @else
                            <a href="{{ route('login') }}" class="text-link">Log in</a>
                        @endif
                    @endauth
                    <a href="{{ route('register') }}" class="button button-small">Book a taxi <span class="arrow">↗</span></a>
                </div>
                <button type="button" class="menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle navigation"><span></span><span></span></button>
                <nav x-cloak x-show="open" class="mobile-nav" aria-label="Mobile navigation">
                    <a href="{{ url('/') }}#services" @click="open = false">Services</a><a href="{{ url('/') }}#fleet" @click="open = false">Our fleet</a><a href="{{ url('/') }}#locations" @click="open = false">Oulu routes</a><a href="{{ url('/') }}#contact" @click="open = false">Contact</a>
                    @guest
                        <a href="{{ route('login') }}" @click="open = false">Log in</a>
                    @endguest
                </nav>
            </div>
        </header>

        <main class="auth-main">
            <div class="auth-layout">
                <aside class="auth-art">
                    <img src="/images/taxi-night.jpg" alt="Yellow taxi ready for a city ride">
                    <div class="auth-art-content">
                        <p class="auth-eyebrow">{{ $authContent['label'] }} <span>· Oulu</span></p>
                        <h1>Your next ride<br><span>starts here.</span></h1>
                        <p>Local city rides, airport transfers and more room for the people travelling with you.</p>
                        <div class="auth-art-note"><span class="auth-art-mark">A</span><span><strong>Arctic Hero</strong><small>Your local taxi service</small></span></div>
                    </div>
                </aside>
                <section class="auth-panel" aria-labelledby="auth-title">
                    <div class="auth-panel-heading">
                        <p class="auth-eyebrow">{{ $authContent['label'] }}</p>
                        <h2 id="auth-title">{{ $authContent['title'] }}</h2>
                        <p>{{ $authContent['description'] }}</p>
                    </div>
                    <div class="auth-card">
                        {{ $slot }}
                    </div>
                    @if (request()->routeIs('login'))
                        <p class="auth-switch">New to Arctic Hero? <a href="{{ route('register') }}">Create an account</a></p>
                    @endif
                </section>
            </div>
        </main>

        <footer class="site-footer auth-footer">
            <div class="footer-main">
                <div class="footer-brand"><a href="{{ url('/') }}" class="brand light-brand"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a><p>Your local taxi for city trips, airport runs and journeys around Oulu.</p></div>
                <div class="footer-links">
                    <div><p>Explore</p><a href="{{ url('/') }}#services">Ride options</a><a href="{{ url('/') }}#fleet">Our fleet</a><a href="{{ url('/') }}#locations">Oulu routes</a></div>
                    <div><p>Account</p><a href="{{ route('login') }}">Log in</a><a href="{{ route('register') }}">Create account</a><a href="{{ url('/') }}#book">Book a taxi</a></div>
                    <div><p>Contact</p><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="{{ url('/') }}#locations">Oulu, Finland</a></div>
                </div>
                <div class="newsletter"><p>Need help planning?</p><span>Talk with our team about your next ride.</span><a class="footer-contact-link" href="mailto:{{ config('company.email') }}">Email Arctic Hero ↗</a></div>
            </div>
            <div class="footer-bottom"><span>© {{ date('Y') }} Arctic Hero</span><span>Oulu, Finland</span></div>
        </footer>
    </body>
</html>

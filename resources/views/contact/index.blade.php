<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact {{ config('company.name', 'Arctic Hero') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-shell contact-page">
    <header class="site-header" x-data="{ open: false }">
        <div class="utility-bar"><div class="utility-inner"><div class="utility-contact"><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a></div><div class="utility-social"><span>OULU, FINLAND</span><span>CITY · AIRPORT · GROUP</span></div></div></div>
        <div class="header-inner">
            <a href="{{ url('/') }}" class="brand" aria-label="Arctic Hero home"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a>
            <nav class="desktop-nav" aria-label="Main navigation">
                <a href="{{ url('/') }}#services">Services</a>
                <a href="{{ route('fleet.index') }}">Our fleet</a>
                <a href="{{ route('booking.create') }}">Book a ride</a>
                <a href="{{ url('/') }}#locations">Oulu routes</a>
                <a href="{{ route('contact.index') }}" aria-current="page">Contact</a>
            </nav>
            <div class="header-actions">
                @auth<a href="{{ route('dashboard') }}" class="text-link">Dashboard</a>@else<a href="{{ route('login') }}" class="text-link">Log in</a>@endauth
                <a href="{{ route('booking.create') }}" class="button button-small">Book a taxi <span class="arrow">↗</span></a>
            </div>
            <button type="button" class="menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle navigation"><span></span><span></span></button>
            <nav x-cloak x-show="open" class="mobile-nav" aria-label="Mobile navigation">
                <a href="{{ url('/') }}#services" @click="open = false">Services</a>
                <a href="{{ route('fleet.index') }}" @click="open = false">Our fleet</a>
                <a href="{{ route('booking.create') }}" @click="open = false">Book a ride</a>
                <a href="{{ url('/') }}#locations" @click="open = false">Oulu routes</a>
                <a href="{{ route('contact.index') }}" aria-current="page" @click="open = false">Contact</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="contact-hero">
            <img src="{{ asset('images/taxi-driver.jpg') }}" alt="Arctic Hero driver ready to help with your Oulu journey">
            <div class="contact-hero-copy">
                <p class="contact-eyebrow">Here for the journey · Oulu</p>
                <h1>Let’s talk<br><span>about your ride.</span></h1>
                <p>Questions about a booking, airport pickup, or the right vehicle? Our local team is ready to help.</p>
                <a href="#contact-details" class="contact-hero-link">Reach our team <span>↓</span></a>
            </div>
            <span class="contact-hero-caption">Arctic Hero · Oulu, Finland</span>
        </section>

        <section class="contact-main" id="contact-details">
            <div class="contact-information">
                <p class="contact-eyebrow">Get in touch</p>
                <h2>How can we help?</h2>
                <p class="contact-intro">Reach us directly or send a message using the form. Include your booking reference if you already have a ride planned.</p>
                <a class="contact-method" href="tel:{{ config('company.phone') }}"><span class="contact-method-mark" aria-hidden="true">01</span><span><small>Call our team</small><strong>{{ config('company.phone') }}</strong></span><span class="contact-method-arrow" aria-hidden="true">↗</span></a>
                <a class="contact-method" href="mailto:{{ config('company.email') }}"><span class="contact-method-mark" aria-hidden="true">02</span><span><small>Email us</small><strong>{{ config('company.email') }}</strong></span><span class="contact-method-arrow" aria-hidden="true">↗</span></a>
                <div class="contact-location"><span class="contact-method-mark" aria-hidden="true">03</span><span><small>Local service</small><strong>Oulu, Finland</strong></span></div>
                <div class="contact-assurance"><span aria-hidden="true">A</span><p>Local trips, airport transfers, and group journeys across Oulu and nearby.</p></div>
            </div>

            <div class="contact-form-panel">
                <div class="contact-form-heading"><p class="contact-eyebrow">Send us a note</p><h2>What’s on your mind?</h2><p>Fields marked <b>*</b> are required.</p></div>
                @if (session('contact_sent'))
                    <div class="contact-success" role="status"><strong>Message received.</strong><span>Thanks for getting in touch. Our team will follow up using the details you provided.</span></div>
                @endif
                @if ($errors->any())
                    <div class="contact-errors" role="alert"><strong>Please check your message.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('contact.send') }}" method="post">
                    @csrf
                    <label class="contact-honeypot" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    <div class="contact-form-grid">
                        <label class="contact-field">Your name <b>*</b><input type="text" name="name" value="{{ old('name') }}" maxlength="120" autocomplete="name" required placeholder="Name"></label>
                        <label class="contact-field">Email address <b>*</b><input type="email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" required placeholder="you@example.com"></label>
                        <label class="contact-field">Phone <span>Optional</span><input type="tel" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel" placeholder="+358"></label>
                        <label class="contact-field">What’s this about? <b>*</b><select name="topic" required><option value="">Choose a topic</option><option value="booking" @selected(old('topic') === 'booking')>An existing booking</option><option value="vehicle" @selected(old('topic') === 'vehicle')>Vehicle options</option><option value="service" @selected(old('topic') === 'service')>Our service</option><option value="other" @selected(old('topic') === 'other')>Something else</option></select></label>
                        <label class="contact-field contact-field-message">Your message <b>*</b><textarea name="message" rows="5" minlength="10" maxlength="4000" required placeholder="How can our team help?">{{ old('message') }}</textarea></label>
                    </div>
                    <div class="contact-form-submit"><span>Your message goes directly to our Oulu team.</span><button type="submit">Send message <span aria-hidden="true">↗</span></button></div>
                </form>
            </div>
        </section>

        <section class="contact-booking-band"><div><p class="contact-eyebrow">Already know where you’re going?</p><h2>Plan your Oulu ride.</h2></div><a href="{{ route('booking.create') }}" class="button">Start a booking <span class="arrow">↗</span></a></section>
    </main>

    <footer class="site-footer">
        <div class="fleet-footer-row"><a href="{{ url('/') }}" class="brand light-brand"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a><span>Oulu, Finland</span><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a><span>© {{ date('Y') }} Arctic Hero</span></div>
    </footer>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('company.name', 'Arctic Hero') }} | Move with purpose</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-shell">
        <header class="site-header" x-data="{ open: false }">
            <a href="{{ url('/') }}" class="brand" aria-label="Arctic Hero home">
                <span class="brand-mark"><span></span><span></span><span></span></span>
                <span>ARCTIC <b>HERO</b></span>
            </a>
            <nav class="desktop-nav" aria-label="Main navigation">
                <a href="#book">Book a ride</a>
                <a href="#services">Ride options</a>
                <a href="#story">How it works</a>
                <a href="#locations" class="location-link">Oulu <span class="chevron">⌄</span></a>
            </nav>
            <div class="header-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-link">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-link">Log in</a>
                @endauth
                <a href="#book" class="button button-small">Book a ride <span class="arrow">↗</span></a>
            </div>
            <button type="button" class="menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle navigation">
                <span></span><span></span>
            </button>
            <nav x-cloak x-show="open" class="mobile-nav" aria-label="Mobile navigation">
                <a href="#book" @click="open = false">Book a ride</a>
                <a href="#services" @click="open = false">Ride options</a>
                <a href="#story" @click="open = false">How it works</a>
                <a href="#contact" @click="open = false">Contact us</a>
            </nav>
        </header>

        <main>
            <section id="book" class="hero section-dark">
                <div class="hero-copy">
                    <p class="eyebrow">Your ride, your way <span>Oulu, Finland</span></p>
                    <h1>Where to<br><em>next?</em></h1>
                    <p class="hero-intro">Reliable local rides, airport transfers, and group travel. Tell us where you are going and we will take care of the rest.</p>
                    <a href="#services" class="circle-link">See all ride options <span>↗</span></a>
                </div>
                <form class="booking-card" action="{{ route('register') }}" method="get">
                    <div class="booking-card-top"><span>Book your ride</span><span class="live-status"><i></i> Available now</span></div>
                    <label class="booking-field"><span>Pickup location</span><input name="pickup" type="text" placeholder="Enter pickup point"><b>⌖</b></label>
                    <label class="booking-field"><span>Destination</span><input name="destination" type="text" placeholder="Where are you going?"><b>⌖</b></label>
                    <div class="booking-row"><label class="booking-field"><span>When</span><select name="when"><option>Now</option><option>Later today</option><option>Tomorrow</option></select></label><label class="booking-field"><span>Passengers</span><select name="passengers"><option>1 passenger</option><option>2 passengers</option><option>3+ passengers</option></select></label></div>
                    <button class="button booking-submit" type="submit">Find a ride <span class="arrow">↗</span></button>
                    <p class="booking-note">No account needed to check availability.</p>
                </form>
                <div class="hero-stats"><span>01</span><span class="stat-line"></span><span>Mobility, reimagined</span></div>
                <div class="hero-visual">
                    <div class="sun-glow"></div>
                    <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1600&q=85" alt="Modern vehicle on a city road">
                    <div class="image-caption">Built for the long way home <span>↗</span></div>
                </div>
                <div class="social-rail" aria-label="Social links"><a href="#">in</a><a href="#">ig</a><a href="#">x</a></div>
                <div class="scroll-note">Available across Oulu <span>↓</span></div>
            </section>

            <section id="services" class="services section-light">
                <div class="section-kicker"><span>01 — Choose your ride</span><span>Simple fares <b>→</b></span></div>
                <h2>Every trip starts <span>here.</span></h2>
                <p class="section-lead">Whether you are catching a flight, heading across town, or travelling with a group, choose the ride that fits your day.</p>
                <div class="feature-grid">
                    <article class="feature-card feature-dark">
                        <div class="card-top"><span>01</span><span class="mini-icon">✦</span></div>
                        <div class="route-art"><span></span><span></span><span></span><i>↗</i></div>
                        <div><h3>City rides.<br>Made easy.</h3><a href="#book" class="card-link">Book a local ride <span>↗</span></a></div>
                    </article>
                    <article class="feature-card feature-phone">
                        <div class="card-top"><span>02</span><span class="mini-icon">◌</span></div>
                        <div class="mini-phone"><div class="phone-notch"></div><p>Good morning, Alex</p><strong>Where to next?</strong><div class="map-lines"></div><div class="phone-search">⌕ &nbsp; Search destination</div></div>
                        <div><h3>Airport<br>transfers.</h3><a href="#book" class="card-link">Plan an airport ride <span>↗</span></a></div>
                    </article>
                    <article class="feature-card feature-outline">
                        <div class="card-top"><span>03</span><span class="mini-icon">↗</span></div>
                        <div class="big-number">24<span>/7</span></div>
                        <div><h3>Groups<br> welcome.</h3><a href="#book" class="card-link">Find a larger vehicle <span>↗</span></a></div>
                    </article>
                </div>
            </section>

            <section id="story" class="story section-dark">
                <div class="section-kicker light"><span>02 — In their words</span><span>Our partners</span></div>
                <h2>Less waiting.<br><em>More getting there.</em></h2>
                <div class="testimonial-layout">
                    <div class="client-list"><p>Popular routes</p><a class="active" href="#book">Airport → Oulu centre <span>01</span></a><a href="#book">Oulu centre → Nallikari <span>02</span></a><a href="#book">Train station → Home <span>03</span></a><a href="#book">Any destination <span>04</span></a></div>
                    <figure class="testimonial-image" id="testimonial"><img src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=900&q=85" alt="Oulu city route at dusk"><figcaption><strong>Oulu, made reachable</strong><span>Local rides / every day</span></figcaption></figure>
                    <blockquote><div class="quote-label">[t] &nbsp; [24/7]</div><p>“From airport pickup to the last ride home, Arctic Hero gives us a dependable driver and a clear arrival time.”</p><div class="quote-label">Your journey <span>↗</span></div></blockquote>
                </div>
            </section>

            <section id="contact" class="contact section-light">
                <div class="contact-copy"><p class="eyebrow purple">Need a ride later?</p><h2>Book ahead.<br><span>Travel</span> easy.</h2><p>Reserve an airport transfer, a group vehicle, or your next important journey in advance.</p><p>We will confirm the details before your pickup.</p><a href="{{ route('register') }}" class="button button-purple">Book a scheduled ride <span class="arrow">↗</span></a></div>
                <div class="large-phone"><div class="phone-top"><span>9:41</span><span>•••</span></div><div class="app-map"><span class="map-dot one"></span><span class="map-dot two"></span><span class="map-route"></span></div><div class="ride-card"><span>YOUR RIDE</span><strong>Arriving in 4 min</strong><div><span class="avatar"></span><span>Arctic Hero</span><b>›</b></div></div><div class="phone-tabs"><span>⌂</span><span>◉</span><span>♙</span></div></div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="footer-main"><div class="footer-brand"><a href="{{ url('/') }}" class="brand light-brand"><span class="brand-mark"><span></span><span></span><span></span></span><span>ARCTIC <b>HERO</b></span></a><p>Your local taxi for<br>every kind of journey.</p><div class="footer-social"><a href="#">in</a><a href="#">ig</a><a href="#">x</a></div></div>
                <div class="footer-links"><div><p>Book</p><a href="#book">Book a ride</a><a href="#services">Ride options</a><a href="#story">How it works</a></div><div><p>Travel</p><a href="#contact">Airport transfers</a><a href="#contact">Group travel</a><a href="#contact">Business rides</a></div><div><p>Contact</p><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="#contact">Oulu, Finland</a></div></div>
                <div class="newsletter"><p>Ride updates</p><span>Useful travel tips, never noise.</span><form><input type="email" placeholder="Your email address" aria-label="Your email address"><button type="submit">Subscribe ↗</button></form></div>
            </div><div class="footer-bottom"><span>© {{ date('Y') }} Arctic Hero</span><span>Made for the moving world <b>✦</b></span></div>
        </footer>
</body>
</html>

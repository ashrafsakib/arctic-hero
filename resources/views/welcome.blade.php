<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('company.name', 'Arctic Hero') }} | Taxi rides in Oulu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-shell">
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
                    <a href="#services">Services</a>
                    <a href="#fleet">Our fleet</a>
                    <a href="#locations">Oulu routes</a>
                    <a href="#contact">Contact</a>
                </nav>
                <div class="header-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-link">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-link">Log in</a>
                    @endauth
                    <a href="#book" class="button button-small">Book a taxi <span class="arrow">↗</span></a>
                </div>
                <button type="button" class="menu-toggle" @click="open = !open" :aria-expanded="open.toString()" aria-label="Toggle navigation"><span></span><span></span></button>
                <nav x-cloak x-show="open" class="mobile-nav" aria-label="Mobile navigation">
                    <a href="#services" @click="open = false">Services</a><a href="#fleet" @click="open = false">Our fleet</a><a href="#locations" @click="open = false">Oulu routes</a><a href="#contact" @click="open = false">Contact</a>
                </nav>
            </div>
        </header>

        <main>
            <section id="home" class="hero">
                <div class="hero-copy">
                    <p class="eyebrow">Your local taxi <span>Oulu, Finland</span></p>
                    <h1>Oulu rides.<br><span>Made simple.</span></h1>
                    <p class="hero-intro">City pickups, airport transfers and room for the whole family. Let’s get you where you need to be.</p>
                    <div class="hero-actions"><a href="#book" class="button">Book your taxi <span class="arrow">↗</span></a><a href="#services" class="hero-secondary">Explore our services</a></div>
                </div>
                <figure class="hero-visual"><img src="/images/taxi-city.jpg" alt="Yellow taxis travelling through a city street" fetchpriority="high"></figure>
                <div class="hero-caption">Local journeys · Oulu and nearby</div>
            </section>

            <section id="services" class="services">
                <div class="section-wrap">
                    <div class="section-kicker"><span>Move through Oulu</span><strong>Ride options</strong></div>
                    <h2>For every kind of trip.</h2>
                    <p class="section-lead">A quick trip across town or a planned ride to the airport. Choose a service that fits your day.</p>
                    <div class="service-grid">
                        <article class="service-card"><div class="service-photo"><img src="/images/taxi-city.jpg" alt="Taxi ready for a city journey" loading="lazy"></div><div><h3>City journeys</h3><p>Everyday rides around Oulu, from the first pickup to the last stop.</p></div></article>
                        <article class="service-card"><div class="service-photo"><img src="/images/taxi-navigation.jpg" alt="Taxi driver navigating a city route" loading="lazy"></div><div><h3>Airport transfers</h3><p>Plan a pickup for Oulu Airport and travel with your bags in good hands.</p></div></article>
                        <article class="service-card"><div class="service-photo"><img src="/images/northern-road.jpg" alt="A winter road through northern Finland" loading="lazy"></div><div><h3>Group rides</h3><p>Choose a minivan for family trips and journeys with extra luggage.</p></div></article>
                    </div>
                </div>
            </section>

            <section class="company-section">
                <div class="company-collage" aria-label="Oulu city and a taxi driver">
                    <figure class="company-main-photo"><img src="/images/coastal-journey.jpg" alt="Oulu waterfront and city buildings" loading="lazy"></figure>
                    <figure class="company-detail-photo"><img src="/images/taxi-navigation.jpg" alt="Taxi driver following a route" loading="lazy"></figure>
                </div>
                <div class="company-copy">
                    <p class="section-kicker">Welcome to Arctic Hero</p>
                    <h2>A local ride.<br><span>A little less to think about.</span></h2>
                    <p>We make everyday travel around Oulu easier to plan. Set your pickup, choose the ride that suits your passengers, and share where you’re headed.</p>
                    <ul class="check-list"><li>Local city and airport journeys</li><li>Vehicle options for up to seven passengers</li><li>Pickup time and passenger details in one request</li></ul>
                    <div class="company-contact"><div><span>Call us to plan a trip</span><strong>{{ config('company.phone') }}</strong></div><a href="tel:{{ config('company.phone') }}" class="button">Call Arctic Hero <span class="arrow">↗</span></a></div>
                </div>
            </section>

            <section id="fleet" class="fleet">
                <div class="section-wrap">
                    <div class="fleet-heading">
                        <p class="section-kicker">Choose a vehicle</p>
                        <h2>Room for your ride.</h2>
                        <p>Choose from a standard taxi, premium car or minivan, with room for up to seven passengers.</p>
                    </div>
                    <div class="fleet-grid">
                        <article class="fleet-card"><span class="fleet-tag">Everyday</span><h3>Standard Taxi</h3><p class="fleet-description">Comfortable transport for daily trips around town.</p><div class="fleet-details"><span>Passengers<strong>Up to 4</strong></span><span>Luggage<strong>2 bags</strong></span></div><a href="#book" class="button">Book this ride <span class="arrow">↗</span></a></article>
                        <article class="fleet-card featured"><span class="fleet-tag">Extra comfort</span><h3>Premium Taxi</h3><p class="fleet-description">A quiet, spacious option for business and airport travel.</p><div class="fleet-details"><span>Passengers<strong>Up to 4</strong></span><span>Luggage<strong>3 bags</strong></span></div><a href="#book" class="button">Book this ride <span class="arrow">↗</span></a></article>
                        <article class="fleet-card"><span class="fleet-tag">More room</span><h3>Minivan</h3><p class="fleet-description">Practical space for family and group journeys.</p><div class="fleet-details"><span>Passengers<strong>Up to 7</strong></span><span>Luggage<strong>5 bags</strong></span></div><a href="#book" class="button">Book this ride <span class="arrow">↗</span></a></article>
                    </div>
                </div>
            </section>

            <section id="book" class="booking-section">
                <div class="booking-promo">
                    <img src="/images/taxi-night.jpg" alt="Yellow taxi ready at the curb" loading="lazy">
                    <div class="booking-promo-copy"><p class="booking-kicker">Your next trip starts here</p><h2>Book your<br>Oulu taxi.</h2><p>Share a few details and continue to registration to send your ride request.</p></div>
                </div>
                <form class="booking-card" action="{{ route('register') }}" method="get">
                    <div class="booking-card-top"><div><span class="booking-kicker">Plan your trip</span><strong>Where to?</strong></div></div>
                    <p class="booking-subtitle">Add your route and pickup details.</p>
                    <label class="booking-field"><span>Pickup location</span><input name="pickup" type="text" placeholder="Enter pickup point"><b>⌖</b></label>
                    <label class="booking-field"><span>Destination</span><input name="destination" type="text" placeholder="Where are you going?"><b>⌖</b></label>
                    <div class="booking-row"><label class="booking-field"><span>When</span><select name="when"><option>Now</option><option>Later today</option><option>Tomorrow</option></select></label><label class="booking-field"><span>Passengers</span><select name="passengers"><option>1 passenger</option><option>2 passengers</option><option>3+ passengers</option><option>Up to 7 passengers</option></select></label></div>
                    <button class="button booking-submit" type="submit">Continue to registration <span class="arrow">↗</span></button>
                    <p class="booking-note">Register to continue with your ride request.</p>
                </form>
            </section>

            <section id="locations" class="locations">
                <div class="locations-copy">
                    <p class="section-kicker">Our service area</p>
                    <h2>Oulu is home.<br>We know the way.</h2>
                    <p>Book a ride across the city, to the airport or from the railway station. Add your pickup and destination, then choose the time and passenger count that fit your plans.</p>
                    <ul class="route-list"><li>Oulu city centre</li><li>Oulu Airport</li><li>Railway station</li><li>Neighbourhood pickups</li></ul>
                    <a href="#book" class="card-link">Plan your route <span>↗</span></a>
                </div>
                <figure class="location-photo"><img src="/images/coastal-journey.jpg" alt="Aerial view of Oulu and its waterfront" loading="lazy"><figcaption>Oulu, Finland</figcaption></figure>
            </section>

            <section id="contact" class="contact-band">
                <div><h2>Going somewhere?</h2><p>Plan a local ride, airport transfer or group trip with Arctic Hero.</p></div>
                <a href="#book" class="button">Start a booking <span class="arrow">↗</span></a>
            </section>
        </main>

        <footer class="site-footer">
            <div class="footer-main">
                <div class="footer-brand"><a href="{{ url('/') }}" class="brand light-brand"><span class="brand-mark"></span><span>ARCTIC <b>HERO</b></span></a><p>Your local taxi for city trips, airport runs and journeys around Oulu.</p></div>
                <div class="footer-links"><div><p>Explore</p><a href="#services">Ride options</a><a href="#fleet">Our fleet</a><a href="#locations">Oulu routes</a></div><div><p>Book</p><a href="#book">Start a booking</a><a href="#fleet">Vehicle choices</a><a href="#contact">Contact</a></div><div><p>Contact</p><a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a><a href="tel:{{ config('company.phone') }}">{{ config('company.phone') }}</a><a href="#locations">Oulu, Finland</a></div></div>
                <div class="newsletter"><p>Need help planning?</p><span>Talk with our team about your next ride.</span><a class="footer-contact-link" href="mailto:{{ config('company.email') }}">Email Arctic Hero ↗</a></div>
            </div>
            <div class="footer-bottom"><span>© {{ date('Y') }} Arctic Hero</span><span>Oulu, Finland</span></div>
        </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-white">
        <header x-data="{ open: false }" @keydown.escape.window="open = false" class="border-b border-white/10 bg-slate-950/95 backdrop-blur">
            <div class="mx-auto max-w-6xl px-6">
                <div class="flex h-[4.5rem] items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="{{ config('company.name') }} home">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-400 text-slate-950">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 16.5 7.2 8h9.6l2.2 8.5M7.2 12h9.6M8 16.5v2m8-2v2M6 16.5h12M7 8l1.4-2h7.2L17 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /><circle cx="8" cy="16.5" r="1" fill="currentColor" /><circle cx="16" cy="16.5" r="1" fill="currentColor" /></svg>
                        </span>
                        <span class="text-lg font-bold tracking-tight">{{ config('company.name') }}</span>
                    </a>
                    <nav class="hidden items-center gap-1 md:flex" aria-label="Main navigation">
                        <a href="#services" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">Services</a>
                        <a href="#how-it-works" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">How it works</a>
                        <a href="#contact" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">Help</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="ml-3 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/40">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="ml-3 rounded-lg px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/5">Log in</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-cyan-400 px-5 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-300">Book a ride</a>
                        @endauth
                    </nav>
                    <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="public-mobile-navigation" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-200 hover:bg-white/10 md:hidden">
                        <span class="sr-only">Toggle navigation</span>
                        <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" /></svg>
                        <svg x-cloak x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 6 12 12M6 18 18 6" /></svg>
                    </button>
                </div>
                <div id="public-mobile-navigation" x-cloak x-show="open" x-transition.origin.top class="border-t border-white/10 pb-5 pt-3 md:hidden">
                    <div class="space-y-1">
                        <a href="#services" @click="open = false" class="block rounded-lg px-3 py-3 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">Services</a>
                        <a href="#how-it-works" @click="open = false" class="block rounded-lg px-3 py-3 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">How it works</a>
                        <a href="#contact" @click="open = false" class="block rounded-lg px-3 py-3 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">Help</a>
                    </div>
                    <div class="mt-3 flex gap-3 border-t border-white/10 pt-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="flex-1 rounded-full border border-white/20 px-4 py-3 text-center text-sm font-semibold">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="flex-1 rounded-full border border-white/20 px-4 py-3 text-center text-sm font-semibold">Log in</a>
                            <a href="{{ route('register') }}" class="flex-1 rounded-full bg-cyan-400 px-4 py-3 text-center text-sm font-bold text-slate-950">Book a ride</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>
        <main class="mx-auto grid max-w-6xl gap-12 px-6 pb-20 pt-20 lg:grid-cols-[1.1fr_.9fr] lg:items-center">
            <section id="how-it-works">
                <p class="mb-5 text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">Oulu transportation</p>
                <h1 class="max-w-3xl text-5xl font-semibold tracking-tight sm:text-6xl">Reliable journeys, handled by Arctic Hero.</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">Request a company-operated taxi for airport transfers, city trips, group travel, and exploring Oulu. Our team contacts you before confirming every booking.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="#" class="rounded-full bg-cyan-400 px-6 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Book a taxi</a>
                    <a href="tel:{{ config('company.phone') }}" class="rounded-full border border-slate-700 px-6 py-3 font-semibold text-slate-200 hover:border-slate-500">Call {{ config('company.phone') }}</a>
                </div>
            </section>
            <aside id="services" class="rounded-3xl border border-slate-800 bg-slate-900 p-8 shadow-2xl shadow-cyan-950/30">
                <p class="text-sm font-semibold text-cyan-300">Simple booking promise</p>
                <ol class="mt-6 space-y-6 text-slate-300">
                    <li><strong class="block text-white">01. Share your journey</strong><span class="text-sm">Tell us where and when you need a taxi.</span></li>
                    <li><strong class="block text-white">02. Get an estimate</strong><span class="text-sm">Choose the vehicle that fits your passengers and luggage.</span></li>
                    <li><strong class="block text-white">03. We confirm personally</strong><span class="text-sm">Our team contacts you by phone or email before the trip.</span></li>
                </ol>
            </aside>
        </main>
        <footer id="contact" class="mx-auto max-w-6xl border-t border-slate-800 px-6 py-6 text-sm text-slate-400">
            {{ config('company.name') }} · {{ config('company.email') }} · Company no. {{ config('company.company_number') }} · VAT {{ config('company.vat_number') }}
        </footer>
    </body>
</html>

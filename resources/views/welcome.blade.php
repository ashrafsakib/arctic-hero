<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-white">
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <span class="text-xl font-semibold tracking-tight">{{ config('company.name') }}</span>
            <nav class="flex items-center gap-4 text-sm text-slate-300">
                @auth
                    <a href="{{ url('/dashboard') }}" class="hover:text-white">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-cyan-400 px-4 py-2 font-medium text-slate-950 hover:bg-cyan-300">Create account</a>
                @endauth
            </nav>
        </header>
        <main class="mx-auto grid max-w-6xl gap-12 px-6 pb-20 pt-20 lg:grid-cols-[1.1fr_.9fr] lg:items-center">
            <section>
                <p class="mb-5 text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">Oulu transportation</p>
                <h1 class="max-w-3xl text-5xl font-semibold tracking-tight sm:text-6xl">Reliable journeys, handled by Arctic Hero.</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">Request a company-operated taxi for airport transfers, city trips, group travel, and exploring Oulu. Our team contacts you before confirming every booking.</p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="#" class="rounded-full bg-cyan-400 px-6 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Book a taxi</a>
                    <a href="tel:{{ config('company.phone') }}" class="rounded-full border border-slate-700 px-6 py-3 font-semibold text-slate-200 hover:border-slate-500">Call {{ config('company.phone') }}</a>
                </div>
            </section>
            <aside class="rounded-3xl border border-slate-800 bg-slate-900 p-8 shadow-2xl shadow-cyan-950/30">
                <p class="text-sm font-semibold text-cyan-300">Simple booking promise</p>
                <ol class="mt-6 space-y-6 text-slate-300">
                    <li><strong class="block text-white">01. Share your journey</strong><span class="text-sm">Tell us where and when you need a taxi.</span></li>
                    <li><strong class="block text-white">02. Get an estimate</strong><span class="text-sm">Choose the vehicle that fits your passengers and luggage.</span></li>
                    <li><strong class="block text-white">03. We confirm personally</strong><span class="text-sm">Our team contacts you by phone or email before the trip.</span></li>
                </ol>
            </aside>
        </main>
        <footer class="mx-auto max-w-6xl border-t border-slate-800 px-6 py-6 text-sm text-slate-400">
            {{ config('company.name') }} · {{ config('company.email') }} · Company no. {{ config('company.company_number') }} · VAT {{ config('company.vat_number') }}
        </footer>
    </body>
</html>

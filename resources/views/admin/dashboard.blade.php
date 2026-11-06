<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Company dashboard') }}</h2>
    </x-slot>

    <div class="admin-page py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="admin-page-heading mb-7">
                <p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Arctic Hero operations</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Admin dashboard</h1>
                <p class="mt-2 text-slate-600">Manage rides, pricing, locations, and customer feedback.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <section class="rounded border border-slate-200 bg-white p-6">
                    <p class="text-sm text-slate-600">Pending bookings</p>
                    <p class="mt-2 text-4xl font-semibold text-slate-900">{{ $pendingBookingCount }}</p>
                    <a class="mt-5 inline-flex items-center gap-3 bg-yellow-300 px-4 py-3 text-sm font-bold text-slate-950 hover:bg-yellow-400" href="{{ route('admin.bookings.index', ['status' => 'pending']) }}">Review requests <span aria-hidden="true">→</span></a>
                </section>
                <section class="rounded border border-slate-200 bg-white p-6">
                    <p class="text-sm text-slate-600">Vehicles in fleet</p>
                    <p class="mt-2 text-4xl font-semibold text-slate-900">{{ $vehicleCount }}</p>
                    <a class="mt-5 inline-flex items-center gap-3 bg-yellow-300 px-4 py-3 text-sm font-bold text-slate-950 hover:bg-yellow-400" href="{{ route('admin.vehicles.index') }}">Manage fleet <span aria-hidden="true">→</span></a>
                </section>
                <section class="rounded border border-slate-200 bg-white p-6">
                    <p class="text-sm text-slate-600">Active vehicles</p>
                    <p class="mt-2 text-4xl font-semibold text-slate-900">{{ $activeVehicleCount }}</p>
                    <a class="mt-5 inline-block text-sm font-semibold text-slate-800 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.vehicles.index', ['status' => 'active']) }}">View active vehicles</a>
                </section>
                <section class="rounded border border-slate-200 bg-white p-6">
                    <p class="text-sm text-slate-600">New contact submissions</p>
                    <p class="mt-2 text-4xl font-semibold text-slate-900">{{ $newContactCount }}</p>
                    <a class="mt-5 inline-block text-sm font-semibold text-slate-800 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.contact-submissions.index', ['status' => 'new']) }}">Open inbox</a>
                </section>
                <section class="rounded border border-slate-200 bg-white p-6"><p class="text-sm text-slate-600">Cancellation requests</p><p class="mt-2 text-4xl font-semibold text-slate-900">{{ $cancellationRequestCount }}</p><a class="mt-5 inline-block text-sm font-semibold underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.bookings.index', ['status' => 'cancellation_requested']) }}">Review requests</a></section>
                <section class="rounded border border-slate-200 bg-white p-6"><p class="text-sm text-slate-600">Active pricing rules</p><p class="mt-2 text-4xl font-semibold text-slate-900">{{ $activePricingRuleCount }}</p><a class="mt-5 inline-block text-sm font-semibold underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.pricing-rules.index') }}">Manage pricing</a></section>
                <section class="rounded border border-slate-200 bg-white p-6"><p class="text-sm text-slate-600">Saved locations</p><p class="mt-2 text-4xl font-semibold text-slate-900">{{ $locationCount }}</p><a class="mt-5 inline-block text-sm font-semibold underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.locations.index') }}">Manage map pins</a></section>
                <section class="rounded border border-slate-200 bg-white p-6"><p class="text-sm text-slate-600">Visible reviews</p><p class="mt-2 text-4xl font-semibold text-slate-900">{{ $visibleReviewCount }}</p><a class="mt-5 inline-block text-sm font-semibold underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.reviews.index') }}">Moderate reviews</a></section>
                <section class="rounded border border-slate-200 bg-white p-6"><p class="text-sm text-slate-600">Reports and settings</p><p class="mt-4 flex flex-wrap gap-4 text-sm font-semibold"><a class="underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.reports.index') }}">Open reports</a><a class="underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.settings.edit') }}">Company settings</a></p></section>
            </div>
        </div>
    </div>
</x-app-layout>

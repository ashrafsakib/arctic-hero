<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Company dashboard') }}</h2>
    </x-slot>

    <div class="admin-page py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="admin-page-heading mb-7">
                <p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Arctic Hero operations</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Admin dashboard</h1>
                <p class="mt-2 text-slate-600">Manage vehicles and review your fleet at a glance.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
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
            </div>
        </div>
    </div>
</x-app-layout>

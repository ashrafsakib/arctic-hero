<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Company dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-sm uppercase tracking-wide text-slate-500">Arctic Hero Operations</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Booking management foundation</h1>
                <p class="mt-2 text-slate-600">The admin workspace is ready for booking requests, vehicles, pricing, and reports.</p>
            </div>
        </div>
    </div>
</x-app-layout>

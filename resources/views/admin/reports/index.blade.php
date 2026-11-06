<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-800">Operations report</h2></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Booking analytics</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Operations report</h1><p class="mt-2 text-sm text-slate-600">Booking volume, status mix, and completed-trip fare totals for the selected period.</p></div>
        @if ($errors->any())<p class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert">{{ $errors->first() }}</p>@endif
        <form class="flex flex-wrap items-end gap-3 border border-slate-200 bg-white p-4" method="get" action="{{ route('admin.reports.index') }}">
            <label class="text-sm font-medium text-slate-700">From<input class="mt-1 block min-h-10 border-slate-300" name="date_from" type="date" value="{{ $dateFrom }}" required></label>
            <label class="text-sm font-medium text-slate-700">To<input class="mt-1 block min-h-10 border-slate-300" name="date_to" type="date" value="{{ $dateTo }}" required></label>
            <button class="min-h-10 bg-slate-900 px-4 text-sm font-semibold text-white" type="submit">Run report</button>
        </form>
        <div class="grid gap-4 sm:grid-cols-3">
            <article class="border border-slate-200 bg-white p-5"><p class="text-sm text-slate-600">Total bookings</p><p class="mt-2 text-3xl font-semibold text-slate-900">{{ $totalBookings }}</p></article>
            <article class="border border-slate-200 bg-white p-5"><p class="text-sm text-slate-600">Completed trips</p><p class="mt-2 text-3xl font-semibold text-slate-900">{{ $completedCount }}</p></article>
            <article class="border border-slate-200 bg-white p-5"><p class="text-sm text-slate-600">Completed fare total</p><p class="mt-2 text-3xl font-semibold text-slate-900">€{{ number_format($completedRevenue, 2) }}</p><small class="text-slate-500">Excludes cancelled and incomplete rides.</small></article>
        </div>
        <div class="grid gap-6 lg:grid-cols-2">
            <section class="border border-slate-200 bg-white p-5"><h2 class="text-lg font-semibold text-slate-900">Bookings by status</h2><dl class="mt-4 divide-y divide-slate-100">@forelse ($statusCounts as $status => $count)<div class="flex justify-between gap-4 py-3"><dt>{{ \Illuminate\Support\Str::headline($status) }}</dt><dd class="font-semibold">{{ $count }}</dd></div>@empty<p class="py-4 text-sm text-slate-600">No bookings for this period.</p>@endforelse</dl></section>
            <section class="border border-slate-200 bg-white p-5"><h2 class="text-lg font-semibold text-slate-900">Top pickup locations</h2><ol class="mt-4 divide-y divide-slate-100">@forelse ($topPickups as $pickup)<li class="flex justify-between gap-4 py-3"><span>{{ $pickup->pickup_address }}</span><strong>{{ $pickup->aggregate }}</strong></li>@empty<li class="py-4 text-sm text-slate-600">No pickup data for this period.</li>@endforelse</ol></section>
        </div>
    </div></div>
</x-app-layout>

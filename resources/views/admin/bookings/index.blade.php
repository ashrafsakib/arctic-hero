<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-gray-800">Booking management</h2></x-slot>

    <div class="admin-page py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Ride operations</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Bookings</h1><p class="mt-2 text-sm text-slate-600">Review customer requests, update their status, and keep the ride history current.</p></div>
            @if (session('success'))<div class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</div>@endif

            <div class="grid gap-3 sm:grid-cols-3">
                <a class="border border-slate-200 bg-white p-4 text-slate-900 hover:border-yellow-500" href="{{ route('admin.bookings.index') }}"><span class="text-sm text-slate-600">All bookings</span><strong class="mt-1 block text-2xl">{{ $totalCount }}</strong></a>
                <a class="border border-slate-200 bg-white p-4 text-slate-900 hover:border-yellow-500" href="{{ route('admin.bookings.index', ['status' => 'pending']) }}"><span class="text-sm text-slate-600">New requests</span><strong class="mt-1 block text-2xl text-amber-800">{{ $pendingCount }}</strong></a>
                <a class="border border-slate-200 bg-white p-4 text-slate-900 hover:border-yellow-500" href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}"><span class="text-sm text-slate-600">Confirmed</span><strong class="mt-1 block text-2xl text-emerald-800">{{ $confirmedCount }}</strong></a>
            </div>

            <section class="overflow-hidden border border-slate-200 bg-white">
                <form class="flex flex-wrap items-end gap-3 border-b border-slate-200 p-4" method="get" action="{{ route('admin.bookings.index') }}">
                    <label class="min-w-56 flex-1 text-sm font-medium text-slate-700">Search bookings
                        <input class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" type="search" name="search" value="{{ request('search') }}" placeholder="Reference, name, route, or email">
                    </label>
                    <label class="w-full sm:w-40 text-sm font-medium text-slate-700">Status
                        <select class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ \Illuminate\Support\Str::headline($status) }}</option>@endforeach</select>
                    </label>
                    <label class="w-full sm:w-40 text-sm font-medium text-slate-700">From date<input class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" type="date" name="date_from" value="{{ request('date_from') }}"></label>
                    <label class="w-full sm:w-40 text-sm font-medium text-slate-700">To date<input class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" type="date" name="date_to" value="{{ request('date_to') }}"></label>
                    <button class="min-h-10 bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Filter</button>
                    @if (request()->hasAny(['search', 'status', 'date_from', 'date_to']))<a class="min-h-10 px-2 py-2 text-sm text-slate-700 underline underline-offset-4" href="{{ route('admin.bookings.index') }}">Clear</a>@endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="px-4 py-3 font-semibold">Booking</th><th class="px-4 py-3 font-semibold">Customer</th><th class="px-4 py-3 font-semibold">Pickup</th><th class="px-4 py-3 font-semibold">Route</th><th class="px-4 py-3 font-semibold">Vehicle</th><th class="px-4 py-3 font-semibold">Fare</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 text-right font-semibold">Action</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($bookings as $booking)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4"><a class="font-semibold text-slate-900 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.bookings.show', $booking) }}">{{ $booking->booking_code }}</a></td>
                                    <td class="px-4 py-4"><span class="font-medium text-slate-900">{{ $booking->customer->name }}</span><span class="mt-1 block text-xs text-slate-600">{{ $booking->customer->email }}</span></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-slate-700">{{ $booking->booking_date->format('M j, Y') }}<span class="mt-1 block text-xs">{{ \Illuminate\Support\Carbon::parse($booking->booking_time)->format('g:i A') }}</span></td>
                                    <td class="max-w-52 px-4 py-4 text-slate-700"><span class="block truncate">{{ $booking->pickup_address }}</span><span class="my-1 block text-xs text-slate-500">to</span><span class="block truncate">{{ $booking->destination_address }}</span></td>
                                    <td class="px-4 py-4 text-slate-700">{{ $booking->vehicleType->name }}<span class="mt-1 block text-xs">{{ $booking->passengers }} {{ $booking->passengers === 1 ? 'passenger' : 'passengers' }}</span></td>
                                    <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-900">€{{ number_format((float) $booking->total_fare, 2) }}</td>
                                    <td class="px-4 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold {{ in_array($booking->status, ['confirmed', 'completed'], true) ? 'bg-emerald-100 text-emerald-900' : (in_array($booking->status, ['rejected', 'cancelled'], true) ? 'bg-red-100 text-red-900' : 'bg-amber-100 text-amber-900') }}">{{ \Illuminate\Support\Str::headline($booking->status) }}</span></td>
                                    <td class="px-4 py-4 text-right"><a class="font-semibold text-slate-800 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.bookings.show', $booking) }}">Review</a></td>
                                </tr>
                            @empty
                                <tr><td class="px-4 py-12 text-center text-slate-600" colspan="8">No bookings match these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($bookings->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $bookings->links() }}</div>@endif
            </section>
        </div>
    </div>
</x-app-layout>
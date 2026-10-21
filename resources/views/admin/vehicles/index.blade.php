<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-4"><h2 class="text-xl font-semibold leading-tight text-gray-800">Fleet management</h2><a class="bg-yellow-300 px-4 py-2 text-sm font-bold text-slate-950 hover:bg-yellow-400" href="{{ route('admin.vehicles.create') }}">Add vehicle</a></div></x-slot>

    <div class="admin-page py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-5">
                <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Vehicle inventory</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Your fleet</h1><p class="mt-2 text-sm text-slate-600">Manage vehicle records, assignments, and operating status.</p></div>
                <a class="bg-yellow-300 px-4 py-3 text-sm font-bold text-slate-950 hover:bg-yellow-400" href="{{ route('admin.vehicles.create') }}">Add a vehicle <span aria-hidden="true">+</span></a>
            </div>

            @if (session('success'))<div class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</div>@endif
            @if ($errors->any())<div class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert">{{ $errors->first() }}</div>@endif

            <div class="grid gap-3 sm:grid-cols-3">
                <article class="border border-slate-200 bg-white p-4"><span class="text-sm text-slate-600">All vehicles</span><strong class="mt-1 block text-2xl text-slate-900">{{ $totalVehicles }}</strong></article>
                <article class="border border-slate-200 bg-white p-4"><span class="text-sm text-slate-600">Active</span><strong class="mt-1 block text-2xl text-emerald-800">{{ $activeVehicles }}</strong></article>
                <article class="border border-slate-200 bg-white p-4"><span class="text-sm text-slate-600">In maintenance</span><strong class="mt-1 block text-2xl text-amber-800">{{ $maintenanceVehicles }}</strong></article>
            </div>

            <section class="overflow-hidden border border-slate-200 bg-white">
                <form class="flex flex-wrap items-end gap-3 border-b border-slate-200 p-4" method="get" action="{{ route('admin.vehicles.index') }}">
                    <label class="min-w-56 flex-1 text-sm font-medium text-slate-700">Search fleet
                        <input class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" type="search" name="search" value="{{ request('search') }}" placeholder="Name, make, model or plate">
                    </label>
                    <label class="w-full sm:w-48 text-sm font-medium text-slate-700">Status
                        <select class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                    </label>
                    <button class="min-h-10 bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Filter</button>
                    @if (request()->hasAny(['search', 'status']))<a class="min-h-10 px-2 py-2 text-sm text-slate-700 underline underline-offset-4" href="{{ route('admin.vehicles.index') }}">Clear</a>@endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="px-4 py-3 font-semibold">Vehicle</th><th class="px-4 py-3 font-semibold">Type</th><th class="px-4 py-3 font-semibold">Registration</th><th class="px-4 py-3 font-semibold">Capacity</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 text-right font-semibold">Actions</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($vehicles as $vehicle)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4"><a class="font-semibold text-slate-900 underline decoration-transparent underline-offset-4 hover:decoration-yellow-600" href="{{ route('admin.vehicles.show', $vehicle) }}">{{ $vehicle->name }}</a><span class="mt-1 block text-xs text-slate-600">{{ trim(($vehicle->brand ?? '').' '.($vehicle->model ?? '')) ?: 'Make and model not set' }}</span></td>
                                    <td class="px-4 py-4 text-slate-700">{{ $vehicle->vehicleType->name }}</td>
                                    <td class="px-4 py-4 font-medium text-slate-900">{{ $vehicle->registration_number }}</td>
                                    <td class="px-4 py-4 text-slate-700">{{ $vehicle->vehicleType->passenger_capacity }} passengers · {{ $vehicle->vehicleType->luggage_capacity }} bags</td>
                                    <td class="px-4 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold {{ $vehicle->status === 'active' ? 'bg-emerald-100 text-emerald-900' : ($vehicle->status === 'maintenance' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst($vehicle->status) }}</span></td>
                                    <td class="px-4 py-4"><div class="flex justify-end gap-3"><a class="font-medium text-slate-800 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.vehicles.show', $vehicle) }}">View</a><a class="font-medium text-slate-800 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.vehicles.edit', $vehicle) }}">Edit</a></div></td>
                                </tr>
                            @empty
                                <tr><td class="px-4 py-12 text-center text-slate-600" colspan="6">No vehicles match these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($vehicles->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $vehicles->links() }}</div>@endif
            </section>
        </div>
    </div>
</x-app-layout>
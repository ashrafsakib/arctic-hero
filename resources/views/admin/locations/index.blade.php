<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-4"><h2 class="text-xl font-semibold text-slate-800">Location map pins</h2><a class="bg-yellow-300 px-4 py-2 text-sm font-bold text-slate-950" href="{{ route('admin.locations.create') }}">Add location</a></div></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Public routes and booking locations</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Location map pins</h1><p class="mt-2 text-sm text-slate-600">Manage named Oulu stops and their saved map coordinates.</p></div>
        @if (session('success'))<p class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</p>@endif
        @if ($errors->any())<p class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert">{{ $errors->first() }}</p>@endif
        <section class="overflow-x-auto border border-slate-200 bg-white">
            <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="px-4 py-3">Location</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Coordinates</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($locations as $location)
                        <tr><td class="px-4 py-4"><strong>{{ $location->name }}</strong><span class="mt-1 block text-xs text-slate-600">{{ $location->address ?: 'No address' }}</span></td><td class="px-4 py-4">{{ \Illuminate\Support\Str::headline($location->category) }}</td><td class="px-4 py-4">{{ $location->latitude ?? '—' }}, {{ $location->longitude ?? '—' }}</td><td class="px-4 py-4">{{ ucfirst($location->status) }}</td><td class="px-4 py-4"><div class="flex justify-end gap-3"><a class="underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.locations.edit', $location) }}">Edit pin</a><form method="post" action="{{ route('admin.locations.destroy', $location) }}" onsubmit="return confirm('Remove this location?')">@csrf @method('DELETE')<button class="text-red-700 underline underline-offset-4" type="submit">Delete</button></form></div></td></tr>
                    @empty
                        <tr><td class="px-4 py-10 text-center text-slate-600" colspan="5">No locations are configured.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($locations->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $locations->links() }}</div>@endif
        </section>
    </div></div>
</x-app-layout>

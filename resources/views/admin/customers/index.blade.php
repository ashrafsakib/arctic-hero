<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-800">Customer accounts</h2></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Customer directory</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Customer accounts</h1><p class="mt-2 text-sm text-slate-600">Search customer contact details, review booking history, and manage account access.</p></div>
        <div class="grid gap-4 sm:grid-cols-3">
            <article class="border border-slate-200 bg-white p-5"><p class="text-sm text-slate-600">Customers</p><p class="mt-2 text-3xl font-semibold text-slate-900">{{ $customerCount }}</p></article>
            <article class="border border-slate-200 bg-white p-5"><p class="text-sm text-slate-600">Active</p><p class="mt-2 text-3xl font-semibold text-slate-900">{{ $activeCount }}</p></article>
            <article class="border border-slate-200 bg-white p-5"><p class="text-sm text-slate-600">Inactive</p><p class="mt-2 text-3xl font-semibold text-slate-900">{{ $inactiveCount }}</p></article>
        </div>
        <form class="flex flex-wrap items-end gap-3 border border-slate-200 bg-white p-4" method="get" action="{{ route('admin.customers.index') }}">
            <label class="text-sm font-medium text-slate-700">Search name, email, or phone<input class="mt-1 block min-h-10 border-slate-300" name="search" value="{{ request('search') }}" maxlength="100"></label>
            <label class="text-sm font-medium text-slate-700">Account status<select class="mt-1 block min-h-10 border-slate-300" name="status"><option value="">All customers</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select></label>
            <button class="min-h-10 bg-slate-900 px-4 text-sm font-semibold text-white" type="submit">Search</button>
        </form>
        <section class="overflow-x-auto border border-slate-200 bg-white" tabindex="0" role="region" aria-label="Customer accounts table">
            <table class="w-full min-w-[700px] border-collapse text-left text-sm">
                <caption class="sr-only">Customer accounts matching the selected filters</caption>
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="px-4 py-3" scope="col">Customer</th><th class="px-4 py-3" scope="col">Phone</th><th class="px-4 py-3" scope="col">Bookings</th><th class="px-4 py-3" scope="col">Status</th><th class="px-4 py-3 text-right" scope="col">Details</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                        <tr><td class="px-4 py-4"><strong>{{ $customer->name }}</strong><span class="mt-1 block text-xs text-slate-600">{{ $customer->email }}</span></td><td class="px-4 py-4">{{ $customer->phone ?: '—' }}</td><td class="px-4 py-4">{{ $customer->bookings_count }}</td><td class="px-4 py-4"><span class="px-2 py-1 text-xs font-semibold {{ $customer->status === 'active' ? 'bg-emerald-100 text-emerald-900' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($customer->status) }}</span></td><td class="px-4 py-4 text-right"><a class="font-semibold underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.customers.show', $customer) }}">View customer</a></td></tr>
                    @empty
                        <tr><td class="px-4 py-10 text-center text-slate-600" colspan="5">No customers match this search.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($customers->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $customers->links() }}</div>@endif
        </section>
    </div></div>
</x-app-layout>

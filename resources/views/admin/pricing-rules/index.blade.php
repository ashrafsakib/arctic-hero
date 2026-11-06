<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-4"><h2 class="text-xl font-semibold text-slate-800">Pricing rules</h2><a class="bg-yellow-300 px-4 py-2 text-sm font-bold text-slate-950" href="{{ route('admin.pricing-rules.create') }}">Add pricing rule</a></div></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Fare configuration</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Pricing rules</h1><p class="mt-2 text-sm text-slate-600">Active rules add a fixed surcharge when a ride's scheduled pickup falls inside the rule window.</p></div>
        @if (session('success'))<p class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</p>@endif
        @if ($errors->any())<p class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert">{{ $errors->first() }}</p>@endif
        <div class="overflow-x-auto border border-slate-200 bg-white" tabindex="0" role="region" aria-label="Pricing rules table">
            <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                <caption class="sr-only">Configured pricing rules</caption>
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="px-4 py-3" scope="col">Rule</th><th class="px-4 py-3" scope="col">Surcharge</th><th class="px-4 py-3" scope="col">Effective window</th><th class="px-4 py-3" scope="col">Status</th><th class="px-4 py-3 text-right" scope="col">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pricingRules as $pricingRule)
                        <tr>
                            <td class="px-4 py-4"><strong>{{ $pricingRule->name }}</strong>@if ($pricingRule->description)<span class="mt-1 block text-xs text-slate-600">{{ $pricingRule->description }}</span>@endif</td>
                            <td class="px-4 py-4">€{{ number_format((float) $pricingRule->extra_charge, 2) }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $pricingRule->starts_at?->format('M j, Y H:i') ?? 'Any time' }} – {{ $pricingRule->ends_at?->format('M j, Y H:i') ?? 'No end' }}</td>
                            <td class="px-4 py-4">{{ ucfirst($pricingRule->status) }}</td>
                            <td class="px-4 py-4"><div class="flex justify-end gap-3"><a class="underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.pricing-rules.edit', $pricingRule) }}">Edit</a><form method="post" action="{{ route('admin.pricing-rules.destroy', $pricingRule) }}" onsubmit="return confirm('Delete this pricing rule?')">@csrf @method('DELETE')<button class="text-red-700 underline underline-offset-4" type="submit">Delete</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-10 text-center text-slate-600" colspan="5">No pricing rules have been added.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($pricingRules->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $pricingRules->links() }}</div>@endif
        </div>
    </div></div>
</x-app-layout>

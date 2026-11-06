<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-800">Review moderation</h2></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Customer feedback</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Review moderation</h1><p class="mt-2 text-sm text-slate-600">Control public visibility and add a response visible to the customer.</p></div>
        @if (session('success'))<p class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</p>@endif
        @if ($errors->any())<p class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert">{{ $errors->first() }}</p>@endif
        <form class="flex flex-wrap items-end gap-3 border border-slate-200 bg-white p-4" method="get" action="{{ route('admin.reviews.index') }}">
            <label class="text-sm font-medium text-slate-700">Visibility<select class="mt-1 block min-h-10 border-slate-300" name="status"><option value="">All reviews</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
            <button class="min-h-10 bg-slate-900 px-4 text-sm font-semibold text-white" type="submit">Filter</button>
        </form>
        <div class="space-y-4">
            @forelse ($reviews as $review)
                <article class="border border-slate-200 bg-white p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div><h2 class="font-semibold text-slate-900">{{ $review->customer->name }} · {{ $review->rating }}/5</h2><p class="mt-1 text-xs text-slate-600">Booking {{ $review->booking->booking_code }} · {{ $review->created_at->format('M j, Y') }}</p>@if ($review->comment)<p class="mt-3 whitespace-pre-wrap text-sm text-slate-800">{{ $review->comment }}</p>@endif</div>
                        <span class="px-2 py-1 text-xs font-semibold {{ $review->status === 'visible' ? 'bg-emerald-100 text-emerald-900' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($review->status) }}</span>
                    </div>
                    <form class="mt-5 grid gap-4 border-t border-slate-200 pt-4 sm:grid-cols-[180px_minmax(0,1fr)_auto]" method="post" action="{{ route('admin.reviews.update', $review) }}">
                        @csrf @method('PATCH')
                        <label class="text-sm font-medium text-slate-700">Visibility<select class="mt-1 block min-h-11 w-full border-slate-300" name="status" required>@foreach ($statuses as $status)<option value="{{ $status }}" @selected($review->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
                        <label class="text-sm font-medium text-slate-700">Response to customer<textarea class="mt-1 block w-full border-slate-300" name="admin_response" rows="3" maxlength="2000">{{ $review->admin_response }}</textarea></label>
                        <button class="min-h-11 self-end bg-yellow-300 px-4 text-sm font-bold text-slate-950" type="submit">Save review</button>
                    </form>
                </article>
            @empty
                <p class="border border-slate-200 bg-white px-5 py-10 text-center text-slate-600">No reviews match this filter.</p>
            @endforelse
        </div>
        @if ($reviews->hasPages())<div>{{ $reviews->links() }}</div>@endif
    </div></div>
</x-app-layout>

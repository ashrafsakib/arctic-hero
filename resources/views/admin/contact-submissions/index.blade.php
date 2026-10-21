<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-gray-800">Contact inbox</h2></x-slot>

    <div class="admin-page py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-5">
                <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">Website messages</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">Contact inbox</h1><p class="mt-2 text-sm text-slate-600">Review questions and requests sent through the public contact page.</p></div>
            </div>

            @if (session('success'))<div class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</div>@endif
            @if ($errors->any())<div class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert">{{ $errors->first() }}</div>@endif

            <div class="grid gap-3 sm:grid-cols-3">
                <a class="border border-slate-200 bg-white p-4 text-slate-900 hover:border-yellow-500" href="{{ route('admin.contact-submissions.index', ['status' => 'new']) }}"><span class="text-sm text-slate-600">New</span><strong class="mt-1 block text-2xl">{{ $newCount }}</strong></a>
                <a class="border border-slate-200 bg-white p-4 text-slate-900 hover:border-yellow-500" href="{{ route('admin.contact-submissions.index', ['status' => 'read']) }}"><span class="text-sm text-slate-600">Read</span><strong class="mt-1 block text-2xl">{{ $readCount }}</strong></a>
                <a class="border border-slate-200 bg-white p-4 text-slate-900 hover:border-yellow-500" href="{{ route('admin.contact-submissions.index', ['status' => 'closed']) }}"><span class="text-sm text-slate-600">Closed</span><strong class="mt-1 block text-2xl">{{ $closedCount }}</strong></a>
            </div>

            <section class="overflow-hidden border border-slate-200 bg-white">
                <form class="flex flex-wrap items-end gap-3 border-b border-slate-200 p-4" method="get" action="{{ route('admin.contact-submissions.index') }}">
                    <label class="min-w-56 flex-1 text-sm font-medium text-slate-700">Search messages
                        <input class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" type="search" name="search" value="{{ request('search') }}" placeholder="Name, email or message">
                    </label>
                    <label class="w-full sm:w-40 text-sm font-medium text-slate-700">Topic
                        <select class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" name="topic"><option value="">All topics</option>@foreach ($topics as $topic)<option value="{{ $topic }}" @selected(request('topic') === $topic)>{{ ucfirst($topic) }}</option>@endforeach</select>
                    </label>
                    <label class="w-full sm:w-40 text-sm font-medium text-slate-700">Status
                        <select class="mt-1 block min-h-10 w-full border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                    </label>
                    <button class="min-h-10 bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Filter</button>
                    @if (request()->hasAny(['search', 'topic', 'status']))<a class="min-h-10 px-2 py-2 text-sm text-slate-700 underline underline-offset-4" href="{{ route('admin.contact-submissions.index') }}">Clear</a>@endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] border-collapse text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600"><tr><th class="px-4 py-3 font-semibold">Sender</th><th class="px-4 py-3 font-semibold">Topic</th><th class="px-4 py-3 font-semibold">Message</th><th class="px-4 py-3 font-semibold">Received</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 text-right font-semibold">Action</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($submissions as $submission)
                                <tr class="{{ $submission->status === 'new' ? 'bg-yellow-50/50' : 'hover:bg-slate-50' }}">
                                    <td class="px-4 py-4"><a class="font-semibold text-slate-900 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.contact-submissions.show', $submission) }}">{{ $submission->name }}</a><a class="mt-1 block text-xs text-slate-600 hover:text-slate-900" href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></td>
                                    <td class="px-4 py-4 text-slate-700">{{ ucfirst($submission->topic) }}</td>
                                    <td class="max-w-xs px-4 py-4 text-slate-600">{{ \Illuminate\Support\Str::limit($submission->message, 90) }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-slate-600">{{ $submission->created_at->format('M j, Y') }}<span class="mt-1 block text-xs">{{ $submission->created_at->format('g:i A') }}</span></td>
                                    <td class="px-4 py-4"><span class="inline-flex px-2 py-1 text-xs font-semibold {{ $submission->status === 'new' ? 'bg-yellow-100 text-yellow-900' : ($submission->status === 'closed' ? 'bg-slate-100 text-slate-700' : 'bg-sky-100 text-sky-900') }}">{{ ucfirst($submission->status) }}</span></td>
                                    <td class="px-4 py-4 text-right"><a class="font-semibold text-slate-800 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.contact-submissions.show', $submission) }}">Open</a></td>
                                </tr>
                            @empty
                                <tr><td class="px-4 py-12 text-center text-slate-600" colspan="6">No contact submissions match these filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($submissions->hasPages())<div class="border-t border-slate-200 px-4 py-3">{{ $submissions->links() }}</div>@endif
            </section>
        </div>
    </div>
</x-app-layout>
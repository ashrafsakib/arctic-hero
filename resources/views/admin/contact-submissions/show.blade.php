<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-4"><h2 class="text-xl font-semibold leading-tight text-gray-800">Contact submission</h2><a class="text-sm font-semibold text-slate-700 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.contact-submissions.index') }}">← Inbox</a></div></x-slot>

    <div class="admin-page py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))<div class="border-l-4 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-950" role="status">{{ session('success') }}</div>@endif
            <section class="border border-slate-200 bg-white">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 p-5 sm:p-7">
                    <div><p class="text-xs font-bold uppercase tracking-widest text-yellow-700">{{ ucfirst($submission->topic) }} · Received {{ $submission->created_at->format('M j, Y · g:i A') }}</p><h1 class="mt-2 text-3xl font-semibold text-slate-900">{{ $submission->name }}</h1><div class="mt-2 flex flex-wrap gap-x-5 gap-y-2 text-sm"><a class="text-slate-800 underline decoration-yellow-500 underline-offset-4" href="mailto:{{ $submission->email }}">{{ $submission->email }}</a>@if ($submission->phone)<a class="text-slate-700" href="tel:{{ $submission->phone }}">{{ $submission->phone }}</a>@endif</div></div>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $submission->status === 'new' ? 'bg-yellow-100 text-yellow-900' : ($submission->status === 'closed' ? 'bg-slate-100 text-slate-700' : 'bg-sky-100 text-sky-900') }}">{{ ucfirst($submission->status) }}</span>
                </div>
                <div class="p-5 sm:p-7"><h2 class="text-sm font-semibold text-slate-700">Message</h2><p class="mt-3 whitespace-pre-wrap text-sm leading-7 text-slate-800">{{ $submission->message }}</p></div>
                <div class="flex flex-wrap items-end justify-between gap-5 border-t border-slate-200 bg-slate-50 p-5 sm:p-7">
                    <div><p class="text-xs font-bold uppercase tracking-widest text-slate-500">Email notification</p><p class="mt-1 text-sm text-slate-800">{{ ucfirst($submission->email_status) }}@if ($submission->email_sent_at) · {{ $submission->email_sent_at->format('M j, Y g:i A') }}@endif</p></div>
                    <div class="flex flex-wrap items-end gap-3">
                        <form class="flex items-end gap-2" action="{{ route('admin.contact-submissions.update', $submission) }}" method="post">
                            @csrf
                            @method('PATCH')
                            <label class="text-xs font-semibold text-slate-600">Status<select class="mt-1 block min-h-10 border-slate-300 text-sm focus:border-yellow-600 focus:ring-yellow-500" name="status"><option value="new" @selected($submission->status === 'new')>New</option><option value="read" @selected($submission->status === 'read')>Read</option><option value="closed" @selected($submission->status === 'closed')>Closed</option></select></label>
                            <button class="min-h-10 bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Update</button>
                        </form>
                        <form action="{{ route('admin.contact-submissions.destroy', $submission) }}" method="post" onsubmit="return confirm('Delete this contact submission?')">
                            @csrf
                            @method('DELETE')
                            <button class="min-h-10 border border-red-300 px-4 text-sm font-semibold text-red-800 hover:bg-red-50" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </section>
            <div class="flex justify-end"><a class="inline-flex min-h-11 items-center bg-yellow-300 px-4 text-sm font-bold text-slate-950 hover:bg-yellow-400" href="mailto:{{ $submission->email }}?subject={{ rawurlencode('Re: Arctic Hero contact') }}">Reply to sender ↗</a></div>
        </div>
    </div>
</x-app-layout>
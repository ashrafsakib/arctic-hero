<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-slate-800">Edit map pin</h2></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
        @if ($errors->any())<p class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm" role="alert">{{ $errors->first() }}</p>@endif
        <section class="border border-slate-200 bg-white p-5 sm:p-6"><form method="post" action="{{ route('admin.locations.update', $location) }}" class="space-y-6">@csrf @method('PATCH') @include('admin.locations._form')<div class="flex justify-end gap-3"><a class="px-4 py-3 text-sm underline" href="{{ route('admin.locations.index') }}">Cancel</a><button class="bg-yellow-300 px-5 py-3 text-sm font-bold text-slate-950" type="submit">Save location</button></div></form></section>
    </div></div>
</x-app-layout>

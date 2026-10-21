<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-gray-800">Edit vehicle</h2></x-slot>
    <div class="admin-page py-8"><div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div><a class="text-sm font-medium text-slate-700 underline decoration-yellow-500 underline-offset-4" href="{{ route('admin.vehicles.show', $vehicle) }}">← Vehicle details</a><h1 class="mt-4 text-3xl font-semibold text-slate-900">Edit {{ $vehicle->name }}</h1><p class="mt-2 text-sm text-slate-600">Update the vehicle’s fleet record.</p></div>
        @if ($errors->any())<div class="border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-950" role="alert"><p class="font-semibold">Check the highlighted details</p><ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form class="space-y-6 border border-slate-200 bg-white p-5 sm:p-7" action="{{ route('admin.vehicles.update', $vehicle) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.vehicles._form')
            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5"><a class="px-4 py-3 text-sm font-semibold text-slate-700 underline underline-offset-4" href="{{ route('admin.vehicles.show', $vehicle) }}">Cancel</a><button class="bg-yellow-300 px-5 py-3 text-sm font-bold text-slate-950 hover:bg-yellow-400" type="submit">Save changes</button></div>
        </form>
    </div></div>
</x-app-layout>
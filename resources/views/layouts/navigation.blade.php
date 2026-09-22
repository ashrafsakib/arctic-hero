<nav x-data="{ open: false }" @keydown.escape.window="open = false" class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-[4.5rem] items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ config('company.name') }} home">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-950 text-cyan-300 shadow-sm">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 16.5 7.2 8h9.6l2.2 8.5M7.2 12h9.6M8 16.5v2m8-2v2M6 16.5h12M7 8l1.4-2h7.2L17 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="8" cy="16.5" r="1" fill="currentColor" />
                        <circle cx="16" cy="16.5" r="1" fill="currentColor" />
                    </svg>
                </span>
                <span class="text-lg font-bold tracking-tight text-slate-950">{{ config('company.name') }}</span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="rounded-lg px-4 py-2 text-sm font-medium">
                    {{ __('Overview') }}
                </x-nav-link>
                <a href="{{ url('/#services') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-950">Services</a>
                <a href="{{ url('/#how-it-works') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-950">How it works</a>
                <a href="{{ url('/#contact') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-950">Help</a>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-full py-2 pl-2 pr-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50" aria-label="Open profile">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold uppercase text-cyan-900">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    <span class="max-w-28 truncate">{{ Auth::user()->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">Log out</button>
                </form>
            </div>

            <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-navigation" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 md:hidden">
                <span class="sr-only">Toggle navigation</span>
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg x-cloak x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 6 12 12M6 18 18 6" /></svg>
            </button>
        </div>

        <div id="mobile-navigation" x-cloak x-show="open" x-transition.origin.top class="border-t border-slate-100 pb-5 pt-3 md:hidden">
            <div class="space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Overview') }}</x-responsive-nav-link>
                <a href="{{ url('/#services') }}" @click="open = false" class="block rounded-lg px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Services</a>
                <a href="{{ url('/#how-it-works') }}" @click="open = false" class="block rounded-lg px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">How it works</a>
                <a href="{{ url('/#contact') }}" @click="open = false" class="block rounded-lg px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Help</a>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 px-3 pt-4">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 text-sm font-medium text-slate-800">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold uppercase text-cyan-900">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    <span><span class="block">{{ Auth::user()->name }}</span><span class="block text-xs font-normal text-slate-500">Manage account</span></span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-950">Log out</button>
                </form>
            </div>
        </div>
    </div>
</nav>

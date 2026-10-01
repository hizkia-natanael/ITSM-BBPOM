<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded-xl p-1 transition">
                        <x-application-logo class="block h-9 sm:h-10 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 {{ request()->routeIs('dashboard') ? 'text-emerald-800 border-b-2 border-emerald-600 font-semibold' : 'text-slate-600 hover:text-emerald-700' }}">
                        <i class="ri-dashboard-line text-base me-1.5"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index') || request()->routeIs('tickets.show')" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 {{ (request()->routeIs('tickets.index') || request()->routeIs('tickets.show')) ? 'text-emerald-800 border-b-2 border-emerald-600 font-semibold' : 'text-slate-600 hover:text-emerald-700' }}">
                        <i class="ri-file-list-3-line text-base me-1.5"></i>
                        {{ __('Daftar Pengaduan') }}
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                        <a href="/admin" class="inline-flex items-center px-3 py-2 text-sm font-medium text-emerald-800 hover:text-emerald-950 transition duration-150 my-auto">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="ri-shield-keyhole-line text-sm"></i>
                                Panel Admin (Filament)
                            </span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-slate-200 text-sm leading-4 font-medium rounded-xl !text-slate-700 !bg-white hover:!bg-slate-50 focus:outline-none transition ease-in-out duration-150 shadow-2xs">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <div class="text-start">
                                    <div class="font-semibold text-xs !text-slate-900 leading-tight">{{ Auth::user()->name }}</div>
                                    <div class="text-[10px] !text-slate-500 uppercase leading-tight mt-0.5">{{ Auth::user()->role }}</div>
                                </div>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 !text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2.5 border-b border-slate-100 text-xs !text-slate-500 !bg-white">
                            Masuk sebagai <strong class="!text-slate-900 block truncate">{{ Auth::user()->email }}</strong>
                        </div>

                        <div class="!bg-white py-1">
                            <x-dropdown-link :href="route('profile.edit')" class="text-xs !text-slate-700 hover:!text-emerald-800 hover:!bg-slate-50">
                                <i class="ri-user-line me-1.5 text-slate-400"></i>
                                {{ __('Profil Saya') }}
                            </x-dropdown-link>

                            @if(Auth::user()->isAdmin())
                                <x-dropdown-link href="/admin" class="text-xs !text-emerald-800 font-semibold hover:!bg-emerald-50">
                                    <i class="ri-dashboard-line me-1.5 text-emerald-600"></i>
                                    {{ __('Masuk Panel Admin') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();" class="text-xs !text-rose-600 hover:!bg-rose-50">
                                    <i class="ri-logout-box-line me-1.5 text-rose-500"></i>
                                    {{ __('Keluar (Log Out)') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden !bg-white border-t border-slate-200">
        <div class="pt-2 pb-3 space-y-1 !bg-white">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index')">
                {{ __('Daftar Pengaduan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')">
                {{ __('Buat Pengaduan Baru') }}
            </x-responsive-nav-link>
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link href="/admin">
                    {{ __('Panel Admin (Filament)') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-200 !bg-white">
            <div class="px-4">
                <div class="font-semibold text-base !text-slate-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs !text-slate-500">{{ Auth::user()->email }} ({{ Auth::user()->role }})</div>
            </div>

            <div class="mt-3 space-y-1 !bg-white">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="!text-rose-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

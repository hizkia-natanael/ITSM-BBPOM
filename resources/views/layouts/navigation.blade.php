<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 sm:h-[4.25rem]">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center pe-4 sm:pe-5 sm:border-e sm:border-slate-200/80">
                    <a href="{{ route('dashboard') }}" class="focus:outline-none focus:ring-2 focus:ring-emerald-500 rounded-xl p-1 transition flex items-center">
                        <x-application-logo class="block h-9 sm:h-10 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-3 sm:-my-px sm:ms-4 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 {{ request()->routeIs('dashboard') ? 'text-emerald-800 border-b-2 border-emerald-600 font-semibold' : 'text-slate-600 hover:text-emerald-700' }}">
                        <i class="ri-dashboard-line text-base me-1.5"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index') || request()->routeIs('tickets.show')" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 {{ (request()->routeIs('tickets.index') || request()->routeIs('tickets.show')) ? 'text-emerald-800 border-b-2 border-emerald-600 font-semibold' : 'text-slate-600 hover:text-emerald-700' }}">
                        <i class="ri-file-list-3-line text-base me-1.5"></i>
                        {{ __('Daftar Pengaduan') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 {{ request()->routeIs('tickets.create') ? 'text-emerald-800 border-b-2 border-emerald-600 font-semibold' : 'text-slate-600 hover:text-emerald-700' }}">
                        <i class="ri-apps-2-line text-base me-1.5"></i>
                        {{ __('Katalog Layanan') }}
                    </x-nav-link>

                    <x-nav-link :href="route('guide.index')" :active="request()->routeIs('guide.index')" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 {{ request()->routeIs('guide.index') ? 'text-emerald-800 border-b-2 border-emerald-600 font-semibold' : 'text-slate-600 hover:text-emerald-700' }}">
                        <i class="ri-book-read-line text-base me-1.5"></i>
                        {{ __('Buku Panduan') }}
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                        <a href="{{ url('/admin') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-emerald-800 hover:text-emerald-950 transition duration-150 my-auto">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="ri-shield-keyhole-line text-sm"></i>
                                Panel Admin (Filament)
                            </span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-1 sm:gap-2">
                <!-- Notification Bell (balasan tim IT) -->
                <x-user-reply-notification-bell />

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-2">
                    <x-user-profile-menu />
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
                {{ __('Katalog Layanan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('guide.index')" :active="request()->routeIs('guide.index')">
                <i class="ri-book-read-line me-1.5 text-slate-400"></i>
                {{ __('Buku Panduan') }}
            </x-responsive-nav-link>
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="url('/admin')">
                    {{ __('Panel Admin (Filament)') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-200 !bg-white">
            @php
                $mobileUser = Auth::user();
                $mobileParts = explode(' ', trim($mobileUser->name));
                $mobileInitials = count($mobileParts) >= 2
                    ? strtoupper(substr($mobileParts[0], 0, 1) . substr($mobileParts[1], 0, 1))
                    : strtoupper(substr($mobileUser->name, 0, 2));
            @endphp
            <div class="px-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs ring-2 ring-emerald-500/20">
                    {{ $mobileInitials }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-sm text-slate-900 truncate">{{ $mobileUser->name }}</div>
                    <div class="text-xs text-slate-500 font-mono truncate mt-0.5">
                        {{ $mobileUser->nip ? 'NIP. ' . $mobileUser->nip : 'Pegawai BBPOM' }}
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1 !bg-white">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="ri-user-line me-1.5 text-slate-400"></i>
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="!text-rose-600">
                        <i class="ri-logout-box-r-line me-1.5 text-rose-500"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

@php
    $user = Auth::user();
    $nameParts = explode(' ', trim($user->name));
    $initials = count($nameParts) >= 2
        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
        : strtoupper(substr($user->name, 0, 2));
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false" @keydown.escape.window="open = false">
    <!-- Trigger Pill Button (Minimalist Glassmorphic) -->
    <button 
        type="button"
        @click="open = ! open"
        :aria-expanded="open.toString()"
        class="group relative inline-flex items-center gap-2.5 p-1 sm:pe-3 rounded-2xl bg-white/95 hover:bg-white border border-slate-200/90 hover:border-emerald-500/40 shadow-2xs hover:shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all duration-150 active:scale-[0.99]"
        title="Menu Profil Pengguna"
    >
        <div class="relative shrink-0">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-xs ring-2 ring-emerald-500/15 group-hover:ring-emerald-500/35 transition-all">
                {{ $initials }}
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white" title="Status: Online & Terverifikasi"></span>
        </div>

        <div class="text-start hidden sm:block max-w-[140px] md:max-w-[180px]">
            <div class="font-bold text-xs text-slate-900 truncate leading-tight group-hover:text-emerald-950 transition-colors">
                {{ $user->name }}
            </div>
            <div class="text-[10px] text-slate-500 font-mono truncate mt-0.5" title="NIP. {{ $user->nip ?: '-' }}">
                {{ $user->nip ? 'NIP. ' . $user->nip : 'Pegawai BBPOM' }}
            </div>
        </div>

        <div class="ms-0.5 text-slate-400 group-hover:text-emerald-700 transition-colors shrink-0">
            <i class="ri-arrow-down-s-line text-base transition-transform duration-200" :class="{ 'rotate-180 text-emerald-700': open }"></i>
        </div>
    </button>

    <!-- Compact Dropdown Panel -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        class="absolute z-50 mt-2 w-64 sm:w-72 end-0 rounded-2xl bg-white shadow-xl shadow-slate-950/15 border border-slate-200/90 ring-1 ring-black/5 overflow-hidden"
        style="display: none;"
        @click="open = false"
    >
        <!-- Identity Header Card -->
        <div class="p-3.5 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-500/20 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-md ring-2 ring-white/20">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs sm:text-sm font-bold text-white tracking-tight truncate" title="{{ $user->name }}">
                        {{ $user->name }}
                    </h4>
                    <p class="text-[11px] text-emerald-300 font-mono truncate mt-0.5" title="{{ $user->nip ? 'NIP. ' . $user->nip : 'Pegawai BBPOM' }}">
                        {{ $user->nip ? 'NIP. ' . $user->nip : 'Pegawai BBPOM' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Menu Links -->
        <div class="p-1.5 space-y-1 bg-white">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-emerald-800 hover:bg-slate-50 transition group">
                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 group-hover:bg-emerald-100 group-hover:text-emerald-800 flex items-center justify-center shrink-0 transition text-sm">
                    <i class="ri-user-settings-line"></i>
                </div>
                <span class="text-xs font-semibold text-slate-800 group-hover:text-emerald-900 transition flex-1">Profil Saya</span>
                <i class="ri-arrow-right-s-line text-slate-300 group-hover:text-emerald-600 transition"></i>
            </a>

            <a href="{{ route('guide.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-emerald-800 hover:bg-slate-50 transition group">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100 group-hover:text-emerald-800 flex items-center justify-center shrink-0 transition text-sm">
                    <i class="ri-book-read-line"></i>
                </div>
                <span class="text-xs font-semibold text-slate-800 group-hover:text-emerald-900 transition flex-1">Buku Panduan</span>
                <i class="ri-arrow-right-s-line text-slate-300 group-hover:text-emerald-600 transition"></i>
            </a>

            @if($user->isAdmin())
                <a href="/admin" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-emerald-900 bg-emerald-50/60 hover:bg-emerald-100/80 border border-emerald-200/60 transition group">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs text-sm">
                        <i class="ri-shield-keyhole-line"></i>
                    </div>
                    <span class="text-xs font-semibold text-emerald-950 flex-1">Panel Admin (Filament)</span>
                    <i class="ri-external-link-line text-emerald-700 text-xs"></i>
                </a>
            @endif
        </div>

        <!-- Footer: Log Out Option -->
        <div class="p-1.5 border-t border-slate-100 bg-slate-50/70">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 active:bg-rose-100 border border-transparent hover:border-rose-200 transition-all group">
                    <span class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-rose-50 group-hover:bg-rose-100 text-rose-600 flex items-center justify-center transition text-sm">
                            <i class="ri-logout-box-r-line"></i>
                        </span>
                        <span>Keluar (Log Out)</span>
                    </span>
                    <i class="ri-arrow-right-s-line text-rose-400 group-hover:translate-x-0.5 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>
</div>

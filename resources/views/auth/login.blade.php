<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TIK BBPOM di Semarang — Portal Masuk</title>

    <!-- Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Remix Icon Library -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 min-h-screen relative flex items-center justify-center p-4 sm:p-6 lg:p-8 select-none">

    <!-- Fullscreen Nature Background with Soft Blur & Vignette -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <img
            src="{{ asset('images/bg-waterfall.jpg') }}"
            alt="Latar Belakang Alam"
            class="w-full h-full object-cover filter blur-[4px] scale-105 brightness-[0.82] contrast-[1.05]"
        >
        <div class="absolute inset-0 bg-slate-950/30"></div>
    </div>

    <!-- Centered Modal Card Container -->
    <div class="w-full max-w-4xl bg-slate-950/40 rounded-[28px] sm:rounded-[32px] p-1.5 sm:p-2 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.65)] border border-white/20 backdrop-blur-md">
        <div class="grid grid-cols-1 md:grid-cols-2 rounded-[24px] sm:rounded-[26px] overflow-hidden">

            <!-- LEFT COLUMN: Dark Portal Branding -->
            <div class="relative bg-gradient-to-b from-[#0c1424] via-[#0d1627] to-[#0a101d] text-white p-8 sm:p-10 lg:p-12 flex flex-col justify-between overflow-hidden">
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex items-center gap-2.5 text-cyan-400 text-[11px] sm:text-xs font-semibold tracking-widest uppercase">
                    <span class="w-5 h-[2px] bg-cyan-400 rounded-full"></span>
                    OFFICIAL PORTAL
                </div>

                <div class="relative z-10 my-8 sm:my-auto py-4">
                    <div class="inline-block">
                        <div class="flex items-center gap-2">
                            <span class="text-4xl sm:text-5xl font-black tracking-tight text-white">TIK</span>
                            <span class="text-4xl sm:text-5xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">BBPOM</span>
                        </div>
                        <div class="h-1 w-16 bg-gradient-to-r from-cyan-400 to-emerald-400 rounded-full mt-2"></div>
                    </div>
                    <p class="mt-5 text-sm sm:text-base font-semibold text-slate-100 leading-snug">
                        Layanan Teknologi Informasi dan Komunikasi
                    </p>
                    <p class="mt-1 text-xs text-slate-400 leading-relaxed">
                        Balai Besar Pengawas Obat dan Makanan di Semarang
                    </p>
                </div>

                <div class="relative z-10 pt-6 border-t border-slate-800/80">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2.5">INTEGRATED WITH</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-[11px] font-medium text-slate-200 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            BBPOM Semarang
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-[11px] font-medium text-slate-200 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Badan POM RI
                        </span>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Frosted Glass Form -->
            <div class="relative bg-white/90 sm:bg-white/85 backdrop-blur-2xl p-8 sm:p-10 lg:p-12 flex flex-col justify-between select-text border-t md:border-t-0 md:border-l border-white/50">
                <div>
                    <!-- Logo & Heading -->
                    <div class="text-center mb-6">
                        <img
                            src="{{ asset('images/logo-bpom.png') }}"
                            alt="Logo Badan POM"
                            class="h-14 sm:h-16 w-auto mx-auto object-contain drop-shadow-sm mb-3"
                        >
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Selamat Datang</h2>
                        <p class="text-xs text-slate-500 mt-1">Silakan masuk untuk mengakses portal Layanan TIK</p>
                    </div>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs font-medium text-rose-700">
                            <div class="flex items-center gap-2 mb-1 font-semibold text-rose-800">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Gagal Masuk</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="loginData()" @submit="onSubmit">
                        @csrf

                        {{-- Hidden email — populated by Alpine on user selection --}}
                        <input type="hidden" name="email" x-model="selectedEmail">

                        <!-- 1. Nama Pegawai — Searchable Dropdown -->
                        <div class="relative">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                <i class="ri-user-line text-slate-500 text-sm"></i>
                                <span>Nama Pegawai</span>
                            </label>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-10">
                                    <i class="ri-search-line text-sm"></i>
                                </div>
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="open = true"
                                    @input="open = true"
                                    @blur="closeSoon"
                                    @keydown.escape="open = false"
                                    @keydown.enter.prevent="selectFirst"
                                    @keydown.arrow-down.prevent="highlightNext"
                                    @keydown.arrow-up.prevent="highlightPrev"
                                    placeholder="Ketik nama untuk mencari pegawai..."
                                    autocomplete="off"
                                    :class="selectedEmail ? 'border-emerald-500/60 focus:border-emerald-600 bg-white' : 'border-slate-300 bg-slate-50/90 hover:bg-white focus:bg-white focus:border-slate-800'"
                                    class="w-full pl-10 pr-10 py-2.5 border focus:ring-2 focus:ring-slate-800/10 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition"
                                >
                                <!-- Clear button — mousedown.prevent keeps focus on input -->
                                <button
                                    type="button"
                                    x-show="selectedEmail"
                                    @mousedown.prevent="clearSelection"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-rose-500 transition"
                                    title="Hapus pilihan"
                                    style="display: none;"
                                >
                                    <i class="ri-close-circle-line text-sm"></i>
                                </button>
                                <!-- Chevron -->
                                <div x-show="!selectedEmail" class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="ri-arrow-down-s-line text-sm" x-show="!open"></i>
                                    <i class="ri-arrow-up-s-line text-sm" x-show="open" style="display: none;"></i>
                                </div>
                            </div>

                            <!-- Floating Panel -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-2xl shadow-slate-900/10 overflow-hidden"
                                style="display: none;"
                            >
                                <!-- Empty state -->
                                <div x-show="filteredUsers.length === 0" class="px-4 py-4 text-center">
                                    <i class="ri-user-search-line text-slate-300 text-xl mb-1 block"></i>
                                    <p class="text-xs text-slate-400">Pegawai tidak ditemukan</p>
                                </div>

                                <!-- Grouped results -->
                                <div x-show="filteredUsers.length > 0" class="max-h-56 overflow-y-auto">

                                    <!-- Admin group -->
                                    <template x-if="adminUsers.length > 0">
                                        <div>
                                            <div class="px-3.5 py-1.5 bg-slate-50 border-b border-slate-100">
                                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Administrator</p>
                                            </div>
                                            <template x-for="user in adminUsers" :key="'a-' + user.id">
                                                <div
                                                    @mousedown.prevent="selectUser(user)"
                                                    @mouseenter="highlightedId = user.id"
                                                    :class="highlightedId === user.id ? 'bg-emerald-50' : 'hover:bg-slate-50'"
                                                    class="flex items-center justify-between gap-3 px-3.5 py-2.5 cursor-pointer transition-colors border-b border-slate-50/80"
                                                >
                                                    <p class="text-xs font-medium text-slate-800 truncate" x-text="user.name"></p>
                                                    <span class="flex-shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Admin</span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Pegawai group -->
                                    <template x-if="pegawaiUsers.length > 0">
                                        <div>
                                            <div class="px-3.5 py-1.5 bg-slate-50 border-b border-slate-100">
                                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Pegawai</p>
                                            </div>
                                            <template x-for="user in pegawaiUsers" :key="'p-' + user.id">
                                                <div
                                                    @mousedown.prevent="selectUser(user)"
                                                    @mouseenter="highlightedId = user.id"
                                                    :class="highlightedId === user.id ? 'bg-emerald-50' : 'hover:bg-slate-50'"
                                                    class="flex items-center justify-between gap-3 px-3.5 py-2.5 cursor-pointer transition-colors border-b border-slate-50/80 last:border-0"
                                                >
                                                    <p class="text-xs font-medium text-slate-800 truncate" x-text="user.name"></p>
                                                    <span class="flex-shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Pegawai</span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                </div>
                            </div>
                        </div>

                        <!-- 2. NIP — auto-filled read-only -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                <i class="ri-id-card-line text-slate-500 text-sm"></i>
                                <span>NIP</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="ri-barcode-line text-sm"></i>
                                </div>
                                <input
                                    type="text"
                                    :value="selectedNip"
                                    placeholder="Terisi otomatis setelah pilih nama..."
                                    readonly
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono text-slate-700 placeholder:text-slate-400 placeholder:font-sans shadow-sm cursor-default select-all"
                                >
                            </div>
                        </div>

                        <!-- 3. Password — manual input -->
                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                <i class="ri-lock-password-line text-slate-500 text-sm"></i>
                                <span>Password</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="ri-key-line text-sm"></i>
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password..."
                                    class="w-full pl-10 pr-11 py-2.5 bg-slate-50/90 hover:bg-white focus:bg-white border border-slate-300 focus:border-slate-800 focus:ring-2 focus:ring-slate-800/10 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition"
                                >
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition"
                                    title="Tampilkan / Sembunyikan Password"
                                >
                                    <i x-show="!showPassword" class="ri-eye-line text-sm"></i>
                                    <i x-show="showPassword" class="ri-eye-off-line text-sm text-emerald-600" style="display: none;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="rounded border-slate-300 text-slate-800 shadow-sm focus:ring-slate-800"
                                >
                                <span class="ms-2 text-xs text-slate-600">Ingat saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full bg-[#1e293b] hover:bg-[#0f172a] active:scale-[0.99] text-white py-3 px-4 rounded-xl text-xs sm:text-sm font-semibold tracking-wide shadow-lg shadow-slate-900/25 transition-all duration-150 flex items-center justify-center gap-2 group cursor-pointer"
                            >
                                <span>Masuk Situs</span>
                                <i class="ri-arrow-right-line text-sm transition-transform group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="mt-8 pt-4 border-t border-slate-200/60 text-center">
                    <p class="text-[11px] text-slate-500 font-medium">
                        &copy; {{ date('Y') }} Badan Pengawas Obat dan Makanan
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Alpine.js — all state in named function (AGENTS.md HTML-parser safety rule) --}}
    <script>
        function loginData() {
            var allUsers = @json($users);

            return {
                search: '',
                open: false,
                selectedEmail: '',
                selectedName: '',
                selectedNip: '',
                highlightedId: null,
                users: allUsers,
                _closeTimer: null,

                // Filtered list matching search query
                get filteredUsers() {
                    var q = this.search.trim().toLowerCase();
                    if (q === '') { return this.users; }
                    return this.users.filter(function(u) {
                        return u.name.toLowerCase().indexOf(q) !== -1 ||
                            (u.nip && u.nip.indexOf(q) !== -1);
                    });
                },

                // Grouped computed props for the two sections
                get adminUsers() {
                    return this.filteredUsers.filter(function(u) { return u.role === 'admin'; });
                },

                get pegawaiUsers() {
                    return this.filteredUsers.filter(function(u) { return u.role !== 'admin'; });
                },

                // KEY FIX: close with a small delay so @mousedown.prevent on items
                // can fire selectUser() before the dropdown is hidden.
                closeSoon: function() {
                    var self = this;
                    self._closeTimer = setTimeout(function() {
                        self.open = false;
                    }, 150);
                },

                selectUser: function(user) {
                    var self = this;
                    // Cancel any pending close from @blur
                    clearTimeout(self._closeTimer);
                    self.selectedEmail = user.email;
                    self.selectedName = user.name;
                    self.selectedNip = user.nip || '';
                    self.search = user.name;
                    self.open = false;
                    self.highlightedId = null;
                    self.$nextTick(function() {
                        var pw = document.getElementById('password');
                        if (pw) { pw.focus(); }
                    });
                },

                clearSelection: function() {
                    clearTimeout(this._closeTimer);
                    this.selectedEmail = '';
                    this.selectedName = '';
                    this.selectedNip = '';
                    this.search = '';
                    this.open = false;
                    this.highlightedId = null;
                },

                selectFirst: function() {
                    if (this.filteredUsers.length > 0) {
                        var target = this.highlightedId
                            ? this.filteredUsers.find(function(u) { return u.id === this.highlightedId; }, this)
                            : null;
                        this.selectUser(target || this.filteredUsers[0]);
                    }
                },

                highlightNext: function() {
                    var list = this.filteredUsers;
                    if (list.length === 0) { return; }
                    var idx = list.findIndex(function(u) { return u.id === this.highlightedId; }, this);
                    var next = idx < list.length - 1 ? idx + 1 : 0;
                    this.highlightedId = list[next].id;
                    this.open = true;
                },

                highlightPrev: function() {
                    var list = this.filteredUsers;
                    if (list.length === 0) { return; }
                    var idx = list.findIndex(function(u) { return u.id === this.highlightedId; }, this);
                    var prev = idx > 0 ? idx - 1 : list.length - 1;
                    this.highlightedId = list[prev].id;
                },

                onSubmit: function(e) {
                    if (!this.selectedEmail) {
                        e.preventDefault();
                        this.open = true;
                    }
                },
            };
        }
    </script>

</body>
</html>

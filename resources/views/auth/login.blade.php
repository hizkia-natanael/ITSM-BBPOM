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
                <!-- Ambient Glow Decoration -->
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Top Tagline -->
                <div class="relative z-10 flex items-center gap-2.5 text-cyan-400 text-[11px] sm:text-xs font-semibold tracking-widest uppercase">
                    <span class="w-5 h-[2px] bg-cyan-400 rounded-full"></span>
                    OFFICIAL PORTAL
                </div>

                <!-- Middle Branding -->
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

                <!-- Bottom Badges -->
                <div class="relative z-10 pt-6 border-t border-slate-800/80">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2.5">
                        INTEGRATED WITH
                    </p>
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
                    <!-- Official BPOM Logo & Heading -->
                    <div class="text-center mb-6">
                        <img 
                            src="{{ asset('images/logo-bpom.png') }}" 
                            alt="Logo Badan POM" 
                            class="h-14 sm:h-16 w-auto mx-auto object-contain drop-shadow-sm mb-3"
                        >
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                            Selamat Datang
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Silakan masuk untuk mengakses portal Layanan TIK
                        </p>
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

                        {{-- Hidden email field — populated by Alpine when user selects a pegawai --}}
                        <input type="hidden" name="email" x-model="selectedEmail">

                        <!-- Pegawai Searchable Dropdown -->
                        <div class="relative">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                                <i class="ri-user-line text-slate-500 text-sm"></i>
                                <span>Nama Pegawai</span>
                            </label>

                            <!-- Search Input -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-10">
                                    <i class="ri-search-line text-sm"></i>
                                </div>
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="open = true"
                                    @input="open = true"
                                    @keydown.escape="open = false"
                                    @keydown.enter.prevent="selectFirst"
                                    @keydown.arrow-down.prevent="highlightNext"
                                    @keydown.arrow-up.prevent="highlightPrev"
                                    placeholder="Ketik nama untuk mencari pegawai..."
                                    autocomplete="off"
                                    :class="selectedEmail ? 'border-emerald-500/60 focus:border-emerald-600 bg-white' : 'border-slate-300 bg-slate-50/90 hover:bg-white focus:bg-white focus:border-slate-800'"
                                    class="w-full pl-10 pr-10 py-2.5 border focus:ring-2 focus:ring-slate-800/10 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition"
                                >
                                <!-- Clear selection button -->
                                <button
                                    type="button"
                                    x-show="selectedEmail"
                                    @click="clearSelection"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-rose-500 transition"
                                    title="Hapus pilihan"
                                    style="display: none;"
                                >
                                    <i class="ri-close-circle-line text-sm"></i>
                                </button>
                                <!-- Chevron indicator -->
                                <div x-show="!selectedEmail" class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="ri-arrow-down-s-line text-sm" x-show="!open"></i>
                                    <i class="ri-arrow-up-s-line text-sm" x-show="open" style="display: none;"></i>
                                </div>
                            </div>

                            <!-- Dropdown panel -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                @click.outside="open = false"
                                class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200/80 rounded-xl shadow-xl overflow-hidden"
                                style="display: none;"
                            >
                                <!-- Results list -->
                                <ul x-show="filteredUsers.length > 0" class="max-h-52 overflow-y-auto divide-y divide-slate-100 py-1">
                                    <template x-for="(user, index) in filteredUsers" :key="user.id">
                                        <li
                                            @click="selectUser(user)"
                                            @mouseenter="highlightedIndex = index"
                                            :class="highlightedIndex === index ? 'bg-slate-100' : 'hover:bg-slate-50'"
                                            class="flex items-center justify-between gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                        >
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                    <i class="ri-user-line text-white text-xs"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-semibold text-slate-800 truncate" x-text="user.name"></p>
                                                    <p class="text-[10px] text-slate-400 font-mono" x-text="user.nip ? user.nip : 'NIP tidak tersedia'"></p>
                                                </div>
                                            </div>
                                            <span
                                                :class="user.role === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                                                class="flex-shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                                x-text="user.role === 'admin' ? 'Admin' : 'Pegawai'"
                                            ></span>
                                        </li>
                                    </template>
                                </ul>
                                <!-- Empty state -->
                                <div x-show="filteredUsers.length === 0" class="px-4 py-3 text-center">
                                    <p class="text-xs text-slate-400">Pegawai tidak ditemukan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Employee Card (shown after selection) -->
                        <div
                            x-show="selectedEmail"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 shadow-sm"
                            style="display: none;"
                        >
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <i class="ri-user-check-line text-white text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700 leading-none mb-0.5">Pegawai Dipilih</p>
                                <p class="text-xs font-semibold text-slate-800 truncate" x-text="selectedName"></p>
                                <p class="text-[10px] text-slate-500 font-mono tracking-wider" x-text="selectedNip ? 'NIP: ' + selectedNip : 'NIP tidak tercatat'"></p>
                            </div>
                            <i class="ri-check-double-line text-emerald-500 text-base flex-shrink-0"></i>
                        </div>

                        <!-- Password Field with Show/Hide Toggle -->
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

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full bg-[#1e293b] hover:bg-[#0f172a] active:scale-[0.99] text-white py-3 px-4 rounded-xl text-xs sm:text-sm font-semibold tracking-wide shadow-lg shadow-slate-900/25 transition-all duration-150 flex items-center justify-center gap-2 group cursor-pointer"
                            >
                                <span>Masuk Situs</span>
                                <i class="ri-arrow-right-line text-sm transition-transform group-hover:translate-x-1"></i>
                            </button>
                        </div>

                        @if (Route::has('register'))
                            <div class="text-center pt-2">
                                <p class="text-xs text-slate-500">
                                    Belum memiliki akun? 
                                    <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                                        Registrasi Pegawai
                                    </a>
                                </p>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Footer Copyright -->
                <div class="mt-8 pt-4 border-t border-slate-200/60 text-center">
                    <p class="text-[11px] text-slate-500 font-medium">
                        &copy; {{ date('Y') }} Badan Pengawas Obat dan Makanan
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Alpine.js login state — all logic in named function per AGENTS.md safety rules --}}
    <script>
        function loginData() {
            var allUsers = @json($users);

            return {
                search: '',
                open: false,
                selectedEmail: '',
                selectedName: '',
                selectedNip: '',
                highlightedIndex: -1,
                users: allUsers,

                get filteredUsers() {
                    var q = this.search.trim().toLowerCase();
                    if (q === '') {
                        return this.users;
                    }
                    return this.users.filter(function(u) {
                        var nameMatch = u.name.toLowerCase().indexOf(q) !== -1;
                        var nipMatch = u.nip && u.nip.indexOf(q) !== -1;
                        return nameMatch || nipMatch;
                    });
                },

                selectUser: function(user) {
                    var self = this;
                    self.selectedEmail = user.email;
                    self.selectedName = user.name;
                    self.selectedNip = user.nip || '';
                    self.search = user.name;
                    self.open = false;
                    self.highlightedIndex = -1;
                    self.$nextTick(function() {
                        var pw = document.getElementById('password');
                        if (pw) { pw.focus(); }
                    });
                },

                clearSelection: function() {
                    this.selectedEmail = '';
                    this.selectedName = '';
                    this.selectedNip = '';
                    this.search = '';
                    this.open = false;
                    this.highlightedIndex = -1;
                },

                selectFirst: function() {
                    if (this.filteredUsers.length > 0) {
                        var idx = this.highlightedIndex >= 0 ? this.highlightedIndex : 0;
                        this.selectUser(this.filteredUsers[idx]);
                    }
                },

                highlightNext: function() {
                    var max = this.filteredUsers.length - 1;
                    if (this.highlightedIndex < max) {
                        this.highlightedIndex++;
                        this.open = true;
                    }
                },

                highlightPrev: function() {
                    if (this.highlightedIndex > 0) {
                        this.highlightedIndex--;
                    }
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

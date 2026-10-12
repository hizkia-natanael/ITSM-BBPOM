<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/logo-bpom.png') }}">

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
<body class="font-sans antialiased text-slate-800 min-h-screen select-none bg-slate-50 overflow-x-hidden">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        <!-- LEFT HALF: Panoramic Full-Height Showcase (6 cols) -->
        <div class="hidden lg:flex lg:col-span-6 relative overflow-hidden flex-col justify-between p-12 xl:p-16 text-white min-h-screen">
            <!-- Full Height Background Image (Gedung Kantor BBPOM di Semarang) -->
            <img
                src="{{ asset('images/kantor-bbpom-semarang.jpg') }}"
                alt="Gedung Kantor Balai Besar POM di Semarang"
                class="absolute inset-0 w-full h-full object-cover object-center scale-105 brightness-[0.82] contrast-[1.05]"
            >
            <!-- Cinematic Dark & Emerald Gradients -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-emerald-950/30"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/70 via-slate-950/20 to-transparent"></div>

            <!-- Top Brand Bar -->
            <div class="relative z-10 flex items-center gap-3">
                <div class="p-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20">
                    <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="h-8 w-auto">
                </div>
                <div>
                    <p class="text-sm font-bold tracking-tight text-white">Balai Besar POM di Semarang</p>
                    <p class="text-[11px] text-emerald-300 font-medium">Pusat Layanan Teknologi Informasi & Komunikasi</p>
                </div>
            </div>

            <!-- Center Manifesto -->
            <div class="relative z-10 my-auto py-8 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-5 backdrop-blur-md">
                    <i class="ri-pulse-fill text-emerald-400"></i>
                    <span>Inovasi Tata Kelola TI</span>
                </div>
                <h1 class="text-4xl xl:text-5xl font-black text-white leading-tight tracking-tight">
                    Melayani dengan Integritas, Kecepatan, dan Akuntabilitas.
                </h1>
                <p class="mt-5 text-sm xl:text-base text-slate-300 leading-relaxed font-normal">
                    Portal terpadu untuk pencatatan tiket gangguan, permohonan fasilitas TI, serta monitoring berkala seluruh infrastruktur teknologi BBPOM di Semarang.
                </p>
            </div>

            <!-- Bottom Stats Strip -->
            <div class="relative z-10 pt-6 border-t border-white/15 grid grid-cols-3 gap-4">
                <div>
                    <p class="text-2xl font-black text-emerald-400">12+</p>
                    <p class="text-[11px] text-slate-300 mt-0.5">Kategori Layanan TI</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-teal-300">4.9 / 5</p>
                    <p class="text-[11px] text-slate-300 mt-0.5">Indeks Kepuasan Layanan</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-cyan-300">100%</p>
                    <p class="text-[11px] text-slate-300 mt-0.5">Terkontrol Digital</p>
                </div>
            </div>
        </div>

        <!-- RIGHT HALF: Authentication Panel (6 cols) -->
        <div class="lg:col-span-6 flex flex-col justify-between p-6 sm:p-10 lg:p-14 xl:p-16 bg-white min-h-screen select-text">
            <div class="w-full max-w-md mx-auto my-auto py-6">

                <!-- Mobile Header Logo (Visible on mobile/tablet) -->
                <div class="lg:hidden text-center mb-6">
                    <img
                        src="{{ asset('images/logo-bpom.png') }}"
                        alt="Logo Badan POM"
                        class="h-14 w-auto mx-auto object-contain mb-3"
                    >
                    <h2 class="text-xl font-black text-slate-900">TIK BBPOM di Semarang</h2>
                </div>

                <!-- Desktop Form Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900">Masuk Akun</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                        Pilih nama pegawai atau masukkan kredensial untuk mengakses sistem helpdesk.
                    </p>
                </div>

                <!-- Session Alert -->
                @if (session('status'))
                    <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800 flex items-center gap-2">
                        <i class="ri-checkbox-circle-fill text-emerald-600 text-base flex-shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-medium text-rose-700">
                        <div class="flex items-center gap-2 mb-1.5 font-semibold text-rose-800">
                            <i class="ri-error-warning-fill text-rose-600 text-base flex-shrink-0"></i>
                            <span>Autentikasi Gagal</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700/90 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="loginData()" @submit="onSubmit">
                    @csrf
                    <input type="hidden" name="email" x-model="selectedEmail">

                    <!-- 1. Nama Pegawai (Searchable Dropdown) -->
                    <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-user-line text-emerald-700"></i>
                                <span>Nama Pegawai</span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-normal">Pencarian Cepat</span>
                        </label>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-10">
                                <i class="ri-search-line"></i>
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
                                placeholder="Cari nama atau NIP..."
                                autocomplete="off"
                                :class="selectedEmail ? 'border-emerald-600 ring-2 ring-emerald-500/10 bg-emerald-50/20 font-medium' : 'border-slate-300 bg-slate-50/60 focus:bg-white focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10'"
                                class="w-full pl-10 pr-10 py-3 border rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition"
                            >

                            <!-- Clear button -->
                            <button
                                type="button"
                                x-show="selectedEmail"
                                @mousedown.prevent="clearSelection"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-rose-600 transition"
                                title="Hapus pilihan"
                                style="display: none;"
                            >
                                <i class="ri-close-circle-fill text-base"></i>
                            </button>

                            <!-- Dropdown arrow -->
                            <div x-show="!selectedEmail" class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ri-arrow-down-s-line text-base" x-show="!open"></i>
                                <i class="ri-arrow-up-s-line text-base" x-show="open" style="display: none;"></i>
                            </div>
                        </div>

                        <!-- Dropdown panel -->
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-120"
                            x-transition:enter-start="opacity-0 -translate-y-1.5"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-80"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1.5"
                            class="absolute z-50 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl shadow-slate-900/15 overflow-hidden"
                            style="display: none;"
                        >
                            <!-- Empty -->
                            <div x-show="isResultsEmpty" class="px-4 py-5 text-center">
                                <i class="ri-user-search-line text-slate-300 text-2xl mb-1 block"></i>
                                <p class="text-xs text-slate-500 font-medium">Pegawai tidak ditemukan</p>
                            </div>

                            <!-- List -->
                            <div x-show="hasResults" class="max-h-60 overflow-y-auto divide-y divide-slate-100">
                                <template x-if="hasAdminUsers">
                                    <div>
                                        <div class="px-3.5 py-1.5 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Administrator TI
                                        </div>
                                        <template x-for="user in adminUsers" :key="'admin-' + user.id">
                                            <div
                                                @mousedown.prevent="selectUser(user)"
                                                @mouseenter="highlightedId = user.id"
                                                :class="highlightedId === user.id ? 'bg-emerald-50 text-emerald-900' : 'hover:bg-slate-50'"
                                                class="flex items-center justify-between gap-3 px-3.5 py-2.5 cursor-pointer transition-colors"
                                            >
                                                <span class="text-xs font-semibold text-slate-800 truncate" x-text="user.name"></span>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Admin</span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="hasPegawaiUsers">
                                    <div>
                                        <div class="px-3.5 py-1.5 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Pegawai
                                        </div>
                                        <template x-for="user in pegawaiUsers" :key="'pegawai-' + user.id">
                                            <div
                                                @mousedown.prevent="selectUser(user)"
                                                @mouseenter="highlightedId = user.id"
                                                :class="highlightedId === user.id ? 'bg-emerald-50 text-emerald-900' : 'hover:bg-slate-50'"
                                                class="flex items-center justify-between gap-3 px-3.5 py-2.5 cursor-pointer transition-colors"
                                            >
                                                <span class="text-xs font-semibold text-slate-800 truncate" x-text="user.name"></span>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Pegawai</span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- 2. NIP -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-id-card-line text-slate-500"></i>
                                <span>NIP Pegawai</span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-normal">Auto-filled</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ri-barcode-line"></i>
                            </div>
                            <input
                                type="text"
                                :value="selectedNip"
                                placeholder="Terisi otomatis..."
                                readonly
                                class="w-full pl-10 pr-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono text-slate-700 shadow-sm cursor-default"
                            >
                        </div>
                    </div>

                    <!-- 3. Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-lock-password-line text-slate-500"></i>
                                <span>Kata Sandi</span>
                            </span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ri-key-line"></i>
                            </div>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password..."
                                class="w-full pl-10 pr-11 py-3 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-800 transition"
                            >
                                <i x-show="!showPassword" class="ri-eye-line text-base"></i>
                                <i x-show="showPassword" class="ri-eye-off-line text-base text-emerald-700" style="display: none;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                class="rounded border-slate-300 text-slate-900 focus:ring-slate-900 cursor-pointer"
                            >
                            <span class="ms-2 text-xs font-medium text-slate-600">Tetap masuk</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-slate-700 hover:text-emerald-700 transition">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white py-3.5 px-4 rounded-xl text-xs sm:text-sm font-bold tracking-wide shadow-lg shadow-slate-900/20 transition-all flex items-center justify-center gap-2 group cursor-pointer"
                        >
                            <span>Masuk Sekarang</span>
                            <i class="ri-arrow-right-line text-base transition-transform group-hover:translate-x-1"></i>
                        </button>
                    </div>
                </form>

            </div>

            <!-- Panel Footer -->
            <div class="pt-6 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-400">
                    &copy; {{ date('Y') }} Badan POM — Balai Besar Pengawas Obat dan Makanan di Semarang
                </p>
            </div>
        </div>

    </div>

    {{-- Alpine state --}}
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

                get filteredUsers() {
                    var q = this.search.trim().toLowerCase();
                    if (q === '') { return this.users; }
                    return this.users.filter(function(u) {
                        return u.name.toLowerCase().indexOf(q) !== -1 ||
                            (u.nip && u.nip.indexOf(q) !== -1);
                    });
                },

                get adminUsers() {
                    return this.filteredUsers.filter(function(u) { return u.role === 'admin'; });
                },

                get pegawaiUsers() {
                    return this.filteredUsers.filter(function(u) { return u.role !== 'admin'; });
                },

                get isResultsEmpty() {
                    return this.filteredUsers.length === 0;
                },

                get hasResults() {
                    return this.filteredUsers.length > 0;
                },

                get hasAdminUsers() {
                    return this.adminUsers.length > 0;
                },

                get hasPegawaiUsers() {
                    return this.pegawaiUsers.length > 0;
                },

                closeSoon: function() {
                    var self = this;
                    self._closeTimer = setTimeout(function() {
                        self.open = false;
                    }, 160);
                },

                selectUser: function(user) {
                    var self = this;
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

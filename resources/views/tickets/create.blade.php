<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs text-slate-500 mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
                        </li>
                        <li>
                            <span class="mx-1 text-slate-400">/</span>
                        </li>
                        <li>
                            <a href="{{ route('tickets.index') }}" class="hover:text-emerald-700">Daftar Pengaduan</a>
                        </li>
                        <li>
                            <span class="mx-1 text-slate-400">/</span>
                        </li>
                        <li class="font-semibold text-emerald-800">App Gateway Layanan</li>
                    </ol>
                </nav>
                <h2 class="font-bold text-xl text-slate-900 leading-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    Katalog Layanan & Pengaduan TIK
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Balai Besar Pengawas Obat dan Makanan di Semarang
                </p>
            </div>
            <div>
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition">
                    &larr; Riwayat Pengaduan
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Main Container with Alpine.js Reactive Gateway State -->
    <div 
        x-data="{
            search: '',
            activeGroup: 'all',
            selectedServiceKey: '{{ old('service_key', $initialService ?? '') }}',
            selectedSubCategory: '{{ old('subcategory', $initialSubcategory ?? '') }}',
            services: {{ json_encode($services) }},
            groups: {{ json_encode($groups) }},
            get currentService() {
                return this.selectedServiceKey && this.services[this.selectedServiceKey] 
                    ? this.services[this.selectedServiceKey] 
                    : null;
            },
            init() {
                if (this.selectedServiceKey) {
                    this.ensureDefaultSubCategory(this.selectedServiceKey);
                }
            },
            ensureDefaultSubCategory(key) {
                if (key === 'sertifikat_bse') {
                    if (!['reset_password', 'permintaan_otp', 'lainnya'].includes(this.selectedSubCategory)) {
                        this.selectedSubCategory = 'reset_password';
                    }
                } else if (key === 'zoom') {
                    if (!['permintaan_link_zoom', 'lainnya'].includes(this.selectedSubCategory)) {
                        this.selectedSubCategory = 'permintaan_link_zoom';
                    }
                } else if (key === 'formulir_kegiatan') {
                    if (!['permintaan_daftar_hadir', 'permintaan_qrcode', 'survey', 'post_test_pretest', 'lainnya'].includes(this.selectedSubCategory)) {
                        this.selectedSubCategory = 'permintaan_daftar_hadir';
                    }
                } else if (key === 'install_windows') {
                    if (!['permintaan_install_windows', 'lainnya'].includes(this.selectedSubCategory)) {
                        this.selectedSubCategory = 'permintaan_install_windows';
                    }
                } else if (key === 'lisensi_office') {
                    if (!['permintaan_lisensi_office', 'lainnya'].includes(this.selectedSubCategory)) {
                        this.selectedSubCategory = 'permintaan_lisensi_office';
                    }
                } else if (key === 'lainnya') {
                    this.selectedSubCategory = 'lainnya';
                }
            },
            selectService(key) {
                this.selectedServiceKey = key;
                this.ensureDefaultSubCategory(key);
                const s = this.services[key];
                if (s) {
                    const catSelect = document.getElementById('category_id');
                    if (catSelect && s.default_category_id) {
                        catSelect.value = s.default_category_id;
                    }
                    const subjectInput = document.getElementById('subject');
                    if (subjectInput && !subjectInput.value) {
                        subjectInput.value = '[' + s.name + '] ';
                    }
                }
                window.scrollTo({ top: 180, behavior: 'smooth' });
            },
            clearService() {
                this.selectedServiceKey = '';
            },
            isMatch(key) {
                const s = this.services[key];
                if (!s) return false;
                if (this.activeGroup !== 'all' && s.group !== this.activeGroup) return false;
                if (!this.search.trim()) return true;
                const q = this.search.toLowerCase().trim();
                return s.name.toLowerCase().includes(q) 
                    || s.long_name.toLowerCase().includes(q) 
                    || s.description.toLowerCase().includes(q)
                    || (s.keywords && s.keywords.some(k => k.toLowerCase().includes(q)));
            },
            get visibleCount() {
                return Object.keys(this.services).filter(k => this.isMatch(k)).length;
            }
        }"
        class="space-y-6"
    >

        <!-- VIEW 1: APP GATEWAY CATALOG (Shown when no service is selected) -->
        <div x-show="!selectedServiceKey" x-transition.opacity.duration.300ms class="space-y-6">

            <!-- Hero Search & Filter Section -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden border border-slate-700/50">
                <div class="absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 max-w-3xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        App Gateway Layanan TI
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Pilih Menu Layanan yang Ingin Anda Laporkan
                    </h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Cari aplikasi, akun dinas, kebutuhan acara, fasilitas jaringan, atau perbaikan sistem operasi PC di bawah ini untuk langsung terhubung dengan formulir spesifik.
                    </p>

                    <!-- Instant Search Bar -->
                    <div class="mt-6 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            x-model="search" 
                            placeholder="Cari layanan, misal: zoom, sertifikat bsre, formulir presensi, survey, windows, lisensi office..."
                            class="w-full pl-12 pr-10 py-3.5 bg-white/95 focus:bg-white text-slate-900 rounded-2xl text-xs sm:text-sm placeholder:text-slate-400 shadow-xl border border-transparent focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition"
                        >
                        <button 
                            x-show="search.length > 0" 
                            @click="search = ''" 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Group Tabs Filter -->
                <div class="relative z-10 mt-6 pt-5 border-t border-slate-700/60 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-400 font-medium me-1">Kategori:</span>
                    <template x-for="(label, key) in groups" :key="key">
                        <button 
                            type="button" 
                            @click="activeGroup = key"
                            :class="activeGroup === key 
                                ? 'bg-emerald-600 text-white font-semibold shadow-sm' 
                                : 'bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700/60 font-medium'"
                            class="px-3.5 py-1.5 rounded-xl text-xs transition duration-150"
                            x-text="label"
                        ></button>
                    </template>
                    <span class="ms-auto text-xs text-slate-400 font-medium" x-text="visibleCount + ' Layanan Tersedia'"></span>
                </div>
            </div>

            <!-- 12 Service Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                @foreach($services as $key => $svc)
                    <div 
                        x-show="isMatch('{{ $key }}')"
                        x-transition.opacity.duration.200ms
                        @click="selectService('{{ $key }}')"
                        class="group relative bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-emerald-400 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 cursor-pointer flex flex-col justify-between"
                    >
                        <div>
                            <!-- Header: Badge & Icon -->
                            <div class="flex items-start justify-between gap-2 mb-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-slate-100 group-hover:bg-emerald-100 transition">
                                    <span class="text-slate-700 group-hover:text-emerald-900">{{ $svc['badge'] }}</span>
                                </span>

                                <div class="w-10 h-10 rounded-xl bg-slate-100 group-hover:bg-emerald-700 flex items-center justify-center transition shadow-sm">
                                    <i class="{{ $svc['icon'] }} text-xl text-slate-700 group-hover:text-white"></i>
                                </div>
                            </div>

                            <!-- Name & Long Name -->
                            <h4 class="font-bold text-base text-slate-900 group-hover:text-emerald-800 transition">
                                {{ $svc['name'] }}
                            </h4>
                            <p class="text-[11px] font-medium text-slate-400 mt-0.5 leading-snug">
                                {{ $svc['long_name'] }}
                            </p>

                            <!-- Description -->
                            <p class="mt-2.5 text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $svc['description'] }}
                            </p>
                        </div>

                        <!-- Card Footer Action -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-emerald-700 group-hover:text-emerald-800">
                            <span>Pilih Layanan</span>
                            <span class="transform group-hover:translate-x-1 transition-transform">&rarr;</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Zero Search Results State -->
            <div x-show="visibleCount === 0" class="text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Layanan Tidak Ditemukan</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tidak ada layanan yang cocok dengan kata kunci "<span class="font-semibold text-slate-700" x-text="search"></span>". Anda dapat memilih menu <strong>Lainnya</strong> untuk kebutuhan TI umum.
                </p>
                <div class="mt-4 flex justify-center gap-2">
                    <button type="button" @click="search = ''; activeGroup = 'all'" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        Reset Pencarian
                    </button>
                    <button type="button" @click="selectService('lainnya')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition">
                        Pilih Menu "Lainnya"
                    </button>
                </div>
            </div>

        </div>

        <!-- VIEW 2: DYNAMIC TICKET FORM (Shown when a service is selected) -->
        <div x-show="selectedServiceKey" x-transition.opacity.duration.300ms class="space-y-6">

            <!-- Selected Service Notification Banner -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-md">
                        <i :class="currentService ? currentService.icon : 'ri-customer-service-2-line'" class="text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/50">
                                Layanan Terpilih
                            </span>
                            <span class="text-xs text-slate-400 font-medium" x-text="currentService ? currentService.badge : ''"></span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-0.5" x-text="currentService ? currentService.name : ''"></h3>
                        <p class="text-xs text-slate-500" x-text="currentService ? currentService.long_name : ''"></p>
                    </div>
                </div>
                <div>
                    <button 
                        type="button" 
                        @click="clearService()" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:border-slate-400 bg-slate-50 hover:bg-white text-xs font-semibold text-slate-700 shadow-sm transition"
                    >
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Ganti Layanan (Katalog)</span>
                    </button>
                </div>
            </div>

            <!-- Form Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Form Column -->
                <div class="lg:col-span-2">

                    <!-- Global Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs">
                            <div class="flex items-center gap-2 font-bold text-rose-800 mb-1.5">
                                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Mohon lengkapi formulir dengan benar:</span>
                            </div>
                            <ul class="list-disc list-inside text-rose-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- ======================================================== -->
                    <!-- FORM 1: SERTIFIKAT BSRE (Modern, Simple, Impeccable)      -->
                    <!-- ======================================================== -->
                    <div x-show="selectedServiceKey === 'sertifikat_bse'" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Sertifikat Elektronik BSrE</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Layanan penerbitan, reset password passphrase, dan permintaan OTP penandatanganan dokumen elektronik.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="service_key" value="sertifikat_bse">
                            <input type="hidden" name="subcategory" :value="selectedSubCategory">

                            <!-- Sleek Segmented Pill Bar -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">
                                    Pilih Sub Kategori Layanan BSrE:
                                </label>
                                <div class="inline-flex flex-wrap p-1 bg-slate-100 rounded-xl gap-1">
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'reset_password'"
                                        :class="selectedSubCategory === 'reset_password' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-lock-password-line text-amber-600"></i>
                                        <span>Reset Password BSrE</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'permintaan_otp'"
                                        :class="selectedSubCategory === 'permintaan_otp' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-shield-keyhole-line text-blue-600"></i>
                                        <span>Permintaan OTP</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'lainnya'"
                                        :class="selectedSubCategory === 'lainnya' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-more-line text-slate-600"></i>
                                        <span>Lainnya</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Auto-filled Metadata Header -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Tanggal Pengajuan
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-calendar-line text-emerald-700"></i>
                                        <span>{{ $todayFormatted }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Otomatis</span>
                                    </div>
                                    <input type="hidden" name="tanggal_pengajuan" value="{{ $todayDate }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Nama Pemohon
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-user-line text-emerald-700"></i>
                                        <span>{{ $user->name }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Akun Login</span>
                                    </div>
                                    <input type="hidden" name="nama" value="{{ old('nama', $user->name) }}">
                                </div>
                            </div>

                            <!-- VARIASI 1: RESET PASSWORD & PERMINTAAN OTP -->
                            <div x-show="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'" class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="bsre_nip" class="block text-xs font-semibold text-slate-700 mb-1">
                                            NIP (Nomor Induk Pegawai) <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="bsre_nip" 
                                            name="nip" 
                                            value="{{ old('nip', $user->nip ?? '') }}" 
                                            placeholder="Contoh: 198501012010121001" 
                                            maxlength="30" 
                                            :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                                            :disabled="selectedSubCategory === 'lainnya'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('nip')" />
                                    </div>

                                    <div>
                                        <label for="bsre_nik" class="block text-xs font-semibold text-slate-700 mb-1">
                                            NIK (Nomor Induk Kependudukan) <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="bsre_nik" 
                                            name="nik" 
                                            value="{{ old('nik', $user->nik ?? '') }}" 
                                            placeholder="Masukkan 16 digit NIK sesuai KTP..." 
                                            maxlength="20" 
                                            :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                                            :disabled="selectedSubCategory === 'lainnya'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('nik')" />
                                    </div>

                                    <div>
                                        <label for="bsre_email" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Email Dinas / Terdaftar di BSrE <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="email" 
                                            id="bsre_email" 
                                            name="email" 
                                            value="{{ old('email', $user->email) }}" 
                                            placeholder="nama@bbpom.go.id" 
                                            :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                                            :disabled="selectedSubCategory === 'lainnya'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('email')" />
                                    </div>

                                    <div>
                                        <label for="bsre_nomor_telepon" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="tel" 
                                            id="bsre_nomor_telepon" 
                                            name="nomor_telepon" 
                                            value="{{ old('nomor_telepon', $user->phone_number ?? '') }}" 
                                            placeholder="Contoh: 081234567890" 
                                            :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                                            :disabled="selectedSubCategory === 'lainnya'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('nomor_telepon')" />
                                    </div>
                                </div>
                            </div>

                            <!-- VARIASI 2: SUB KATEGORI LAINNYA -->
                            <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4" x-data="{ fileName: '' }">
                                <div>
                                    <label for="bsre_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deskripsi Kendala / Permohonan <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea 
                                        id="bsre_deskripsi" 
                                        name="deskripsi" 
                                        rows="4" 
                                        :required="selectedSubCategory === 'lainnya'"
                                        :disabled="selectedSubCategory !== 'lainnya'"
                                        placeholder="Jelaskan kebutuhan sertifikat elektronik, perpanjangan masa berlaku, atau kendala teknis lainnya..." 
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    >{{ old('deskripsi') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Berkas Lampiran <span class="text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                            <span>Pilih File</span>
                                            <input 
                                                type="file" 
                                                name="file_lampiran" 
                                                class="sr-only"
                                                @change="fileName = $event.target.files[0]?.name"
                                                :disabled="selectedSubCategory !== 'lainnya'"
                                            >
                                        </label>
                                        <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF, DOCX (Maks 5MB)'"></span>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-[0.99]">
                                        <span x-text="selectedSubCategory === 'reset_password' ? 'Ajukan Reset Password BSrE' : (selectedSubCategory === 'permintaan_otp' ? 'Ajukan Permintaan OTP' : 'Kirim Pengaduan BSrE')"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ======================================================== -->
                    <!-- FORM 2: ZOOM MEETING / WEBINAR                            -->
                    <!-- ======================================================== -->
                    <div x-show="selectedServiceKey === 'zoom'" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Layanan Zoom Meeting & Webinar</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Permintaan link ruang virtual rapat dinas, webinar, dan koordinasi daring BBPOM Semarang.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="service_key" value="zoom">
                            <input type="hidden" name="subcategory" :value="selectedSubCategory">

                            <!-- Sleek Segmented Pill Bar -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">
                                    Pilih Sub Kategori Layanan Zoom:
                                </label>
                                <div class="inline-flex flex-wrap p-1 bg-slate-100 rounded-xl gap-1">
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'permintaan_link_zoom'"
                                        :class="selectedSubCategory === 'permintaan_link_zoom' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-video-chat-line text-blue-600"></i>
                                        <span>Permintaan Link Zoom</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'lainnya'"
                                        :class="selectedSubCategory === 'lainnya' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-more-line text-slate-600"></i>
                                        <span>Lainnya</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Auto-filled Metadata Header -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Tanggal Pengajuan
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-calendar-line text-emerald-700"></i>
                                        <span>{{ $todayFormatted }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Otomatis</span>
                                    </div>
                                    <input type="hidden" name="tanggal_pengajuan" value="{{ $todayDate }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Nama Pemohon
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-user-line text-emerald-700"></i>
                                        <span>{{ $user->name }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Akun Login</span>
                                    </div>
                                    <input type="hidden" name="nama" value="{{ old('nama', $user->name) }}">
                                </div>
                            </div>

                            <!-- VARIASI 1: PERMINTAAN LINK ZOOM -->
                            <div x-show="selectedSubCategory === 'permintaan_link_zoom'" class="space-y-4">
                                <div>
                                    <label for="zoom_nama_acara" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Nama Acara / Kegiatan <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="zoom_nama_acara" 
                                        name="nama_acara" 
                                        value="{{ old('nama_acara') }}" 
                                        placeholder="Contoh: Rapat Koordinasi Evaluasi Pengawasan Obat Semester I" 
                                        :required="selectedSubCategory === 'permintaan_link_zoom'"
                                        :disabled="selectedSubCategory !== 'permintaan_link_zoom'"
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    >
                                    <x-input-error class="mt-1" :messages="$errors->get('nama_acara')" />
                                </div>

                                <div>
                                    <label for="zoom_jam" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Jam / Waktu Pelaksanaan <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="zoom_jam" 
                                        name="jam" 
                                        value="{{ old('jam') }}" 
                                        placeholder="Contoh: 09:00 - 12:00 WIB atau 13:30 WIB s/d Selesai" 
                                        :required="selectedSubCategory === 'permintaan_link_zoom'"
                                        :disabled="selectedSubCategory !== 'permintaan_link_zoom'"
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    >
                                    <x-input-error class="mt-1" :messages="$errors->get('jam')" />
                                </div>
                            </div>

                            <!-- VARIASI 2: SUB KATEGORI LAINNYA -->
                            <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4" x-data="{ fileName: '' }">
                                <div>
                                    <label for="zoom_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deskripsi Kebutuhan / Kendala <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea 
                                        id="zoom_deskripsi" 
                                        name="deskripsi" 
                                        rows="4" 
                                        :required="selectedSubCategory === 'lainnya'"
                                        :disabled="selectedSubCategory !== 'lainnya'"
                                        placeholder="Jelaskan kebutuhan pengaturan khusus Zoom, breakout room, kapasitas besar, atau kendala lainnya..." 
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    >{{ old('deskripsi') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Berkas Lampiran <span class="text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                            <span>Pilih File</span>
                                            <input 
                                                type="file" 
                                                name="file_lampiran" 
                                                class="sr-only"
                                                @change="fileName = $event.target.files[0]?.name"
                                                :disabled="selectedSubCategory !== 'lainnya'"
                                            >
                                        </label>
                                        <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF, DOCX (Maks 5MB)'"></span>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-[0.99]">
                                        <span x-text="selectedSubCategory === 'permintaan_link_zoom' ? 'Ajukan Permintaan Link Zoom' : 'Kirim Pengaduan Zoom'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ======================================================== -->
                    <!-- FORM 3: FORMULIR, QRCODE, SURVEY, PRETEST/POSTTEST        -->
                    <!-- ======================================================== -->
                    <div 
                        x-show="selectedServiceKey === 'formulir_kegiatan'" 
                        class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7"
                        x-data="{
                            surveyQuestions: [{ text: '', tipe: 'Pilihan Skala 4' }],
                            addSurveyQuestion() {
                                this.surveyQuestions.push({ text: '', tipe: 'Pilihan Skala 4' });
                            },
                            removeSurveyQuestion(index) {
                                if (this.surveyQuestions.length > 1) {
                                    this.surveyQuestions.splice(index, 1);
                                } else {
                                    this.surveyQuestions[0].text = '';
                                }
                            },
                            testQuestions: [{ text: '', tipe: 'Multiple Answer' }],
                            addTestQuestion() {
                                this.testQuestions.push({ text: '', tipe: 'Multiple Answer' });
                            },
                            removeTestQuestion(index) {
                                if (this.testQuestions.length > 1) {
                                    this.testQuestions.splice(index, 1);
                                } else {
                                    this.testQuestions[0].text = '';
                                }
                            }
                        }"
                    >
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Formulir, QR Code, Survey & Pretest/Posttest</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Layanan pembuatan presensi daftar hadir, generate QR Code resmi, kuesioner survei, evaluasi pretest/posttest, dan tautan kegiatan.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="service_key" value="formulir_kegiatan">
                            <input type="hidden" name="subcategory" :value="selectedSubCategory">

                            <!-- Sleek Segmented Pill Bar -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">
                                    Pilih Sub Kategori Layanan:
                                </label>
                                <div class="inline-flex flex-wrap p-1 bg-slate-100 rounded-xl gap-1">
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'permintaan_daftar_hadir'"
                                        :class="selectedSubCategory === 'permintaan_daftar_hadir' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-user-follow-line text-emerald-600"></i>
                                        <span>Daftar Hadir</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'permintaan_qrcode'"
                                        :class="selectedSubCategory === 'permintaan_qrcode' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-qr-code-line text-blue-600"></i>
                                        <span>QR Code</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'survey'"
                                        :class="selectedSubCategory === 'survey' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-survey-line text-indigo-600"></i>
                                        <span>Survey</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'post_test_pretest'"
                                        :class="selectedSubCategory === 'post_test_pretest' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-file-list-3-line text-amber-600"></i>
                                        <span>Post Test / Pretest</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'lainnya'"
                                        :class="selectedSubCategory === 'lainnya' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-more-line text-slate-600"></i>
                                        <span>Lainnya</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Auto-filled Metadata Header -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Tanggal Pengajuan
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-calendar-line text-emerald-700"></i>
                                        <span>{{ $todayFormatted }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Otomatis</span>
                                    </div>
                                    <input type="hidden" name="tanggal_pengajuan" value="{{ $todayDate }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Nama Pemohon
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-user-line text-emerald-700"></i>
                                        <span>{{ $user->name }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Akun Login</span>
                                    </div>
                                    <input type="hidden" name="nama" value="{{ old('nama', $user->name) }}">
                                </div>
                            </div>

                            <!-- VARIASI a: PERMINTAAN DAFTAR HADIR -->
                            <div x-show="selectedSubCategory === 'permintaan_daftar_hadir'" class="space-y-4">
                                <div>
                                    <label for="kegiatan_nama_acara" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Nama Acara / Kegiatan <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="kegiatan_nama_acara" 
                                        name="nama_acara" 
                                        value="{{ old('nama_acara') }}" 
                                        placeholder="Contoh: Sosialisasi Pengawasan Pangan Jajanan Anak Sekolah" 
                                        :required="selectedSubCategory === 'permintaan_daftar_hadir'"
                                        :disabled="selectedSubCategory !== 'permintaan_daftar_hadir'"
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    >
                                    <x-input-error class="mt-1" :messages="$errors->get('nama_acara')" />
                                </div>

                                <div>
                                    <label for="kegiatan_jam" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Jam / Waktu Pelaksanaan <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="kegiatan_jam" 
                                        name="jam" 
                                        value="{{ old('jam') }}" 
                                        placeholder="Contoh: 08:00 - 12:00 WIB" 
                                        :required="selectedSubCategory === 'permintaan_daftar_hadir'"
                                        :disabled="selectedSubCategory !== 'permintaan_daftar_hadir'"
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    >
                                    <x-input-error class="mt-1" :messages="$errors->get('jam')" />
                                </div>
                            </div>

                            <!-- VARIASI b: PERMINTAAN QRCODE -->
                            <div x-show="selectedSubCategory === 'permintaan_qrcode'" class="space-y-4">
                                <div>
                                    <label for="kegiatan_link_qrcode" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Link Sasaran yang Akan Dijadikan QR Code <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="kegiatan_link_qrcode" 
                                        name="link_qrcode" 
                                        value="{{ old('link_qrcode') }}" 
                                        placeholder="Contoh: https://bbpomsemarang.pom.go.id/materi-bimtek atau tautan Google Drive" 
                                        :required="selectedSubCategory === 'permintaan_qrcode'"
                                        :disabled="selectedSubCategory !== 'permintaan_qrcode'"
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    >
                                    <span class="text-[11px] text-slate-500 mt-1 block">
                                        Teknisi TIK akan men-generate QR Code resmi beresolusi tinggi dengan logo BBPOM.
                                    </span>
                                    <x-input-error class="mt-1" :messages="$errors->get('link_qrcode')" />
                                </div>
                            </div>

                            <!-- VARIASI c: SURVEY -->
                            <div x-show="selectedSubCategory === 'survey'" class="space-y-4" x-data="{ surveyFileName: '' }">
                                <div>
                                    <label for="survey_judul" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Judul Survey <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="survey_judul" 
                                        name="judul_survey" 
                                        value="{{ old('judul_survey') }}" 
                                        placeholder="Contoh: Survei Kepuasan Masyarakat terhadap Layanan Sertifikasi BBPOM" 
                                        :required="selectedSubCategory === 'survey'"
                                        :disabled="selectedSubCategory !== 'survey'"
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    >
                                    <x-input-error class="mt-1" :messages="$errors->get('judul_survey')" />
                                </div>

                                <!-- Upload File (Opsional - bila upload file, pertanyaan di bawah boleh kosong) -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Berkas Kuesioner (PDF / Word)
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                            <span>Pilih Berkas</span>
                                            <input 
                                                type="file" 
                                                name="file_lampiran" 
                                                class="sr-only"
                                                @change="surveyFileName = $event.target.files[0]?.name"
                                                :disabled="selectedSubCategory !== 'survey'"
                                            >
                                        </label>
                                        <span class="text-xs text-slate-500 truncate" x-text="surveyFileName ? 'File: ' + surveyFileName : 'Format: PDF, DOC, DOCX (Maks 5MB) - Jika ada berkas, pertanyaan di bawah boleh dikosongkan'"></span>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                                </div>

                                <!-- Dynamic Questions Builder -->
                                <div class="pt-2 border-t border-slate-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-xs font-semibold text-slate-700">
                                            Daftar Pertanyaan Survei (Opsional jika mengunggah berkas)
                                        </label>
                                        <button 
                                            type="button" 
                                            @click="addSurveyQuestion()"
                                            class="text-xs text-emerald-700 hover:text-emerald-800 font-bold flex items-center gap-1"
                                        >
                                            <i class="ri-add-line text-sm"></i>
                                            <span>Tambah Pertanyaan</span>
                                        </button>
                                    </div>

                                    <!-- Serialized Hidden Input for Backend -->
                                    <input 
                                        type="hidden" 
                                        name="pertanyaan_json" 
                                        :value="JSON.stringify(surveyQuestions.filter(q => q.text.trim() !== ''))"
                                        :disabled="selectedSubCategory !== 'survey'"
                                    >

                                    <div class="space-y-3">
                                        <template x-for="(item, idx) in surveyQuestions" :key="idx">
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                <span class="text-xs font-bold text-slate-500 w-6" x-text="(idx + 1) + '.'"></span>
                                                <input 
                                                    type="text" 
                                                    x-model="item.text"
                                                    placeholder="Tuliskan butir pertanyaan survei..." 
                                                    class="flex-1 w-full rounded-lg border-slate-300 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                                >
                                                <select 
                                                    x-model="item.tipe" 
                                                    class="w-full sm:w-44 rounded-lg border-slate-300 text-xs text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                                >
                                                    <option value="Pilihan Skala 4">Pilihan Skala 4</option>
                                                    <option value="Yes/No">Yes/No</option>
                                                    <option value="Pilihan option">Pilihan option</option>
                                                    <option value="Essay/text">Essay/text</option>
                                                    <option value="Rating Bintang">Rating Bintang</option>
                                                </select>
                                                <button 
                                                    type="button" 
                                                    @click="removeSurveyQuestion(idx)" 
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 transition"
                                                    title="Hapus baris pertanyaan"
                                                >
                                                    <i class="ri-delete-bin-line text-sm"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('pertanyaan_json')" />
                                </div>
                            </div>

                            <!-- VARIASI d: POST TEST / PRETEST -->
                            <div x-show="selectedSubCategory === 'post_test_pretest'" class="space-y-4" x-data="{ testFileName: '' }">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="sm:col-span-2">
                                        <label for="test_judul_acara" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Judul Acara / Kegiatan <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="test_judul_acara" 
                                            name="judul_acara" 
                                            value="{{ old('judul_acara') }}" 
                                            placeholder="Contoh: Pelatihan Internal Pengujian Mikrobiologi Laboratorium" 
                                            :required="selectedSubCategory === 'post_test_pretest'"
                                            :disabled="selectedSubCategory !== 'post_test_pretest'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('judul_acara')" />
                                    </div>

                                    <div>
                                        <label for="test_jenis" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Jenis Evaluasi <span class="text-rose-500">*</span>
                                        </label>
                                        <select 
                                            id="test_jenis" 
                                            name="jenis_test" 
                                            :required="selectedSubCategory === 'post_test_pretest'"
                                            :disabled="selectedSubCategory !== 'post_test_pretest'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                            <option value="pretest" {{ old('jenis_test') === 'pretest' ? 'selected' : '' }}>Pretest (Awal)</option>
                                            <option value="post_test" {{ old('jenis_test') === 'post_test' ? 'selected' : '' }}>Post Test (Akhir)</option>
                                        </select>
                                        <x-input-error class="mt-1" :messages="$errors->get('jenis_test')" />
                                    </div>
                                </div>

                                <!-- Upload File (Opsional - bila upload file, pertanyaan di bawah boleh kosong) -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Berkas Soal (PDF / Word)
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                            <span>Pilih Berkas</span>
                                            <input 
                                                type="file" 
                                                name="file_lampiran" 
                                                class="sr-only"
                                                @change="testFileName = $event.target.files[0]?.name"
                                                :disabled="selectedSubCategory !== 'post_test_pretest'"
                                            >
                                        </label>
                                        <span class="text-xs text-slate-500 truncate" x-text="testFileName ? 'File: ' + testFileName : 'Format: PDF, DOC, DOCX (Maks 5MB) - Jika ada berkas, soal di bawah boleh dikosongkan'"></span>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                                </div>

                                <!-- Dynamic Questions Builder for Test -->
                                <div class="pt-2 border-t border-slate-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-xs font-semibold text-slate-700">
                                            Daftar Butir Soal (Opsional jika mengunggah berkas)
                                        </label>
                                        <button 
                                            type="button" 
                                            @click="addTestQuestion()"
                                            class="text-xs text-emerald-700 hover:text-emerald-800 font-bold flex items-center gap-1"
                                        >
                                            <i class="ri-add-line text-sm"></i>
                                            <span>Tambah Soal</span>
                                        </button>
                                    </div>

                                    <!-- Serialized Hidden Input for Backend -->
                                    <input 
                                        type="hidden" 
                                        name="pertanyaan_json" 
                                        :value="JSON.stringify(testQuestions.filter(q => q.text.trim() !== ''))"
                                        :disabled="selectedSubCategory !== 'post_test_pretest'"
                                    >

                                    <div class="space-y-3">
                                        <template x-for="(item, idx) in testQuestions" :key="idx">
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                <span class="text-xs font-bold text-slate-500 w-6" x-text="(idx + 1) + '.'"></span>
                                                <input 
                                                    type="text" 
                                                    x-model="item.text"
                                                    placeholder="Tuliskan butir soal evaluasi..." 
                                                    class="flex-1 w-full rounded-lg border-slate-300 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                                >
                                                <select 
                                                    x-model="item.tipe" 
                                                    class="w-full sm:w-44 rounded-lg border-slate-300 text-xs text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                                >
                                                    <option value="Multiple Answer">Multiple Answer</option>
                                                    <option value="Essay/Text">Essay/Text</option>
                                                    <option value="File Upload">File Upload</option>
                                                </select>
                                                <button 
                                                    type="button" 
                                                    @click="removeTestQuestion(idx)" 
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 transition"
                                                    title="Hapus butir soal"
                                                >
                                                    <i class="ri-delete-bin-line text-sm"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('pertanyaan_json')" />
                                </div>
                            </div>

                            <!-- VARIASI e: SUB KATEGORI LAINNYA -->
                            <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4" x-data="{ fileName: '' }">
                                <div>
                                    <label for="kegiatan_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deskripsi Kebutuhan / Permohonan <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea 
                                        id="kegiatan_deskripsi" 
                                        name="deskripsi" 
                                        rows="4" 
                                        :required="selectedSubCategory === 'lainnya'"
                                        :disabled="selectedSubCategory !== 'lainnya'"
                                        placeholder="Jelaskan kebutuhan pembuatan Link Tree, Google Form khusus, atau format formulir lainnya..." 
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    >{{ old('deskripsi') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Berkas Lampiran <span class="text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                            <span>Pilih File</span>
                                            <input 
                                                type="file" 
                                                name="file_lampiran" 
                                                class="sr-only"
                                                @change="fileName = $event.target.files[0]?.name"
                                                :disabled="selectedSubCategory !== 'lainnya'"
                                            >
                                        </label>
                                        <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF, DOCX (Maks 5MB)'"></span>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-[0.99]">
                                        <span>Ajukan Permohonan</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ======================================================== -->
                    <!-- FORM 4: INSTALL ULANG WINDOWS                             -->
                    <!-- ======================================================== -->
                    <div x-show="selectedServiceKey === 'install_windows'" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Install Ulang Operating System Windows</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Layanan perbaikan OS, formatting, pembersihan malware, instalasi fresh Windows 10/11 untuk perangkat dinas BBPOM.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="service_key" value="install_windows">
                            <input type="hidden" name="subcategory" :value="selectedSubCategory">

                            <!-- Sleek Segmented Pill Bar -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">
                                    Pilih Sub Kategori:
                                </label>
                                <div class="inline-flex flex-wrap p-1 bg-slate-100 rounded-xl gap-1">
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'permintaan_install_windows'"
                                        :class="selectedSubCategory === 'permintaan_install_windows' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-computer-line text-slate-700"></i>
                                        <span>Permintaan Install Ulang Windows</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'lainnya'"
                                        :class="selectedSubCategory === 'lainnya' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-more-line text-slate-600"></i>
                                        <span>Lainnya</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Auto-filled Metadata Header -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Tanggal Pengajuan
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-calendar-line text-emerald-700"></i>
                                        <span>{{ $todayFormatted }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Otomatis</span>
                                    </div>
                                    <input type="hidden" name="tanggal_pengajuan" value="{{ $todayDate }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Nama Pemohon
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-user-line text-emerald-700"></i>
                                        <span>{{ $user->name }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Akun Login</span>
                                    </div>
                                    <input type="hidden" name="nama" value="{{ old('nama', $user->name) }}">
                                </div>
                            </div>

                            <!-- VARIASI a: PERMINTAAN INSTALL ULANG WINDOWS -->
                            <div x-show="selectedSubCategory === 'permintaan_install_windows'" class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="windows_bidang" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Bidang / Substansi <span class="text-rose-500">*</span>
                                        </label>
                                        <select 
                                            id="windows_bidang" 
                                            name="bidang" 
                                            :required="selectedSubCategory === 'permintaan_install_windows'"
                                            :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                            <option value="">-- Pilih Bidang --</option>
                                            <option value="Tata Usaha" {{ old('bidang') === 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                                            <option value="Pengujian" {{ old('bidang') === 'Pengujian' ? 'selected' : '' }}>Pengujian</option>
                                            <option value="Penindakan" {{ old('bidang') === 'Penindakan' ? 'selected' : '' }}>Penindakan</option>
                                            <option value="Infokom" {{ old('bidang') === 'Infokom' ? 'selected' : '' }}>Infokom</option>
                                            <option value="Pemeriksaan" {{ old('bidang') === 'Pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                                        </select>
                                        <x-input-error class="mt-1" :messages="$errors->get('bidang')" />
                                    </div>

                                    <div>
                                        <label for="windows_merk" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Merk Laptop / Komputer <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="windows_merk" 
                                            name="merk_perangkat" 
                                            value="{{ old('merk_perangkat') }}" 
                                            placeholder="Contoh: Lenovo ThinkPad L14" 
                                            :required="selectedSubCategory === 'permintaan_install_windows'"
                                            :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('merk_perangkat')" />
                                    </div>

                                    <div>
                                        <label for="windows_kode_bmn" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Kode BMN Perangkat <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="windows_kode_bmn" 
                                            name="kode_bmn" 
                                            value="{{ old('kode_bmn') }}" 
                                            placeholder="Contoh: 3.05.01.04.001.000123" 
                                            :required="selectedSubCategory === 'permintaan_install_windows'"
                                            :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('kode_bmn')" />
                                    </div>
                                </div>

                                <div>
                                    <label for="windows_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deskripsi Alasan / Kendala <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea 
                                        id="windows_deskripsi" 
                                        name="deskripsi" 
                                        rows="4" 
                                        :required="selectedSubCategory === 'permintaan_install_windows'"
                                        :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                                        placeholder="Jelaskan kendala (misal: sering blue screen, lambat setelah update, terinfeksi virus, peremajaan sistem)..." 
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    >{{ old('deskripsi') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                                </div>
                            </div>

                            <!-- VARIASI b: SUB KATEGORI LAINNYA -->
                            <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4" x-data="{ fileName: '' }">
                                <div>
                                    <label for="windows_deskripsi_lainnya" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deskripsi Kendala Sistem Operasi <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea 
                                        id="windows_deskripsi_lainnya" 
                                        name="deskripsi" 
                                        rows="4" 
                                        :required="selectedSubCategory === 'lainnya'"
                                        :disabled="selectedSubCategory !== 'lainnya'"
                                        placeholder="Jelaskan kebutuhan driver, error sistem operasi, atau kendala Windows lainnya..." 
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    >{{ old('deskripsi') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Berkas Lampiran <span class="text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                            <span>Pilih File</span>
                                            <input 
                                                type="file" 
                                                name="file_lampiran" 
                                                class="sr-only"
                                                @change="fileName = $event.target.files[0]?.name"
                                                :disabled="selectedSubCategory !== 'lainnya'"
                                            >
                                        </label>
                                        <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF (Maks 5MB)'"></span>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-[0.99]">
                                        <span x-text="selectedSubCategory === 'permintaan_install_windows' ? 'Ajukan Install Ulang Windows' : 'Kirim Pengaduan OS'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ======================================================== -->
                    <!-- FORM 5: LISENSI MICROSOFT OFFICE                          -->
                    <!-- ======================================================== -->
                    <div x-show="selectedServiceKey === 'lisensi_office'" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Lisensi Microsoft Office</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Aktivasi lisensi resmi Microsoft 365, Word, Excel, PowerPoint, dan penanganan notifikasi unlicensed product.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="service_key" value="lisensi_office">
                            <input type="hidden" name="subcategory" :value="selectedSubCategory">

                            <!-- Sleek Segmented Pill Bar -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">
                                    Pilih Sub Kategori:
                                </label>
                                <div class="inline-flex flex-wrap p-1 bg-slate-100 rounded-xl gap-1">
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'permintaan_lisensi_office'"
                                        :class="selectedSubCategory === 'permintaan_lisensi_office' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-key-line text-amber-600"></i>
                                        <span>Permintaan Lisensi Office</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="selectedSubCategory = 'lainnya'"
                                        :class="selectedSubCategory === 'lainnya' ? 'bg-white text-slate-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
                                    >
                                        <i class="ri-more-line text-slate-600"></i>
                                        <span>Lainnya</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Auto-filled Metadata Header -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Tanggal Pengajuan
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-calendar-line text-emerald-700"></i>
                                        <span>{{ $todayFormatted }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Otomatis</span>
                                    </div>
                                    <input type="hidden" name="tanggal_pengajuan" value="{{ $todayDate }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Nama Pemohon
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-user-line text-emerald-700"></i>
                                        <span>{{ $user->name }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Akun Login</span>
                                    </div>
                                    <input type="hidden" name="nama" value="{{ old('nama', $user->name) }}">
                                </div>
                            </div>

                            <!-- VARIASI a: PERMINTAAN LISENSI OFFICE -->
                            <div x-show="selectedSubCategory === 'permintaan_lisensi_office'" class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="office_bidang" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Bidang / Substansi <span class="text-rose-500">*</span>
                                        </label>
                                        <select 
                                            id="office_bidang" 
                                            name="bidang" 
                                            :required="selectedSubCategory === 'permintaan_lisensi_office'"
                                            :disabled="selectedSubCategory !== 'permintaan_lisensi_office'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                            <option value="">-- Pilih Bidang --</option>
                                            <option value="Tata Usaha" {{ old('bidang') === 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                                            <option value="Pengujian" {{ old('bidang') === 'Pengujian' ? 'selected' : '' }}>Pengujian</option>
                                            <option value="Penindakan" {{ old('bidang') === 'Penindakan' ? 'selected' : '' }}>Penindakan</option>
                                            <option value="Infokom" {{ old('bidang') === 'Infokom' ? 'selected' : '' }}>Infokom</option>
                                            <option value="Pemeriksaan" {{ old('bidang') === 'Pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                                        </select>
                                        <x-input-error class="mt-1" :messages="$errors->get('bidang')" />
                                    </div>

                                    <div>
                                        <label for="office_merk" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Merk Laptop / Komputer <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="office_merk" 
                                            name="merk_perangkat" 
                                            value="{{ old('merk_perangkat') }}" 
                                            placeholder="Contoh: Dell Latitude 3420" 
                                            :required="selectedSubCategory === 'permintaan_lisensi_office'"
                                            :disabled="selectedSubCategory !== 'permintaan_lisensi_office'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('merk_perangkat')" />
                                    </div>

                                    <div>
                                        <label for="office_kode_bmn" class="block text-xs font-semibold text-slate-700 mb-1">
                                            Kode BMN Perangkat <span class="text-rose-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="office_kode_bmn" 
                                            name="kode_bmn" 
                                            value="{{ old('kode_bmn') }}" 
                                            placeholder="Contoh: 3.05.01.04.001.000456" 
                                            :required="selectedSubCategory === 'permintaan_lisensi_office'"
                                            :disabled="selectedSubCategory !== 'permintaan_lisensi_office'"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
                                        >
                                        <x-input-error class="mt-1" :messages="$errors->get('kode_bmn')" />
                                    </div>
                                </div>

                                <div>
                                    <label for="office_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deskripsi Kendala Lisensi <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea 
                                        id="office_deskripsi" 
                                        name="deskripsi" 
                                        rows="4" 
                                        :required="selectedSubCategory === 'permintaan_lisensi_office'"
                                        :disabled="selectedSubCategory !== 'permintaan_lisensi_office'"
                                        placeholder="Jelaskan kebutuhan lisensi atau kendala (misal: muncul Unlicensed Product, butuh aktivasi Microsoft 365 baru)..." 
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    >{{ old('deskripsi') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                                </div>
                            </div>

                            <!-- VARIASI b: SUB KATEGORI LAINNYA -->
                            <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4" x-data="{ fileName: '' }">
                                <div>
                                    <label for="office_deskripsi_lainnya" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deskripsi Kebutuhan Aplikasi Perkantoran <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea 
                                        id="office_deskripsi_lainnya" 
                                        name="deskripsi" 
                                        rows="4" 
                                        :required="selectedSubCategory === 'lainnya'"
                                        :disabled="selectedSubCategory !== 'lainnya'"
                                        placeholder="Jelaskan kendala atau permohonan software office lainnya..." 
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    >{{ old('deskripsi') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Berkas Lampiran <span class="text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                            <span>Pilih File</span>
                                            <input 
                                                type="file" 
                                                name="file_lampiran" 
                                                class="sr-only"
                                                @change="fileName = $event.target.files[0]?.name"
                                                :disabled="selectedSubCategory !== 'lainnya'"
                                            >
                                        </label>
                                        <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF (Maks 5MB)'"></span>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-[0.99]">
                                        <span x-text="selectedSubCategory === 'permintaan_lisensi_office' ? 'Ajukan Lisensi Office' : 'Kirim Pengaduan Office'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ======================================================== -->
                    <!-- FORM 6: LAYANAN LAINNYA (Dukungan Umum TIK)               -->
                    <!-- ======================================================== -->
                    <div x-show="selectedServiceKey === 'lainnya'" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7" x-data="{ fileName: '' }">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Pengaduan Layanan TIK Lainnya</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Permohonan perbaikan printer, scanner, proyektor, audio ruang rapat, atau konsultasi kebutuhan teknologi informasi lainnya.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="service_key" value="lainnya">

                            <!-- Auto-filled Metadata Header -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Tanggal Pengajuan
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-calendar-line text-emerald-700"></i>
                                        <span>{{ $todayFormatted }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Otomatis</span>
                                    </div>
                                    <input type="hidden" name="tanggal_pengajuan" value="{{ $todayDate }}">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">
                                        Nama Pemohon
                                    </label>
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                                        <i class="ri-user-line text-emerald-700"></i>
                                        <span>{{ $user->name }}</span>
                                        <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">Akun Login</span>
                                    </div>
                                    <input type="hidden" name="nama" value="{{ old('nama', $user->name) }}">
                                </div>
                            </div>

                            <div>
                                <label for="lainnya_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Deskripsi Permintaan / Kendala <span class="text-rose-500">*</span>
                                </label>
                                <textarea 
                                    id="lainnya_deskripsi" 
                                    name="deskripsi" 
                                    rows="5" 
                                    required 
                                    placeholder="Jelaskan kebutuhan teknis Anda secara rinci, nomor ruangan atau lokasi perangkat, serta kronologi kendala..." 
                                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                >{{ old('deskripsi') }}</textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Upload Berkas Lampiran <span class="text-slate-400 font-normal">(Opsional, foto error/dokumen)</span>
                                </label>
                                <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                    <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                        <span>Pilih File</span>
                                        <input 
                                            type="file" 
                                            name="file_lampiran" 
                                            class="sr-only"
                                            @change="fileName = $event.target.files[0]?.name"
                                        >
                                    </label>
                                    <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF, DOCX, ZIP (Maks 5MB)'"></span>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-[0.99]">
                                        <span>Kirim Pengaduan</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ======================================================== -->
                    <!-- FORM 7: FORMULIR STANDAR (SIPT, Srikandi, Email, dll)     -->
                    <!-- ======================================================== -->
                    <div 
                        x-show="selectedServiceKey && !['sertifikat_bse', 'zoom', 'formulir_kegiatan', 'install_windows', 'lisensi_office', 'lainnya'].includes(selectedServiceKey)" 
                        class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7"
                        x-data="{ fileName: '' }"
                    >
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Formulir Pengaduan Layanan</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Lengkapi detail permohonan atau kendala di bawah ini agar teknisi TIK BBPOM dapat segera menindaklanjuti.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="service_key" :value="selectedServiceKey">

                            <!-- Subkategori if defined in config -->
                            <template x-if="currentService && currentService.subcategories && Object.keys(currentService.subcategories).length > 0">
                                <div>
                                    <label for="std_subcategory" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Sub Kategori <span x-text="currentService ? currentService.name : ''"></span>
                                    </label>
                                    <select 
                                        name="subcategory" 
                                        id="std_subcategory"
                                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    >
                                        <option value="">-- Pilih Sub Kategori Spesifik --</option>
                                        <template x-for="(sub, subKey) in currentService.subcategories" :key="subKey">
                                            <option :value="typeof sub === 'string' ? sub : subKey" x-text="typeof sub === 'string' ? sub : subKey"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>

                            <!-- Subjek / Judul -->
                            <div>
                                <label for="subject" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Subjek / Judul Kendala <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    id="subject" 
                                    name="subject" 
                                    type="text" 
                                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm" 
                                    value="{{ old('subject') }}" 
                                    placeholder="Tuliskan ringkasan kendala atau permohonan..." 
                                    required 
                                >
                                <x-input-error class="mt-1" :messages="$errors->get('subject')" />
                            </div>

                            <!-- Kategori ITSM -->
                            <div>
                                <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Kategori Database ITSM <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    id="category_id" 
                                    name="category_id" 
                                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    required
                                >
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-1" :messages="$errors->get('category_id')" />
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Deskripsi Rinci Kebutuhan / Kendala <span class="text-rose-500">*</span>
                                </label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="5" 
                                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    placeholder="Jelaskan kebutuhan atau kendala Anda secara lengkap..."
                                    required
                                >{{ old('description') }}</textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('description')" />
                            </div>

                            <!-- Upload Lampiran -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Lampiran Bukti / Screenshot (Opsional)
                                </label>
                                <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                                    <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                                        <span>Pilih Berkas</span>
                                        <input 
                                            id="attachment" 
                                            name="attachment" 
                                            type="file" 
                                            class="sr-only"
                                            @change="fileName = $event.target.files[0]?.name"
                                        >
                                    </label>
                                    <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF, DOCX, ZIP (Maks 5MB)'"></span>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('attachment')" />
                            </div>

                            <!-- Actions -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition active:scale-[0.99]">
                                        <span>Kirim Pengaduan Tiket</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- Right Guide & Help Column -->
                <div class="space-y-6">
                    <!-- Service Quick Info -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Tentang Layanan Ini
                        </h4>
                        
                        <div class="mt-3 text-xs text-slate-600 leading-relaxed">
                            <p x-text="currentService ? currentService.description : ''"></p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400">
                            ID Layanan: <code class="font-mono text-slate-600" x-text="selectedServiceKey"></code>
                        </div>
                    </div>

                    <!-- SLA & Rules -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            SLA Tindak Lanjut TI
                        </h4>
                        <ul class="mt-3 space-y-2 text-xs text-slate-600 leading-relaxed">
                            <li class="flex items-start gap-1.5">
                                <span class="text-emerald-700 font-bold">&bull;</span>
                                <span>Verifikasi tiket maks. <strong>1 - 2 jam kerja</strong>.</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <span class="text-emerald-700 font-bold">&bull;</span>
                                <span>Permohonan tautan Zoom dan form kegiatan diproses pada hari kerja yang sama.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-200/60 p-6 text-emerald-950">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Kontak Darurat TI BBPOM</h4>
                        <p class="text-xs text-emerald-800/90 mt-1.5 leading-relaxed">
                            Bila sistem perkantoran lumpuh total atau acara mendesak, silakan hubungi tim IT di Ruang Pengelola TI.
                        </p>
                        <div class="mt-3 text-xs font-bold text-emerald-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Ekstensi Internal: 104 / 105
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Modern Pill Breadcrumb -->
                <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-2" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition-colors flex items-center gap-1">
                        <i class="ri-home-4-line text-xs"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="ri-arrow-right-s-line text-slate-300 text-xs"></i>
                    <a href="{{ route('tickets.index') }}" class="hover:text-emerald-700 transition-colors">Daftar Pengaduan</a>
                    <i class="ri-arrow-right-s-line text-slate-300 text-xs"></i>
                    <span class="font-semibold text-emerald-800">App Gateway Layanan</span>
                </nav>
                <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-600 ring-4 ring-emerald-100"></span>
                    Katalog Layanan & Pengaduan TIK
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Pilih modul layanan atau isi formulir pengaduan teknologi informasi Balai Besar POM di Semarang
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold shadow-xs hover:shadow transition-all">
                    <i class="ri-arrow-left-line text-sm"></i>
                    <span>Riwayat Pengaduan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Main Container with Alpine.js Reactive Gateway State -->
    <div x-data="ticketGatewayData()" class="space-y-6">

        <!-- Modal Konfirmasi Meninggalkan Halaman (Atomic Component) -->
        <x-leave-form-confirmation-modal />

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
                            <i class="ri-search-line text-lg"></i>
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
                            <i class="ri-close-line text-lg"></i>
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
                    <i class="ri-error-warning-line text-2xl"></i>
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

        <!-- VIEW 2: UNIFIED DYNAMIC TICKET FORM (Shown when a service is selected) -->
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
                        @click="requestClearService()" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:border-slate-400 bg-slate-50 hover:bg-white text-xs font-bold text-slate-700 shadow-sm transition">
                        <i class="ri-arrow-left-line"></i>
                        <span>Ganti Layanan (Katalog)</span>
                    </button>
                </div>
            </div>

            <!-- Form Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Form Column: Unified Card Container -->
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

                    <!-- UNIFIED ENTERPRISE FORM CARD (SIPT/SRIKANDI STANDARD) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900" x-text="currentService ? currentService.name : 'Formulir Layanan'"></h3>
                            <p class="text-xs text-slate-500 mt-1" x-text="currentService ? currentService.description : 'Lengkapi detail permohonan atau kendala di bawah ini agar teknisi TIK BBPOM dapat segera menindaklanjuti.'"></p>
                        </div>

                        <form 
                            method="POST" 
                            action="{{ route('tickets.store') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6"
                            @submit="isSubmitting = true"
                        >
                            @csrf
                            <input type="hidden" name="service_key" :value="selectedServiceKey">
                            <input type="hidden" name="subcategory" :value="selectedSubcategory">
                            <input type="hidden" name="subcategory_key" :value="selectedSubcategory">

                            <!-- Auto-filled Metadata Header (Tanggal Otomatis & Nama Pemohon Otomatis) -->
                            @include('tickets.forms.partials.metadata-header')

                            <!-- 1. SIPT -->
                            <div x-show="selectedServiceKey === 'sipt'">
                                @include('tickets.forms.form-sipt')
                            </div>

                            <!-- 2. SRIKANDI -->
                            <div x-show="selectedServiceKey === 'srikandi'">
                                @include('tickets.forms.form-srikandi')
                            </div>

                            <!-- 3. EMAIL CORPORATE -->
                            <div x-show="selectedServiceKey === 'email_corporate'">
                                @include('tickets.forms.form-email-corporate')
                            </div>

                            <!-- 4. SERTIFIKAT BSRE -->
                            <div x-show="selectedServiceKey === 'sertifikat_bse'">
                                @include('tickets.forms.form-bsre')
                            </div>

                            <!-- 5. ZOOM -->
                            <div x-show="selectedServiceKey === 'zoom'">
                                @include('tickets.forms.form-zoom')
                            </div>

                            <!-- 6. FORMULIR & KEGIATAN -->
                            <div x-show="selectedServiceKey === 'formulir_kegiatan'">
                                @include('tickets.forms.form-formulir-kegiatan')
                            </div>

                            <!-- 7. INTERNET -->
                            <div x-show="selectedServiceKey === 'internet'">
                                @include('tickets.forms.form-internet')
                            </div>

                            <!-- 8. SUBSITE -->
                            <div x-show="selectedServiceKey === 'subsite'">
                                @include('tickets.forms.form-subsite')
                            </div>

                            <!-- 9. INFOGRAFIS -->
                            <div x-show="selectedServiceKey === 'infografis'">
                                @include('tickets.forms.form-infografis')
                            </div>

                            <!-- 10. INSTALL WINDOWS -->
                            <div x-show="selectedServiceKey === 'install_windows'">
                                @include('tickets.forms.form-install-windows')
                            </div>

                            <!-- 11. LISENSI OFFICE -->
                            <div x-show="selectedServiceKey === 'lisensi_office'">
                                @include('tickets.forms.form-lisensi-office')
                            </div>

                            <!-- 12. LAINNYA -->
                            <div x-show="selectedServiceKey === 'lainnya'">
                                @include('tickets.forms.form-lainnya')
                            </div>

                            <input type="hidden" id="category_id" name="category_id" :value="currentService ? currentService.default_category_id : 5">

                            <!-- Form Action Buttons (Unified Enterprise Bar) -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button type="button" @click="clearService()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    &larr; Ganti Layanan
                                </button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button 
                                        type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:scale-[0.99] text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition"
                                    >
                                        <i class="ri-send-plane-fill"></i>
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
                            Panduan Sub Kategori
                        </h4>
                        
                        <div class="mt-3 text-xs text-slate-600 leading-relaxed">
                            <p x-text="currentService ? currentService.description : ''"></p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                            Sub kategori aktif: <strong class="text-emerald-800" x-text="currentService && currentService.subcategories ? (currentService.subcategories[selectedSubcategory] || 'Umum') : 'Umum'"></strong>
                        </div>
                    </div>

                    <!-- SLA & Rules (Atomic Component) -->
                    <x-sla-info-card />
                </div>

            </div>

        </div>

    </div>

    <!-- Script to handle file input display & Alpine Gateway state -->
    <script>
        function ticketGatewayData() {
            return {
                search: '',
                activeGroup: 'all',
                selectedServiceKey: @json(old('service_key', $initialService ?? '')),
                selectedSubcategory: @json(old('subcategory_key', old('subcategory', $initialSubcategory ?? ''))),
                selectedSubCategory: @json(old('subcategory', old('subcategory_key', $initialSubcategory ?? ''))),
                services: @json($services),
                groups: @json($groups),
                isSubmitting: false,
                showLeaveConfirm: false,
                pendingLeaveTarget: null,

                get currentService() {
                    return this.services[this.selectedServiceKey] || null;
                },

                selectService(key) {
                    this.selectedServiceKey = key;
                    const s = this.services[key];
                    if (s && s.subcategories && Object.keys(s.subcategories).length > 0) {
                        if (!this.selectedSubcategory || !s.subcategories[this.selectedSubcategory]) {
                            const first = Object.keys(s.subcategories)[0];
                            this.selectedSubcategory = first;
                            this.selectedSubCategory = first;
                        } else {
                            this.selectedSubCategory = this.selectedSubcategory;
                        }
                    } else {
                        this.selectedSubcategory = '';
                        this.selectedSubCategory = '';
                    }
                },

                clearService() {
                    this.selectedServiceKey = null;
                    this.selectedSubcategory = '';
                    this.selectedSubCategory = '';
                },

                isMatch(key) {
                    const svc = this.services[key];
                    if (!svc) return false;

                    if (this.activeGroup !== 'all' && svc.group !== this.activeGroup) {
                        return false;
                    }

                    if (!this.search || this.search.trim() === '') {
                        return true;
                    }

                    const q = this.search.toLowerCase().trim();
                    const nameMatch = (svc.name || '').toLowerCase().includes(q);
                    const longNameMatch = (svc.long_name || '').toLowerCase().includes(q);
                    const descMatch = (svc.description || '').toLowerCase().includes(q);
                    const badgeMatch = (svc.badge || '').toLowerCase().includes(q);

                    return nameMatch || longNameMatch || descMatch || badgeMatch;
                },

                get visibleCount() {
                    return Object.keys(this.services).filter(key => this.isMatch(key)).length;
                },

                isDirty() {
                    if (this.isSubmitting) return false;
                    if (!this.selectedServiceKey) return false;

                    const form = document.querySelector('form[action*="tickets"]');
                    if (!form) return false;

                    const ignoredNames = [
                        '_token', 
                        'service_key', 
                        'subcategory', 
                        'subcategory_key', 
                        'category_id', 
                        'tanggal_pengajuan', 
                        'nama'
                    ];

                    for (let el of form.elements) {
                        if (!el.name || ignoredNames.includes(el.name) || el.type === 'submit') continue;
                        if (el.offsetParent === null) continue;

                        if (el.type === 'file') {
                            if (el.files && el.files.length > 0) return true;
                        } else if (el.type === 'checkbox' || el.type === 'radio') {
                            if (el.checked) return true;
                        } else if (el.value && el.value.trim().length > 0) {
                            return true;
                        }
                    }
                    return false;
                },

                confirmLeave(target) {
                    if (this.isDirty() && !this.isSubmitting) {
                        this.pendingLeaveTarget = target;
                        this.showLeaveConfirm = true;
                    } else if (target === 'clearService') {
                        this.clearService();
                    } else if (typeof target === 'string') {
                        window.location.href = target;
                    }
                },

                cancelLeave() {
                    this.showLeaveConfirm = false;
                    this.pendingLeaveTarget = null;
                },

                proceedLeave() {
                    this.showLeaveConfirm = false;
                    const target = this.pendingLeaveTarget;
                    this.pendingLeaveTarget = null;
                    if (target === 'clearService') {
                        this.clearService();
                    } else if (typeof target === 'string') {
                        window.location.href = target;
                    }
                },

                requestClearService() {
                    if (this.isDirty()) {
                        this.confirmLeave('clearService');
                    } else {
                        this.clearService();
                    }
                },

                init() {
                    try {
                        localStorage.removeItem('bbpom_itsm_ticket_draft');
                    } catch (e) {}

                    if (this.selectedServiceKey && (!this.selectedSubcategory || this.selectedSubcategory === '')) {
                        const s = this.services[this.selectedServiceKey];
                        if (s && s.subcategories && Object.keys(s.subcategories).length > 0) {
                            const first = Object.keys(s.subcategories)[0];
                            this.selectedSubcategory = first;
                            this.selectedSubCategory = first;
                        }
                    } else if (this.selectedSubcategory) {
                        this.selectedSubCategory = this.selectedSubcategory;
                    }

                    // Intercept in-app navigation link clicks ONLY when actively filling a selected service form
                    document.addEventListener('click', (e) => {
                        if (!this.selectedServiceKey) return;

                        const anchor = e.target.closest('a[href]');
                        if (!anchor) return;
                        const href = anchor.getAttribute('href');
                        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                        if (anchor.target === '_blank') return;
                        if (this.isDirty() && !this.isSubmitting) {
                            e.preventDefault();
                            this.confirmLeave(anchor.href);
                        }
                    });

                    // Warn on tab refresh/close ONLY when user has typed unsaved content into active service form
                    window.addEventListener('beforeunload', (e) => {
                        if (this.selectedServiceKey && this.isDirty() && !this.isSubmitting) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                }
            };
        }
    </script>
</x-app-layout>

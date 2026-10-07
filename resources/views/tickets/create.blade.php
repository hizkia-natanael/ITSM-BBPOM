<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('dashboard')],
                    ['label' => 'Daftar Pengaduan', 'url' => route('tickets.index')],
                    ['label' => 'Buat Pengaduan']
                ]" />
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
    <div x-data="ticketGatewayData()">

        <!-- VIEW 1: APP GATEWAY CATALOG (Atomic Component) -->
        <x-ticket-gateway-catalog :services="$services" />

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
                            id="ticketForm"
                            method="POST" 
                            action="{{ route('tickets.store') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6"
                            @submit="startSubmission()"
                            @input="userHasTyped = true"
                            @change="userHasTyped = true"
                        >
                            @csrf
                            <input type="hidden" name="service_key" :value="selectedServiceKey">
                            <input type="hidden" name="subcategory" :value="selectedSubcategory">
                            <input type="hidden" name="subcategory_key" :value="selectedSubcategory">

                            <!-- Auto-filled Metadata Header (Tanggal Otomatis & Nama Pemohon Otomatis) -->
                            @include('tickets.forms.partials.metadata-header')

                            <!-- 1. SIPT -->
                            <fieldset :disabled="selectedServiceKey !== 'sipt'" x-show="selectedServiceKey === 'sipt'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-sipt')
                            </fieldset>

                            <!-- 2. SRIKANDI -->
                            <fieldset :disabled="selectedServiceKey !== 'srikandi'" x-show="selectedServiceKey === 'srikandi'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-srikandi')
                            </fieldset>

                            <!-- 3. EMAIL CORPORATE -->
                            <fieldset :disabled="selectedServiceKey !== 'email_corporate'" x-show="selectedServiceKey === 'email_corporate'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-email-corporate')
                            </fieldset>

                            <!-- 4. SERTIFIKAT BSRE -->
                            <fieldset :disabled="selectedServiceKey !== 'sertifikat_bse'" x-show="selectedServiceKey === 'sertifikat_bse'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-bsre')
                            </fieldset>

                            <!-- 5. ZOOM -->
                            <fieldset :disabled="selectedServiceKey !== 'zoom'" x-show="selectedServiceKey === 'zoom'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-zoom')
                            </fieldset>

                            <!-- 6. FORMULIR & KEGIATAN -->
                            <fieldset :disabled="selectedServiceKey !== 'formulir_kegiatan'" x-show="selectedServiceKey === 'formulir_kegiatan'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-formulir-kegiatan')
                            </fieldset>

                            <!-- 7. INTERNET -->
                            <fieldset :disabled="selectedServiceKey !== 'internet'" x-show="selectedServiceKey === 'internet'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-internet')
                            </fieldset>

                            <!-- 8. SUBSITE -->
                            <fieldset :disabled="selectedServiceKey !== 'subsite'" x-show="selectedServiceKey === 'subsite'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-subsite')
                            </fieldset>

                            <!-- 9. INFOGRAFIS -->
                            <fieldset :disabled="selectedServiceKey !== 'infografis'" x-show="selectedServiceKey === 'infografis'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-infografis')
                            </fieldset>

                            <!-- 10. INSTALL WINDOWS -->
                            <fieldset :disabled="selectedServiceKey !== 'install_windows'" x-show="selectedServiceKey === 'install_windows'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-install-windows')
                            </fieldset>

                            <!-- 11. LISENSI OFFICE -->
                            <fieldset :disabled="selectedServiceKey !== 'lisensi_office'" x-show="selectedServiceKey === 'lisensi_office'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-lisensi-office')
                            </fieldset>

                            <!-- 12. LAINNYA -->
                            <fieldset :disabled="selectedServiceKey !== 'lainnya'" x-show="selectedServiceKey === 'lainnya'" class="border-0 p-0 m-0 min-w-0">
                                @include('tickets.forms.form-lainnya')
                            </fieldset>

                            <input type="hidden" id="category_id" name="category_id" :value="currentService && currentService.default_category_id ? currentService.default_category_id : ''">

                            <!-- Form Action Buttons (Unified Enterprise Bar) -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button 
                                    type="button" 
                                    @click="requestClearService()" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                                >
                                    <i class="ri-arrow-left-line text-xs"></i>
                                    <span>Ganti Layanan</span>
                                </button>
                                <div class="flex items-center gap-2">
                                    <a 
                                        href="{{ route('tickets.index') }}" 
                                        @click="if (isDirty()) { $event.preventDefault(); confirmLeave('{{ route('tickets.index') }}'); }"
                                        class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                                    >
                                        Batal
                                    </a>
                                    <button 
                                        type="submit" 
                                        :disabled="isSubmitting"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:scale-[0.99] text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition disabled:opacity-60 disabled:cursor-not-allowed"
                                    >
                                        <i class="ri-send-plane-fill" x-show="!isSubmitting"></i>
                                        <i class="ri-loader-4-line animate-spin" x-show="isSubmitting" x-cloak></i>
                                        <span x-text="isSubmitting ? 'Memproses Tiket...' : 'Kirim Pengaduan Tiket'">Kirim Pengaduan Tiket</span>
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

        <!-- Modal Konfirmasi Meninggalkan Halaman (Atomic Component) -->
        <x-leave-form-confirmation-modal />

        <!-- Modal Loading Screen Pengiriman Tiket (Atomic Component) -->
        <x-ticket-submission-loading-screen />

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
                submissionStepIndex: 0,
                submissionSteps: [
                    'Memvalidasi data dan menyiapkan berkas lampiran...',
                    'Mengenkripsi dan mengunggah berkas ke server...',
                    'Mendaftarkan nomor tiket ke antrean Helpdesk TIK...'
                ],
                submissionTimer: null,
                userHasTyped: false,
                showLeaveConfirm: false,
                pendingLeaveTarget: null,

                get currentSubmissionStepText() {
                    return this.submissionSteps[this.submissionStepIndex] || this.submissionSteps[0];
                },

                get currentService() {
                    return this.services[this.selectedServiceKey] || null;
                },

                selectService(key) {
                    this.selectedServiceKey = key;
                    this.userHasTyped = false;
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

                    // Push browser history state so the browser back button can be intercepted
                    try {
                        window.history.pushState({ serviceKey: key }, '', window.location.href);
                    } catch (e) {}

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                clearService() {
                    this.selectedServiceKey = null;
                    this.selectedSubcategory = '';
                    this.selectedSubCategory = '';
                    this.userHasTyped = false;
                    const form = document.getElementById('ticketForm') || document.querySelector('form[action*="tickets"]');
                    if (form) {
                        form.reset();
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
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
                    if (this.userHasTyped) return true;

                    const form = document.getElementById('ticketForm') || document.querySelector('form[action*="tickets"]');
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
                        if (!el.name || ignoredNames.includes(el.name) || el.type === 'submit' || el.type === 'button') continue;
                        if (el.disabled) continue;

                        // Check if element is inside the active service container
                        const serviceContainer = el.closest('[x-show*="selectedServiceKey"]');
                        if (serviceContainer && serviceContainer.getAttribute('x-show') !== 'selectedServiceKey' && !serviceContainer.getAttribute('x-show').includes(this.selectedServiceKey)) {
                            continue;
                        }

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
                    } else if (target === 'clearService' || target === 'history_back') {
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
                    this.userHasTyped = false;

                    if (target === 'clearService' || target === 'history_back') {
                        this.clearService();
                    } else if (typeof target === 'string') {
                        this.isSubmitting = true;
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

                resetSubmissionState() {
                    this.isSubmitting = false;
                    this.submissionStepIndex = 0;
                    if (this.submissionTimer) {
                        clearInterval(this.submissionTimer);
                        this.submissionTimer = null;
                    }
                },

                startSubmission() {
                    this.isSubmitting = true;
                    this.submissionStepIndex = 0;
                    try {
                        sessionStorage.setItem('bbpom_ticket_submitted_success', '1');
                    } catch (e) {}
                    if (this.submissionTimer) {
                        clearInterval(this.submissionTimer);
                    }
                    this.submissionTimer = setInterval(() => {
                        if (this.submissionStepIndex < 2) {
                            this.submissionStepIndex++;
                        }
                    }, 2200);
                },

                init() {
                    this.resetSubmissionState();

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

                    // Reset submission loading state whenever page is shown (handles Back/Forward Cache / bfcache)
                    window.addEventListener('pageshow', (event) => {
                        this.resetSubmissionState();
                        let wasSubmitted = false;
                        try {
                            wasSubmitted = Boolean(sessionStorage.getItem('bbpom_ticket_submitted_success'));
                            sessionStorage.removeItem('bbpom_ticket_submitted_success');
                        } catch (e) {}

                        if (event.persisted || wasSubmitted) {
                            this.clearService();
                        }
                    });

                    // Intercept browser back button (popstate)
                    window.addEventListener('popstate', (e) => {
                        this.resetSubmissionState();
                        if (this.selectedServiceKey && this.isDirty()) {
                            try {
                                window.history.pushState({ serviceKey: this.selectedServiceKey }, '', window.location.href);
                            } catch (err) {}
                            this.confirmLeave('history_back');
                        } else if (this.selectedServiceKey) {
                            this.clearService();
                        }
                    });

                    // Intercept in-app navigation link clicks with capture phase
                    document.addEventListener('click', (e) => {
                        if (!this.selectedServiceKey) return;

                        const anchor = e.target.closest('a[href]');
                        if (!anchor) return;
                        const href = anchor.getAttribute('href');
                        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                        if (anchor.target === '_blank') return;

                        if (this.isDirty() && !this.isSubmitting) {
                            e.preventDefault();
                            e.stopPropagation();
                            this.confirmLeave(anchor.href);
                        }
                    }, true);

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

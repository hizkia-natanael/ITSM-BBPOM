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

        <!-- VIEW 1: APP GATEWAY CATALOG (Shown when no service is selected) -->
        <div x-show="!selectedServiceKey" x-transition.opacity.duration.300ms class="space-y-6">

            <!-- Hero Search & Helpdesk Info Banner (Konsep A: Split Banner) -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-5 sm:p-6 lg:p-7 text-white shadow-lg relative overflow-hidden border border-slate-700/50">
                <div class="absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-center">
                    <!-- Left: Title, Search & Filter -->
                    <div class="lg:col-span-8 flex flex-col justify-between">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                App Gateway Layanan TI
                            </span>
                            <span class="text-[11px] text-slate-300/80 font-medium px-2 py-0.5 rounded-full bg-white/[0.08] border border-white/10" x-text="visibleCount + ' Layanan Tersedia'"></span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white leading-tight">
                            Pilih Menu Layanan yang Ingin Anda Laporkan
                        </h3>
                        <p class="mt-1.5 text-xs text-slate-300 leading-relaxed max-w-xl">
                            Cari aplikasi, fasilitas jaringan, atau perbaikan sistem dinas untuk terhubung langsung dengan formulir permohonan.
                        </p>

                        <!-- Instant Search Bar -->
                        <div class="mt-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ri-search-line text-base"></i>
                            </div>
                            <input 
                                type="text" 
                                x-model="search" 
                                placeholder="Cari layanan, misal: zoom, bsre, presensi, survey, windows, lisensi..."
                                class="w-full pl-10 pr-9 py-2.5 bg-white/95 focus:bg-white text-slate-900 rounded-xl text-xs sm:text-sm placeholder:text-slate-400 shadow-md border border-transparent focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                            >
                            <button 
                                x-show="search" 
                                @click="search = ''" 
                                type="button" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                title="Reset"
                            >
                                <i class="ri-close-line text-base"></i>
                            </button>
                        </div>

                        <!-- Group Tabs Filter (Strictly Single Row, Scrollbar Hidden) -->
                        <div class="mt-3.5 flex items-center gap-1.5 pt-3 border-t border-slate-700/50 flex-nowrap overflow-x-auto no-scrollbar [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                            <span class="text-[11px] text-slate-400 font-medium me-0.5 shrink-0 flex items-center gap-1">
                                <i class="ri-filter-3-line text-xs text-slate-400"></i>
                                <span>Kategori:</span>
                            </span>
                            <template x-for="(label, key) in groups" :key="key">
                                <button 
                                    type="button" 
                                    @click="activeGroup = key"
                                    :class="activeGroup === key 
                                        ? 'bg-emerald-600 text-white font-bold shadow-xs' 
                                        : 'bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700/60 font-medium'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition duration-150 shrink-0 whitespace-nowrap"
                                    x-text="label"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <!-- Right: Helpdesk Information Panel (Narrower 4-column card) -->
                    <div class="lg:col-span-4">
                        <div class="rounded-2xl bg-white/[0.08] backdrop-blur-md border border-white/15 p-3.5 sm:p-4 shadow-xl relative overflow-hidden">
                            <div class="flex items-center justify-between pb-2.5 border-b border-white/10 mb-2.5">
                                <div class="flex items-center gap-1.5">
                                    <i class="ri-customer-service-2-line text-emerald-400 text-sm"></i>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-200">Helpdesk TIK</span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Siaga
                                </span>
                            </div>

                            <div class="space-y-2 text-xs">
                                <!-- 1. Jam Layanan -->
                                <div class="flex items-start gap-2.5">
                                    <div class="w-6 h-6 rounded-md bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="ri-time-line text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-100 text-[11px] leading-tight">Jam Layanan</p>
                                        <p class="text-[10.5px] text-slate-300/90 mt-0.5">Senin &ndash; Jumat, 08.00 &ndash; 16.00</p>
                                    </div>
                                </div>

                                <!-- 2. SLA Penanganan -->
                                <div class="flex items-start gap-2.5">
                                    <div class="w-6 h-6 rounded-md bg-amber-500/20 border border-amber-400/30 text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="ri-flashlight-line text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-100 text-[11px] leading-tight">SLA Respons Awal</p>
                                        <p class="text-[10.5px] text-slate-300/90 mt-0.5">Target respon dalam &le; 2 Jam</p>
                                    </div>
                                </div>

                                <!-- 3. Bantuan & Lokasi -->
                                <div class="flex items-start gap-2.5">
                                    <div class="w-6 h-6 rounded-md bg-cyan-500/20 border border-cyan-400/30 text-cyan-300 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="ri-building-4-line text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-100 text-[11px] leading-tight">Ruang Tim TI &amp; Ext</p>
                                        <p class="text-[10.5px] text-slate-300/90 mt-0.5">Tata Usaha Lt. 2 (Ext. 104)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 12 Service Cards Grid (Konsep 3: Compact Metric & Service Badge) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                @foreach($services as $key => $svc)
                    @php
                        $subCount = !empty($svc['subcategories']) ? count($svc['subcategories']) : 0;
                        $colorClass = match($svc['color'] ?? 'slate') {
                            'indigo' => 'from-indigo-600 to-indigo-700 shadow-indigo-500/25',
                            'blue' => 'from-blue-600 to-cyan-600 shadow-blue-500/25',
                            'emerald' => 'from-emerald-600 to-teal-600 shadow-emerald-500/25',
                            'cyan' => 'from-cyan-600 to-blue-600 shadow-cyan-500/25',
                            'sky' => 'from-sky-600 to-blue-700 shadow-sky-500/25',
                            'teal' => 'from-teal-600 to-emerald-700 shadow-teal-500/25',
                            'purple' => 'from-purple-600 to-indigo-600 shadow-purple-500/25',
                            'violet' => 'from-violet-600 to-purple-700 shadow-violet-500/25',
                            'amber' => 'from-amber-500 to-orange-600 shadow-amber-500/25',
                            default => 'from-slate-700 to-slate-800 shadow-slate-500/25',
                        };
                    @endphp
                    <div 
                        x-show="isMatch('{{ $key }}')"
                        x-transition.opacity.duration.200ms
                        @click="selectService('{{ $key }}')"
                        class="group relative bg-white rounded-2xl p-4 border border-slate-200/80 hover:border-emerald-500/60 shadow-xs hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center gap-3.5 sm:gap-4"
                    >
                        <!-- Service Icon Badge with Vibrant Color Gradient -->
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $colorClass }} flex items-center justify-center shrink-0 shadow-md transition-transform duration-200 group-hover:scale-105">
                            <i class="{{ $svc['icon'] }} text-2xl text-white"></i>
                        </div>

                        <!-- Service Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-sm sm:text-base text-slate-900 group-hover:text-emerald-800 transition truncate">
                                    {{ $svc['name'] }}
                                </h4>
                                @if($subCount > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border-emerald-200 transition shrink-0">
                                        {{ $subCount }} Modul
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 truncate mt-0.5">
                                {{ $svc['long_name'] }}
                            </p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wide">
                                    {{ $svc['badge'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Micro-interaction Action Indicator -->
                        <div class="w-8 h-8 rounded-xl bg-slate-50 group-hover:bg-emerald-600 text-slate-400 group-hover:text-white flex items-center justify-center transition-all duration-200 shrink-0">
                            <i class="ri-arrow-right-line text-sm transition-transform group-hover:translate-x-0.5"></i>
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
                            id="ticketForm"
                            method="POST" 
                            action="{{ route('tickets.store') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6"
                            @submit="isSubmitting = true"
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

                            <input type="hidden" id="category_id" name="category_id" :value="currentService ? currentService.default_category_id : 5">

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

        <!-- Modal Konfirmasi Meninggalkan Halaman (Atomic Component) -->
        <x-leave-form-confirmation-modal />

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
                userHasTyped: false,
                showLeaveConfirm: false,
                pendingLeaveTarget: null,

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

                    // Intercept browser back button (popstate)
                    window.addEventListener('popstate', (e) => {
                        if (this.selectedServiceKey && this.isDirty() && !this.isSubmitting) {
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

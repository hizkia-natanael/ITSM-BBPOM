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
    <!-- Main Container with Alpine.js Reactive Gateway State -->
    <div x-data="ticketGatewayData()" class="space-y-6">

        <!-- Alert Pemulihan Draf Isian Formulir (Atomic Component) -->
        <x-draft-recovery-alert />

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
                        @click="requestClearService()" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:border-slate-400 bg-slate-50 hover:bg-white text-xs font-bold text-slate-700 shadow-sm transition">
                        <i class="ri-arrow-left-line"></i>
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

                        <form 
                            method="POST" 
                            action="{{ route('tickets.store') }}" 
                            enctype="multipart/form-data" 
                            class="space-y-6"
                            @submit="isSubmitting = true; discardDraft()"
                            @input.debounce.500ms="saveDraft()"
                            @change="saveDraft()"
                        >
                            @csrf
                            <!-- Hidden fields to identify service and subcategory -->
                            <input type="hidden" name="service_key" :value="selectedServiceKey">
                            <input type="hidden" name="subcategory_key" :value="selectedSubcategory">

                            <!-- SUB-KATEGORI SELECTOR -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                        <i class="ri-list-check-2 text-emerald-700"></i>
                                        <span>Pilih Sub Kategori <span x-text="currentService ? currentService.name : ''"></span></span>
                                    </label>
                                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        Wajib Dipilih
                                    </span>
                                </div>

                                <!-- When subcategories are configured (Services 1, 2, 3, 7, 8, 9) -->
                                <template x-if="currentService && currentService.subcategories && Object.keys(currentService.subcategories).length > 0">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                                        <template x-for="(subLabel, subKey) in currentService.subcategories" :key="subKey">
                                            <button 
                                                type="button" 
                                                @click="selectedSubcategory = subKey"
                                                :class="selectedSubcategory === subKey 
                                                    ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' 
                                                    : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                                                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
                                            >
                                                <span x-text="subLabel"></span>
                                                <i x-show="selectedSubcategory === subKey" class="ri-check-line text-sm"></i>
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                <!-- When subcategories are not configured yet (Services 4, 5, 6, 10, 11, 12 - Colleague's part) -->
                                <template x-if="!currentService || !currentService.subcategories || Object.keys(currentService.subcategories).length === 0">
                                    <div class="mt-2 text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-dashed border-slate-300">
                                        <div class="flex items-center gap-2 text-amber-700 font-semibold mb-1">
                                            <i class="ri-information-line text-base"></i>
                                            <span>Sub Kategori Siap Dikonfigurasi</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-relaxed">
                                            Daftar sub kategori untuk layanan ini dapat langsung ditambahkan di file <code class="px-1 py-0.5 bg-white text-slate-800 rounded font-mono text-[10px] border">config/services_gateway.php</code>.
                                        </p>
                                        <div class="mt-2.5">
                                            <input 
                                                type="text" 
                                                name="custom_subcategory" 
                                                placeholder="Ketik rincian kebutuhan / sub kategori..."
                                                class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white"
                                            >
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- ========================================== -->
                            <!-- DYNAMIC FORM SECTIONS FOR SERVICES 1, 2, 3, 7, 8, 9 -->
                            <!-- ========================================== -->

                            <!-- 1. SIPT FIELDS -->
                            <div x-show="selectedServiceKey === 'sipt'" class="space-y-4 pt-2 border-t border-slate-100">
                                
                                <!-- A. Reset Password SIPT -->
                                <template x-if="selectedSubcategory === 'reset_password'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="sipt_username" :value="__('Username SIPT')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="sipt_username" name="sipt_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Masukkan username akun SIPT Anda..." />
                                        </div>
                                        <div>
                                            <x-input-label for="sipt_password" :value="__('Request Password Baru')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="sipt_password" name="sipt_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Masukkan usulan password baru yang diinginkan..." />
                                        </div>
                                    </div>
                                </template>

                                <!-- B. Rollback Sampel SIPT -->
                                <template x-if="selectedSubcategory === 'rollback_sampel'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="sipt_sample_number" :value="__('Nomor Sampel')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="sipt_sample_number" name="sipt_sample_number" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: SMP-2026-X123..." />
                                        </div>
                                        <div>
                                            <x-input-label for="sipt_commodity" :value="__('Komoditi')" class="font-semibold text-xs text-slate-700" />
                                            <select id="sipt_commodity" name="sipt_commodity" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm">
                                                <option value="Pangan">Pangan</option>
                                                <option value="Obat">Obat</option>
                                                <option value="Obat Tradisional">Obat Tradisional</option>
                                                <option value="Kosmetik">Kosmetik</option>
                                                <option value="Suplemen Kesehatan">Suplemen Kesehatan</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                    </div>
                                </template>

                                <!-- C. Perbaikan Data Sampel SIPT -->
                                <template x-if="selectedSubcategory === 'perbaikan_sampel'">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="sipt_sample_code" :value="__('Kode Sampel')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="sipt_sample_code" name="sipt_sample_code" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Kode sampel..." />
                                            </div>
                                            <div>
                                                <x-input-label for="sipt_sample_commodity" :value="__('Komoditi')" class="font-semibold text-xs text-slate-700" />
                                                <select id="sipt_sample_commodity" name="sipt_commodity" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm">
                                                    <option value="Pangan">Pangan</option>
                                                    <option value="Obat">Obat</option>
                                                    <option value="Obat Tradisional">Obat Tradisional</option>
                                                    <option value="Kosmetik">Kosmetik</option>
                                                    <option value="Suplemen Kesehatan">Suplemen Kesehatan</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <x-input-label for="sipt_sample_name" :value="__('Nama Sampel')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="sipt_sample_name" name="sipt_sample_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Nama produk / sampel..." />
                                        </div>
                                        <div>
                                            <x-input-label for="sipt_before_repair" :value="__('Sebelum Perbaikan (Data Lama)')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="sipt_before_repair" name="sipt_before_repair" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Tuliskan data sebelum perbaikan..."></textarea>
                                        </div>
                                        <div>
                                            <x-input-label for="sipt_after_repair" :value="__('Perbaikan (Data Baru)')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="sipt_after_repair" name="sipt_after_repair" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Tuliskan data perbaikan yang benar..."></textarea>
                                        </div>
                                    </div>
                                </template>

                                <!-- D. Cek Posisi Sampel SIPT -->
                                <template x-if="selectedSubcategory === 'cek_posisi_sampel'">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="sipt_pos_sample_code" :value="__('Kode Sampel')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="sipt_pos_sample_code" name="sipt_sample_code" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Kode sampel yang ingin dilacak..." />
                                            </div>
                                            <div>
                                                <x-input-label for="sipt_pos_commodity" :value="__('Komoditi')" class="font-semibold text-xs text-slate-700" />
                                                <select id="sipt_pos_commodity" name="sipt_commodity" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm">
                                                    <option value="Pangan">Pangan</option>
                                                    <option value="Obat">Obat</option>
                                                    <option value="Obat Tradisional">Obat Tradisional</option>
                                                    <option value="Kosmetik">Kosmetik</option>
                                                    <option value="Suplemen Kesehatan">Suplemen Kesehatan</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <x-input-label for="sipt_pos_sample_name" :value="__('Nama Sampel')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="sipt_pos_sample_name" name="sipt_sample_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Nama sampel yang dicari..." />
                                        </div>
                                    </div>
                                </template>

                                <!-- E. Lainnya SIPT -->
                                <template x-if="selectedSubcategory === 'lainnya'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="sipt_description" :value="__('Deskripsi Kendala SIPT')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="sipt_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Jelaskan kendala SIPT yang Anda alami..."></textarea>
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <!-- 2. SRIKANDI FIELDS -->
                            <div x-show="selectedServiceKey === 'srikandi'" class="space-y-4 pt-2 border-t border-slate-100">
                                
                                <!-- A. Reset Password Srikandi -->
                                <template x-if="selectedSubcategory === 'reset_password'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="srikandi_username" :value="__('Username Srikandi')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="srikandi_username" name="srikandi_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Username akun Srikandi Anda..." />
                                        </div>
                                        <div>
                                            <x-input-label for="srikandi_password" :value="__('Request Password Baru')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="srikandi_password" name="srikandi_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Password baru yang diinginkan..." />
                                        </div>
                                    </div>
                                </template>

                                <!-- B. Pembuatan Akun Baru Srikandi -->
                                <template x-if="selectedSubcategory === 'buat_akun'">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="srikandi_fullname" :value="__('Nama Lengkap')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="srikandi_fullname" name="srikandi_fullname" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Nama lengkap pegawai..." />
                                            </div>
                                            <div>
                                                <x-input-label for="srikandi_nik" :value="__('NIK (Nomor Induk Kependudukan)')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="srikandi_nik" name="srikandi_nik" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="16 digit NIK..." />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="srikandi_phone" :value="__('No. Telepon / WhatsApp')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="srikandi_phone" name="srikandi_phone" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="08xxxxxxxxxx..." />
                                            </div>
                                            <div>
                                                <x-input-label for="srikandi_email" :value="__('Alamat Email')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="srikandi_email" name="srikandi_email" type="email" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="nama@bbpom.go.id..." />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="srikandi_req_username" :value="__('Request Username Srikandi')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="srikandi_req_username" name="srikandi_req_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Usulan username..." />
                                            </div>
                                            <div>
                                                <x-input-label for="srikandi_req_password" :value="__('Request Password Awal')" class="font-semibold text-xs text-slate-700" />
                                                <x-text-input id="srikandi_req_password" name="srikandi_req_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Usulan password..." />
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- C. Penonaktifan Akun Srikandi -->
                                <template x-if="selectedSubcategory === 'nonaktif_akun'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="srikandi_nonaktif_user" :value="__('Username Srikandi')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="srikandi_nonaktif_user" name="srikandi_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Username akun yang akan dinonaktifkan..." />
                                        </div>
                                        <div>
                                            <x-input-label for="srikandi_disable_reason" :value="__('Alasan Dinonaktifkan')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="srikandi_disable_reason" name="srikandi_disable_reason" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Contoh: Pensiun, mutasi unit kerja luar, dll..."></textarea>
                                        </div>
                                    </div>
                                </template>

                                <!-- D. Reset MFA Srikandi -->
                                <template x-if="selectedSubcategory === 'reset_mfa'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="srikandi_mfa_username" :value="__('Username Srikandi')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="srikandi_mfa_username" name="srikandi_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Username Srikandi yang terkendala MFA..." />
                                        </div>
                                        <p class="text-[11px] text-slate-500">Teknisi IT BBPOM akan me-reset otentikasi ganda (MFA) sehingga Anda dapat memindai ulang barcode autentikator.</p>
                                    </div>
                                </template>

                                <!-- E. Lainnya Srikandi -->
                                <template x-if="selectedSubcategory === 'lainnya'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="srikandi_description" :value="__('Deskripsi Kendala Srikandi')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="srikandi_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Jelaskan kendala Srikandi yang dihadapi..."></textarea>
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <!-- 3. EMAIL CORPORATE FIELDS -->
                            <div x-show="selectedServiceKey === 'email_corporate'" class="space-y-4 pt-2 border-t border-slate-100">
                                
                                <!-- A. Reset Password Email -->
                                <template x-if="selectedSubcategory === 'reset_password'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="email_nip" :value="__('NIP (Nomor Induk Pegawai)')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="email_nip" name="email_nip" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="NIP pegawai..." />
                                        </div>
                                        <div>
                                            <x-input-label for="email_new_password" :value="__('Request Password Baru')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="email_new_password" name="email_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Ketik usulan password baru..." />
                                        </div>
                                    </div>
                                </template>

                                <!-- B. Lainnya Email Corporate -->
                                <template x-if="selectedSubcategory === 'lainnya'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="email_description" :value="__('Deskripsi Kendala Email')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="email_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Jelaskan masalah email dinas (misal kuota penuh, gagal kirim webmail)..."></textarea>
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <!-- 7. INTERNET & JARINGAN FIELDS -->
                            <div x-show="selectedServiceKey === 'internet'" class="space-y-4 pt-2 border-t border-slate-100">
                                
                                <!-- A. Keluhan Internet Lemot / Mati -->
                                <template x-if="selectedSubcategory === 'lemot_mati'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="internet_ap_location" :value="__('Lokasi Titik Access Point / Ruangan')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="internet_ap_location" name="ap_location" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: Ruang Lab Kimia Lt. 2 / Meja TU..." />
                                        </div>
                                        <div>
                                            <x-input-label for="internet_complaint" :value="__('Rincian Keluhan')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="internet_complaint" name="complaint_details" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Jelaskan kondisi jaringan (misal putus-nyambung, tidak ada sinyal WiFi)..."></textarea>
                                        </div>
                                    </div>
                                </template>

                                <!-- B. Request Penambahan Access Point -->
                                <template x-if="selectedSubcategory === 'tambah_ap'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="internet_req_ap_location" :value="__('Request Lokasi Titik Access Point')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="internet_req_ap_location" name="ap_location" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: Sudut Ruang Pertemuan Bawah..." />
                                        </div>
                                        <div>
                                            <x-input-label for="internet_req_reason" :value="__('Keluhan / Alasan Kebutuhan')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="internet_req_reason" name="complaint_details" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Alasan penambahan titik AP (misal area blindspot sinyal)..."></textarea>
                                        </div>
                                    </div>
                                </template>

                                <!-- C. Lainnya Internet -->
                                <template x-if="selectedSubcategory === 'lainnya'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="internet_other_desc" :value="__('Deskripsi Kendala Jaringan')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="internet_other_desc" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Jelaskan kebutuhan jaringan atau kabel LAN..."></textarea>
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <!-- 8. SUBSITE FIELDS -->
                            <div x-show="selectedServiceKey === 'subsite'" class="space-y-4 pt-2 border-t border-slate-100">
                                
                                <!-- A. Upload Berita Internal -->
                                <template x-if="selectedSubcategory === 'upload_berita'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="subsite_news_title" :value="__('Judul Berita')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="subsite_news_title" name="news_title" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Judul artikel / berita kegiatan..." />
                                        </div>
                                        <div>
                                            <x-input-label for="subsite_news_content" :value="__('Isi Berita')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="subsite_news_content" name="news_content" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Tuliskan naskah isi berita yang akan ditayangkan..."></textarea>
                                        </div>
                                        <div>
                                            <x-input-label for="subsite_upload_date" :value="__('Request Tanggal Upload')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="subsite_upload_date" name="target_date" type="date" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" />
                                        </div>
                                    </div>
                                </template>

                                <!-- B. Upload Dokumen Subsite -->
                                <template x-if="selectedSubcategory === 'upload_dokumen'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="subsite_doc_name" :value="__('Nama Dokumen (untuk tampilan subsite)')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="subsite_doc_name" name="doc_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Judul dokumen resmi yang akan ditampilkan..." />
                                        </div>
                                        <div>
                                            <x-input-label for="subsite_doc_location" :value="__('Lokasi Upload / Halaman')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="subsite_doc_location" name="doc_location" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Misal: Menu Informasi Publik / Standar Layanan..." />
                                        </div>
                                    </div>
                                </template>

                                <!-- C. Hapus Dokumen Subsite -->
                                <template x-if="selectedSubcategory === 'hapus_dokumen'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="subsite_del_title" :value="__('Judul Dokumen yang Akan Dihapus')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="subsite_del_title" name="doc_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Nama dokumen di subsite..." />
                                        </div>
                                        <div>
                                            <x-input-label for="subsite_del_url" :value="__('Link Dokumen yang Ingin Dihapus')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="subsite_del_url" name="doc_url" type="url" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="https://semarang.pom.go.id/..." />
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <!-- 9. INFOGRAFIS FIELDS -->
                            <div x-show="selectedServiceKey === 'infografis'" class="space-y-4 pt-2 border-t border-slate-100">
                                
                                <template x-if="selectedSubcategory === 'pembuatan_infografis'">
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="infographic_title" :value="__('Judul Infografis')" class="font-semibold text-xs text-slate-700" />
                                            <x-text-input id="infographic_title" name="infographic_title" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Judul / topik media informasi..." />
                                        </div>
                                        <div>
                                            <x-input-label for="infographic_content" :value="__('Isi Materi / Pesan Utama')" class="font-semibold text-xs text-slate-700" />
                                            <textarea id="infographic_content" name="infographic_content" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Tuliskan poin-poin materi, teks yang ingin ditampilkan, atau panduan visual..."></textarea>
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <!-- FALLBACK / GENERAL FIELDS FOR OTHER SERVICES (4, 5, 6, 10, 11, 12) -->
                            <div x-show="!['sipt', 'srikandi', 'email_corporate', 'internet', 'subsite', 'infografis'].includes(selectedServiceKey)" class="space-y-4 pt-2 border-t border-slate-100">
                                <div>
                                    <x-input-label for="general_subject" :value="__('Subjek / Judul Kebutuhan')" class="font-semibold text-xs text-slate-700" />
                                    <x-text-input id="general_subject" name="subject" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Ringkasan kebutuhan layanan..." />
                                </div>
                                <div>
                                    <x-input-label for="general_description" :value="__('Deskripsi Kebutuhan / Kendala')" class="font-semibold text-xs text-slate-700" />
                                    <textarea id="general_description" name="description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Jelaskan kebutuhan Anda secara rinci..."></textarea>
                                </div>
                            </div>

                            <!-- UPLOAD LAMPIRAN / FILE (Universal for all subcategories) -->
                            <div class="pt-2 border-t border-slate-100">
                                <x-input-label for="attachment" :value="__('Upload File Lampiran / Foto / Dokumen Pendukung (Opsional)')" class="font-semibold text-xs text-slate-700" />
                                <span class="text-[11px] text-slate-400 block mb-2">Format: JPG, PNG, PDF, DOCX, ZIP (Maks. 5MB)</span>
                                
                                <div class="mt-1 flex justify-center px-6 pt-4 pb-5 border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl transition bg-slate-50/50">
                                    <div class="space-y-1 text-center">
                                        <i class="ri-upload-cloud-2-line text-3xl text-slate-400"></i>
                                        <div class="flex text-xs text-slate-600 justify-center">
                                            <label for="attachment" class="relative cursor-pointer bg-white px-3 py-1.5 rounded-lg border border-slate-200 font-semibold text-emerald-700 hover:text-emerald-800 shadow-sm transition">
                                                <span>Pilih Berkas Lampiran</span>
                                                <input id="attachment" name="attachment" type="file" class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-[11px] text-slate-400" id="file-chosen-text">Belum ada file yang dipilih</p>
                                    </div>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('attachment')" />
                            </div>

                            <!-- Hidden Category ID for backend -->
                            <input type="hidden" id="category_id" name="category_id" :value="currentService ? currentService.default_category_id : 5">

                            <!-- Form Action Buttons -->
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
                selectedSubcategory: @json(old('subcategory_key', '')),
                services: @json($services),
                groups: @json($groups),
                hasDraft: false,
                draftTimeText: '',
                draftServiceKey: '',
                draftSubcategory: '',
                draftFieldsCount: 0,
                showRestoredToast: false,
                isSubmitting: false,
                showLeaveConfirm: false,
                pendingLeaveTarget: null,

                get currentService() {
                    return this.services[this.selectedServiceKey] || null;
                },

                get draftService() {
                    return this.services[this.draftServiceKey] || null;
                },

                get draftSubcategoryLabel() {
                    if (this.draftService && this.draftService.subcategories && this.draftSubcategory) {
                        return this.draftService.subcategories[this.draftSubcategory] || this.draftSubcategory;
                    }
                    return '';
                },

                selectService(key) {
                    this.selectedServiceKey = key;
                    const s = this.services[key];
                    if (s && s.subcategories && Object.keys(s.subcategories).length > 0) {
                        if (!this.selectedSubcategory || !s.subcategories[this.selectedSubcategory]) {
                            this.selectedSubcategory = Object.keys(s.subcategories)[0];
                        }
                    } else {
                        this.selectedSubcategory = '';
                    }
                },

                clearService() {
                    this.selectedServiceKey = null;
                    this.selectedSubcategory = '';
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

                checkDraft() {
                    try {
                        const raw = localStorage.getItem('bbpom_itsm_ticket_draft');
                        if (raw) {
                            const data = JSON.parse(raw);
                            if (data && data._saved_at) {
                                const savedDate = new Date(data._saved_at);
                                const timeStr = savedDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).replace('.', ':');
                                this.draftTimeText = 'Tersimpan ' + timeStr + ' WIB';
                                this.draftServiceKey = data._service_key || '';
                                this.draftSubcategory = data._subcategory_key || '';

                                let count = 0;
                                for (let k in data) {
                                    if (!k.startsWith('_') && data[k] !== null && data[k] !== undefined && String(data[k]).trim().length > 0) {
                                        count++;
                                    }
                                }
                                this.draftFieldsCount = count;
                                if (count > 0 || this.draftServiceKey) {
                                    this.hasDraft = true;
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Error checking draft:', e);
                    }
                },

                saveDraft() {
                    if (this.isSubmitting) return;
                    const form = document.querySelector('form[action*="tickets"]');
                    if (!form) return;

                    const formData = {};
                    let hasContent = false;

                    for (let el of form.elements) {
                        if (!el.name || el.name === '_token' || el.type === 'file' || el.type === 'submit') continue;
                        if (el.type === 'checkbox' || el.type === 'radio') {
                            if (el.checked) {
                                formData[el.name] = el.value;
                                hasContent = true;
                            }
                        } else if (el.value && el.value.trim().length > 0) {
                            formData[el.name] = el.value;
                            if (el.name !== 'service_key' && el.name !== 'subcategory_key' && el.name !== 'category_id') {
                                hasContent = true;
                            }
                        }
                    }

                    if (this.selectedServiceKey || hasContent) {
                        formData._service_key = this.selectedServiceKey;
                        formData._subcategory_key = this.selectedSubcategory;
                        formData._saved_at = new Date().toISOString();
                        localStorage.setItem('bbpom_itsm_ticket_draft', JSON.stringify(formData));
                    }
                },

                restoreDraft() {
                    try {
                        const raw = localStorage.getItem('bbpom_itsm_ticket_draft');
                        if (!raw) return;
                        const data = JSON.parse(raw);

                        if (data._service_key) {
                            this.selectService(data._service_key);
                        }
                        if (data._subcategory_key) {
                            this.selectedSubcategory = data._subcategory_key;
                        }

                        this.$nextTick(() => {
                            const form = document.querySelector('form[action*="tickets"]');
                            if (form) {
                                for (let key in data) {
                                    if (key.startsWith('_')) continue;
                                    const el = form.elements[key];
                                    if (el) {
                                        if (el.type === 'checkbox' || el.type === 'radio') {
                                            el.checked = (el.value === data[key]);
                                        } else {
                                            el.value = data[key];
                                        }
                                        el.dispatchEvent(new Event('input', { bubbles: true }));
                                        el.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                }
                            }
                            this.hasDraft = false;
                            this.showRestoredToast = true;
                            setTimeout(() => {
                                this.showRestoredToast = false;
                            }, 3500);
                        });
                    } catch (e) {
                        console.error('Error restoring draft:', e);
                    }
                },

                discardDraft() {
                    localStorage.removeItem('bbpom_itsm_ticket_draft');
                    this.hasDraft = false;
                    this.draftServiceKey = '';
                    this.draftSubcategory = '';
                    this.draftFieldsCount = 0;
                },

                isDirty() {
                    if (this.isSubmitting) return false;
                    const form = document.querySelector('form[action*="tickets"]');
                    if (!form) return false;

                    for (let el of form.elements) {
                        if (!el.name || el.name === '_token' || el.type === 'file' || el.type === 'submit') continue;
                        if (el.name !== 'service_key' && el.name !== 'subcategory_key' && el.name !== 'category_id' && el.value && el.value.trim().length > 0) {
                            return true;
                        }
                    }
                    return false;
                },

                confirmLeave(target) {
                    if (this.isDirty() && !this.isSubmitting) {
                        this.saveDraft();
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
                    this.saveDraft();
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
                    if (this.selectedServiceKey && (!this.selectedSubcategory || this.selectedSubcategory === '')) {
                        const s = this.services[this.selectedServiceKey];
                        if (s && s.subcategories && Object.keys(s.subcategories).length > 0) {
                            this.selectedSubcategory = Object.keys(s.subcategories)[0];
                        }
                    }

                    this.checkDraft();

                    // Intercept in-app navigation link clicks when form has unsaved content
                    document.addEventListener('click', (e) => {
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

                    // Silently auto-save draft on refresh/navigation without showing the basic native browser leave prompt
                    window.addEventListener('beforeunload', () => {
                        if (this.isDirty() && !this.isSubmitting) {
                            this.saveDraft();
                        }
                    });
                }
            };
        }

        document.getElementById('attachment')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const textElem = document.getElementById('file-chosen-text');
            if (fileName && textElem) {
                textElem.textContent = 'File terpilih: ' + fileName;
                textElem.classList.remove('text-slate-400');
                textElem.classList.add('text-emerald-700', 'font-semibold');
            }
        });
    </script>
</x-app-layout>

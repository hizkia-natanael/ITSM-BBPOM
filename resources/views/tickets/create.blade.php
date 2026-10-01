<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
                        </li>
                        <li>
                            <span class="mx-1 text-gray-400">/</span>
                        </li>
                        <li>
                            <a href="{{ route('tickets.index') }}" class="hover:text-emerald-700">Daftar Pengaduan</a>
                        </li>
                        <li>
                            <span class="mx-1 text-gray-400">/</span>
                        </li>
                        <li class="font-semibold text-emerald-800">App Gateway Layanan</li>
                    </ol>
                </nav>
                <h2 class="font-bold text-xl text-gray-900 leading-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    Katalog Layanan & Pengaduan TIK
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Balai Besar Pengawas Obat dan Makanan di Semarang
                </p>
            </div>
            <div>
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
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
            services: {{ json_encode($services) }},
            groups: {{ json_encode($groups) }},
            get currentService() {
                return this.selectedServiceKey && this.services[this.selectedServiceKey] 
                    ? this.services[this.selectedServiceKey] 
                    : null;
            },
            selectService(key) {
                this.selectedServiceKey = key;
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
                <!-- Ambient decorative elements -->
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
                            placeholder="Cari layanan, misal: zoom, srikandi, email, internet, lisensi office, windows..."
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
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-slate-100 text-slate-600 group-hover:bg-emerald-50 group-hover:text-emerald-700 transition">
                                    {{ $svc['badge'] }}
                                </span>

                                <div class="w-10 h-10 rounded-xl bg-slate-100 group-hover:bg-emerald-600 group-hover:text-white text-slate-700 flex items-center justify-center transition shadow-sm">
                                    <i class="{{ $svc['icon'] }} text-xl"></i>
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
            <div class="bg-white rounded-2xl border-2 border-emerald-500/40 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white flex items-center justify-center flex-shrink-0 shadow-md">
                        <i :class="currentService ? currentService.icon : 'ri-customer-service-2-line'" class="text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">
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
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:border-slate-400 bg-slate-50 hover:bg-white text-xs font-bold text-slate-700 shadow-sm transition"
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
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
                        
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h3 class="text-base font-bold text-slate-900">Formulir Pengaduan Layanan</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Lengkapi detail permohonan atau kendala di bawah ini agar teknisi TIK BBPOM dapat segera menindaklanjuti.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <!-- Hidden field to track selected service key -->
                            <input type="hidden" name="service_key" :value="selectedServiceKey">

                            <!-- SUB-KATEGORI SECTION (Extensible Hook for User & Colleague) -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <span>Sub Kategori <span x-text="currentService ? currentService.name : ''"></span></span>
                                    </label>
                                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-full">
                                        Kontekstual
                                    </span>
                                </div>

                                <!-- When subcategories array is configured -->
                                <template x-if="currentService && currentService.subcategories && currentService.subcategories.length > 0">
                                    <div class="mt-2 space-y-2">
                                        <select 
                                            name="subcategory" 
                                            id="subcategory"
                                            class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                        >
                                            <option value="">-- Pilih Sub Kategori Spesifik --</option>
                                            <template x-for="sub in currentService.subcategories" :key="sub">
                                                <option :value="sub" x-text="sub"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>

                                <!-- Placeholder notice when subcategories are pending team input -->
                                <template x-if="!currentService || !currentService.subcategories || currentService.subcategories.length === 0">
                                    <div class="mt-2 text-xs text-slate-600 bg-white p-3.5 rounded-xl border border-dashed border-slate-300">
                                        <div class="flex items-center gap-2 text-amber-700 font-semibold mb-1">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Slot Sub Kategori Siap Diisi</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-relaxed">
                                            Daftar sub kategori untuk <strong x-text="currentService ? currentService.name : ''"></strong> dapat langsung ditambahkan di file konfigurasi <code class="px-1 py-0.5 bg-slate-100 text-slate-800 rounded font-mono text-[10px]">config/services_gateway.php</code>.
                                        </p>
                                        <div class="mt-2.5">
                                            <input 
                                                type="text" 
                                                name="custom_subcategory" 
                                                placeholder="Ketik rincian spesifik kendala (opsional)..."
                                                class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-600 focus:ring-emerald-600 bg-slate-50/50"
                                            >
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Subjek / Judul -->
                            <div>
                                <x-input-label for="subject" :value="__('Subjek / Judul Kendala')" class="font-semibold text-xs text-slate-700" />
                                <span class="text-[11px] text-slate-400 block mb-1">Contoh: [Zoom] Permohonan Akun Meeting Sosialisasi Sertifikasi</span>
                                <x-text-input 
                                    id="subject" 
                                    name="subject" 
                                    type="text" 
                                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" 
                                    :value="old('subject')" 
                                    placeholder="Tuliskan ringkasan kendala atau permohonan..."
                                    required 
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('subject')" />
                            </div>

                            <!-- Kategori Pengaduan Database -->
                            <div>
                                <x-input-label for="category_id" :value="__('Kategori Database ITSM')" class="font-semibold text-xs text-slate-700" />
                                <span class="text-[11px] text-slate-400 block mb-1">Kategori utama untuk pelaporan & routing teknisi BBPOM</span>
                                <select 
                                    id="category_id" 
                                    name="category_id" 
                                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                                    required
                                >
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>

                            <!-- Deskripsi Masalah -->
                            <div>
                                <x-input-label for="description" :value="__('Deskripsi Rinci Kebutuhan / Kendala')" class="font-semibold text-xs text-slate-700" />
                                <span class="text-[11px] text-slate-400 block mb-1">
                                    Sertakan informasi ruangan/unit kerja, tanggal/waktu jika terkait acara, atau kronologi pesan error.
                                </span>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="5" 
                                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
                                    placeholder="Jelaskan kebutuhan atau kendala Anda secara lengkap..."
                                    required
                                >{{ old('description') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <!-- Upload Lampiran -->
                            <div>
                                <x-input-label for="attachment" :value="__('Lampiran Bukti / Screenshot / Nota Dinas (Opsional)')" class="font-semibold text-xs text-slate-700" />
                                <span class="text-[11px] text-slate-400 block mb-2">Format: JPG, PNG, PDF, DOCX, ZIP (Maks. 5MB)</span>
                                
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl transition bg-slate-50/50">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-xs text-slate-600 justify-center">
                                            <label for="attachment" class="relative cursor-pointer bg-white px-3 py-1.5 rounded-lg border border-slate-200 font-semibold text-emerald-700 hover:text-emerald-800 shadow-sm transition">
                                                <span>Pilih Berkas</span>
                                                <input id="attachment" name="attachment" type="file" class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-[11px] text-slate-400" id="file-chosen-text">Belum ada file yang dipilih</p>
                                    </div>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                            </div>

                            <!-- Form Action Buttons -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <button 
                                    type="button" 
                                    @click="clearService()" 
                                    class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                                >
                                    &larr; Ganti Layanan
                                </button>
                                
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('tickets.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                        Batal
                                    </a>
                                    <button 
                                        type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:scale-[0.99] text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-700/20 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
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
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
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
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
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
                                <span>Penanganan insiden sistem & jaringan prioritas tinggi.</span>
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

    <!-- Script to handle file input display -->
    <script>
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

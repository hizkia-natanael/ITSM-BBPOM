@props(['services'])

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

            <!-- Right: 3-Step Mini Stepper Workflow Card (Konsep B) -->
            <div class="hidden lg:flex flex-col justify-center lg:col-span-4 shrink-0">
                <div class="rounded-2xl bg-white/[0.08] backdrop-blur-md border border-white/15 p-4 sm:p-4.5 shadow-xl relative overflow-hidden">
                    <div class="flex items-center justify-between pb-2.5 border-b border-white/10 mb-3">
                        <div class="flex items-center gap-1.5">
                            <i class="ri-route-line text-emerald-400 text-sm"></i>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-200">Alur Pengaduan</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            3 Langkah
                        </span>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Langkah 1 -->
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                                1
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-100 text-[11px] leading-tight">Pilih Menu Layanan</p>
                                <p class="text-[10px] text-slate-300/80 mt-0.5">Tentukan jenis kebutuhan atau kendala teknis</p>
                            </div>
                        </div>

                        <!-- Langkah 2 -->
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                                2
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-100 text-[11px] leading-tight">Lengkapi Formulir</p>
                                <p class="text-[10px] text-slate-300/80 mt-0.5">Isi rincian permohonan &amp; lampirkan berkas</p>
                            </div>
                        </div>

                        <!-- Langkah 3 -->
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                                3
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-100 text-[11px] leading-tight">Pantau Tindak Lanjut</p>
                                <p class="text-[10px] text-slate-300/80 mt-0.5">Komunikasi langsung dengan admin via chat</p>
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

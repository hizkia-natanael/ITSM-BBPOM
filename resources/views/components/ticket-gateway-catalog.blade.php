@props(['services'])

<div x-show="!selectedServiceKey" x-transition.opacity.duration.300ms class="space-y-6">

    <!-- Hero Search & Category Filter Banner (Full-Width) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-5 sm:p-6 lg:p-7 text-white shadow-lg relative overflow-hidden border border-slate-700/50">
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 w-full flex flex-col justify-between">
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
            <p class="mt-1.5 text-xs text-slate-300 leading-relaxed max-w-2xl">
                Cari aplikasi, fasilitas jaringan, atau perbaikan sistem dinas untuk terhubung langsung dengan formulir permohonan.
            </p>

            <!-- Instant Search Bar -->
            <div class="mt-4 relative max-w-4xl">
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
    </div>

    <!-- 12 Service Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
        @foreach($services as $key => $svc)
            <x-ticket-gateway-card :svc-key="$key" :svc="$svc" />
        @endforeach
    </div>

    <!-- Zero Search Results State -->
    <x-ticket-gateway-empty-state />

</div>

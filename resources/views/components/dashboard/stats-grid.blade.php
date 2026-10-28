@props(['stats'])

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-5">
    <!-- Card 1: Total -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-400/80 transition-all flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-slate-500">Total Pengaduan</p>
            <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1 tabular-nums">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Keseluruhan tiket dibuat</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
            <i class="ri-file-list-3-line text-2xl"></i>
        </div>
    </div>

    <!-- Card 2: Pending (Amber) -->
    <div class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-sm hover:border-amber-400 transition-all flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-amber-700">Tiket Pending</p>
            <p class="text-2xl sm:text-3xl font-bold text-amber-600 mt-1 tabular-nums">{{ $stats['pending'] }}</p>
            <p class="text-[11px] text-amber-600/70 mt-1">Menunggu respon tim IT</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
            <i class="ri-time-line text-2xl"></i>
        </div>
    </div>

    <!-- Card 3: Diproses (Blue) -->
    <div class="bg-white p-5 rounded-2xl border border-blue-200/80 shadow-sm hover:border-blue-400 transition-all flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-blue-700">Sedang Diproses</p>
            <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-1 tabular-nums">{{ $stats['diproses'] }}</p>
            <p class="text-[11px] text-blue-600/70 mt-1">Dalam penanganan teknisi</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
            <i class="ri-tools-line text-2xl"></i>
        </div>
    </div>

    <!-- Card 4: Selesai (Emerald) -->
    <div class="bg-white p-5 rounded-2xl border border-emerald-200/80 shadow-sm hover:border-emerald-400 transition-all flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-emerald-700">Tiket Selesai</p>
            <p class="text-2xl sm:text-3xl font-bold text-emerald-600 mt-1 tabular-nums">{{ $stats['selesai'] }}</p>
            <p class="text-[11px] text-emerald-600/70 mt-1">Telah terselesaikan</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <i class="ri-checkbox-circle-line text-2xl"></i>
        </div>
    </div>

    <!-- Card 5: Total Pengunjung (Database Persistent Counter) -->
    <x-ui.online-visitor-counter variant="stat" />
</div>

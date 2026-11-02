@props([
    'ticket',
    'status',
    'updatedAtFormatted',
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
    <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
            <i class="ri-pulse-line text-emerald-600"></i>
            <span>ELITE Live Tracking Lifecycle</span>
        </div>
        <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2 mt-1">
            <span>Alur Eksekusi Penanganan Pengaduan</span>
        </h3>
    </div>
    <div class="text-left sm:text-right">
        <span class="text-[11px] text-slate-400 block font-medium">Terakhir Diperbarui</span>
        <span class="text-xs font-bold text-slate-800" x-text="updatedAt">
            {{ $updatedAtFormatted }}
        </span>
    </div>
</div>

{{-- Alert status ditolak --}}
<div x-show="isDitolak()" x-cloak class="mb-6 rounded-2xl bg-rose-50/90 border border-rose-200/80 p-5 flex items-start gap-3.5 shadow-xs">
    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
        <i class="ri-close-circle-fill text-2xl"></i>
    </div>
    <div class="flex-1">
        <h4 class="font-bold text-rose-900 text-sm">Pengaduan Berstatus Ditolak</h4>
        <p class="text-xs text-rose-700 mt-1 leading-relaxed">
            Permohonan layanan ini tidak dapat dilanjutkan oleh tim IT BBPOM di Semarang. Alasan serta petunjuk lebih lanjut dapat Anda cermati pada kolom riwayat tanggapan petugas di bawah.
        </p>
    </div>
</div>

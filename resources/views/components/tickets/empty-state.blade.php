<div class="py-16 px-4 text-center">
    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center mb-3">
        <i class="ri-inbox-archive-line text-3xl"></i>
    </div>
    <h4 class="font-bold text-slate-900 text-sm sm:text-base">Tidak Ditemukan Pengaduan</h4>
    <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mt-1">
        @if(request()->hasAny(['search', 'category_id', 'status']))
            Tidak ada tiket yang sesuai dengan filter atau kata kunci yang Anda masukkan. Coba sesuaikan kata kunci pencarian.
        @else
            Anda belum memiliki riwayat pengaduan kendala TI.
        @endif
    </p>
    <div class="mt-4 flex justify-center gap-2">
        @if(request()->hasAny(['search', 'category_id', 'status']))
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                Hapus Filter
            </a>
        @endif
        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-sm transition">
            <i class="ri-add-line text-base"></i>
            <span>Buat Pengaduan Baru</span>
        </a>
    </div>
</div>

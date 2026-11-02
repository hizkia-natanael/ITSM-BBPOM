@props(['ticket'])

<div class="hidden print:block mb-8 pb-4 border-b-2 border-slate-900">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="h-16 w-auto object-contain">
            <div>
                <h2 class="text-xs uppercase tracking-widest font-semibold text-slate-600">Badan Pengawas Obat dan Makanan Republik Indonesia</h2>
                <h1 class="text-lg font-bold uppercase tracking-wide text-slate-900">Balai Besar Pengawas Obat dan Makanan di Semarang</h1>
                <p class="text-[11px] text-slate-600">Jl. Sukun Raya No.41A, Srondol Wetan, Kec. Banyumanik, Kota Semarang, Jawa Tengah 50263</p>
            </div>
        </div>
        <div class="text-right">
            <div class="font-mono font-bold text-base border border-slate-900 px-3 py-1 bg-slate-50 inline-block">
                {{ $ticket->ticket_number }}
            </div>
            <div class="text-[10px] text-slate-500 mt-1 uppercase font-semibold">Lembar Bukti Layanan TIK</div>
        </div>
    </div>
    <div class="mt-4 pt-2 border-t border-slate-300 text-center font-bold text-sm tracking-wider uppercase text-slate-800">
        Tanda Terima Pendaftaran Pengaduan &amp; Permohonan Layanan TIK
    </div>
</div>

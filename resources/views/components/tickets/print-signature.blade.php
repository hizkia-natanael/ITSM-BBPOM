@props(['ticket'])

<div class="hidden print:block mt-12 pt-8 border-t border-slate-300">
    <div class="grid grid-cols-2 gap-8 text-center text-xs">
        <div>
            <p class="text-slate-600 mb-1">Pemohon Layanan / Pengadu,</p>
            <p class="text-[10px] text-slate-400 mb-16">Tanggal: {{ now()->format('d/m/Y') }}</p>
            <p class="font-bold underline text-slate-900">{{ $ticket->user->name }}</p>
            <p class="text-[11px] text-slate-500">Pegawai BBPOM di Semarang</p>
        </div>
        <div>
            <p class="text-slate-600 mb-1">Petugas Penerima Layanan TIK,</p>
            <p class="text-[10px] text-slate-400 mb-16">Tanggal: {{ now()->format('d/m/Y') }}</p>
            @if($ticket->handledBy)
                <p class="font-bold text-slate-900 underline">{{ $ticket->handledBy->name }}</p>
                <p class="text-[11px] text-slate-500">{{ $ticket->handledBy->nip ? 'NIP. ' . $ticket->handledBy->nip : 'Tim Teknologi Informasi BBPOM' }}</p>
            @else
                <p class="font-bold text-slate-900 underline">( ............................................ )</p>
                <p class="text-[11px] text-slate-500">Tim Teknologi Informasi BBPOM</p>
            @endif
        </div>
    </div>
    <div class="mt-12 text-center text-[10px] text-slate-400 italic">
        Dokumen ini dicetak secara otomatis melalui Sistem Manajemen Layanan Teknologi Informasi (ITSM) BBPOM di Semarang pada {{ now()->format('d/m/Y H:i') }} WIB.
    </div>
</div>

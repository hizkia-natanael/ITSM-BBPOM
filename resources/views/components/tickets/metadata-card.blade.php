@props(['ticket'])

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
            <i class="ri-information-line text-emerald-700"></i>
            Ringkasan Tiket
        </h4>
        <span class="text-[10px] font-mono text-slate-400">#{{ $ticket->id }}</span>
    </div>
    
    <div class="space-y-3.5 text-xs">
        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
            <span class="text-slate-500 flex items-center gap-1.5">
                <i class="ri-barcode-line text-slate-400"></i>
                Nomor Tiket:
            </span>
            <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">
                {{ $ticket->ticket_number }}
            </span>
        </div>

        <div class="flex items-center justify-between py-1.5 border-b border-slate-100/80 gap-2">
            <span class="text-slate-500 flex items-center gap-1.5 shrink-0">
                <i class="ri-pulse-line text-slate-400"></i>
                Status Saat Ini:
            </span>
            <x-ticket-status-badge :status="$ticket->status" :handler="$ticket->handledBy" class="text-[11px] sm:text-xs py-0.5" />
        </div>

        <div class="flex items-center justify-between py-1.5 border-b border-slate-100/80 gap-2">
            <span class="text-slate-500 flex items-center gap-1.5 shrink-0">
                <i class="ri-apps-2-line text-slate-400"></i>
                Kategori Layanan:
            </span>
            <span class="font-semibold text-slate-900 text-right">
                {{ $ticket->service_name ?: $ticket->category->name }}
            </span>
        </div>

        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
            <span class="text-slate-500 flex items-center gap-1.5">
                <i class="ri-calendar-line text-slate-400"></i>
                Waktu Pengajuan:
            </span>
            <span class="font-medium text-slate-800 text-right">
                {{ $ticket->created_at->format('d/m/Y H:i') }} WIB
            </span>
        </div>

        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
            <span class="text-slate-500 flex items-center gap-1.5">
                <i class="ri-history-line text-slate-400"></i>
                Pembaruan Terakhir:
            </span>
            <span class="font-medium text-slate-700 text-right">
                {{ $ticket->updated_at->diffForHumans() }}
            </span>
        </div>

        <div class="flex items-center justify-between py-1">
            <span class="text-slate-500 flex items-center gap-1.5">
                <i class="ri-time-line text-slate-400"></i>
                Target Respon (SLA):
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                1x24 Jam Kerja
            </span>
        </div>
    </div>
</div>

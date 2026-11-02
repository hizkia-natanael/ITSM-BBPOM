@props(['ticket'])

@php
    $status = $ticket->status;
    $isDitolak = $status === 'ditolak';

    $acceptedActor = $ticket->acceptedBy ?? ($ticket->events->where('status', 'diterima')->first()?->actor ?? null);
    $acceptedAt = $ticket->accepted_at ?? ($ticket->events->where('status', 'diterima')->first()?->created_at ?? null);

    $processedActor = $ticket->processedBy ?? ($ticket->events->where('status', 'diproses')->first()?->actor ?? null);
    $processedAt = $ticket->processed_at ?? ($ticket->events->where('status', 'diproses')->first()?->created_at ?? null);

    $resolvedActor = $ticket->resolvedBy ?? ($ticket->events->whereIn('status', ['selesai', 'ditolak'])->first()?->actor ?? null);
    $resolvedAt = $ticket->resolved_at ?? ($ticket->events->whereIn('status', ['selesai', 'ditolak'])->first()?->created_at ?? null);

    $progressPercent = match($status) {
        'selesai' => 100,
        'diproses' => 66.66,
        'diterima' => 33.33,
        default => 0,
    };
@endphp

<div 
    x-data="eliteTicketFlowTracker({
        ticketId: {{ $ticket->id }},
        initialStatus: '{{ $status }}',
        initialUpdatedAt: '{{ $ticket->updated_at->translatedFormat('d M Y, H:i') }} WIB'
    })"
    {{ $attributes->merge(['class' => 'bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-7 print:hidden']) }}
>
    <x-tickets.tracker.tracker-header 
        :ticket="$ticket" 
        :status="$status" 
        :updated-at-formatted="$ticket->updated_at->translatedFormat('d M Y, H:i') . ' WIB'" 
    />

    <!-- 4-Stage Horizontal Flow Matrix -->
    <div x-show="!isDitolak()" class="relative py-2">
        <!-- Connecting Track Line -->
        <div class="hidden sm:block absolute top-6 left-[12.5%] right-[12.5%] -translate-y-1/2 h-1 z-0 overflow-hidden rounded-full">
            <div class="w-full h-full bg-slate-200/80"></div>
            <div 
                class="absolute top-0 left-0 h-full bg-emerald-600 transition-all duration-700 ease-out"
                :style="`width: ${progressPercent()}%`"
                style="width: {{ $progressPercent }}%;"
            ></div>
        </div>

        <!-- 4 Grid Columns for Each Milestone -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative z-10">
            <!-- Tahap 1: Pengaduan Masuk -->
            <x-tickets.tracker.tracker-stage-item
                :step-number="1"
                title="Pengaduan Masuk"
                subtitle="Tercatat di sistem antrean ELITE"
                icon="ri-file-text-line"
                :actor-name="$ticket->user?->name ?? 'Pemohon'"
                actor-role="Pelapor Layanan"
                :timestamp="$ticket->created_at->format('d/m/Y H:i') . ' WIB'"
                :is-done="true"
                :is-active="$status === 'pending'"
            />

            <!-- Tahap 2: Diterima Admin -->
            <x-tickets.tracker.tracker-stage-item
                :step-number="2"
                title="Diterima Admin"
                subtitle="Verifikasi & persetujuan aduan"
                icon="ri-shield-check-line"
                :actor-name="$acceptedActor?->name ?? ($status !== 'pending' ? ($ticket->handledBy?->name ?? 'Admin IT') : null)"
                actor-role="Petugas Penerima"
                :timestamp="$acceptedAt ? $acceptedAt->format('d/m/Y H:i') . ' WIB' : null"
                :is-done="in_array($status, ['diterima', 'diproses', 'selesai'])"
                :is-active="$status === 'diterima'"
            />

            <!-- Tahap 3: Sedang Diproses -->
            <x-tickets.tracker.tracker-stage-item
                :step-number="3"
                title="Sedang Diproses"
                subtitle="Pengerjaan teknis kendala"
                icon="ri-tools-line"
                :actor-name="$processedActor?->name ?? (in_array($status, ['diproses', 'selesai']) ? ($ticket->handledBy?->name ?? 'Teknisi IT') : null)"
                actor-role="Teknisi Pelaksana"
                :timestamp="$processedAt ? $processedAt->format('d/m/Y H:i') . ' WIB' : null"
                :is-done="in_array($status, ['diproses', 'selesai'])"
                :is-active="$status === 'diproses'"
            />

            <!-- Tahap 4: Tuntas Selesai -->
            <x-tickets.tracker.tracker-stage-item
                :step-number="4"
                title="{{ $isDitolak ? 'Aduan Ditolak' : 'Tuntas Selesai' }}"
                subtitle="Kendala berhasil dituntaskan"
                icon="{{ $isDitolak ? 'ri-close-circle-line' : 'ri-checkbox-circle-line' }}"
                :actor-name="$resolvedActor?->name ?? (in_array($status, ['selesai', 'ditolak']) ? ($ticket->handledBy?->name ?? 'Admin IT') : null)"
                actor-role="{{ $isDitolak ? 'Penolak Aduan' : 'Petugas Penyelesai' }}"
                :timestamp="$resolvedAt ? $resolvedAt->format('d/m/Y H:i') . ' WIB' : null"
                :is-done="in_array($status, ['selesai', 'ditolak'])"
                :is-active="in_array($status, ['selesai', 'ditolak'])"
                :is-rejected="$isDitolak"
            />
        </div>
    </div>
</div>

<x-tickets.tracker.tracker-script />

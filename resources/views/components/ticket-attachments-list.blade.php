@props([
    'ticket',
    'showTopBorder' => true,
])

@php
    $attachments = $ticket->attachments;
    $hasMultiple = $attachments->isNotEmpty();
    $legacyPath = $ticket->attachment ?: (
        $ticket->bsreDetail?->file_lampiran
        ?: ($ticket->zoomDetail?->file_lampiran
        ?: ($ticket->kegiatanDetail?->file_lampiran
        ?: ($ticket->windowsDetail?->file_lampiran
        ?: ($ticket->officeDetail?->file_lampiran
        ?: $ticket->lainnyaDetail?->file_lampiran))))
    );
    $hasLegacy = !empty($legacyPath);

    $hasAnyImage = false;
    if ($hasMultiple) {
        $hasAnyImage = $attachments->contains(fn ($a) => (bool) $a->is_image);
    } elseif ($hasLegacy) {
        $hasAnyImage = in_array(strtolower(pathinfo($legacyPath, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'gif', 'webp']);
    }
@endphp

@if($hasMultiple || $hasLegacy)
    <div 
        class="bbpom-att-wrapper {{ $showTopBorder ? 'mt-8 pt-6 border-t border-slate-100' : 'mt-5 pt-4 border-t border-dashed border-slate-200' }}" 
        x-data="bbpomTicketAttachmentsLightbox()"
    >
        <!-- Header -->
        <div class="bbpom-att-header flex items-center justify-between mb-3.5">
            <h4 class="bbpom-att-header-title text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                <i class="ri-attachment-2 text-emerald-700 text-sm"></i>
                <span>Berkas Lampiran Pendukung</span>
                <span class="bbpom-att-header-badge px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                    {{ $hasMultiple ? $attachments->count() . ' Berkas' : '1 Berkas' }}
                </span>
            </h4>
        </div>

        @if($hasMultiple)
            @include('components.ticket-attachments.card-multiple')
        @elseif($hasLegacy)
            @include('components.ticket-attachments.card-legacy')
        @endif

        @include('components.ticket-attachments.lightbox')
    </div>
@endif

@include('components.ticket-attachments.styles')

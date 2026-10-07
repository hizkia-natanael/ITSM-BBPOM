@php
    $ticket = $getRecord();
    if ($ticket) {
        $ticket->loadMissing([
            'attachments',
            'bsreDetail',
            'zoomDetail',
            'kegiatanDetail',
            'windowsDetail',
            'officeDetail',
            'lainnyaDetail',
        ]);
    }
@endphp

@if($ticket)
    <div class="w-full bbpom-admin-attachments-wrapper">
        <x-ticket-attachments-list :ticket="$ticket" :show-top-border="false" />
    </div>
@endif

@props(['ticket'])

@php
    $ticket->loadMissing(['responses.user', 'responses.attachments', 'responses.replyTo.user', 'responses.replyTo.attachments', 'handledBy', 'user']);
    $responses = $ticket->responses;
    $lastMessageId = (int) ($responses->max('id') ?? 0);
    $initialIds = $responses->pluck('id')->values()->all();
@endphp

@include('components.ticket-chat.script')
@include('components.ticket-chat.styles')

<div 
    x-data="bbpomTicketConversation({ ticketId: {{ $ticket->id }}, replyUrl: '{{ route('tickets.reply', $ticket) }}', syncUrl: '{{ route('tickets.chat-sync', $ticket) }}', csrfToken: '{{ csrf_token() }}', lastMessageId: {{ $lastMessageId }}, initialIds: {{ json_encode($initialIds) }} })"
    class="bbpom-conversation-card bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 mb-6" 
    style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-family: inherit;"
>
    <!-- Header Percakapan -->
    <div class="bbpom-chat-header border-b border-slate-100 pb-4 mb-4 flex items-center justify-between" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem; margin-bottom: 1rem;">
        <div class="bbpom-chat-header-left flex items-center gap-2.5" style="display: flex; align-items: center; gap: 0.625rem;">
            <i class="ri-discuss-line text-emerald-600 text-2xl" style="color: #059669; font-size: 1.5rem; line-height: 1;"></i>
            <div class="bbpom-chat-header-title-group flex items-center gap-2 flex-wrap" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <h3 class="text-base font-bold text-slate-900 leading-tight" style="color: #0f172a; font-size: 1rem; font-weight: 700; margin: 0; line-height: 1.25;">
                    Percakapan Diskusi Tiket
                </h3>
                @if($ticket->status === 'selesai')
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200/60" style="background-color: #d1fae5; color: #065f46; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center;">Selesai</span>
                @elseif($ticket->status === 'diproses')
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200/60" style="background-color: #dbeafe; color: #1e40af; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center;">Diproses</span>
                @elseif($ticket->status === 'ditolak')
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800 border border-rose-200/60" style="background-color: #ffe4e6; color: #9f1239; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #fecdd3; display: inline-flex; align-items: center;">Ditolak</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200/60" style="background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #fde68a; display: inline-flex; align-items: center;">Pending</span>
                @endif
            </div>
        </div>
        <span class="bbpom-chat-header-count px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/60" style="background-color: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center;">
            {{ $responses->count() }} Pesan
        </span>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center gap-2" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 0.75rem; padding: 0.75rem 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem;">
            <i class="ri-checkbox-circle-fill text-emerald-600 text-base" style="color: #059669; font-size: 1.125rem;"></i>
            <span style="font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    @if($ticket->user_id === auth()->id())
        <x-push-optin-card />
    @endif

    <!-- Stream Kontainer Obrolan -->
    <div 
        x-ref="streamContainer"
        class="bbpom-chat-stream bg-slate-50/70 rounded-2xl p-3.5 sm:p-5 border border-slate-200/70 min-h-[320px] max-h-[540px] overflow-y-auto space-y-2.5 scroll-smooth"
        style="background-color: rgba(248, 250, 252, 0.75); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 1rem; padding: 1.25rem; min-height: 320px; max-height: 540px; overflow-y: auto;"
    >
        @php
            $ticketDateKey = $ticket->created_at->format('Y-m-d');
            $grouped = $responses->groupBy(fn ($item) => $item->created_at->format('Y-m-d'));
            $allDateKeys = $grouped->keys()->all();
            if (!in_array($ticketDateKey, $allDateKeys)) {
                array_unshift($allDateKeys, $ticketDateKey);
            }
        @endphp

        @foreach($allDateKeys as $dateKey)
            @php
                $today = now()->format('Y-m-d');
                $yesterday = now()->subDay()->format('Y-m-d');
                $label = $dateKey === $today ? 'Hari Ini' : ($dateKey === $yesterday ? 'Kemarin' : \Carbon\Carbon::parse($dateKey)->translatedFormat('d F Y'));
            @endphp

            <div class="flex justify-center my-2.5" style="display: flex; justify-content: center; margin: 0.625rem 0;">
                <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-slate-200/80 text-slate-500 shadow-2xs" style="background-color: #ffffff; border: 1px solid rgba(226, 232, 240, 0.9); color: #64748b; font-size: 11px; font-weight: 500; padding: 2px 10px; border-radius: 9999px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    {{ $label }}
                </span>
            </div>

            @if($dateKey === $ticketDateKey)
                <div class="flex items-end justify-start gap-2 max-w-[85%] sm:max-w-[70%]" style="display: flex; align-items: flex-end; justify-content: flex-start; gap: 0.5rem; max-width: 85%; margin-bottom: 0.625rem;">
                    <div class="w-7 h-7 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background-color: #d1fae5; border: 1px solid #86efac; color: #065f46; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                        {{ $ticket->handledBy ? strtoupper(substr($ticket->handledBy->name, 0, 2)) : 'IT' }}
                    </div>
                    <div class="bg-white rounded-2xl rounded-bl-xs px-3.5 py-2.5 border border-slate-200/80 shadow-2xs text-left" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 1rem 0.25rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                        <div class="flex items-center justify-start gap-1.5 mb-1" style="display: flex; align-items: center; justify-content: flex-start; gap: 0.375rem;">
                            <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">Helpdesk TIK</span>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-blue-100 text-blue-800 border border-blue-200/60" style="background-color: #dbeafe; color: #1e40af; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px;">
                                {{ $ticket->handledBy ? $ticket->handledBy->name : 'Sistem' }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0;">
                            Halo Bapak/Ibu <strong>{{ $ticket->user->name }}</strong>, pengaduan Anda terkait <em>"{{ $ticket->subject }}"</em> telah tercatat di sistem Helpdesk TIK BBPOM. Tim kami akan segera meninjau dan merespon pengaduan ini. Silakan sampaikan pesan tambahan atau bagikan file bila diperlukan di kolom chat ini.
                        </p>
                        <div class="text-[10px] text-slate-400 text-right mt-1 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;">
                            {{ $ticket->created_at->format('H:i') }} WIB
                        </div>
                    </div>
                </div>
            @endif

            @if($grouped->has($dateKey))
                @include('components.ticket-chat.message-item')
            @endif
        @endforeach

        @include('components.ticket-chat.dynamic-messages')
    </div>

    @include('components.ticket-chat.composer')
</div>

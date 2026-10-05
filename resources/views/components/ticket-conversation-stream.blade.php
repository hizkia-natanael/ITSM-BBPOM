@props([
    'ticket',
])

<div 
    x-data="bbpomTicketConversation()"
    class="bbpom-conversation-card bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 mb-6" 
    style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-family: inherit;"
>
    <!-- WhatsApp Header -->
    <div class="bbpom-chat-header border-b border-slate-100 pb-4 mb-4 flex items-center justify-between" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem; margin-bottom: 1rem;">
        <div class="bbpom-chat-header-left flex items-center gap-2.5" style="display: flex; align-items: center; gap: 0.625rem;">
            <i class="ri-whatsapp-line text-emerald-600 text-2xl" style="color: #059669; font-size: 1.5rem; line-height: 1;"></i>
            <div class="bbpom-chat-header-title-group flex items-center gap-2 flex-wrap" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <h3 class="text-base font-bold text-slate-900 leading-tight" style="color: #0f172a; font-size: 1rem; font-weight: 700; margin: 0; line-height: 1.25;">
                    Percakapan Diskusi Tiket
                </h3>
                @if($ticket->status === 'selesai')
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200/60" style="background-color: #d1fae5; color: #065f46; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center;">
                        Selesai
                    </span>
                @elseif($ticket->status === 'diproses')
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200/60" style="background-color: #dbeafe; color: #1e40af; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center;">
                        Diproses
                    </span>
                @elseif($ticket->status === 'ditolak')
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800 border border-rose-200/60" style="background-color: #ffe4e6; color: #9f1239; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #fecdd3; display: inline-flex; align-items: center;">
                        Ditolak
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200/60" style="background-color: #fef3c7; color: #92400e; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; border: 1px solid #fde68a; display: inline-flex; align-items: center;">
                        Pending
                    </span>
                @endif
            </div>
        </div>
        <span class="bbpom-chat-header-count px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/60" style="background-color: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center;">
            {{ $ticket->responses->count() }} Pesan
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

    <!-- WhatsApp Chat Stream Container -->
    <div 
        x-ref="streamContainer"
        class="bbpom-chat-stream bg-slate-50/70 rounded-2xl p-3.5 sm:p-5 border border-slate-200/70 min-h-[320px] max-h-[520px] overflow-y-auto space-y-2.5 scroll-smooth"
        style="background-color: rgba(248, 250, 252, 0.75); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 1rem; padding: 1.25rem; min-height: 320px; max-height: 520px; overflow-y: auto;"
    >
        @php
            $ticket->loadMissing(['responses.user', 'handledBy', 'user']);
            $responses = $ticket->responses;
            $ticketDateKey = $ticket->created_at->format('Y-m-d');
            $grouped = $responses->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });

            $allDateKeys = $grouped->keys()->all();
            if (!in_array($ticketDateKey, $allDateKeys)) {
                array_unshift($allDateKeys, $ticketDateKey);
            }
        @endphp

        @foreach($allDateKeys as $dateKey)
            @php
                $today = now()->format('Y-m-d');
                $yesterday = now()->subDay()->format('Y-m-d');
                if ($dateKey === $today) {
                    $label = 'Hari Ini';
                } elseif ($dateKey === $yesterday) {
                    $label = 'Kemarin';
                } else {
                    $label = \Carbon\Carbon::parse($dateKey)->translatedFormat('d F Y');
                }
            @endphp

            <!-- Date Separator Pill (e.g. Kemarin / Hari Ini) -->
            <div class="flex justify-center my-2.5" style="display: flex; justify-content: center; margin: 0.625rem 0;">
                <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-slate-200/80 text-slate-500 shadow-2xs" style="background-color: #ffffff; border: 1px solid rgba(226, 232, 240, 0.9); color: #64748b; font-size: 11px; font-weight: 500; padding: 2px 10px; border-radius: 9999px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    {{ $label }}
                </span>
            </div>

            {{-- Always show Helpdesk TIK Bot greeting under the ticket's creation date --}}
            @if($dateKey === $ticketDateKey)
                <!-- Bot / IT Helpdesk Greeting Bubble (Right Side) -->
                <div class="flex items-end justify-end gap-2 max-w-[85%] sm:max-w-[70%] ml-auto" style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 0.5rem; max-width: 85%; margin-left: auto; margin-bottom: 0.625rem;">
                    <div class="bg-white rounded-2xl rounded-br-xs px-3.5 py-2.5 border border-slate-200 shadow-2xs text-left" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 0.25rem 1rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                        <div class="flex items-center justify-end gap-1.5 mb-1" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.375rem;">
                            <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">
                                Helpdesk TIK
                            </span>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-blue-100 text-blue-800 border border-blue-200/60" style="background-color: #dbeafe; color: #1e40af; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px;">
                                {{ $ticket->handledBy ? $ticket->handledBy->name : 'Sistem' }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0;">
                            Halo Bapak/Ibu <strong>{{ $ticket->user->name }}</strong>, pengaduan Anda terkait <em>"{{ $ticket->subject }}"</em> telah tercatat di sistem Helpdesk TIK BBPOM. Tim kami akan segera meninjau dan merespon pengaduan ini. Silakan sampaikan informasi tambahan di kolom chat ini jika diperlukan.
                        </p>
                        <div class="text-[10px] text-slate-400 text-right mt-1 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;">
                            {{ $ticket->created_at->format('H:i') }} WIB
                        </div>
                    </div>
                    <div class="w-7 h-7 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background-color: #d1fae5; border: 1px solid #86efac; color: #065f46; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                        {{ $ticket->handledBy ? strtoupper(substr($ticket->handledBy->name, 0, 2)) : 'IT' }}
                    </div>
                </div>
            @endif

            @if($grouped->has($dateKey))
                @foreach($grouped->get($dateKey) as $response)
                    @php
                        $isAdmin = $response->user->isAdmin();
                    @endphp

                    @if(!$isAdmin)
                        <!-- Pelapor Bubble (LEFT SIDE) -->
                        <div class="flex items-end gap-2 max-w-[85%] sm:max-w-[70%]" style="display: flex; align-items: flex-end; gap: 0.5rem; max-width: 85%; margin-bottom: 0.625rem;">
                            <!-- Avatar Circle with User Initials -->
                            <div class="w-7 h-7 rounded-full bg-slate-200/90 border border-slate-300 text-slate-700 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background-color: #e2e8f0; border: 1px solid #cbd5e1; color: #334155; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                {{ strtoupper(substr($response->user->name, 0, 2)) }}
                            </div>

                            <!-- Message Bubble -->
                            <div class="bg-slate-100/95 rounded-2xl rounded-bl-xs px-3.5 py-2.5 border border-slate-200/60 shadow-2xs text-left" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 1rem 0.25rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                                <!-- Sender Name & Role Header -->
                                <div class="flex items-center gap-1.5 mb-1" style="display: flex; align-items: center; gap: 0.375rem;">
                                    <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">
                                        {{ $response->user->name }}
                                    </span>
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200/60" style="background-color: #d1fae5; color: #065f46; font-size: 9.5px; font-weight: 600; padding: 1px 6px; border-radius: 4px;">
                                        Pemohon
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                                <div class="text-[10px] text-slate-400 text-right mt-1 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;">
                                    {{ $response->created_at->format('H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Petugas IT Bubble (RIGHT SIDE) -->
                        <div class="flex items-end justify-end gap-2 max-w-[85%] sm:max-w-[70%] ml-auto" style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 0.5rem; max-width: 85%; margin-left: auto; margin-bottom: 0.625rem;">
                            <!-- Message Bubble -->
                            <div class="bg-white rounded-2xl rounded-br-xs px-3.5 py-2.5 border border-slate-200 shadow-2xs text-left" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 0.25rem 1rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                                <!-- Role Pill & Admin Name inside bubble -->
                                <div class="flex items-center justify-end gap-1.5 mb-1" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.375rem;">
                                    <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">
                                        {{ $response->user->name }}
                                    </span>
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-blue-100 text-blue-800 border border-blue-200/60" style="background-color: #dbeafe; color: #1e40af; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px;">
                                        Petugas IT
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                                <div class="text-[10px] text-slate-400 text-right mt-1 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;">
                                    {{ $response->created_at->format('H:i') }} WIB
                                </div>
                            </div>

                            <!-- Avatar Circle IT -->
                            <div class="w-7 h-7 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background-color: #d1fae5; border: 1px solid #86efac; color: #065f46; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                {{ strtoupper(substr($response->user->name, 0, 2)) }}
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>

    <!-- WhatsApp Style Bottom Input Bar -->
    <form 
        x-ref="replyForm"
        id="ticketConversationReplyForm" 
        method="POST" 
        action="{{ route('tickets.reply', $ticket) }}" 
        class="mt-4 pt-3 border-t border-slate-100"
        style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;"
        @submit="onFormSubmit($event)"
    >
        @csrf
        <div class="flex items-center gap-2.5" style="display: flex; align-items: center; gap: 0.625rem;">
            <div class="flex-1 relative" style="flex: 1; position: relative;">
                <textarea 
                    x-ref="replyTextarea"
                    id="ticket_conversation_response_text" 
                    name="response_text" 
                    rows="1" 
                    class="w-full rounded-2xl border-slate-300 py-3 px-4 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs resize-none max-h-32 overflow-hidden"
                    style="width: 100%; border-radius: 1rem; border: 1px solid #cbd5e1; padding: 0.75rem 1rem; font-size: 0.8125rem; color: #1e293b; resize: none; max-height: 128px; overflow-y: hidden; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.03); outline: none;"
                    placeholder="Ketik tanggapan Anda di sini..."
                    required
                    @input="adjustTextareaHeight()"
                    @keydown.enter="handleEnterSend($event)"
                >{{ old('response_text') }}</textarea>
            </div>

            <button 
                type="submit" 
                class="w-11 h-11 rounded-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white flex items-center justify-center transition-all shadow-xs shrink-0 hover:scale-105"
                style="width: 2.75rem; height: 2.75rem; border-radius: 9999px; background-color: #059669; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: none; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"
                title="Kirim Pesan"
                onmouseover="this.style.backgroundColor='#047857'; this.style.transform='scale(1.05)';"
                onmouseout="this.style.backgroundColor='#059669'; this.style.transform='scale(1)';"
            >
                <i class="ri-send-plane-2-fill text-lg" style="font-size: 1.125rem;"></i>
            </button>
        </div>
        @if ($errors->has('response_text'))
            <p class="text-xs text-rose-600 mt-1 font-medium" style="color: #e11d48; font-size: 0.75rem; margin-top: 0.25rem;">
                {{ $errors->first('response_text') }}
            </p>
        @endif
        
        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 px-1" style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-top: 0.5rem; padding: 0 0.25rem;">
            <span>Tekan <strong>Enter</strong> untuk mengirim, <strong>Shift + Enter</strong> untuk baris baru</span>
            <span class="flex items-center gap-1" style="display: flex; align-items: center; gap: 0.25rem;">
                <i class="ri-shield-check-line text-emerald-600" style="color: #059669;"></i>
                Percakapan resmi Helpdesk TIK
            </span>
        </div>
    </form>
</div>

<script>
window.bbpomTicketConversation = function bbpomTicketConversation() {
    return {
        isSubmitting: false,
        init() {
            this.$nextTick(() => {
                this.scrollToBottom();
                this.adjustTextareaHeight();
            });
        },
        scrollToBottom() {
            if (this.$refs.streamContainer) {
                this.$refs.streamContainer.scrollTop = this.$refs.streamContainer.scrollHeight;
            }
        },
        adjustTextareaHeight() {
            const el = this.$refs.replyTextarea;
            if (!el) return;
            el.style.height = 'auto';
            if (el.scrollHeight > 128) {
                el.style.height = '128px';
                el.style.overflowY = 'auto';
            } else {
                el.style.height = el.scrollHeight + 'px';
                el.style.overflowY = 'hidden';
            }
        },
        onFormSubmit(e) {
            const el = this.$refs.replyTextarea;
            if (!el || el.value.trim().length === 0) {
                if (e) e.preventDefault();
                return false;
            }
            if (this.isSubmitting) {
                if (e) e.preventDefault();
                return false;
            }
            this.isSubmitting = true;
            return true;
        },
        handleEnterSend(e) {
            if (e && e.shiftKey) {
                return;
            }
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const el = this.$refs.replyTextarea;
            const form = this.$refs.replyForm;
            if (!el || !form) return;

            const text = el.value.trim();
            if (text.length === 0) return;

            if (this.isSubmitting) return;
            this.isSubmitting = true;

            form.submit();
        }
    };
};
</script>

<style>
.bbpom-conversation-card {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 1rem !important;
    padding: 1.5rem !important;
    margin-bottom: 1.5rem !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    font-family: inherit !important;
}
.bbpom-chat-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    border-bottom: 1px solid #f1f5f9 !important;
    padding-bottom: 1rem !important;
    margin-bottom: 1rem !important;
}
.bbpom-chat-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 0.625rem !important;
}
.bbpom-chat-header-title-group {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    flex-wrap: wrap !important;
}
.bbpom-chat-header-count {
    display: inline-flex !important;
    align-items: center !important;
}
.bbpom-chat-stream {
    background-color: rgba(248, 250, 252, 0.75) !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-radius: 1rem !important;
    padding: 1.25rem !important;
    min-height: 320px !important;
    max-height: 520px !important;
    overflow-y: auto !important;
}
</style>


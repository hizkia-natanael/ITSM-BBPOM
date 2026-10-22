@foreach($grouped->get($dateKey) as $response)
    @php
        $isMe = auth()->check() && (int) $response->user_id === (int) auth()->id();
        $isSenderAdmin = $response->user->isAdmin();
    @endphp

    @if($isMe)
        <div id="bbpom_msg_{{ $response->id }}" class="bbpom-msg-row flex items-end justify-end gap-2 max-w-[90%] sm:max-w-[75%] ml-auto group transition-all" style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 0.5rem; max-width: 90%; margin-left: auto; margin-bottom: 0.625rem;">
            <button type="button" @click="setReply({ id: {{ $response->id }}, userName: '{{ addslashes($response->user->name) }}', text: '{{ addslashes($response->snippet) }}' })" class="bbpom-reply-trigger" title="Balas pesan ini">
                <i class="ri-reply-line text-base"></i>
            </button>

            <div class="bg-emerald-50/90 rounded-2xl rounded-br-xs px-3.5 py-2.5 border border-emerald-200/70 shadow-2xs text-left" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 1rem 1rem 0.25rem 1rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                <div class="flex items-center gap-1.5 mb-1" style="display: flex; align-items: center; gap: 0.375rem;">
                    <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">{{ $response->user->name }}</span>
                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold {{ $isSenderAdmin ? 'bg-blue-100 text-blue-800 border border-blue-200/60' : 'bg-emerald-200/80 text-emerald-900 border border-emerald-300/60' }}" style="font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px;">
                        {{ $isSenderAdmin ? 'Petugas IT (Anda)' : 'Pemohon (Anda)' }}
                    </span>
                </div>

                @if($response->replyTo)
                    <div @click="scrollToMessage({{ $response->reply_to_id }})" class="bbpom-quote-card mb-2 p-2 rounded-xl text-left" style="background-color: rgba(5, 150, 105, 0.12); border-left: 3px solid #059669; border-radius: 0.5rem; padding: 0.375rem 0.625rem; cursor: pointer;" title="Menuju pesan kutipan #{{ $response->reply_to_id }}">
                        <div class="flex items-center gap-1.5 leading-none mb-1" style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem;">
                            <i class="ri-reply-fill text-xs" style="color: #047857; font-size: 0.75rem;"></i>
                            <span class="bbpom-quote-user text-[11px] font-bold" style="font-size: 11px; font-weight: 700; color: #065f46;">{{ $response->replyTo->user ? $response->replyTo->user->name : 'Pengguna' }}</span>
                            <span class="text-[9px] text-slate-500 font-semibold" style="font-size: 9px; color: #64748b;">({{ $response->replyTo->user && $response->replyTo->user->isAdmin() ? 'Petugas IT' : 'Pemohon' }})</span>
                        </div>
                        @if($response->replyTo->attachments->isNotEmpty())
                            @php $parentAtt = $response->replyTo->attachments->first(); @endphp
                            <div class="flex items-center gap-1 text-[11px] text-slate-700 font-medium mb-0.5" style="display: flex; align-items: center; gap: 0.25rem; font-size: 11px;">
                                <i class="{{ $parentAtt->pure_icon_class }}" style="color: {{ $parentAtt->document_hex_color }};"></i>
                                <span class="truncate">{{ $parentAtt->file_name }}</span>
                            </div>
                        @endif
                        <p class="text-[11px] text-slate-600 truncate leading-tight" style="font-size: 11px; color: #475569; margin: 0; line-height: 1.25;">{{ $response->replyTo->snippet }}</p>
                    </div>
                @endif

                @if(!empty(trim($response->response_text)))
                    <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                @endif

                @if($response->attachments->isNotEmpty())
                    <div class="mt-2 space-y-2" style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($response->attachments as $att)
                            @include('components.ticket-chat.attachment-card', ['att' => $att])
                        @endforeach
                    </div>
                @endif

                <div class="text-[10px] text-slate-500 text-right mt-1.5 font-mono flex items-center justify-end gap-1" style="font-size: 10px; color: #64748b; text-align: right; font-family: monospace; display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem;">
                    <span>{{ $response->created_at->format('H:i') }} WIB</span>
                    <i class="ri-check-double-line text-emerald-600 text-xs" style="color: #059669; font-size: 0.75rem;"></i>
                </div>
            </div>

            <div class="w-7 h-7 rounded-full {{ $isSenderAdmin ? 'bg-blue-100 border border-blue-300 text-blue-800' : 'bg-emerald-100 border border-emerald-300 text-emerald-800' }} flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                {{ strtoupper(substr($response->user->name, 0, 2)) }}
            </div>
        </div>
    @else
        <div id="bbpom_msg_{{ $response->id }}" class="bbpom-msg-row flex items-end gap-2 max-w-[90%] sm:max-w-[75%] group transition-all" style="display: flex; align-items: flex-end; gap: 0.5rem; max-width: 90%; margin-bottom: 0.625rem;">
            <div class="w-7 h-7 rounded-full {{ $isSenderAdmin ? 'bg-blue-100 border border-blue-300 text-blue-800' : 'bg-slate-200/90 border border-slate-300 text-slate-700' }} flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                {{ strtoupper(substr($response->user->name, 0, 2)) }}
            </div>

            <div class="bg-white rounded-2xl rounded-bl-xs px-3.5 py-2.5 border border-slate-200/80 shadow-2xs text-left" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 1rem 0.25rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                <div class="flex items-center gap-1.5 mb-1" style="display: flex; align-items: center; gap: 0.375rem;">
                    <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">{{ $response->user->name }}</span>
                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-semibold {{ $isSenderAdmin ? 'bg-blue-100 text-blue-800 border border-blue-200/60' : 'bg-emerald-100 text-emerald-800 border border-emerald-200/60' }}" style="font-size: 9.5px; font-weight: 600; padding: 1px 6px; border-radius: 4px;">
                        {{ $isSenderAdmin ? 'Petugas IT' : 'Pemohon' }}
                    </span>
                </div>

                @if($response->replyTo)
                    <div @click="scrollToMessage({{ $response->reply_to_id }})" class="bbpom-quote-card mb-2 p-2 rounded-xl text-left" style="background-color: #f1f5f9; border-left: 3px solid #059669; border-radius: 0.5rem; padding: 0.375rem 0.625rem; cursor: pointer;" title="Menuju pesan kutipan #{{ $response->reply_to_id }}">
                        <div class="flex items-center gap-1.5 leading-none mb-1" style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem;">
                            <i class="ri-reply-fill text-xs" style="color: #047857; font-size: 0.75rem;"></i>
                            <span class="bbpom-quote-user text-[11px] font-bold" style="font-size: 11px; font-weight: 700; color: #065f46;">{{ $response->replyTo->user ? $response->replyTo->user->name : 'Pengguna' }}</span>
                            <span class="text-[9px] text-slate-500 font-semibold" style="font-size: 9px; color: #64748b;">({{ $response->replyTo->user && $response->replyTo->user->isAdmin() ? 'Petugas IT' : 'Pemohon' }})</span>
                        </div>
                        @if($response->replyTo->attachments->isNotEmpty())
                            @php $parentAtt = $response->replyTo->attachments->first(); @endphp
                            <div class="flex items-center gap-1 text-[11px] text-slate-700 font-medium mb-0.5" style="display: flex; align-items: center; gap: 0.25rem; font-size: 11px;">
                                <i class="{{ $parentAtt->pure_icon_class }}" style="color: {{ $parentAtt->document_hex_color }};"></i>
                                <span class="truncate">{{ $parentAtt->file_name }}</span>
                            </div>
                        @endif
                        <p class="text-[11px] text-slate-600 truncate leading-tight" style="font-size: 11px; color: #475569; margin: 0; line-height: 1.25;">{{ $response->replyTo->snippet }}</p>
                    </div>
                @endif

                @if(!empty(trim($response->response_text)))
                    <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                @endif

                @if($response->attachments->isNotEmpty())
                    <div class="mt-2 space-y-2" style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($response->attachments as $att)
                            @include('components.ticket-chat.attachment-card', ['att' => $att])
                        @endforeach
                    </div>
                @endif

                <div class="text-[10px] text-slate-400 text-right mt-1.5 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;">
                    {{ $response->created_at->format('H:i') }} WIB
                </div>
            </div>

            <button type="button" @click="setReply({ id: {{ $response->id }}, userName: '{{ addslashes($response->user->name) }}', text: '{{ addslashes($response->snippet) }}' })" class="bbpom-reply-trigger" title="Balas pesan ini">
                <i class="ri-reply-line text-base"></i>
            </button>
        </div>
    @endif
@endforeach

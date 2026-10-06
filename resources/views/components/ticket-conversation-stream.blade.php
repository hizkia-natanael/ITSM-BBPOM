@props([
    'ticket',
])

<script>
window.bbpomTicketConversation = function bbpomTicketConversation(config) {
    const cfg = config || {};
    return {
        ticketId: cfg.ticketId || null,
        replyUrl: cfg.replyUrl || '',
        csrfToken: cfg.csrfToken || '',
        isSubmitting: false,
        hasFile: false,
        fileName: '',
        fileSizeFormatted: '',
        fileExtension: '',
        compressionMode: 'compressed',
        errorMessage: '',
        init() {
            this.$nextTick(() => {
                this.scrollToBottom();
                this.adjustTextareaHeight();
            });
        },
        hasFileSelected() {
            return Boolean(this.hasFile);
        },
        isCompressedMode() {
            return this.compressionMode === 'compressed';
        },
        isOriginalMode() {
            return this.compressionMode === 'original';
        },
        hasErrorMessage() {
            return this.errorMessage.length > 0;
        },
        setMode(mode) {
            this.compressionMode = mode;
        },
        handleFileChange(e) {
            this.errorMessage = '';
            const files = e.target.files;
            if (!files || files.length === 0) {
                this.clearFile();
                return;
            }
            const file = files[0];
            if (file.size > 52428800) {
                this.errorMessage = 'Ukuran berkas melebihi batas maksimal 50 MB.';
                this.clearFile();
                return;
            }
            this.hasFile = true;
            this.fileName = file.name;
            const parts = file.name.split('.');
            this.fileExtension = parts.length > 1 ? parts.pop().toLowerCase() : '';
            this.fileSizeFormatted = this.formatBytes(file.size);
        },
        clearFile() {
            this.hasFile = false;
            this.fileName = '';
            this.fileExtension = '';
            this.fileSizeFormatted = '';
            this.compressionMode = 'compressed';
            const input = this.getFileInput();
            if (input) {
                input.value = '';
            }
        },
        getFileInput() {
            if (this.$refs && this.$refs.fileInput) {
                return this.$refs.fileInput;
            }
            return document.getElementById('ticket_conversation_file_input_' + this.ticketId);
        },
        fileIconClass() {
            const ext = this.fileExtension;
            if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return 'ri-image-2-line';
            if (ext === 'pdf') return 'ri-file-pdf-2-line';
            if (['doc', 'docx'].indexOf(ext) !== -1) return 'ri-file-word-2-line';
            if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return 'ri-file-excel-2-line';
            if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return 'ri-file-zip-line';
            if (['ppt', 'pptx'].indexOf(ext) !== -1) return 'ri-file-ppt-2-line';
            return 'ri-file-text-line';
        },
        formatBytes(bytes) {
            const b = Number(bytes) || 0;
            if (b >= 1048576) {
                return (b / 1048576).toFixed(1) + ' MB';
            }
            if (b >= 1024) {
                return Math.round(b / 1024) + ' KB';
            }
            return b + ' B';
        },
        scrollToBottom() {
            if (this.$refs.streamContainer) {
                this.$refs.streamContainer.scrollTop = this.$refs.streamContainer.scrollHeight;
            }
        },
        adjustTextareaHeight() {
            const el = this.$refs.replyTextarea || document.getElementById('ticket_conversation_response_text_' + this.ticketId);
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
        handleEnterSend(e) {
            if (e && e.shiftKey) {
                return;
            }
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.submitReply(e);
        },
        async submitReply(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if (this.isSubmitting) return;

            const el = this.$refs.replyTextarea || document.getElementById('ticket_conversation_response_text_' + this.ticketId);
            const text = el ? el.value.trim() : '';
            const fileInput = this.getFileInput();
            const hasSelectedFile = Boolean(fileInput && fileInput.files && fileInput.files.length > 0);

            if (text.length === 0 && !hasSelectedFile) {
                this.errorMessage = 'Ketik pesan tanggapan atau lampirkan berkas terlebih dahulu.';
                return;
            }

            this.errorMessage = '';
            this.isSubmitting = true;

            const token = this.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const formData = new FormData();
            formData.append('_token', token);
            formData.append('response_text', text);
            formData.append('compression_mode', this.compressionMode);
            if (hasSelectedFile && fileInput.files[0]) {
                formData.append('attachment', fileInput.files[0]);
            }

            try {
                const response = await fetch(this.replyUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (response.ok) {
                    window.location.reload();
                    return;
                }

                const data = await response.json().catch(() => null);
                if (data && data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    this.errorMessage = data.errors[firstKey][0] || 'Gagal mengirim berkas.';
                } else if (data && data.message) {
                    this.errorMessage = data.message;
                } else {
                    this.errorMessage = 'Terjadi kendala saat mengirim. Silakan periksa kembali berkas Anda.';
                }
                this.isSubmitting = false;
            } catch (err) {
                this.errorMessage = 'Koneksi terputus saat mengunggah berkas. Silakan coba kembali.';
                this.isSubmitting = false;
            }
        }
    };
};
</script>

<div 
    x-data="bbpomTicketConversation({ ticketId: {{ $ticket->id }}, replyUrl: '{{ route('tickets.reply', $ticket) }}', csrfToken: '{{ csrf_token() }}' })"
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

    <!-- Stream Kontainer Obrolan -->
    <div 
        x-ref="streamContainer"
        class="bbpom-chat-stream bg-slate-50/70 rounded-2xl p-3.5 sm:p-5 border border-slate-200/70 min-h-[320px] max-h-[540px] overflow-y-auto space-y-2.5 scroll-smooth"
        style="background-color: rgba(248, 250, 252, 0.75); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 1rem; padding: 1.25rem; min-height: 320px; max-height: 540px; overflow-y: auto;"
    >
        @php
            $ticket->loadMissing(['responses.user', 'responses.attachments', 'handledBy', 'user']);
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

            <!-- Pemisah Tanggal -->
            <div class="flex justify-center my-2.5" style="display: flex; justify-content: center; margin: 0.625rem 0;">
                <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-slate-200/80 text-slate-500 shadow-2xs" style="background-color: #ffffff; border: 1px solid rgba(226, 232, 240, 0.9); color: #64748b; font-size: 11px; font-weight: 500; padding: 2px 10px; border-radius: 9999px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    {{ $label }}
                </span>
            </div>

            {{-- Pesan Sambutan Otomatis IT Helpdesk --}}
            @if($dateKey === $ticketDateKey)
                <div class="flex items-end justify-start gap-2 max-w-[85%] sm:max-w-[70%]" style="display: flex; align-items: flex-end; justify-content: flex-start; gap: 0.5rem; max-width: 85%; margin-bottom: 0.625rem;">
                    <div class="w-7 h-7 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background-color: #d1fae5; border: 1px solid #86efac; color: #065f46; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                        {{ $ticket->handledBy ? strtoupper(substr($ticket->handledBy->name, 0, 2)) : 'IT' }}
                    </div>
                    <div class="bg-white rounded-2xl rounded-bl-xs px-3.5 py-2.5 border border-slate-200/80 shadow-2xs text-left" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 1rem 0.25rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                        <div class="flex items-center justify-start gap-1.5 mb-1" style="display: flex; align-items: center; justify-content: flex-start; gap: 0.375rem;">
                            <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">
                                Helpdesk TIK
                            </span>
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
                @foreach($grouped->get($dateKey) as $response)
                    @php
                        $isMe = auth()->check() && (int) $response->user_id === (int) auth()->id();
                        $isSenderAdmin = $response->user->isAdmin();
                    @endphp

                    @if($isMe)
                        <!-- Pesan Keluar / Pengguna Aktif (KANAN) -->
                        <div class="flex items-end justify-end gap-2 max-w-[90%] sm:max-w-[75%] ml-auto" style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 0.5rem; max-width: 90%; margin-left: auto; margin-bottom: 0.625rem;">
                            <!-- Bubble Pesan Kanan -->
                            <div class="bg-emerald-50/90 rounded-2xl rounded-br-xs px-3.5 py-2.5 border border-emerald-200/70 shadow-2xs text-left" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 1rem 1rem 0.25rem 1rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                                <div class="flex items-center justify-end gap-1.5 mb-1" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.375rem;">
                                    <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">
                                        {{ $response->user->name }}
                                    </span>
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold {{ $isSenderAdmin ? 'bg-blue-100 text-blue-800 border border-blue-200/60' : 'bg-emerald-200/80 text-emerald-900 border border-emerald-300/60' }}" style="font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px;">
                                        {{ $isSenderAdmin ? 'Petugas IT (Anda)' : 'Pemohon (Anda)' }}
                                    </span>
                                </div>

                                @if(!empty(trim($response->response_text)))
                                    <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                                @endif

                                <!-- Kartu Lampiran Berkas Format WhatsApp Document (Pesan Keluar) -->
                                @if($response->attachments->isNotEmpty())
                                    <div class="mt-2 space-y-2">
                                        @foreach($response->attachments as $att)
                                            <div class="rounded-xl p-2.5 sm:p-3 bg-white/95 border border-emerald-200/90 shadow-2xs flex items-center justify-between gap-3 max-w-sm transition-all hover:shadow-xs group" style="background-color: rgba(255, 255, 255, 0.95); border: 1px solid rgba(167, 243, 208, 0.9); border-radius: 0.75rem; padding: 0.625rem 0.75rem;">
                                                <!-- Icon Box & File Info -->
                                                <a href="{{ $att->download_url }}" class="flex items-center gap-2.5 min-w-0 flex-1 text-left no-underline group-hover:opacity-95" style="display: flex; align-items: center; gap: 0.625rem; min-width: 0; flex: 1; text-decoration: none;" title="Unduh {{ $att->file_name }}">
                                                    <div class="w-10 h-10 rounded-xl {{ $att->document_color_class }} flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center" style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                                                        <i class="{{ $att->icon_class }} text-base text-white leading-none" style="font-size: 1rem; color: #ffffff;"></i>
                                                        <span class="text-[8.5px] font-extrabold tracking-wider uppercase mt-0.5 text-white/95" style="font-size: 8.5px; font-weight: 800; color: #ffffff; text-transform: uppercase;">{{ $att->extension_upper }}</span>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs sm:text-[13px] font-bold text-slate-800 group-hover:text-emerald-700 truncate block leading-tight transition-colors" style="font-size: 0.8125rem; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.25;" title="{{ $att->file_name }}">
                                                            {{ $att->file_name }}
                                                        </p>
                                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" style="display: flex; align-items: center; gap: 0.375rem; font-size: 10.5px; color: #64748b; margin-top: 0.25rem;">
                                                            <span>{{ $att->formatted_size }}</span>
                                                            <span class="text-slate-300">•</span>
                                                            <span class="uppercase font-semibold text-slate-600">{{ $att->extension_upper }}</span>
                                                            @if($att->is_compressed)
                                                                <span class="text-slate-300">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60" title="Ukuran asli: {{ $att->formatted_original_size }}">
                                                                    <i class="ri-flashlight-line"></i>
                                                                    <span>-{{ $att->saving_percentage }}%</span>
                                                                </span>
                                                            @else
                                                                <span class="text-slate-300">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full border border-slate-200">
                                                                    <i class="ri-hd-line text-slate-400"></i>
                                                                    <span>Asli</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                                <!-- Tombol Unduh WhatsApp -->
                                                <a 
                                                    href="{{ $att->download_url }}" 
                                                    class="shrink-0 w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all shadow-xs hover:scale-105 active:scale-95" 
                                                    style="width: 2.25rem; height: 2.25rem; border-radius: 9999px; background-color: #059669; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; text-decoration: none;"
                                                    title="Unduh {{ $att->file_name }}"
                                                >
                                                    <i class="ri-download-2-line text-base" style="font-size: 1rem;"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="text-[10px] text-slate-500 text-right mt-1.5 font-mono flex items-center justify-end gap-1" style="font-size: 10px; color: #64748b; text-align: right; font-family: monospace; display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem;">
                                    <span>{{ $response->created_at->format('H:i') }} WIB</span>
                                    <i class="ri-check-double-line text-emerald-600 text-xs" style="color: #059669; font-size: 0.75rem;"></i>
                                </div>
                            </div>

                            <!-- Avatar Circle User (Kanan) -->
                            <div class="w-7 h-7 rounded-full {{ $isSenderAdmin ? 'bg-blue-100 border border-blue-300 text-blue-800' : 'bg-emerald-100 border border-emerald-300 text-emerald-800' }} flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                {{ strtoupper(substr($response->user->name, 0, 2)) }}
                            </div>
                        </div>
                    @else
                        <!-- Pesan Masuk / Lawan Bicara (KIRI) -->
                        <div class="flex items-end gap-2 max-w-[90%] sm:max-w-[75%]" style="display: flex; align-items: flex-end; gap: 0.5rem; max-width: 90%; margin-bottom: 0.625rem;">
                            <!-- Avatar Circle Sender (Kiri) -->
                            <div class="w-7 h-7 rounded-full {{ $isSenderAdmin ? 'bg-blue-100 border border-blue-300 text-blue-800' : 'bg-slate-200/90 border border-slate-300 text-slate-700' }} flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                {{ strtoupper(substr($response->user->name, 0, 2)) }}
                            </div>

                            <!-- Bubble Pesan Kiri -->
                            <div class="bg-white rounded-2xl rounded-bl-xs px-3.5 py-2.5 border border-slate-200/80 shadow-2xs text-left" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 1rem 0.25rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                                <div class="flex items-center gap-1.5 mb-1" style="display: flex; align-items: center; gap: 0.375rem;">
                                    <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;">
                                        {{ $response->user->name }}
                                    </span>
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-semibold {{ $isSenderAdmin ? 'bg-blue-100 text-blue-800 border border-blue-200/60' : 'bg-emerald-100 text-emerald-800 border border-emerald-200/60' }}" style="font-size: 9.5px; font-weight: 600; padding: 1px 6px; border-radius: 4px;">
                                        {{ $isSenderAdmin ? 'Petugas IT' : 'Pemohon' }}
                                    </span>
                                </div>

                                @if(!empty(trim($response->response_text)))
                                    <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                                @endif

                                <!-- Kartu Lampiran Berkas Format WhatsApp Document (Pesan Masuk) -->
                                @if($response->attachments->isNotEmpty())
                                    <div class="mt-2 space-y-2">
                                        @foreach($response->attachments as $att)
                                            <div class="rounded-xl p-2.5 sm:p-3 bg-slate-50/90 border border-slate-200/90 shadow-2xs flex items-center justify-between gap-3 max-w-sm transition-all hover:shadow-xs group" style="background-color: rgba(248, 250, 252, 0.9); border: 1px solid rgba(226, 232, 240, 0.95); border-radius: 0.75rem; padding: 0.625rem 0.75rem;">
                                                <!-- Icon Box & File Info -->
                                                <a href="{{ $att->download_url }}" class="flex items-center gap-2.5 min-w-0 flex-1 text-left no-underline group-hover:opacity-95" style="display: flex; align-items: center; gap: 0.625rem; min-width: 0; flex: 1; text-decoration: none;" title="Unduh {{ $att->file_name }}">
                                                    <div class="w-10 h-10 rounded-xl {{ $att->document_color_class }} flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center" style="width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                                                        <i class="{{ $att->icon_class }} text-base text-white leading-none" style="font-size: 1rem; color: #ffffff;"></i>
                                                        <span class="text-[8.5px] font-extrabold tracking-wider uppercase mt-0.5 text-white/95" style="font-size: 8.5px; font-weight: 800; color: #ffffff; text-transform: uppercase;">{{ $att->extension_upper }}</span>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs sm:text-[13px] font-bold text-slate-800 group-hover:text-emerald-700 truncate block leading-tight transition-colors" style="font-size: 0.8125rem; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.25;" title="{{ $att->file_name }}">
                                                            {{ $att->file_name }}
                                                        </p>
                                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" style="display: flex; align-items: center; gap: 0.375rem; font-size: 10.5px; color: #64748b; margin-top: 0.25rem;">
                                                            <span>{{ $att->formatted_size }}</span>
                                                            <span class="text-slate-300">•</span>
                                                            <span class="uppercase font-semibold text-slate-600">{{ $att->extension_upper }}</span>
                                                            @if($att->is_compressed)
                                                                <span class="text-slate-300">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60" title="Ukuran asli: {{ $att->formatted_original_size }}">
                                                                    <i class="ri-flashlight-line"></i>
                                                                    <span>-{{ $att->saving_percentage }}%</span>
                                                                </span>
                                                            @else
                                                                <span class="text-slate-300">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full border border-slate-200">
                                                                    <i class="ri-hd-line text-slate-400"></i>
                                                                    <span>Asli</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                                <!-- Tombol Unduh WhatsApp -->
                                                <a 
                                                    href="{{ $att->download_url }}" 
                                                    class="shrink-0 w-9 h-9 rounded-full bg-slate-200 hover:bg-emerald-600 text-slate-700 hover:text-white flex items-center justify-center transition-all shadow-xs hover:scale-105 active:scale-95" 
                                                    style="width: 2.25rem; height: 2.25rem; border-radius: 9999px; background-color: #e2e8f0; color: #334155; display: flex; align-items: center; justify-content: center; flex-shrink: 0; text-decoration: none;"
                                                    title="Unduh {{ $att->file_name }}"
                                                >
                                                    <i class="ri-download-2-line text-base" style="font-size: 1rem;"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="text-[10px] text-slate-400 text-right mt-1.5 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;">
                                    {{ $response->created_at->format('H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>

    <!-- Bar Masukan Pesan & Lampiran Berkas -->
    <div 
        id="ticketConversationInputArea" 
        class="mt-4 pt-3 border-t border-slate-100"
        style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;"
    >
        <!-- Input File Asli & Tersembunyi -->
        <input 
            type="file" 
            x-ref="fileInput" 
            id="ticket_conversation_file_input_{{ $ticket->id }}" 
            name="attachment" 
            class="hidden" 
            style="display: none;"
            @change="handleFileChange($event)" 
        />

        <!-- Floating Attachment Pill Preview -->
        <div 
            x-show="hasFileSelected()" 
            class="mb-3 p-3 rounded-2xl bg-white border border-emerald-300 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all"
            style="background-color: #ffffff; border: 1px solid rgba(110, 231, 183, 0.9); border-radius: 1rem; padding: 0.75rem 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
        >
            <div class="flex items-center gap-2.5 min-w-0" style="display: flex; align-items: center; gap: 0.625rem;">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center justify-center shrink-0" style="width: 2.25rem; height: 2.25rem; border-radius: 0.75rem; background-color: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i :class="fileIconClass()" class="text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800 truncate" style="font-size: 0.8125rem; font-weight: 700; color: #1e293b; margin: 0;" x-text="fileName"></p>
                    <p class="text-[11px] text-slate-500" style="font-size: 11px; color: #64748b; margin: 0;">
                        Ukuran Berkas: <span class="font-semibold text-slate-700" x-text="fileSizeFormatted"></span>
                    </p>
                </div>
            </div>

            <!-- Segmented Mode Switcher Pill -->
            <div class="flex items-center gap-2 self-end sm:self-auto shrink-0" style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="inline-flex p-0.5 rounded-xl bg-slate-100 border border-slate-200 text-[11px] font-semibold" style="display: inline-flex; padding: 2px; border-radius: 0.75rem; background-color: #f1f5f9; border: 1px solid #e2e8f0; font-size: 11px; font-weight: 600;">
                    <button 
                        type="button" 
                        @click="setMode('compressed')" 
                        :class="isCompressedMode() ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-2.5 py-1 rounded-lg transition-all flex items-center gap-1 cursor-pointer border-0"
                        title="Kompresi cerdas hemat kapasitas penyimpanan database & server"
                    >
                        <i class="ri-sparkling-fill text-xs"></i>
                        <span>Kompres Cerdas</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setMode('original')" 
                        :class="isOriginalMode() ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-2.5 py-1 rounded-lg transition-all flex items-center gap-1 cursor-pointer border-0"
                        title="Kirim ukuran & resolusi asli tanpa kompresi"
                    >
                        <i class="ri-hd-line text-xs"></i>
                        <span>Ukuran Asli</span>
                    </button>
                </div>

                <button 
                    type="button" 
                    @click="clearFile()" 
                    class="w-7 h-7 rounded-full bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-700 flex items-center justify-center transition-colors cursor-pointer border-0"
                    style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                    title="Batalkan lampiran berkas"
                >
                    <i class="ri-close-line text-base"></i>
                </button>
            </div>
        </div>

        <!-- Input Baris Chat & Tombol Lampiran -->
        <div class="flex items-center gap-2.5" style="display: flex; align-items: center; gap: 0.625rem;">
            <!-- Label Klip Lampiran Berkas (Memicu File Chooser secara Native) -->
            <label 
                for="ticket_conversation_file_input_{{ $ticket->id }}" 
                role="button" 
                class="w-11 h-11 rounded-full border border-slate-200/90 bg-slate-50 hover:bg-slate-100 active:bg-slate-200 text-slate-600 hover:text-emerald-700 flex items-center justify-center transition-all hover:scale-105 shrink-0 cursor-pointer"
                style="width: 2.75rem; height: 2.75rem; border-radius: 9999px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer; transition: all 0.15s ease;"
                title="Pilih Berkas untuk Dilampirkan (Semua Format Dokumen & Gambar)"
            >
                <i class="ri-attachment-2 text-xl" style="font-size: 1.25rem;"></i>
            </label>

            <!-- Textarea Balasan -->
            <div class="flex-1 relative" style="flex: 1; position: relative;">
                <textarea 
                    x-ref="replyTextarea"
                    id="ticket_conversation_response_text_{{ $ticket->id }}" 
                    name="response_text" 
                    rows="1" 
                    class="w-full rounded-2xl border-slate-300 py-3 px-4 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs resize-none max-h-32 overflow-hidden"
                    style="width: 100%; border-radius: 1rem; border: 1px solid #cbd5e1; padding: 0.75rem 1rem; font-size: 0.8125rem; color: #1e293b; resize: none; max-height: 128px; overflow-y: hidden; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.03); outline: none;"
                    placeholder="Ketik tanggapan Anda di sini... (atau lampirkan berkas di samping)"
                    @input="adjustTextareaHeight()"
                    @keydown.enter="handleEnterSend($event)"
                >{{ old('response_text') }}</textarea>
            </div>

            <!-- Tombol Kirim Mandiri (AJAX Fetch Safe) -->
            <button 
                type="button" 
                @click="submitReply($event)" 
                :disabled="isSubmitting"
                class="w-11 h-11 rounded-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white flex items-center justify-center transition-all shadow-xs shrink-0 hover:scale-105 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                style="width: 2.75rem; height: 2.75rem; border-radius: 9999px; background-color: #059669; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: none; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"
                title="Kirim Pesan"
                onmouseover="this.style.backgroundColor='#047857'; this.style.transform='scale(1.05)';"
                onmouseout="this.style.backgroundColor='#059669'; this.style.transform='scale(1)';"
            >
                <i x-show="!isSubmitting" class="ri-send-plane-2-fill text-lg" style="font-size: 1.125rem;"></i>
                <i x-show="isSubmitting" class="ri-loader-4-line animate-spin text-lg" style="font-size: 1.125rem;"></i>
            </button>
        </div>

        <!-- Notifikasi Error Validasi / Pengiriman -->
        <div x-show="hasErrorMessage()" class="mt-2 text-xs text-rose-600 font-medium flex items-center gap-1.5" style="color: #e11d48; font-size: 0.75rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.375rem;">
            <i class="ri-error-warning-line text-sm"></i>
            <span x-text="errorMessage"></span>
        </div>
        
        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 px-1 flex-wrap gap-1" style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-top: 0.5rem; padding: 0 0.25rem;">
            <span>Tekan <strong>Enter</strong> untuk mengirim, <strong>Shift + Enter</strong> untuk baris baru</span>
            <span class="flex items-center gap-1" style="display: flex; align-items: center; gap: 0.25rem;">
                <i class="ri-shield-check-line text-emerald-600" style="color: #059669;"></i>
                Berbagi berkas aman & terenkripsi Helpdesk TIK
            </span>
        </div>
    </div>
</div>

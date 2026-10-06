@props([
    'ticket',
])

@php
    $ticket->loadMissing([
        'responses.user',
        'responses.attachments',
        'responses.replyTo.user',
        'responses.replyTo.attachments',
        'handledBy',
        'user',
    ]);
    $responses = $ticket->responses;
    $lastMessageId = (int) ($responses->max('id') ?? 0);
    $initialIds = $responses->pluck('id')->values()->all();
@endphp

<script>
window.bbpomTicketConversation = function bbpomTicketConversation(config) {
    const cfg = config || {};
    return {
        ticketId: cfg.ticketId || null,
        replyUrl: cfg.replyUrl || '',
        syncUrl: cfg.syncUrl || '',
        csrfToken: cfg.csrfToken || '',
        isSubmitting: false,
        hasFile: false,
        fileName: '',
        fileSizeFormatted: '',
        fileExtension: '',
        compressionMode: 'compressed',
        errorMessage: '',
        replyingTo: null,
        lastMessageId: Number(cfg.lastMessageId) || 0,
        dynamicMessages: [],
        existingIds: null,
        pollTimer: null,
        isPolling: false,

        init() {
            this.existingIds = new Set(cfg.initialIds || []);
            this.$nextTick(() => {
                this.scrollToBottom();
                this.adjustTextareaHeight();
            });
            this.startPolling();
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.stopPolling();
                } else {
                    this.startPolling();
                    this.pollSync();
                }
            });
        },
        startPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(() => {
                this.pollSync();
            }, 2500);
        },
        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },
        async pollSync() {
            if (this.isPolling || this.isSubmitting || !this.syncUrl) return;
            this.isPolling = true;
            try {
                const res = await fetch(`${this.syncUrl}?after_id=${this.lastMessageId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                if (!res.ok) {
                    this.isPolling = false;
                    return;
                }
                const data = await res.json();
                if (data && data.responses && data.responses.length > 0) {
                    let hasNew = false;
                    for (const msg of data.responses) {
                        const id = Number(msg.id);
                        if (id > this.lastMessageId) {
                            this.lastMessageId = id;
                        }
                        if (!this.existingIds.has(id)) {
                            this.existingIds.add(id);
                            this.dynamicMessages.push(msg);
                            hasNew = true;
                        }
                    }
                    if (hasNew) {
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }
                }
            } catch (err) {
                // Ignore transient network errors
            } finally {
                this.isPolling = false;
            }
        },
        hasReplyTarget() {
            return Boolean(this.replyingTo && this.replyingTo.id);
        },
        setReply(msg) {
            this.replyingTo = msg;
            this.$nextTick(() => {
                const el = this.$refs.replyTextarea || document.getElementById('ticket_conversation_response_text_' + this.ticketId);
                if (el) {
                    el.focus();
                }
            });
        },
        cancelReply() {
            this.replyingTo = null;
        },
        replyAuthorText() {
            return 'Membalas ' + (this.replyingTo ? this.replyingTo.userName : '');
        },
        replySnippetText() {
            return this.replyingTo ? this.replyingTo.text : '';
        },
        scrollToMessage(id) {
            const el = document.getElementById('bbpom_msg_' + id);
            if (!el) return;

            const container = this.$refs && this.$refs.streamContainer ? this.$refs.streamContainer : document.querySelector('.bbpom-chat-stream');
            if (container) {
                const containerRect = container.getBoundingClientRect();
                const elRect = el.getBoundingClientRect();
                const targetScrollTop = container.scrollTop + (elRect.top - containerRect.top) - (container.clientHeight / 2) + (elRect.clientHeight / 2);
                container.scrollTo({ top: Math.max(0, targetScrollTop), behavior: 'smooth' });
            }

            el.classList.remove('bbpom-highlight-pulse');
            void el.offsetWidth;
            el.classList.add('bbpom-highlight-pulse');
            setTimeout(() => {
                el.classList.remove('bbpom-highlight-pulse');
            }, 1050);
        },
        triggerFilePicker() {
            const input = this.getFileInput();
            if (input) {
                input.click();
            }
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
        hasText(msg) {
            return Boolean(msg && msg.response_text && msg.response_text.trim().length > 0);
        },
        hasAttachments(msg) {
            return Boolean(msg && msg.attachments && msg.attachments.length > 0);
        },
        hasReplyTo(msg) {
            return Boolean(msg && msg.reply_to && msg.reply_to.id);
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
            if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return 'ri-palette-line';
            return 'ri-file-text-line';
        },
        fileColorHex() {
            const ext = this.fileExtension;
            if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return '#059669';
            if (ext === 'pdf') return '#e11d48';
            if (['doc', 'docx'].indexOf(ext) !== -1) return '#2563eb';
            if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return '#0d9488';
            if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return '#d97706';
            if (['ppt', 'pptx'].indexOf(ext) !== -1) return '#ea580c';
            if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return '#9333ea';
            return '#475569';
        },
        fileColorBgHex() {
            const ext = this.fileExtension;
            if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return '#f0fdf4';
            if (ext === 'pdf') return '#fff1f2';
            if (['doc', 'docx'].indexOf(ext) !== -1) return '#eff6ff';
            if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return '#f0fdfa';
            if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return '#fffbeb';
            if (['ppt', 'pptx'].indexOf(ext) !== -1) return '#fff7ed';
            if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return '#faf5ff';
            return '#f8fafc';
        },
        fileColorBorderHex() {
            const ext = this.fileExtension;
            if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'].indexOf(ext) !== -1) return '#bbf7d0';
            if (ext === 'pdf') return '#fecdd3';
            if (['doc', 'docx'].indexOf(ext) !== -1) return '#bfdbfe';
            if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) return '#99f6e4';
            if (['zip', 'rar', '7z', 'tar', 'gz'].indexOf(ext) !== -1) return '#fde68a';
            if (['ppt', 'pptx'].indexOf(ext) !== -1) return '#fed7aa';
            if (['psd', 'ai', 'fig', 'sketch'].indexOf(ext) !== -1) return '#e9d5ff';
            return '#e2e8f0';
        },
        fileExtensionUpper() {
            return (this.fileExtension || 'FILE').toUpperCase();
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

            let token = this.csrfToken;
            if (!token) {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) token = meta.getAttribute('content');
            }
            if (!token) {
                const input = document.querySelector('input[name="_token"]');
                if (input) token = input.value;
            }

            const formData = new FormData();
            if (token) {
                formData.append('_token', token);
            }
            formData.append('response_text', text);
            formData.append('compression_mode', this.compressionMode);
            if (this.hasReplyTarget()) {
                formData.append('reply_to_id', this.replyingTo.id);
            }
            if (hasSelectedFile && fileInput.files[0]) {
                formData.append('attachment', fileInput.files[0]);
            }

            try {
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                };
                if (token) {
                    headers['X-CSRF-TOKEN'] = token;
                }

                const response = await fetch(this.replyUrl, {
                    method: 'POST',
                    headers: headers,
                    body: formData,
                });

                if (response.ok) {
                    const data = await response.json().catch(() => null);
                    if (data && data.response) {
                        const msg = data.response;
                        const id = Number(msg.id);
                        if (id > this.lastMessageId) {
                            this.lastMessageId = id;
                        }
                        if (!this.existingIds.has(id)) {
                            this.existingIds.add(id);
                            this.dynamicMessages.push(msg);
                        }
                    }

                    if (el) el.value = '';
                    this.clearFile();
                    this.cancelReply();
                    this.adjustTextareaHeight();
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                    this.isSubmitting = false;
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

<style>
@keyframes bbpomSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.bbpom-spinner-rotate {
    animation: bbpomSpin 0.85s linear infinite !important;
}
@keyframes bbpomHighlightPulse {
    0% {
        background-color: rgba(5, 150, 105, 0.24);
    }
    30% {
        background-color: rgba(5, 150, 105, 0.28);
    }
    100% {
        background-color: transparent;
    }
}
.bbpom-highlight-pulse {
    animation: bbpomHighlightPulse 1.0s ease-out forwards !important;
    border-radius: 1rem !important;
}

/* Row Message & Reply Trigger Micro-interactions */
.bbpom-msg-row {
    position: relative;
    transition: background-color 0.15s ease;
}
.bbpom-reply-trigger {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: scale(0.85) !important;
    transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1), transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.15s ease, color 0.15s ease !important;
    border-radius: 9999px !important;
    width: 2rem !important;
    height: 2rem !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: none !important;
    cursor: pointer !important;
    background: transparent !important;
    color: #94a3b8 !important;
    flex-shrink: 0 !important;
    align-self: center !important;
}
.bbpom-msg-row:hover .bbpom-reply-trigger,
.bbpom-msg-row:focus-within .bbpom-reply-trigger {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: scale(1) !important;
}
.bbpom-reply-trigger:hover {
    background-color: rgba(226, 232, 240, 0.85) !important;
    color: #047857 !important;
    transform: scale(1.12) !important;
}
.bbpom-reply-trigger:active {
    transform: scale(0.95) !important;
}

/* Quote Reply Inside Message Bubble (WhatsApp Style) */
.bbpom-quote-card {
    transition: filter 0.15s ease, background-color 0.15s ease !important;
    cursor: pointer !important;
    user-select: none !important;
    border-left: 3px solid #059669 !important;
}
.bbpom-quote-card:hover {
    filter: brightness(0.94) !important;
}

/* Document Attachment Card & Download Button */
.bbpom-doc-card {
    transition: transform 0.18s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.18s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.18s ease !important;
    cursor: default !important;
}
.bbpom-doc-card:hover {
    transform: translateY(-1.5px) !important;
    box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.08) !important;
}
.bbpom-doc-card:hover .bbpom-doc-name {
    color: #047857 !important;
}
.bbpom-chat-download-btn {
    transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.15s ease !important;
}
.bbpom-chat-download-btn:hover {
    transform: scale(1.08) !important;
    background-color: #047857 !important;
    box-shadow: 0 4px 12px -1px rgba(5, 150, 105, 0.45) !important;
}
.bbpom-chat-download-btn:active {
    transform: scale(0.95) !important;
}

/* File Clip Button */
.bbpom-clip-btn {
    transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease !important;
}
.bbpom-clip-btn:hover {
    background-color: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    color: #047857 !important;
    transform: scale(1.05) !important;
}
.bbpom-clip-btn:active {
    transform: scale(0.95) !important;
}

/* Send Button */
.bbpom-send-btn {
    transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.15s ease, box-shadow 0.15s ease !important;
}
.bbpom-send-btn:hover:not(:disabled) {
    background-color: #047857 !important;
    transform: scale(1.06) !important;
    box-shadow: 0 4px 12px -1px rgba(5, 150, 105, 0.4) !important;
}
.bbpom-send-btn:active:not(:disabled) {
    transform: scale(0.95) !important;
}

/* Compression Mode Switcher Pill & Preview Elements */
.bbpom-file-preview-card {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 0.75rem !important;
    flex-wrap: wrap !important;
    box-sizing: border-box !important;
    width: 100% !important;
    border-radius: 1rem !important;
    padding: 0.75rem 1rem !important;
    margin-bottom: 0.75rem !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.2s ease !important;
}
.bbpom-file-preview-info {
    display: flex !important;
    align-items: center !important;
    gap: 0.625rem !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
}
.bbpom-file-preview-actions {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    flex-shrink: 0 !important;
    margin-left: auto !important;
}
.bbpom-segmented-switcher {
    display: inline-flex !important;
    align-items: center !important;
    padding: 3px !important;
    border-radius: 0.625rem !important;
    background-color: rgba(255, 255, 255, 0.95) !important;
    border: 1px solid #cbd5e1 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    gap: 2px !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
}
.bbpom-mode-pill {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.35rem !important;
    padding: 0.25rem 0.625rem !important;
    border-radius: 0.5rem !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    border: none !important;
    cursor: pointer !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    transition: all 0.15s ease !important;
    font-family: inherit !important;
}
.bbpom-mode-pill:hover {
    filter: brightness(0.96) !important;
}
.bbpom-clear-file-btn {
    width: 1.75rem !important;
    height: 1.75rem !important;
    border-radius: 9999px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    background-color: rgba(255, 255, 255, 0.95) !important;
    border: 1px solid #cbd5e1 !important;
    color: #64748b !important;
    padding: 0 !important;
    flex-shrink: 0 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.15s ease !important;
}
.bbpom-clear-file-btn:hover {
    background-color: #fee2e2 !important;
    border-color: #fca5a5 !important;
    color: #b91c1c !important;
    transform: scale(1.08) !important;
}
.bbpom-action-circle-btn {
    transition: background-color 0.15s ease, color 0.15s ease, transform 0.15s ease !important;
}
.bbpom-action-circle-btn:hover {
    transform: scale(1.1) !important;
}

/* Textarea Focus */
.bbpom-chat-textarea {
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
.bbpom-chat-textarea:focus {
    border-color: #059669 !important;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15) !important;
    outline: none !important;
}
</style>

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
                        <div id="bbpom_msg_{{ $response->id }}" class="bbpom-msg-row flex items-end justify-end gap-2 max-w-[90%] sm:max-w-[75%] ml-auto group transition-all" style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 0.5rem; max-width: 90%; margin-left: auto; margin-bottom: 0.625rem;">
                            <!-- Tombol Balas Pesan (Hover / Touch) -->
                            <button 
                                type="button" 
                                @click="setReply({ id: {{ $response->id }}, userName: '{{ addslashes($response->user->name) }}', text: '{{ addslashes($response->snippet) }}' })"
                                class="bbpom-reply-trigger"
                                title="Balas pesan ini"
                            >
                                <i class="ri-reply-line text-base"></i>
                            </button>

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

                                {{-- Kartu Balasan Tertaut (Quote Embedded Card) --}}
                                @if($response->replyTo)
                                    <div 
                                        @click="scrollToMessage({{ $response->reply_to_id }})"
                                        class="bbpom-quote-card mb-2 p-2 rounded-xl text-left"
                                        style="background-color: rgba(5, 150, 105, 0.12); border-left: 3px solid #059669; border-radius: 0.5rem; padding: 0.375rem 0.625rem; cursor: pointer;"
                                        title="Menuju pesan kutipan #{{ $response->reply_to_id }}"
                                    >
                                        <div class="flex items-center gap-1.5 leading-none mb-1" style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem;">
                                            <i class="ri-reply-fill text-xs" style="color: #047857; font-size: 0.75rem;"></i>
                                            <span class="bbpom-quote-user text-[11px] font-bold" style="font-size: 11px; font-weight: 700; color: #065f46;">
                                                {{ $response->replyTo->user ? $response->replyTo->user->name : 'Pengguna' }}
                                            </span>
                                            <span class="text-[9px] text-slate-500 font-semibold" style="font-size: 9px; color: #64748b;">
                                                ({{ $response->replyTo->user && $response->replyTo->user->isAdmin() ? 'Petugas IT' : 'Pemohon' }})
                                            </span>
                                        </div>
                                        @if($response->replyTo->attachments->isNotEmpty())
                                            @php $parentAtt = $response->replyTo->attachments->first(); @endphp
                                            <div class="flex items-center gap-1 text-[11px] text-slate-700 font-medium mb-0.5" style="display: flex; align-items: center; gap: 0.25rem; font-size: 11px;">
                                                <i class="{{ $parentAtt->pure_icon_class }}" style="color: {{ $parentAtt->document_hex_color }};"></i>
                                                <span class="truncate">{{ $parentAtt->file_name }}</span>
                                            </div>
                                        @endif
                                        <p class="text-[11px] text-slate-600 truncate leading-tight" style="font-size: 11px; color: #475569; margin: 0; line-height: 1.25;">
                                            {{ $response->replyTo->snippet }}
                                        </p>
                                    </div>
                                @endif

                                @if(!empty(trim($response->response_text)))
                                    <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                                @endif

                                <!-- Kartu Lampiran Berkas Format WhatsApp Document (Pesan Keluar) -->
                                @if($response->attachments->isNotEmpty())
                                    <div class="mt-2 space-y-2" style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($response->attachments as $att)
                                            <div class="bbpom-doc-card rounded-xl p-2.5 sm:p-3 shadow-2xs flex items-center justify-between gap-3 max-w-sm transition-all hover:shadow-xs group" 
                                                 style="display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 0.75rem !important; width: 100% !important; max-width: 24rem !important; background-color: {{ $att->document_card_bg_hex }} !important; border: 1px solid {{ $att->document_card_border_hex }} !important; border-radius: 0.75rem !important; padding: 0.625rem 0.75rem !important; box-sizing: border-box !important;">
                                                <!-- Icon Box & File Info -->
                                                <a href="{{ $att->download_url }}" class="flex items-center gap-2.5 min-w-0 flex-1 text-left no-underline group-hover:opacity-95" 
                                                   style="display: flex !important; align-items: center !important; gap: 0.625rem !important; min-width: 0 !important; flex: 1 1 0% !important; text-decoration: none !important; color: inherit !important;" 
                                                   title="Unduh {{ $att->file_name }}">
                                                    <div class="w-10 h-10 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center" 
                                                         style="width: 2.5rem !important; height: 2.5rem !important; border-radius: 0.75rem !important; background-color: {{ $att->document_hex_color }} !important; color: #ffffff !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;">
                                                        <i class="{{ $att->pure_icon_class }}" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
                                                        <span style="font-size: 8.5px !important; font-weight: 800 !important; color: #ffffff !important; text-transform: uppercase !important; margin-top: 2px !important; line-height: 1 !important;">{{ $att->extension_upper }}</span>
                                                    </div>
                                                    <div class="min-w-0 flex-1" style="min-width: 0 !important; flex: 1 1 0% !important; overflow: hidden !important;">
                                                        <p class="bbpom-doc-name text-xs sm:text-[13px] font-bold text-slate-800 group-hover:text-emerald-700 truncate block leading-tight transition-colors" 
                                                           style="font-size: 0.8125rem !important; font-weight: 700 !important; color: #1e293b !important; margin: 0 !important; line-height: 1.25 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important;" 
                                                           title="{{ $att->file_name }}">
                                                            {{ $att->file_name }}
                                                        </p>
                                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" 
                                                             style="display: flex !important; align-items: center !important; gap: 0.375rem !important; font-size: 10.5px !important; color: #64748b !important; margin-top: 0.25rem !important;">
                                                            <span>{{ $att->formatted_size }}</span>
                                                            <span style="color: #cbd5e1;">•</span>
                                                            <span style="text-transform: uppercase; font-weight: 600; color: #475569;">{{ $att->extension_upper }}</span>
                                                            @if($att->is_compressed)
                                                                <span style="color: #cbd5e1;">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60" 
                                                                      style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 700 !important; color: #047857 !important; background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; padding: 1px 6px !important; border-radius: 9999px !important;" 
                                                                      title="Ukuran asli: {{ $att->formatted_original_size }}">
                                                                    <i class="ri-flashlight-line"></i>
                                                                    <span>-{{ $att->saving_percentage }}%</span>
                                                                </span>
                                                            @else
                                                                <span style="color: #cbd5e1;">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full border border-slate-200" 
                                                                      style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 600 !important; color: #475569 !important; background-color: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; padding: 1px 6px !important; border-radius: 9999px !important;">
                                                                    <i class="ri-hd-line" style="color: #94a3b8;"></i>
                                                                    <span>Asli</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                                <!-- Tombol Unduh WhatsApp -->
                                                <a 
                                                    href="{{ $att->download_url }}" 
                                                    class="bbpom-chat-download-btn shrink-0 w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all shadow-xs hover:scale-105 active:scale-95" 
                                                    style="width: 2.25rem !important; height: 2.25rem !important; border-radius: 9999px !important; background-color: #059669 !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; margin-left: 0.5rem !important; text-decoration: none !important; box-shadow: 0 2px 5px -1px rgba(5, 150, 105, 0.35) !important;" 
                                                    title="Unduh {{ $att->file_name }}"
                                                >
                                                    <i class="ri-download-2-line text-base" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
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
                        <div id="bbpom_msg_{{ $response->id }}" class="bbpom-msg-row flex items-end gap-2 max-w-[90%] sm:max-w-[75%] group transition-all" style="display: flex; align-items: flex-end; gap: 0.5rem; max-width: 90%; margin-bottom: 0.625rem;">
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

                                {{-- Kartu Balasan Tertaut (Quote Embedded Card) --}}
                                @if($response->replyTo)
                                    <div 
                                        @click="scrollToMessage({{ $response->reply_to_id }})"
                                        class="bbpom-quote-card mb-2 p-2 rounded-xl text-left"
                                        style="background-color: #f1f5f9; border-left: 3px solid #059669; border-radius: 0.5rem; padding: 0.375rem 0.625rem; cursor: pointer;"
                                        title="Menuju pesan kutipan #{{ $response->reply_to_id }}"
                                    >
                                        <div class="flex items-center gap-1.5 leading-none mb-1" style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem;">
                                            <i class="ri-reply-fill text-xs" style="color: #047857; font-size: 0.75rem;"></i>
                                            <span class="bbpom-quote-user text-[11px] font-bold" style="font-size: 11px; font-weight: 700; color: #065f46;">
                                                {{ $response->replyTo->user ? $response->replyTo->user->name : 'Pengguna' }}
                                            </span>
                                            <span class="text-[9px] text-slate-500 font-semibold" style="font-size: 9px; color: #64748b;">
                                                ({{ $response->replyTo->user && $response->replyTo->user->isAdmin() ? 'Petugas IT' : 'Pemohon' }})
                                            </span>
                                        </div>
                                        @if($response->replyTo->attachments->isNotEmpty())
                                            @php $parentAtt = $response->replyTo->attachments->first(); @endphp
                                            <div class="flex items-center gap-1 text-[11px] text-slate-700 font-medium mb-0.5" style="display: flex; align-items: center; gap: 0.25rem; font-size: 11px;">
                                                <i class="{{ $parentAtt->pure_icon_class }}" style="color: {{ $parentAtt->document_hex_color }};"></i>
                                                <span class="truncate">{{ $parentAtt->file_name }}</span>
                                            </div>
                                        @endif
                                        <p class="text-[11px] text-slate-600 truncate leading-tight" style="font-size: 11px; color: #475569; margin: 0; line-height: 1.25;">
                                            {{ $response->replyTo->snippet }}
                                        </p>
                                    </div>
                                @endif

                                @if(!empty(trim($response->response_text)))
                                    <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;">{{ trim($response->response_text) }}</p>
                                @endif

                                <!-- Kartu Lampiran Berkas Format WhatsApp Document (Pesan Masuk) -->
                                @if($response->attachments->isNotEmpty())
                                    <div class="mt-2 space-y-2" style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($response->attachments as $att)
                                            <div class="bbpom-doc-card rounded-xl p-2.5 sm:p-3 shadow-2xs flex items-center justify-between gap-3 max-w-sm transition-all hover:shadow-xs group" 
                                                 style="display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 0.75rem !important; width: 100% !important; max-width: 24rem !important; background-color: {{ $att->document_card_bg_hex }} !important; border: 1px solid {{ $att->document_card_border_hex }} !important; border-radius: 0.75rem !important; padding: 0.625rem 0.75rem !important; box-sizing: border-box !important;">
                                                <!-- Icon Box & File Info -->
                                                <a href="{{ $att->download_url }}" class="flex items-center gap-2.5 min-w-0 flex-1 text-left no-underline group-hover:opacity-95" 
                                                   style="display: flex !important; align-items: center !important; gap: 0.625rem !important; min-width: 0 !important; flex: 1 1 0% !important; text-decoration: none !important; color: inherit !important;" 
                                                   title="Unduh {{ $att->file_name }}">
                                                    <div class="w-10 h-10 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center" 
                                                         style="width: 2.5rem !important; height: 2.5rem !important; border-radius: 0.75rem !important; background-color: {{ $att->document_hex_color }} !important; color: #ffffff !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;">
                                                        <i class="{{ $att->pure_icon_class }}" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
                                                        <span style="font-size: 8.5px !important; font-weight: 800 !important; color: #ffffff !important; text-transform: uppercase !important; margin-top: 2px !important; line-height: 1 !important;">{{ $att->extension_upper }}</span>
                                                    </div>
                                                    <div class="min-w-0 flex-1" style="min-width: 0 !important; flex: 1 1 0% !important; overflow: hidden !important;">
                                                        <p class="bbpom-doc-name text-xs sm:text-[13px] font-bold text-slate-800 group-hover:text-emerald-700 truncate block leading-tight transition-colors" 
                                                           style="font-size: 0.8125rem !important; font-weight: 700 !important; color: #1e293b !important; margin: 0 !important; line-height: 1.25 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important;" 
                                                           title="{{ $att->file_name }}">
                                                            {{ $att->file_name }}
                                                        </p>
                                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" 
                                                             style="display: flex !important; align-items: center !important; gap: 0.375rem !important; font-size: 10.5px !important; color: #64748b !important; margin-top: 0.25rem !important;">
                                                            <span>{{ $att->formatted_size }}</span>
                                                            <span style="color: #cbd5e1;">•</span>
                                                            <span style="text-transform: uppercase; font-weight: 600; color: #475569;">{{ $att->extension_upper }}</span>
                                                            @if($att->is_compressed)
                                                                <span style="color: #cbd5e1;">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60" 
                                                                      style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 700 !important; color: #047857 !important; background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; padding: 1px 6px !important; border-radius: 9999px !important;" 
                                                                      title="Ukuran asli: {{ $att->formatted_original_size }}">
                                                                    <i class="ri-flashlight-line"></i>
                                                                    <span>-{{ $att->saving_percentage }}%</span>
                                                                </span>
                                                            @else
                                                                <span style="color: #cbd5e1;">•</span>
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full border border-slate-200" 
                                                                      style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 600 !important; color: #475569 !important; background-color: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; padding: 1px 6px !important; border-radius: 9999px !important;">
                                                                    <i class="ri-hd-line" style="color: #94a3b8;"></i>
                                                                    <span>Asli</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                                <!-- Tombol Unduh WhatsApp -->
                                                <a 
                                                    href="{{ $att->download_url }}" 
                                                    class="bbpom-chat-download-btn shrink-0 w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all shadow-xs hover:scale-105 active:scale-95" 
                                                    style="width: 2.25rem !important; height: 2.25rem !important; border-radius: 9999px !important; background-color: #059669 !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; margin-left: 0.5rem !important; text-decoration: none !important; box-shadow: 0 2px 5px -1px rgba(5, 150, 105, 0.35) !important;" 
                                                    title="Unduh {{ $att->file_name }}"
                                                >
                                                    <i class="ri-download-2-line text-base" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="text-[10px] text-slate-400 text-right mt-1.5 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;">
                                    {{ $response->created_at->format('H:i') }} WIB
                                </div>
                            </div>

                            <!-- Tombol Balas Pesan (Hover / Touch) -->
                            <button 
                                type="button" 
                                @click="setReply({ id: {{ $response->id }}, userName: '{{ addslashes($response->user->name) }}', text: '{{ addslashes($response->snippet) }}' })"
                                class="bbpom-reply-trigger"
                                title="Balas pesan ini"
                            >
                                <i class="ri-reply-line text-base"></i>
                            </button>
                        </div>
                    @endif
                @endforeach
            @endif
        @endforeach

        <!-- Dynamic Messages Rendered via Alpine.js (Realtime Delta Sync) -->
        <template x-for="msg in dynamicMessages" :key="msg.id">
            <div class="w-full transition-all">
                <!-- Outgoing Message (Me - KANAN) -->
                <template x-if="msg.is_me">
                    <div :id="'bbpom_msg_' + msg.id" class="bbpom-msg-row flex items-end justify-end gap-2 max-w-[90%] sm:max-w-[75%] ml-auto group transition-all" style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 0.5rem; max-width: 90%; margin-left: auto; margin-bottom: 0.625rem;">
                        <!-- Tombol Balas Pesan (Hover / Touch) -->
                        <button 
                            type="button" 
                            @click="setReply({ id: msg.id, userName: msg.user_name, text: (msg.response_text || 'Lampiran Berkas') })"
                            class="bbpom-reply-trigger"
                            title="Balas pesan ini"
                        >
                            <i class="ri-reply-line text-base"></i>
                        </button>

                        <!-- Bubble Pesan Kanan -->
                        <div class="bg-emerald-50/90 rounded-2xl rounded-br-xs px-3.5 py-2.5 border border-emerald-200/70 shadow-2xs text-left" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 1rem 1rem 0.25rem 1rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                            <div class="flex items-center justify-end gap-1.5 mb-1" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.375rem;">
                                <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;" x-text="msg.user_name"></span>
                                <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold" 
                                      :class="msg.is_sender_admin ? 'bg-blue-100 text-blue-800 border border-blue-200/60' : 'bg-emerald-200/80 text-emerald-900 border border-emerald-300/60'"
                                      style="font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px;"
                                      x-text="msg.sender_badge"></span>
                            </div>

                            <!-- Kartu Balasan Tertaut (Quote Embedded Card) -->
                            <template x-if="hasReplyTo(msg)">
                                <div 
                                    @click="scrollToMessage(msg.reply_to.id)"
                                    class="bbpom-quote-card mb-2 p-2 rounded-xl text-left"
                                    style="background-color: rgba(5, 150, 105, 0.12); border-left: 3px solid #059669; border-radius: 0.5rem; padding: 0.375rem 0.625rem; cursor: pointer;"
                                    :title="'Menuju pesan kutipan #' + msg.reply_to.id"
                                >
                                    <div class="flex items-center gap-1.5 leading-none mb-1" style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem;">
                                        <i class="ri-reply-fill text-xs" style="color: #047857; font-size: 0.75rem;"></i>
                                        <span class="bbpom-quote-user text-[11px] font-bold" style="font-size: 11px; font-weight: 700; color: #065f46;" x-text="msg.reply_to.user_name"></span>
                                        <span class="text-[9px] text-slate-500 font-semibold" style="font-size: 9px; color: #64748b;" x-text="'(' + msg.reply_to.user_role + ')'"></span>
                                    </div>
                                    <template x-if="msg.reply_to.has_attachment">
                                        <div class="flex items-center gap-1 text-[11px] text-slate-700 font-medium mb-0.5" style="display: flex; align-items: center; gap: 0.25rem; font-size: 11px;">
                                            <i :class="msg.reply_to.attachment_icon" :style="'color: ' + msg.reply_to.attachment_color"></i>
                                            <span class="truncate" x-text="msg.reply_to.attachment_name"></span>
                                        </div>
                                    </template>
                                    <p class="text-[11px] text-slate-600 truncate leading-tight" style="font-size: 11px; color: #475569; margin: 0; line-height: 1.25;" x-text="msg.reply_to.snippet"></p>
                                </div>
                            </template>

                            <template x-if="hasText(msg)">
                                <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;" x-text="msg.response_text"></p>
                            </template>

                            <!-- Lampiran Berkas Format WhatsApp Document -->
                            <template x-if="hasAttachments(msg)">
                                <div class="mt-2 space-y-2" style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                    <template x-for="att in msg.attachments" :key="att.id">
                                        <div class="bbpom-doc-card rounded-xl p-2.5 sm:p-3 shadow-2xs flex items-center justify-between gap-3 max-w-sm transition-all hover:shadow-xs group/att"
                                             :style="'display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 0.75rem !important; width: 100% !important; max-width: 24rem !important; background-color: ' + att.document_card_bg_hex + ' !important; border: 1px solid ' + att.document_card_border_hex + ' !important; border-radius: 0.75rem !important; padding: 0.625rem 0.75rem !important; box-sizing: border-box !important;'">
                                            <a :href="att.download_url" class="flex items-center gap-2.5 min-w-0 flex-1 text-left no-underline group-hover/att:opacity-95" 
                                               style="display: flex !important; align-items: center !important; gap: 0.625rem !important; min-width: 0 !important; flex: 1 1 0% !important; text-decoration: none !important; color: inherit !important;" 
                                               :title="'Unduh ' + att.file_name">
                                                <div class="w-10 h-10 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center"
                                                     :style="'width: 2.5rem !important; height: 2.5rem !important; border-radius: 0.75rem !important; background-color: ' + att.document_hex_color + ' !important; color: #ffffff !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;'">
                                                    <i :class="att.pure_icon_class" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
                                                    <span style="font-size: 8.5px !important; font-weight: 800 !important; color: #ffffff !important; text-transform: uppercase !important; margin-top: 2px !important; line-height: 1 !important;" x-text="att.extension_upper"></span>
                                                </div>
                                                <div class="min-w-0 flex-1" style="min-width: 0 !important; flex: 1 1 0% !important; overflow: hidden !important;">
                                                    <p class="bbpom-doc-name text-xs sm:text-[13px] font-bold text-slate-800 group-hover/att:text-emerald-700 truncate block leading-tight transition-colors" 
                                                       style="font-size: 0.8125rem !important; font-weight: 700 !important; color: #1e293b !important; margin: 0 !important; line-height: 1.25 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important;" 
                                                       :title="att.file_name" x-text="att.file_name"></p>
                                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" 
                                                         style="display: flex !important; align-items: center !important; gap: 0.375rem !important; font-size: 10.5px !important; color: #64748b !important; margin-top: 0.25rem !important;">
                                                        <span x-text="att.formatted_size"></span>
                                                        <span style="color: #cbd5e1;">•</span>
                                                        <span style="text-transform: uppercase; font-weight: 600; color: #475569;" x-text="att.extension_upper"></span>
                                                        <template x-if="att.is_compressed">
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60" 
                                                                  style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 700 !important; color: #047857 !important; background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; padding: 1px 6px !important; border-radius: 9999px !important;">
                                                                <i class="ri-flashlight-line"></i>
                                                                <span x-text="'-' + att.saving_percentage + '%'"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="!att.is_compressed">
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full border border-slate-200" 
                                                                  style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 600 !important; color: #475569 !important; background-color: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; padding: 1px 6px !important; border-radius: 9999px !important;">
                                                                <i class="ri-hd-line" style="color: #94a3b8;"></i>
                                                                <span>Asli</span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </a>
                                            <a :href="att.download_url"
                                               class="bbpom-chat-download-btn shrink-0 w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all shadow-xs hover:scale-105 active:scale-95"
                                               style="width: 2.25rem !important; height: 2.25rem !important; border-radius: 9999px !important; background-color: #059669 !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; margin-left: 0.5rem !important; text-decoration: none !important; box-shadow: 0 2px 5px -1px rgba(5, 150, 105, 0.35) !important;"
                                               :title="'Unduh ' + att.file_name">
                                                <i class="ri-download-2-line text-base" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <div class="text-[10px] text-slate-500 text-right mt-1.5 font-mono flex items-center justify-end gap-1" style="font-size: 10px; color: #64748b; text-align: right; font-family: monospace; display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem;">
                                <span x-text="msg.time"></span>
                                <i class="ri-check-double-line text-emerald-600 text-xs" style="color: #059669; font-size: 0.75rem;"></i>
                            </div>
                        </div>

                        <!-- Avatar Circle User (Kanan) -->
                        <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" 
                             :class="msg.is_sender_admin ? 'bg-blue-100 border border-blue-300 text-blue-800' : 'bg-emerald-100 border border-emerald-300 text-emerald-800'"
                             style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;"
                             x-text="msg.user_initials"></div>
                    </div>
                </template>

                <!-- Incoming Message (Counterpart - KIRI) -->
                <template x-if="!msg.is_me">
                    <div :id="'bbpom_msg_' + msg.id" class="bbpom-msg-row flex items-end gap-2 max-w-[90%] sm:max-w-[75%] group transition-all" style="display: flex; align-items: flex-end; gap: 0.5rem; max-width: 90%; margin-bottom: 0.625rem;">
                        <!-- Avatar Circle Sender (Kiri) -->
                        <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs" 
                             :class="msg.is_sender_admin ? 'bg-blue-100 border border-blue-300 text-blue-800' : 'bg-slate-200/90 border border-slate-300 text-slate-700'"
                             style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;"
                             x-text="msg.user_initials"></div>

                        <!-- Bubble Pesan Kiri -->
                        <div class="bg-white rounded-2xl rounded-bl-xs px-3.5 py-2.5 border border-slate-200/80 shadow-2xs text-left" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem 1rem 1rem 0.25rem; padding: 0.625rem 0.875rem; box-shadow: 0 1px 2px rgba(0,0,0,0.04); max-width: 100%;">
                            <div class="flex items-center gap-1.5 mb-1" style="display: flex; align-items: center; gap: 0.375rem;">
                                <span class="text-[11px] font-bold text-slate-900 leading-tight" style="font-size: 11px; font-weight: 700; color: #0f172a;" x-text="msg.user_name"></span>
                                <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-semibold" 
                                      :class="msg.is_sender_admin ? 'bg-blue-100 text-blue-800 border border-blue-200/60' : 'bg-emerald-100 text-emerald-800 border border-emerald-200/60'"
                                      style="font-size: 9.5px; font-weight: 600; padding: 1px 6px; border-radius: 4px;"
                                      x-text="msg.sender_badge"></span>
                            </div>

                            <!-- Kartu Balasan Tertaut (Quote Embedded Card) -->
                            <template x-if="hasReplyTo(msg)">
                                <div 
                                    @click="scrollToMessage(msg.reply_to.id)"
                                    class="bbpom-quote-card mb-2 p-2 rounded-xl text-left"
                                    style="background-color: #f1f5f9; border-left: 3px solid #059669; border-radius: 0.5rem; padding: 0.375rem 0.625rem; cursor: pointer;"
                                    :title="'Menuju pesan kutipan #' + msg.reply_to.id"
                                >
                                    <div class="flex items-center gap-1.5 leading-none mb-1" style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem;">
                                        <i class="ri-reply-fill text-xs" style="color: #047857; font-size: 0.75rem;"></i>
                                        <span class="bbpom-quote-user text-[11px] font-bold" style="font-size: 11px; font-weight: 700; color: #065f46;" x-text="msg.reply_to.user_name"></span>
                                        <span class="text-[9px] text-slate-500 font-semibold" style="font-size: 9px; color: #64748b;" x-text="'(' + msg.reply_to.user_role + ')'"></span>
                                    </div>
                                    <template x-if="msg.reply_to.has_attachment">
                                        <div class="flex items-center gap-1 text-[11px] text-slate-700 font-medium mb-0.5" style="display: flex; align-items: center; gap: 0.25rem; font-size: 11px;">
                                            <i :class="msg.reply_to.attachment_icon" :style="'color: ' + msg.reply_to.attachment_color"></i>
                                            <span class="truncate" x-text="msg.reply_to.attachment_name"></span>
                                        </div>
                                    </template>
                                    <p class="text-[11px] text-slate-600 truncate leading-tight" style="font-size: 11px; color: #475569; margin: 0; line-height: 1.25;" x-text="msg.reply_to.snippet"></p>
                                </div>
                            </template>

                            <template x-if="hasText(msg)">
                                <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line" style="font-size: 0.8125rem; color: #1e293b; line-height: 1.4; margin: 0.25rem 0; white-space: pre-line;" x-text="msg.response_text"></p>
                            </template>

                            <!-- Lampiran Berkas Format WhatsApp Document -->
                            <template x-if="hasAttachments(msg)">
                                <div class="mt-2 space-y-2" style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                    <template x-for="att in msg.attachments" :key="att.id">
                                        <div class="bbpom-doc-card rounded-xl p-2.5 sm:p-3 shadow-2xs flex items-center justify-between gap-3 max-w-sm transition-all hover:shadow-xs group/att"
                                             :style="'display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 0.75rem !important; width: 100% !important; max-width: 24rem !important; background-color: ' + att.document_card_bg_hex + ' !important; border: 1px solid ' + att.document_card_border_hex + ' !important; border-radius: 0.75rem !important; padding: 0.625rem 0.75rem !important; box-sizing: border-box !important;'">
                                            <a :href="att.download_url" class="flex items-center gap-2.5 min-w-0 flex-1 text-left no-underline group-hover/att:opacity-95" 
                                               style="display: flex !important; align-items: center !important; gap: 0.625rem !important; min-width: 0 !important; flex: 1 1 0% !important; text-decoration: none !important; color: inherit !important;" 
                                               :title="'Unduh ' + att.file_name">
                                                <div class="w-10 h-10 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center"
                                                     :style="'width: 2.5rem !important; height: 2.5rem !important; border-radius: 0.75rem !important; background-color: ' + att.document_hex_color + ' !important; color: #ffffff !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;'">
                                                    <i :class="att.pure_icon_class" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
                                                    <span style="font-size: 8.5px !important; font-weight: 800 !important; color: #ffffff !important; text-transform: uppercase !important; margin-top: 2px !important; line-height: 1 !important;" x-text="att.extension_upper"></span>
                                                </div>
                                                <div class="min-w-0 flex-1" style="min-width: 0 !important; flex: 1 1 0% !important; overflow: hidden !important;">
                                                    <p class="bbpom-doc-name text-xs sm:text-[13px] font-bold text-slate-800 group-hover/att:text-emerald-700 truncate block leading-tight transition-colors" 
                                                       style="font-size: 0.8125rem !important; font-weight: 700 !important; color: #1e293b !important; margin: 0 !important; line-height: 1.25 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important;" 
                                                       :title="att.file_name" x-text="att.file_name"></p>
                                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" 
                                                         style="display: flex !important; align-items: center !important; gap: 0.375rem !important; font-size: 10.5px !important; color: #64748b !important; margin-top: 0.25rem !important;">
                                                        <span x-text="att.formatted_size"></span>
                                                        <span style="color: #cbd5e1;">•</span>
                                                        <span style="text-transform: uppercase; font-weight: 600; color: #475569;" x-text="att.extension_upper"></span>
                                                        <template x-if="att.is_compressed">
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full border border-emerald-200/60" 
                                                                  style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 700 !important; color: #047857 !important; background-color: #ecfdf5 !important; border: 1px solid #a7f3d0 !important; padding: 1px 6px !important; border-radius: 9999px !important;" 
                                                                  :title="'Ukuran asli: ' + att.formatted_original_size">
                                                                <i class="ri-flashlight-line"></i>
                                                                <span x-text="'-' + att.saving_percentage + '%'"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="!att.is_compressed">
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-full border border-slate-200" 
                                                                  style="display: inline-flex !important; align-items: center !important; gap: 2px !important; font-size: 10px !important; font-weight: 600 !important; color: #475569 !important; background-color: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; padding: 1px 6px !important; border-radius: 9999px !important;">
                                                                <i class="ri-hd-line" style="color: #94a3b8;"></i>
                                                                <span>Asli</span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </a>
                                            <a :href="att.download_url"
                                               class="bbpom-chat-download-btn shrink-0 w-9 h-9 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all shadow-xs hover:scale-105 active:scale-95"
                                               style="width: 2.25rem !important; height: 2.25rem !important; border-radius: 9999px !important; background-color: #059669 !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; margin-left: 0.5rem !important; text-decoration: none !important; box-shadow: 0 2px 5px -1px rgba(5, 150, 105, 0.35) !important;"
                                               :title="'Unduh ' + att.file_name">
                                                <i class="ri-download-2-line text-base" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important;"></i>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <div class="text-[10px] text-slate-400 text-right mt-1.5 font-mono" style="font-size: 10px; color: #94a3b8; text-align: right; font-family: monospace;" x-text="msg.time"></div>
                        </div>

                        <!-- Tombol Balas Pesan (Hover / Touch) -->
                        <button 
                            type="button" 
                            @click="setReply({ id: msg.id, userName: msg.user_name, text: (msg.response_text || 'Lampiran Berkas') })"
                            class="bbpom-reply-trigger"
                            title="Balas pesan ini"
                        >
                            <i class="ri-reply-line text-base"></i>
                        </button>
                    </div>
                </template>
            </div>
        </template>
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

        <!-- Banner Kutipan Balasan (Quote Reply Preview Bar) -->
        <div 
            x-show="hasReplyTarget()" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="mb-3 p-2.5 sm:p-3 rounded-2xl bg-emerald-50/90 border border-emerald-200/90 flex items-center justify-between gap-3 shadow-2xs"
            style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-left: 4px solid #059669; border-radius: 0.875rem; padding: 0.625rem 0.875rem; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;"
        >
            <div class="flex items-center gap-2.5 min-w-0 flex-1" style="display: flex; align-items: center; gap: 0.625rem; min-width: 0; flex: 1;">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center shrink-0" style="width: 2rem; height: 2.5rem; border-radius: 0.5rem; background-color: #d1fae5; border: 1px solid #86efac; color: #065f46; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="ri-reply-fill text-sm" style="font-size: 0.875rem;"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[11.5px] font-bold text-emerald-900 truncate block leading-tight" style="font-size: 11.5px; font-weight: 700; color: #065f46; margin: 0; line-height: 1.25;" x-text="replyAuthorText()"></span>
                    <p class="text-xs text-slate-600 truncate mt-0.5 leading-snug" style="font-size: 0.75rem; color: #475569; margin: 0.125rem 0 0 0; line-height: 1.25;" x-text="replySnippetText()"></p>
                </div>
            </div>
            <button 
                type="button" 
                @click="cancelReply()" 
                class="bbpom-action-circle-btn bbpom-cancel-reply-btn w-7 h-7 rounded-full text-emerald-800 flex items-center justify-center cursor-pointer shrink-0 border-0 bg-transparent"
                style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #065f46; background: transparent; border: none;"
                title="Batalkan Balasan"
            >
                <i class="ri-close-line text-lg" style="font-size: 1.125rem;"></i>
            </button>
        </div>

        <!-- Floating Attachment Pill Preview -->
        <div 
            x-show="hasFileSelected()" 
            class="bbpom-file-preview-card mb-3 p-3 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all"
            :style="'background-color: ' + fileColorBgHex() + ' !important; border: 1px solid ' + fileColorBorderHex() + ' !important; border-radius: 1rem; padding: 0.75rem 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; gap: 0.75rem !important; flex-wrap: wrap !important; box-sizing: border-box !important; width: 100% !important;'"
        >
            <div class="bbpom-file-preview-info flex items-center gap-2.5 min-w-0" style="display: flex !important; align-items: center !important; gap: 0.625rem !important; min-width: 0 !important; flex: 1 1 auto !important;">
                <div 
                    class="w-10 h-10 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center" 
                    :style="'width: 2.5rem !important; height: 2.5rem !important; border-radius: 0.75rem !important; background-color: ' + fileColorHex() + ' !important; color: #ffffff !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;'"
                >
                    <i :class="fileIconClass()" style="font-size: 1rem !important; color: #ffffff !important; line-height: 1 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;"></i>
                    <span style="font-size: 8.5px !important; font-weight: 800 !important; color: #ffffff !important; text-transform: uppercase !important; margin-top: 2px !important; line-height: 1 !important;" x-text="fileExtensionUpper()"></span>
                </div>
                <div class="min-w-0 flex-1" style="min-width: 0 !important; flex: 1 1 auto !important; overflow: hidden !important;">
                    <p class="text-xs sm:text-[13px] font-bold text-slate-800 truncate" style="font-size: 0.8125rem !important; font-weight: 700 !important; color: #1e293b !important; margin: 0 !important; line-height: 1.3 !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important;" x-text="fileName"></p>
                    <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" style="display: flex !important; align-items: center !important; gap: 0.375rem !important; font-size: 10.5px !important; color: #64748b !important; margin-top: 0.25rem !important; line-height: 1 !important;">
                        <span x-text="fileSizeFormatted"></span>
                        <span style="color: #cbd5e1; margin: 0 2px !important;">•</span>
                        <span style="text-transform: uppercase !important; font-weight: 600 !important; color: #475569;" x-text="fileExtensionUpper()"></span>
                    </div>
                </div>
            </div>

            <!-- Segmented Mode Switcher Pill & Cancel Button -->
            <div class="bbpom-file-preview-actions flex items-center gap-2 shrink-0" style="display: flex !important; align-items: center !important; gap: 0.5rem !important; flex-shrink: 0 !important; margin-left: auto !important;">
                <div class="bbpom-segmented-switcher inline-flex p-0.5 rounded-xl bg-white/90 border border-slate-200 text-[11px] font-semibold shadow-2xs" style="display: inline-flex !important; align-items: center !important; padding: 3px !important; border-radius: 0.625rem !important; background-color: rgba(255, 255, 255, 0.95) !important; border: 1px solid #cbd5e1 !important; font-size: 11px !important; font-weight: 600 !important; gap: 2px !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;">
                    <button 
                        type="button" 
                        @click="setMode('compressed')" 
                        :style="isCompressedMode() ? 'background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;' : 'background-color: transparent !important; color: #475569 !important;'"
                        class="bbpom-mode-pill"
                        style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 0.35rem !important; padding: 0.25rem 0.625rem !important; border-radius: 0.5rem !important; font-size: 11px !important; font-weight: 600 !important; border: none !important; cursor: pointer !important; line-height: 1.25 !important; white-space: nowrap !important; transition: all 0.15s ease !important; font-family: inherit !important;"
                        title="Kompresi cerdas hemat kapasitas penyimpanan database & server"
                    >
                        <i class="ri-sparkling-fill" style="font-size: 0.8rem !important; line-height: 1 !important; display: inline-flex !important; align-items: center !important;"></i>
                        <span>Kompres Cerdas</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setMode('original')" 
                        :style="isOriginalMode() ? 'background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;' : 'background-color: transparent !important; color: #475569 !important;'"
                        class="bbpom-mode-pill"
                        style="display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 0.35rem !important; padding: 0.25rem 0.625rem !important; border-radius: 0.5rem !important; font-size: 11px !important; font-weight: 600 !important; border: none !important; cursor: pointer !important; line-height: 1.25 !important; white-space: nowrap !important; transition: all 0.15s ease !important; font-family: inherit !important;"
                        title="Kirim ukuran & resolusi asli tanpa kompresi"
                    >
                        <i class="ri-hd-line" style="font-size: 0.8rem !important; line-height: 1 !important; display: inline-flex !important; align-items: center !important;"></i>
                        <span>Ukuran Asli</span>
                    </button>
                </div>

                <button 
                    type="button" 
                    @click="clearFile()" 
                    class="bbpom-action-circle-btn bbpom-clear-file-btn"
                    style="width: 1.75rem !important; height: 1.75rem !important; border-radius: 9999px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; background-color: rgba(255, 255, 255, 0.95) !important; border: 1px solid #cbd5e1 !important; color: #64748b !important; padding: 0 !important; flex-shrink: 0 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; transition: all 0.15s ease !important;"
                    title="Batalkan lampiran berkas"
                >
                    <i class="ri-close-line" style="font-size: 1.1rem !important; line-height: 1 !important; color: #64748b !important;"></i>
                </button>
            </div>
        </div>

        <!-- Input Baris Chat & Tombol Lampiran -->
        <div class="flex items-center gap-2.5" style="display: flex; align-items: center; gap: 0.625rem;">
            <!-- Label Klip Lampiran Berkas (Memicu File Chooser secara Native & Handled) -->
            <label 
                for="ticket_conversation_file_input_{{ $ticket->id }}" 
                @click.prevent="triggerFilePicker()"
                role="button" 
                class="bbpom-clip-btn w-11 h-11 rounded-full border border-slate-200/90 bg-slate-50 text-slate-600 flex items-center justify-center shrink-0 cursor-pointer"
                style="width: 2.75rem; height: 2.75rem; border-radius: 9999px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer;"
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
                    class="bbpom-chat-textarea w-full rounded-2xl border-slate-300 py-3 px-4 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 shadow-2xs resize-none max-h-32 overflow-hidden"
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
                class="bbpom-send-btn w-11 h-11 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                style="width: 2.75rem; height: 2.75rem; border-radius: 9999px; background-color: #059669; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: none; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"
                title="Kirim Pesan"
            >
                <i x-show="!isSubmitting" class="ri-send-plane-fill text-lg" style="font-size: 1.125rem; color: #ffffff !important; line-height: 1; display: inline-flex; align-items: center; justify-content: center;"></i>
                <i x-show="isSubmitting" class="ri-loader-4-line text-lg bbpom-spinner-rotate" style="font-size: 1.125rem; color: #ffffff !important; line-height: 1; display: inline-flex; align-items: center; justify-content: center;"></i>
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

    <!-- Bar Masukan Pesan & Lampiran Berkas -->
    <div 
        id="ticketConversationInputArea" 
        class="mt-4 pt-3 border-t border-slate-100"
        style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;"
    >
        <!-- Input File Asli & Tersembunyi (Mendukung Multi-file) -->
        <input 
            type="file" 
            x-ref="fileInput" 
            id="ticket_conversation_file_input_{{ $ticket->id }}" 
            name="attachments[]" 
            multiple
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
            x-cloak
            x-show="hasFileSelected()" 
            class="bbpom-file-preview-card mb-3 p-3 rounded-2xl shadow-xs transition-all"
            :style="hasFileSelected() ? ('background-color: ' + fileColorBgHex() + ' !important; border: 1px solid ' + fileColorBorderHex() + ' !important;') : ''"
        >
            <!-- Baris Utama (Info & Aksi Selalu Sejajar Rapi) -->
            <div class="bbpom-file-preview-top-row">
                <div class="bbpom-file-preview-info">
                    <!-- Icon Box dengan Mini Counter Badge untuk Multi-file -->
                    <div class="relative shrink-0" style="position: relative; flex-shrink: 0;">
                        <div 
                            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-2xs leading-none p-1 text-center" 
                            :style="'width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; background-color: ' + fileColorHex() + ' !important; color: #ffffff !important; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.12);'"
                        >
                            <i :class="fileIconClass()" style="font-size: 1.25rem; color: #ffffff; line-height: 1; display: inline-flex; align-items: center; justify-content: center;"></i>
                        </div>
                        <template x-if="isMultipleFiles()">
                            <span 
                                class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-amber-600 text-white text-[9px] font-black flex items-center justify-center shadow-xs border-2 border-white"
                                style="position: absolute; top: -0.25rem; right: -0.25rem; width: 1.125rem; height: 1.125rem; border-radius: 9999px; background-color: #d97706; color: #ffffff; font-size: 9px; font-weight: 900; display: flex; align-items: center; justify-content: center; border: 1.5px solid #ffffff; line-height: 1;"
                                x-text="fileCount"
                            ></span>
                        </template>
                    </div>

                    <div class="min-w-0 flex-1" style="min-width: 0; flex: 1 1 auto; overflow: hidden;">
                        <div class="flex items-center gap-1.5 flex-wrap" style="display: flex; align-items: center; gap: 0.375rem;">
                            <p class="text-xs sm:text-[13px] font-bold text-slate-800 truncate" style="font-size: 0.8125rem; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="fileName"></p>
                            <template x-if="isMultipleFiles()">
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300/70" style="font-size: 10px; font-weight: 700; background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; border-radius: 9999px; padding: 1px 6px; line-height: 1;">
                                    <i class="ri-folder-zip-line text-[11px]" style="font-size: 11px;"></i>
                                    <span>Otomatis ZIP</span>
                                </span>
                            </template>
                            <template x-if="!isMultipleFiles() && isAboveOneMb()">
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300/70" style="font-size: 10px; font-weight: 700; background-color: #d1fae5; color: #065f46; border: 1px solid #86efac; border-radius: 9999px; padding: 1px 6px; line-height: 1;">
                                    <i class="ri-flashlight-line text-[11px]" style="font-size: 11px;"></i>
                                    <span>Auto-ZIP (>1MB)</span>
                                </span>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1 flex-wrap text-[10.5px] text-slate-500 font-medium leading-none" style="display: flex; align-items: center; gap: 0.375rem; font-size: 10.5px; color: #64748b; margin-top: 0.25rem; line-height: 1;">
                            <span x-text="fileSizeFormatted"></span>
                            <span style="color: #cbd5e1; margin: 0 2px;">•</span>
                            <span style="text-transform: uppercase; font-weight: 600; color: #475569;" x-text="isMultipleFiles() ? (fileCount + ' Berkas Siap Dikompres') : fileExtensionUpper()"></span>
                        </div>
                    </div>
                </div>

                <!-- Segmented Mode Switcher Pill & Cancel Button -->
                <div class="bbpom-file-preview-actions">
                    <template x-if="!isMultipleFiles()">
                        <div class="bbpom-segmented-switcher">
                            <button 
                                type="button" 
                                @click="setMode('compressed')" 
                                :style="isCompressedMode() ? 'background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;' : 'background-color: transparent !important; color: #475569 !important;'"
                                class="bbpom-mode-pill"
                                title="Kompresi cerdas hemat kapasitas penyimpanan database & server"
                            >
                                <i class="ri-sparkling-fill" style="font-size: 0.8rem; line-height: 1; display: inline-flex; align-items: center;"></i>
                                <span>Kompres Cerdas</span>
                            </button>
                            <button 
                                type="button" 
                                @click="setMode('original')" 
                                :style="isOriginalMode() ? 'background-color: #059669 !important; color: #ffffff !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;' : 'background-color: transparent !important; color: #475569 !important;'"
                                class="bbpom-mode-pill"
                                title="Kirim ukuran & resolusi asli tanpa kompresi"
                            >
                                <i class="ri-hd-line" style="font-size: 0.8rem; line-height: 1; display: inline-flex; align-items: center;"></i>
                                <span>Ukuran Asli</span>
                            </button>
                        </div>
                    </template>

                    <template x-if="isMultipleFiles()">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-2xs" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.35rem 0.75rem; border-radius: 9999px; background-color: #ffffff; border: 1px solid #e2e8f0; font-size: 11px; font-weight: 600; color: #334155; box-shadow: 0 1px 2px rgba(0,0,0,0.04); white-space: nowrap;">
                            <i class="ri-archive-line text-amber-600" style="color: #d97706;"></i>
                            <span>Auto-ZIP Aktif</span>
                        </div>
                    </template>

                    <button 
                        type="button" 
                        @click="clearFile()" 
                        class="bbpom-action-circle-btn bbpom-clear-file-btn"
                        title="Batalkan lampiran berkas"
                    >
                        <i class="ri-close-line" style="font-size: 1.15rem; line-height: 1; color: inherit;"></i>
                    </button>
                </div>
            </div>

            <!-- Baris 2: Mini File Stack Chips (Khusus Multi-file) -->
            <template x-if="isMultipleFiles()">
                <div class="bbpom-file-chips-container">
                    <template x-for="(f, idx) in selectedFiles" :key="idx">
                        <div class="bbpom-file-chip" :title="f.name + ' (' + f.sizeFormatted + ')'" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.5rem; border-radius: 0.375rem; background-color: #ffffff; border: 1px solid #e2e8f0; font-size: 11px; font-weight: 500; color: #334155; max-width: 220px; min-width: 0; line-height: 1.25; white-space: nowrap; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);">
                            <i class="ri-file-line text-slate-400 shrink-0" style="color: #94a3b8; font-size: 11px; flex-shrink: 0;"></i>
                            <span class="bbpom-file-chip-name" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0; flex: 1 1 auto;" x-text="f.name"></span>
                            <span class="bbpom-file-chip-size" style="color: #64748b; font-size: 9.5px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; white-space: nowrap !important; flex-shrink: 0 !important; display: inline-block; line-height: 1; margin-left: 0.25rem; background-color: #f1f5f9; padding: 0.125rem 0.35rem; border-radius: 0.25rem; border: 1px solid #e2e8f0;" x-text="f.sizeFormatted"></span>
                        </div>
                    </template>
                </div>
            </template>
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

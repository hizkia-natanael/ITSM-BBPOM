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
            <!-- Multi-file Responsive Grid -->
            <div class="bbpom-att-grid grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                @foreach($attachments as $att)
                    @php
                        $ext = strtolower($att->file_extension);
                        $typeCategory = match(true) {
                            $att->is_image => 'image',
                            $att->is_pdf => 'pdf',
                            in_array($ext, ['doc', 'docx']) => 'doc',
                            in_array($ext, ['xls', 'xlsx', 'csv']) => 'sheet',
                            in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz']) => 'zip',
                            default => 'other',
                        };
                        $pureIcon = match(true) {
                            $att->is_image => 'ri-image-2-line',
                            $att->is_pdf => 'ri-file-pdf-2-line',
                            in_array($ext, ['doc', 'docx']) => 'ri-file-word-2-line',
                            in_array($ext, ['xls', 'xlsx', 'csv']) => 'ri-file-excel-2-line',
                            in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz']) => 'ri-file-zip-line',
                            default => 'ri-file-text-line',
                        };
                    @endphp
                    <div class="bbpom-att-card p-3.5 rounded-2xl border border-slate-200/90 bg-slate-50/70 hover:bg-white transition-all flex flex-col justify-between gap-3 shadow-xs">
                        <div class="bbpom-att-info-row flex items-start gap-3 min-w-0">
                            @if($att->is_image || $att->is_pdf)
                                <a 
                                    href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}" 
                                    target="_blank" 
                                    wire:ignore
                                    class="bbpom-att-icon-box bbpom-att-type-{{ $typeCategory }} w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border border-slate-200/80 bg-white shadow-xs cursor-pointer"
                                    title="Buka dokumen {{ $att->file_name }}"
                                >
                                    <i class="{{ $pureIcon }} text-xl"></i>
                                </a>
                            @else
                                <a 
                                    href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att]) }}" 
                                    target="_blank" 
                                    download 
                                    wire:ignore
                                    class="bbpom-att-icon-box bbpom-att-type-{{ $typeCategory }} w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border border-slate-200/80 bg-white shadow-xs cursor-pointer"
                                    title="Unduh berkas {{ $att->file_name }}"
                                >
                                    <i class="{{ $pureIcon }} text-xl"></i>
                                </a>
                            @endif

                            <div class="bbpom-att-meta-wrap min-w-0 flex-1">
                                @if($att->is_image || $att->is_pdf)
                                    <a 
                                        href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}" 
                                        target="_blank" 
                                        wire:ignore
                                        class="bbpom-att-name font-bold text-xs text-slate-900 truncate block hover:underline" 
                                        title="{{ $att->file_name }}"
                                    >
                                        {{ $att->file_name }}
                                    </a>
                                @else
                                    <a 
                                        href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att]) }}" 
                                        target="_blank" 
                                        download 
                                        wire:ignore
                                        class="bbpom-att-name font-bold text-xs text-slate-900 truncate block hover:underline" 
                                        title="{{ $att->file_name }}"
                                    >
                                        {{ $att->file_name }}
                                    </a>
                                @endif
                                <div class="bbpom-att-tags-row flex items-center gap-2 mt-1">
                                    <span class="bbpom-att-ext-badge bbpom-att-badge-{{ $typeCategory }} px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $att->badge_class }}">
                                        {{ $att->file_extension }}
                                    </span>
                                    <span class="bbpom-att-size-text text-[11px] text-slate-400 font-medium">
                                        {{ $att->formatted_size }}
                                    </span>
                                </div>
                            </div>
                        </div>


                        <!-- Action Buttons -->
                        <div class="bbpom-att-actions-row flex items-center gap-2 pt-2 border-t border-slate-200/60">
                            @if($att->is_image || $att->is_pdf)
                                <a 
                                    href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}" 
                                    target="_blank" 
                                    wire:ignore
                                    class="bbpom-att-btn-preview inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs"
                                >
                                    <i class="ri-external-link-line text-xs"></i>
                                    <span>Buka Dokumen</span>
                                </a>
                                <a 
                                    href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att]) }}" 
                                    target="_blank" 
                                    download 
                                    wire:ignore
                                    class="bbpom-att-btn-download inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs"
                                >
                                    <i class="ri-download-2-line text-xs"></i>
                                    <span>Unduh</span>
                                </a>
                            @else
                                <a 
                                    href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att]) }}" 
                                    target="_blank" 
                                    download 
                                    wire:ignore
                                    class="bbpom-att-btn-download inline-flex items-center gap-1 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs"
                                >
                                    <i class="ri-download-2-line text-xs"></i>
                                    <span>Unduh / Buka Dokumen</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($hasLegacy)
            @php
                $ext = strtolower(pathinfo($legacyPath, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                $isPdf = $ext === 'pdf';
                $isDoc = in_array($ext, ['doc', 'docx']);
                $isSheet = in_array($ext, ['xls', 'xlsx', 'csv']);
                $isZip = in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz']);

                $typeCategory = match(true) {
                    $isImage => 'image',
                    $isPdf => 'pdf',
                    $isDoc => 'doc',
                    $isSheet => 'sheet',
                    $isZip => 'zip',
                    default => 'other',
                };

                $pureIcon = match(true) {
                    $isImage => 'ri-image-2-line',
                    $isPdf => 'ri-file-pdf-2-line',
                    $isDoc => 'ri-file-word-2-line',
                    $isSheet => 'ri-file-excel-2-line',
                    $isZip => 'ri-file-zip-line',
                    default => 'ri-file-text-line',
                };
                $fileName = basename($legacyPath);
            @endphp

            <div class="bbpom-att-grid grid grid-cols-1 gap-3.5">
                <div class="bbpom-att-card p-4 rounded-2xl border border-slate-200/90 bg-slate-50/70 hover:bg-white transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="bbpom-att-info-row flex items-center gap-3.5">
                            @if($isImage || $isPdf)
                                <a 
                                    href="{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}" 
                                    target="_blank" 
                                    wire:ignore
                                    class="bbpom-att-icon-box bbpom-att-type-{{ $typeCategory }} w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-xs cursor-pointer"
                                    title="Buka dokumen {{ $fileName }}"
                                >
                                    <i class="{{ $pureIcon }} text-2xl"></i>
                                </a>
                            @else
                                <a 
                                    href="{{ route('tickets.attachment', $ticket) }}" 
                                    target="_blank" 
                                    download 
                                    wire:ignore
                                    class="bbpom-att-icon-box bbpom-att-type-{{ $typeCategory }} w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-xs cursor-pointer"
                                    title="Unduh berkas {{ $fileName }}"
                                >
                                    <i class="{{ $pureIcon }} text-2xl"></i>
                                </a>
                            @endif

                            <div class="bbpom-att-meta-wrap">
                                <div class="flex items-center gap-2">
                                    @if($isImage || $isPdf)
                                        <a 
                                            href="{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}" 
                                            target="_blank" 
                                            wire:ignore
                                            class="bbpom-att-name font-bold text-xs sm:text-sm text-slate-900 truncate max-w-xs sm:max-w-md block hover:underline" 
                                            title="{{ $fileName }}"
                                        >
                                            {{ $fileName }}
                                        </a>
                                    @else
                                        <a 
                                            href="{{ route('tickets.attachment', $ticket) }}" 
                                            target="_blank" 
                                            download 
                                            wire:ignore
                                            class="bbpom-att-name font-bold text-xs sm:text-sm text-slate-900 truncate max-w-xs sm:max-w-md block hover:underline" 
                                            title="{{ $fileName }}"
                                        >
                                            {{ $fileName }}
                                        </a>
                                    @endif
                                    <span class="bbpom-att-ext-badge bbpom-att-badge-{{ $typeCategory }} px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase">
                                        {{ $ext }}
                                    </span>
                                </div>
                                <p class="bbpom-att-size-text text-[11px] text-slate-500 mt-0.5">
                                    Dokumen lampiran tersimpan secara aman di server ITSM BBPOM
                                </p>
                            </div>
                        </div>

                        <div class="bbpom-att-actions-row flex items-center gap-2" style="border-top: none; padding-top: 0; margin-top: 0; width: auto;">
                            @if($isImage || $isPdf)
                                <a 
                                    href="{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}" 
                                    target="_blank" 
                                    wire:ignore
                                    class="bbpom-att-btn-preview inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs"
                                >
                                    <i class="ri-external-link-line text-xs"></i>
                                    <span>Buka Dokumen</span>
                                </a>
                                <a 
                                    href="{{ route('tickets.attachment', $ticket) }}" 
                                    target="_blank" 
                                    download 
                                    wire:ignore
                                    class="bbpom-att-btn-download inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs"
                                >
                                    <i class="ri-download-2-line text-xs"></i>
                                    <span>Unduh</span>
                                </a>
                            @else
                                <a 
                                    href="{{ route('tickets.attachment', $ticket) }}" 
                                    target="_blank" 
                                    download 
                                    wire:ignore
                                    class="bbpom-att-btn-download inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs"
                                >
                                    <i class="ri-download-2-line text-xs"></i>
                                    <span>Unduh / Buka Dokumen</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

<style>
/* BBPOM Ticket Attachment Card UI Specification Styles */
.bbpom-att-wrapper {
    width: 100% !important;
    box-sizing: border-box !important;
}
.bbpom-att-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 0.875rem !important;
}
.bbpom-att-header-title {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    color: #475569 !important;
    margin: 0 !important;
}
.bbpom-att-header-title i {
    color: #047857 !important;
    font-size: 0.95rem !important;
}
.bbpom-att-header-badge {
    display: inline-flex !important;
    align-items: center !important;
    padding: 0.15rem 0.55rem !important;
    border-radius: 9999px !important;
    font-size: 0.65rem !important;
    font-weight: 700 !important;
    background-color: #ecfdf5 !important;
    color: #065f46 !important;
    border: 1px solid #a7f3d0 !important;
    letter-spacing: normal !important;
    line-height: 1.25 !important;
}
.bbpom-att-grid {
    display: grid !important;
    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
    gap: 0.875rem !important;
    width: 100% !important;
}
@media (min-width: 640px) {
    .bbpom-att-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}
.bbpom-att-card {
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    gap: 0.75rem !important;
    padding: 0.875rem 1rem !important;
    border-radius: 1rem !important;
    border: 1px solid rgba(203, 213, 225, 0.85) !important;
    background-color: #f8fafc !important;
    box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 1px 2px -1px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-sizing: border-box !important;
    position: relative !important;
    overflow: hidden !important;
}
.bbpom-att-card:hover {
    border-color: #6ee7b7 !important;
    background-color: #ffffff !important;
    box-shadow: 0 4px 14px -2px rgba(5, 150, 105, 0.12), 0 2px 6px -1px rgba(15, 23, 42, 0.05) !important;
    transform: translateY(-1.5px) !important;
}
.bbpom-att-info-row {
    display: flex !important;
    align-items: flex-start !important;
    gap: 0.75rem !important;
    min-width: 0 !important;
    width: 100% !important;
}
.bbpom-att-icon-box {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 2.625rem !important;
    height: 2.625rem !important;
    border-radius: 0.75rem !important;
    border: 1px solid #e2e8f0 !important;
    background-color: #ffffff !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
    flex-shrink: 0 !important;
    font-size: 1.35rem !important;
    line-height: 1 !important;
    transition: transform 0.2s ease !important;
    text-decoration: none !important;
}
.bbpom-att-card:hover .bbpom-att-icon-box {
    transform: scale(1.05) !important;
}
.bbpom-att-meta-wrap {
    min-width: 0 !important;
    flex: 1 1 0% !important;
}
.bbpom-att-name {
    font-size: 0.8125rem !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    margin: 0 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    display: block !important;
    transition: color 0.15s ease !important;
    text-decoration: none !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
}
.bbpom-att-card:hover .bbpom-att-name {
    color: #047857 !important;
}
.bbpom-att-tags-row {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    margin-top: 0.35rem !important;
}
.bbpom-att-ext-badge {
    display: inline-flex !important;
    align-items: center !important;
    padding: 0.125rem 0.45rem !important;
    border-radius: 0.375rem !important;
    font-size: 0.625rem !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    border: 1px solid !important;
    line-height: 1 !important;
}
.bbpom-att-size-text {
    font-size: 0.6875rem !important;
    font-weight: 500 !important;
    color: #64748b !important;
    line-height: 1 !important;
}

/* Image Thumbnail Container */
.bbpom-att-thumb-wrap {
    width: 100% !important;
    margin-top: 0.25rem !important;
    border-radius: 0.625rem !important;
    overflow: hidden !important;
    border: 1px solid #e2e8f0 !important;
    background-color: #ffffff !important;
}
.bbpom-att-thumb-btn {
    position: relative !important;
    display: block !important;
    width: 100% !important;
    height: 7.5rem !important;
    padding: 0 !important;
    border: none !important;
    background: transparent !important;
    cursor: zoom-in !important;
    overflow: hidden !important;
}
.bbpom-att-thumb-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform 0.25s ease, opacity 0.25s ease !important;
}
.bbpom-att-thumb-btn:hover .bbpom-att-thumb-img {
    transform: scale(1.04) !important;
    opacity: 0.9 !important;
}
.bbpom-att-thumb-overlay {
    position: absolute !important;
    inset: 0 !important;
    background-color: rgba(15, 23, 42, 0.4) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 0 !important;
    transition: opacity 0.2s ease !important;
}
.bbpom-att-thumb-btn:hover .bbpom-att-thumb-overlay {
    opacity: 1 !important;
}
.bbpom-att-thumb-overlay span {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
    padding: 0.35rem 0.75rem !important;
    border-radius: 0.5rem !important;
    background-color: rgba(0, 0, 0, 0.7) !important;
    color: #ffffff !important;
    font-size: 0.6875rem !important;
    font-weight: 600 !important;
    backdrop-filter: blur(4px) !important;
}

/* Actions Footer Bar */
.bbpom-att-actions-row {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    padding-top: 0.65rem !important;
    margin-top: auto !important;
    border-top: 1px solid rgba(226, 232, 240, 0.75) !important;
    width: 100% !important;
}
.bbpom-att-btn-preview {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.35rem !important;
    padding: 0.4rem 0.8rem !important;
    border-radius: 0.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    background-color: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #334155 !important;
    text-decoration: none !important;
    cursor: pointer !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
    transition: all 0.15s ease-in-out !important;
    line-height: 1.25 !important;
}
.bbpom-att-btn-preview:hover {
    background-color: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #0f172a !important;
    transform: translateY(-1px) !important;
}
.bbpom-att-btn-download {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.35rem !important;
    padding: 0.4rem 0.95rem !important;
    border-radius: 0.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    background: linear-gradient(135deg, #047857 0%, #065f46 100%) !important;
    border: 1px solid rgba(5, 150, 105, 0.4) !important;
    color: #ffffff !important;
    text-decoration: none !important;
    cursor: pointer !important;
    box-shadow: 0 2px 4px -1px rgba(4, 120, 87, 0.3) !important;
    transition: all 0.15s ease-in-out !important;
    line-height: 1.25 !important;
}
.bbpom-att-btn-download:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    box-shadow: 0 4px 10px -1px rgba(4, 120, 87, 0.45) !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
}
.bbpom-att-btn-download:active {
    transform: translateY(0) !important;
}

/* Contextual Colors for Badges and Icons */
.bbpom-att-type-image i {
    color: #059669 !important;
}
.bbpom-att-badge-image {
    background-color: #ecfdf5 !important;
    color: #047857 !important;
    border-color: #a7f3d0 !important;
}
.bbpom-att-type-pdf i {
    color: #e11d48 !important;
}
.bbpom-att-badge-pdf {
    background-color: #fff1f2 !important;
    color: #be123c !important;
    border-color: #fecdd3 !important;
}
.bbpom-att-type-doc i {
    color: #2563eb !important;
}
.bbpom-att-badge-doc {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
    border-color: #bfdbfe !important;
}
.bbpom-att-type-sheet i {
    color: #047857 !important;
}
.bbpom-att-badge-sheet {
    background-color: #ecfdf5 !important;
    color: #065f46 !important;
    border-color: #a7f3d0 !important;
}
.bbpom-att-type-zip i {
    color: #d97706 !important;
}
.bbpom-att-badge-zip {
    background-color: #fffbeb !important;
    color: #b45309 !important;
    border-color: #fde68a !important;
}
.bbpom-att-type-other i {
    color: #475569 !important;
}
.bbpom-att-badge-other {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    border-color: #cbd5e1 !important;
}
</style>


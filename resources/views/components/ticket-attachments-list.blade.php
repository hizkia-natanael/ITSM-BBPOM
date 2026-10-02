@props(['ticket'])

@php
    $attachments = $ticket->attachments;
    $hasMultiple = $attachments->isNotEmpty();
    $hasLegacy = !empty($ticket->attachment);
@endphp

@if($hasMultiple || $hasLegacy)
    <div 
        class="mt-8 pt-6 border-t border-slate-100" 
        x-data="{ 
            lightboxOpen: false, 
            lightboxSrc: '', 
            lightboxTitle: '',
            openImage(src, title) {
                this.lightboxSrc = src;
                this.lightboxTitle = title;
                this.lightboxOpen = true;
            }
        }"
    >
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                <i class="ri-attachment-2 text-emerald-700 text-sm"></i>
                <span>Berkas Lampiran Pendukung</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                    {{ $hasMultiple ? $attachments->count() . ' Berkas' : '1 Berkas' }}
                </span>
            </h4>
        </div>

        @if($hasMultiple)
            <!-- Multi-file Responsive Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                @foreach($attachments as $att)
                    <div class="p-3.5 rounded-2xl border border-slate-200/90 bg-slate-50/70 hover:bg-slate-50/90 transition-all flex flex-col justify-between gap-3 shadow-xs">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border border-slate-200/80 bg-white shadow-xs">
                                <i class="{{ $att->icon_class }} text-xl"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-xs text-slate-900 truncate" title="{{ $att->file_name }}">
                                    {{ $att->file_name }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $att->badge_class }}">
                                        {{ $att->file_extension }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium">
                                        {{ $att->formatted_size }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60">
                            @if($att->is_image)
                                <button 
                                    type="button" 
                                    @click="openImage('{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}', '{{ addslashes($att->file_name) }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs"
                                >
                                    <i class="ri-eye-line text-xs"></i>
                                    <span>Pratinjau</span>
                                </button>
                            @elseif($att->is_pdf)
                                <a 
                                    href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs"
                                >
                                    <i class="ri-external-link-line text-xs"></i>
                                    <span>Buka Dokumen</span>
                                </a>
                            @endif

                            <a 
                                href="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att]) }}" 
                                download 
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs"
                            >
                                <i class="ri-download-2-line text-xs"></i>
                                <span>Unduh</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($hasLegacy)
            @php
                $ext = strtolower(pathinfo($ticket->attachment, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                $isPdf = $ext === 'pdf';
                $isDoc = in_array($ext, ['doc', 'docx']);
                $isZip = in_array($ext, ['zip', 'rar', '7z']);

                $fileIcon = match(true) {
                    $isImage => 'ri-image-2-line text-emerald-600',
                    $isPdf => 'ri-file-pdf-2-line text-rose-600',
                    $isDoc => 'ri-file-word-2-line text-blue-700',
                    $isZip => 'ri-file-zip-line text-amber-600',
                    default => 'ri-file-line text-slate-600',
                };
                $fileName = basename($ticket->attachment);
            @endphp

            <div class="p-4 rounded-2xl border border-slate-200/90 bg-slate-50/70 hover:bg-slate-50 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-xs">
                            <i class="{{ $fileIcon }} text-2xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-xs sm:text-sm text-slate-900 truncate max-w-xs sm:max-w-md" title="{{ $fileName }}">
                                    {{ $fileName }}
                                </p>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-200 text-slate-700">
                                    {{ $ext }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Dokumen lampiran tersimpan secara aman di server ITSM BBPOM
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($isImage)
                            <button 
                                type="button" 
                                @click="openImage('{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}', '{{ addslashes($fileName) }}')" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs"
                            >
                                <i class="ri-eye-line text-xs"></i>
                                <span>Pratinjau</span>
                            </button>
                        @endif

                        <a 
                            href="{{ route('tickets.attachment', $ticket) }}" 
                            target="_blank" 
                            download 
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs"
                        >
                            <i class="ri-download-2-line text-xs"></i>
                            <span>Unduh Berkas</span>
                        </a>
                    </div>
                </div>

                @if($isImage)
                    <div class="mt-4 pt-3 border-t border-slate-200/60">
                        <button 
                            type="button" 
                            @click="openImage('{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}', '{{ addslashes($fileName) }}')" 
                            class="group relative inline-block rounded-xl overflow-hidden border border-slate-200 bg-white hover:ring-2 hover:ring-emerald-500 transition-all cursor-zoom-in"
                        >
                            <img 
                                src="{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}" 
                                alt="Lampiran {{ $ticket->ticket_number }}" 
                                class="max-h-48 w-auto rounded-lg object-contain group-hover:opacity-90 transition-opacity"
                            >
                            <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-semibold transition-opacity">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-black/60">
                                    <i class="ri-zoom-in-line text-sm"></i>
                                    Perbesar Gambar
                                </span>
                            </div>
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Universal Alpine.js Lightbox Modal -->
        <div 
            x-show="lightboxOpen" 
            x-cloak 
            @keydown.escape.window="lightboxOpen = false"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.away="lightboxOpen = false" 
                class="relative bg-white rounded-2xl max-w-4xl w-full p-4 overflow-hidden shadow-2xl"
            >
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="ri-image-2-line text-emerald-700"></i>
                        <span class="text-xs font-bold text-slate-800" x-text="lightboxTitle"></span>
                    </div>
                    <button 
                        type="button" 
                        @click="lightboxOpen = false" 
                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition"
                    >
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>
                <div class="max-h-[75vh] overflow-auto flex items-center justify-center bg-slate-100/50 rounded-xl p-2">
                    <img 
                        :src="lightboxSrc" 
                        :alt="lightboxTitle" 
                        class="max-h-[70vh] w-auto rounded-lg object-contain shadow-sm"
                    >
                </div>
            </div>
        </div>
    </div>
@endif

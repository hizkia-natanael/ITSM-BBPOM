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
                            @if($isImage)
                                <button 
                                    type="button" 
                                    @click="openImage('{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($fileName, ENT_QUOTES)) }}')"
                                    class="bbpom-att-icon-box bbpom-att-type-{{ $typeCategory }} w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-xs cursor-pointer"
                                    title="Pratinjau {{ $fileName }}"
                                >
                                    <i class="{{ $pureIcon }} text-2xl"></i>
                                </button>
                            @elseif($isPdf)
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
                                    @if($isImage)
                                        <button 
                                            type="button" 
                                            @click="openImage('{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($fileName, ENT_QUOTES)) }}')"
                                            class="bbpom-att-name font-bold text-xs sm:text-sm text-slate-900 truncate max-w-xs sm:max-w-md text-left cursor-pointer hover:underline" 
                                            title="{{ $fileName }}"
                                        >
                                            {{ $fileName }}
                                        </button>
                                    @elseif($isPdf)
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
                            @if($isImage)
                                <button 
                                    type="button" 
                                    @click="openImage('{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($fileName, ENT_QUOTES)) }}')" 
                                    class="bbpom-att-btn-preview inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs cursor-pointer"
                                >
                                    <i class="ri-eye-line text-xs"></i>
                                    <span>Pratinjau</span>
                                </button>
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
                            @elseif($isPdf)
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

                    @if($isImage)
                        <div class="bbpom-att-thumb-wrap mt-4" style="max-width: 20rem;">
                            <button 
                                type="button" 
                                @click="openImage('{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($fileName, ENT_QUOTES)) }}')" 
                                class="bbpom-att-thumb-btn group relative"
                                title="Klik untuk memperbesar gambar"
                            >
                                <img 
                                    src="{{ route('tickets.attachment', ['ticket' => $ticket, 'inline' => 1]) }}" 
                                    alt="Lampiran {{ $ticket->ticket_number }}" 
                                    class="bbpom-att-thumb-img"
                                >
                                <div class="bbpom-att-thumb-overlay">
                                    <span>
                                        <i class="ri-zoom-in-line"></i>
                                        Perbesar Gambar
                                    </span>
                                </div>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

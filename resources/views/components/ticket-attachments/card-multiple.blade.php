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
                            @if($att->is_image)
                                <button 
                                    type="button" 
                                    @click="openImage('{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($att->file_name, ENT_QUOTES)) }}')"
                                    class="bbpom-att-icon-box bbpom-att-type-{{ $typeCategory }} w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border border-slate-200/80 bg-white shadow-xs cursor-pointer"
                                    title="Pratinjau {{ $att->file_name }}"
                                >
                                    <i class="{{ $pureIcon }} text-xl"></i>
                                </button>
                            @elseif($att->is_pdf)
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
                                @if($att->is_image)
                                    <button 
                                        type="button" 
                                        @click="openImage('{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($att->file_name, ENT_QUOTES)) }}')"
                                        class="bbpom-att-name font-bold text-xs text-slate-900 truncate text-left cursor-pointer hover:underline block w-full" 
                                        title="{{ $att->file_name }}"
                                    >
                                        {{ $att->file_name }}
                                    </button>
                                @elseif($att->is_pdf)
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

                        @if($att->is_image)
                            <div class="bbpom-att-thumb-wrap">
                                <button 
                                    type="button" 
                                    @click="openImage('{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($att->file_name, ENT_QUOTES)) }}')"
                                    class="bbpom-att-thumb-btn group relative"
                                    title="Klik untuk memperbesar {{ $att->file_name }}"
                                >
                                    <img 
                                        src="{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}" 
                                        alt="{{ $att->file_name }}" 
                                        class="bbpom-att-thumb-img" 
                                        loading="lazy"
                                    >
                                    <div class="bbpom-att-thumb-overlay">
                                        <span>
                                            <i class="ri-zoom-in-line"></i>
                                            Perbesar
                                        </span>
                                    </div>
                                </button>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="bbpom-att-actions-row flex items-center gap-2 pt-2 border-t border-slate-200/60">
                            @if($att->is_image)
                                <button 
                                    type="button" 
                                    @click="openImage('{{ route('tickets.attachments.item', ['ticket' => $ticket, 'attachment' => $att, 'inline' => 1]) }}', '{{ addslashes(htmlspecialchars($att->file_name, ENT_QUOTES)) }}')"
                                    class="bbpom-att-btn-preview inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs cursor-pointer"
                                >
                                    <i class="ri-eye-line text-xs"></i>
                                    <span>Pratinjau</span>
                                </button>
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
                            @elseif($att->is_pdf)
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

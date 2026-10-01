<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <!-- Modern Breadcrumbs with Remix Icons -->
                <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-2.5" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition-colors flex items-center gap-1">
                        <i class="ri-home-4-line text-xs"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="ri-arrow-right-s-line text-slate-300 text-xs"></i>
                    <a href="{{ route('tickets.index') }}" class="hover:text-emerald-700 transition-colors flex items-center gap-1">
                        <i class="ri-file-list-3-line text-xs"></i>
                        <span>Daftar Pengaduan</span>
                    </a>
                    <i class="ri-arrow-right-s-line text-slate-300 text-xs"></i>
                    <span class="font-mono font-semibold text-emerald-800">{{ $ticket->ticket_number }}</span>
                </nav>

                <!-- Ticket Identifier & Subject Header -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div x-data="{ copied: false }" class="inline-flex items-center gap-1.5">
                        <span class="font-mono text-xs font-bold bg-slate-900 text-white px-3 py-1 rounded-lg tracking-wider shadow-xs">
                            {{ $ticket->ticket_number }}
                        </span>
                        <button 
                            @click="navigator.clipboard.writeText('{{ $ticket->ticket_number }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                            type="button" 
                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-xs"
                            title="Salin Nomor Tiket"
                        >
                            <i x-show="!copied" class="ri-file-copy-line text-xs"></i>
                            <i x-show="copied" class="ri-check-line text-emerald-600 text-xs" x-cloak></i>
                            <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                        </button>
                    </div>

                    <x-ticket-status-badge :status="$ticket->status" />

                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="ri-apps-2-line text-slate-500 text-xs"></i>
                        {{ $ticket->category->name }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-2.5">
                    {{ $ticket->subject }}
                </h1>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2.5 self-start md:self-center shrink-0 print:hidden">
                <a 
                    href="{{ route('tickets.index') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition-all shadow-xs hover:shadow"
                >
                    <i class="ri-arrow-left-line text-sm"></i>
                    <span>Kembali ke Riwayat</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Print Slip Official Header (Visible ONLY during print) -->
    <div class="hidden print:block mb-8 pb-4 border-b-2 border-slate-900">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="h-16 w-auto object-contain">
                <div>
                    <h2 class="text-xs uppercase tracking-widest font-semibold text-slate-600">Badan Pengawas Obat dan Makanan Republik Indonesia</h2>
                    <h1 class="text-lg font-bold uppercase tracking-wide text-slate-900">Balai Besar Pengawas Obat dan Makanan di Semarang</h1>
                    <p class="text-[11px] text-slate-600">Jl. Sukun Raya No.41A, Srondol Wetan, Kec. Banyumanik, Kota Semarang, Jawa Tengah 50263</p>
                </div>
            </div>
            <div class="text-right">
                <div class="font-mono font-bold text-base border border-slate-900 px-3 py-1 bg-slate-50 inline-block">
                    {{ $ticket->ticket_number }}
                </div>
                <div class="text-[10px] text-slate-500 mt-1 uppercase font-semibold">Lembar Bukti Layanan TIK</div>
            </div>
        </div>
        <div class="mt-4 pt-2 border-t border-slate-300 text-center font-bold text-sm tracking-wider uppercase text-slate-800">
            Tanda Terima Pendaftaran Pengaduan & Permohonan Layanan TIK
        </div>
    </div>

    <div class="space-y-6">
        <!-- Status Tracking Stepper / Progress Bar (Atomic Component) -->
        <x-ticket-status-stepper :ticket="$ticket" />

        <!-- Two-Column Enterprise Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Primary Column (8 cols): Detail Tiket, Form Spec, Attachment, Discussion -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Card 1: Rincian Lengkap Pengaduan & Spesifikasi Formulir -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
                    <div class="border-b border-slate-100 pb-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                                <i class="ri-file-list-3-line text-sm"></i>
                                Informasi & Spesifikasi Pengaduan
                            </span>
                            <h2 class="text-lg font-bold text-slate-900 mt-1">
                                {{ $ticket->subject }}
                            </h2>
                        </div>
                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <span class="px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1.5">
                                <i class="ri-folder-2-line text-slate-500"></i>
                                {{ $ticket->category->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Intelligent Structured Description Rendering -->
                    <x-ticket-description :description="$ticket->description" />

                    <!-- Attachment Section (Preview / Download) -->
                    @if($ticket->attachment)
                        @php
                            $ext = strtolower(pathinfo($ticket->attachment, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                            $isPdf = $ext === 'pdf';
                            $isDoc = in_array($ext, ['doc', 'docx']);
                            $isZip = in_array($ext, ['zip', 'rar']);

                            $fileIcon = match(true) {
                                $isImage => 'ri-image-2-line text-blue-600',
                                $isPdf => 'ri-file-pdf-2-line text-rose-600',
                                $isDoc => 'ri-file-word-2-line text-blue-700',
                                $isZip => 'ri-file-zip-line text-amber-600',
                                default => 'ri-file-line text-slate-600',
                            };

                            $fileName = basename($ticket->attachment);
                        @endphp

                        <div class="mt-8 pt-6 border-t border-slate-100" x-data="{ showLightbox: false }">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                                <i class="ri-attachment-2 text-emerald-700 text-sm"></i>
                                Berkas Lampiran Pendukung:
                            </h4>

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
                                                @click="showLightbox = true" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-colors shadow-xs"
                                            >
                                                <i class="ri-eye-line text-xs"></i>
                                                <span>Pratinjau</span>
                                            </button>
                                        @endif

                                        <a 
                                            href="{{ asset('storage/' . $ticket->attachment) }}" 
                                            target="_blank" 
                                            download 
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs"
                                        >
                                            <i class="ri-download-2-line text-xs"></i>
                                            <span>Unduh Berkas</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Image Thumbnail if Image -->
                                @if($isImage)
                                    <div class="mt-4 pt-3 border-t border-slate-200/60">
                                        <button 
                                            type="button" 
                                            @click="showLightbox = true" 
                                            class="group relative inline-block rounded-xl overflow-hidden border border-slate-200 bg-white hover:ring-2 hover:ring-emerald-500 transition-all cursor-zoom-in"
                                        >
                                            <img 
                                                src="{{ asset('storage/' . $ticket->attachment) }}" 
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

                                    <!-- Lightbox Modal using Alpine.js -->
                                    <div 
                                        x-show="showLightbox" 
                                        x-cloak 
                                        @keydown.escape.window="showLightbox = false"
                                        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4"
                                    >
                                        <div 
                                            @click.away="showLightbox = false" 
                                            class="relative bg-white rounded-2xl max-w-4xl w-full p-4 overflow-hidden shadow-2xl"
                                        >
                                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                                <div class="flex items-center gap-2">
                                                    <i class="ri-image-2-line text-emerald-700"></i>
                                                    <span class="text-xs font-bold text-slate-800">{{ $fileName }}</span>
                                                </div>
                                                <button 
                                                    type="button" 
                                                    @click="showLightbox = false" 
                                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition"
                                                >
                                                    <i class="ri-close-line text-lg"></i>
                                                </button>
                                            </div>
                                            <div class="max-h-[75vh] overflow-auto flex items-center justify-center bg-slate-100/50 rounded-xl p-2">
                                                <img 
                                                    src="{{ asset('storage/' . $ticket->attachment) }}" 
                                                    alt="Lampiran {{ $ticket->ticket_number }}" 
                                                    class="max-h-[70vh] w-auto rounded-lg object-contain shadow-sm"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Card 2: Percakapan WhatsApp Style -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 print:hidden">
                    <!-- WhatsApp Header -->
                    <div class="border-b border-slate-100 pb-4 mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="ri-whatsapp-line text-emerald-600 text-2xl"></i>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base font-bold text-slate-900">
                                    Percakapan Diskusi Tiket
                                </h3>
                                @if($ticket->status === 'selesai')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                        Selesai
                                    </span>
                                @elseif($ticket->status === 'diproses')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                        Diproses
                                    </span>
                                @elseif($ticket->status === 'ditolak')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                            {{ $ticket->responses->count() }} Pesan
                        </span>
                    </div>

                    <!-- WhatsApp Chat Stream Container -->
                    <div 
                        x-data 
                        x-init="$el.scrollTop = $el.scrollHeight"
                        class="bg-slate-50/70 rounded-2xl p-4 sm:p-6 border border-slate-200/70 min-h-[320px] max-h-[520px] overflow-y-auto space-y-4 scroll-smooth"
                    >
                        @php
                            $responses = $ticket->responses;
                        @endphp

                        @if($responses->isEmpty())
                            <!-- Date Divider -->
                            <div class="flex justify-center my-2">
                                <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-slate-200/80 text-slate-500 shadow-2xs">
                                    Hari Ini
                                </span>
                            </div>

                            <!-- Bot / IT Helpdesk Greeting Bubble (Right Side) -->
                            <div class="flex items-end justify-end gap-2.5 max-w-[85%] sm:max-w-[75%] ml-auto">
                                <div class="bg-white rounded-2xl rounded-br-xs px-4 py-2.5 border border-slate-200 shadow-2xs">
                                    <div class="mb-1">
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            Petugas IT
                                        </span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-800 leading-relaxed">
                                        Halo Bapak/Ibu <strong>{{ $ticket->user->name }}</strong>, pengaduan Anda terkait <em>"{{ $ticket->subject }}"</em> telah tercatat di sistem Helpdesk TIK BBPOM. Tim kami akan segera meninjau dan merespon pengaduan ini. Silakan sampaikan informasi tambahan di kolom chat ini jika diperlukan.
                                    </p>
                                    <div class="text-[10px] text-slate-400 text-right mt-1 font-mono">
                                        {{ $ticket->created_at->format('H:i') }} WIB
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 self-end mb-0.5 shadow-2xs">
                                    IT
                                </div>
                            </div>
                        @else
                            @php
                                $grouped = $responses->groupBy(function($item) {
                                    return $item->created_at->format('Y-m-d');
                                });
                            @endphp

                            @foreach($grouped as $dateKey => $items)
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
                                <div class="flex justify-center my-3">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-slate-200/80 text-slate-500 shadow-2xs">
                                        {{ $label }}
                                    </span>
                                </div>

                                @foreach($items as $response)
                                    @php
                                        $isAdmin = $response->user->isAdmin();
                                    @endphp

                                    @if(!$isAdmin)
                                        <!-- Pelapor Bubble (LEFT SIDE - matching user FZ in screenshot) -->
                                        <div class="flex items-end gap-2.5 max-w-[85%] sm:max-w-[75%]">
                                            <!-- Avatar Circle with User Initials -->
                                            <div class="w-8 h-8 rounded-full bg-slate-200/90 border border-slate-300 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 self-end mb-0.5 shadow-2xs">
                                                {{ strtoupper(substr($response->user->name, 0, 2)) }}
                                            </div>

                                            <!-- Message Bubble -->
                                            <div class="bg-slate-100/95 rounded-2xl rounded-bl-xs px-4 py-2.5 border border-slate-200/60 shadow-2xs">
                                                <p class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                                                    {{ $response->response_text }}
                                                </p>
                                                <div class="text-[10px] text-slate-400 text-right mt-1 font-mono">
                                                    {{ $response->created_at->format('H:i') }} WIB
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Petugas IT Bubble (RIGHT SIDE - matching Bot in screenshot) -->
                                        <div class="flex items-end justify-end gap-2.5 max-w-[85%] sm:max-w-[75%] ml-auto">
                                            <!-- Message Bubble -->
                                            <div class="bg-white rounded-2xl rounded-br-xs px-4 py-2.5 border border-slate-200 shadow-2xs">
                                                <!-- Role Pill inside bubble -->
                                                <div class="mb-1">
                                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                        Petugas IT
                                                    </span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                                                    {{ $response->response_text }}
                                                </p>
                                                <div class="text-[10px] text-slate-400 text-right mt-1 font-mono">
                                                    {{ $response->created_at->format('H:i') }} WIB
                                                </div>
                                            </div>

                                            <!-- Avatar Circle IT -->
                                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 self-end mb-0.5 shadow-2xs">
                                                IT
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                    </div>

                    <!-- WhatsApp Style Bottom Input Bar -->
                    <form method="POST" action="{{ route('tickets.reply', $ticket) }}" class="mt-4 pt-3 border-t border-slate-100">
                        @csrf
                        <div class="flex items-center gap-2.5">
                            <div class="flex-1 relative">
                                <textarea 
                                    id="response_text" 
                                    name="response_text" 
                                    rows="1" 
                                    class="w-full rounded-2xl border-slate-300 py-3 px-4 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs resize-none max-h-32"
                                    placeholder="Ketik tanggapan Anda di sini..."
                                    required
                                    @keydown.enter.prevent="if(!$event.shiftKey && $el.value.trim().length > 0) $el.form.submit()"
                                >{{ old('response_text') }}</textarea>
                            </div>

                            <button 
                                type="submit" 
                                class="w-11 h-11 rounded-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white flex items-center justify-center transition-all shadow-xs shrink-0 hover:scale-105"
                                title="Kirim Pesan"
                            >
                                <i class="ri-send-plane-2-fill text-lg"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('response_text')" class="mt-1" />
                        
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 px-1">
                            <span>Tekan <strong>Enter</strong> untuk mengirim, <strong>Shift + Enter</strong> untuk baris baru</span>
                            <span class="flex items-center gap-1">
                                <i class="ri-shield-check-line text-emerald-600"></i>
                                Percakapan resmi Helpdesk TIK
                            </span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Sidebar Column (4 cols): Metadata, Pemohon, Helpdesk Info -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Card 1: Ringkasan Status & ITSM Metadata -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                            <i class="ri-information-line text-emerald-700"></i>
                            Ringkasan Tiket
                        </h4>
                        <span class="text-[10px] font-mono text-slate-400">#{{ $ticket->id }}</span>
                    </div>
                    
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
                            <span class="text-slate-500 flex items-center gap-1.5">
                                <i class="ri-barcode-line text-slate-400"></i>
                                Nomor Tiket:
                            </span>
                            <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">
                                {{ $ticket->ticket_number }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
                            <span class="text-slate-500 flex items-center gap-1.5">
                                <i class="ri-pulse-line text-slate-400"></i>
                                Status Saat Ini:
                            </span>
                            <x-ticket-status-badge :status="$ticket->status" />
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
                            <span class="text-slate-500 flex items-center gap-1.5">
                                <i class="ri-apps-2-line text-slate-400"></i>
                                Kategori Layanan:
                            </span>
                            <span class="font-semibold text-slate-800 text-right">
                                {{ $ticket->category->name }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
                            <span class="text-slate-500 flex items-center gap-1.5">
                                <i class="ri-calendar-line text-slate-400"></i>
                                Waktu Pengajuan:
                            </span>
                            <span class="font-medium text-slate-800 text-right">
                                {{ $ticket->created_at->format('d/m/Y H:i') }} WIB
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-slate-100/80">
                            <span class="text-slate-500 flex items-center gap-1.5">
                                <i class="ri-history-line text-slate-400"></i>
                                Pembaruan Terakhir:
                            </span>
                            <span class="font-medium text-slate-700 text-right">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1">
                            <span class="text-slate-500 flex items-center gap-1.5">
                                <i class="ri-time-line text-slate-400"></i>
                                Target Respon (SLA):
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                1x24 Jam Kerja
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Profil Pemohon -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                            <i class="ri-user-shared-line text-emerald-700"></i>
                            Identitas Pemohon
                        </h4>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                            Pegawai
                        </span>
                    </div>

                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 text-white font-bold text-base flex items-center justify-center shrink-0 shadow-xs">
                            {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <h5 class="font-bold text-sm text-slate-900 truncate" title="{{ $ticket->user->name }}">
                                {{ $ticket->user->name }}
                            </h5>
                            <p class="text-xs text-slate-500 truncate mt-0.5" title="{{ $ticket->user->email }}">
                                {{ $ticket->user->email }}
                            </p>
                            <span class="inline-block mt-1 text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                BBPOM di Semarang
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Official Print View Signature Section (Visible ONLY during print) -->
        <div class="hidden print:block mt-12 pt-8 border-t border-slate-300">
            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <p class="text-slate-600 mb-1">Pemohon Layanan / Pengadu,</p>
                    <p class="text-[10px] text-slate-400 mb-16">Tanggal: {{ now()->format('d/m/Y') }}</p>
                    <p class="font-bold underline text-slate-900">{{ $ticket->user->name }}</p>
                    <p class="text-[11px] text-slate-500">Pegawai BBPOM di Semarang</p>
                </div>
                <div>
                    <p class="text-slate-600 mb-1">Petugas Penerima Layanan TIK,</p>
                    <p class="text-[10px] text-slate-400 mb-16">Tanggal: {{ now()->format('d/m/Y') }}</p>
                    <p class="font-bold text-slate-900 underline">( ............................................ )</p>
                    <p class="text-[11px] text-slate-500">Tim Teknologi Informasi BBPOM</p>
                </div>
            </div>
            <div class="mt-12 text-center text-[10px] text-slate-400 italic">
                Dokumen ini dicetak secara otomatis melalui Sistem Manajemen Layanan Teknologi Informasi (ITSM) BBPOM di Semarang pada {{ now()->format('d/m/Y H:i') }} WIB.
            </div>
        </div>
    </div>
</x-app-layout>

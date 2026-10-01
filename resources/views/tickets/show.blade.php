<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-2" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition-colors flex items-center gap-1">
                        <i class="ri-home-4-line text-xs"></i>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('tickets.index') }}" class="hover:text-emerald-700 transition-colors flex items-center gap-1">
                        <i class="ri-file-list-3-line text-xs"></i>
                        <span>Daftar Pengaduan</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="font-mono font-semibold text-emerald-800">{{ $ticket->ticket_number }}</span>
                </nav>

                <!-- Ticket Identifier & Subject Header -->
                <div class="flex flex-wrap items-center gap-3">
                    <div x-data="{ copied: false }" class="inline-flex items-center gap-1.5">
                        <span class="font-mono text-xs font-bold bg-slate-900 text-white px-3 py-1 rounded-lg tracking-wider shadow-xs">
                            {{ $ticket->ticket_number }}
                        </span>
                        <button 
                            @click="navigator.clipboard.writeText('{{ $ticket->ticket_number }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                            type="button" 
                            class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-xs"
                            title="Salin Nomor Tiket"
                        >
                            <i x-show="!copied" class="ri-file-copy-line text-xs"></i>
                            <i x-show="copied" class="ri-check-line text-emerald-600 text-xs" x-cloak></i>
                            <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                        </button>
                    </div>

                    <x-ticket-status-badge :status="$ticket->status" />

                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="ri-apps-2-line text-slate-500 text-xs"></i>
                        {{ $ticket->category->name }}
                    </span>
                </div>

                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-2">
                    {{ $ticket->subject }}
                </h1>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2.5 self-start md:self-center shrink-0 print:hidden">
                <a 
                    href="{{ route('tickets.index') }}" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition shadow-xs"
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
        <!-- Status Tracking Stepper / Progress Bar (ITSM Lifecycle) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 print:hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">ITSM Lifecycle Tracking</span>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5 mt-0.5">
                        <i class="ri-pulse-line text-emerald-700"></i>
                        Status Penanganan Kendala Layanan
                    </h3>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500">Terakhir Diperbarui:</span>
                    <span class="text-xs font-semibold text-slate-800 block">{{ $ticket->updated_at->translatedFormat('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            @if($ticket->status === 'ditolak')
                <!-- Status Ditolak / Dibatalkan Alert -->
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <i class="ri-close-circle-fill text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-rose-900 text-sm">Pengaduan Berstatus Ditolak / Tidak Dapat Diproses</h4>
                        <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                            Permohonan atau pengaduan ini tidak dapat dilanjutkan oleh tim Helpdesk TIK BBPOM di Semarang. Alasan penolakan atau petunjuk perbaikan dapat Anda baca pada kolom riwayat tanggapan petugas di bawah.
                        </p>
                    </div>
                </div>
            @else
                <!-- 4-Stage ITSM Stepper Timeline -->
                @php
                    $isStep1Done = true;
                    $isStep2Done = in_array($ticket->status, ['diproses', 'selesai']);
                    $isStep3Done = in_array($ticket->status, ['diproses', 'selesai']) && ($ticket->responses->where('user.role', 'admin')->count() > 0 || $ticket->status === 'selesai');
                    $isStep4Done = $ticket->status === 'selesai';

                    // Progress percentage for visual connecting bar
                    $progressWidth = '0%';
                    if ($isStep4Done) {
                        $progressWidth = '100%';
                    } elseif ($isStep3Done) {
                        $progressWidth = '66%';
                    } elseif ($isStep2Done) {
                        $progressWidth = '33%';
                    }
                @endphp

                <div class="relative py-2">
                    <!-- Progress Bar Background Line -->
                    <div class="hidden sm:block absolute top-6 left-12 right-12 h-1 bg-slate-100 z-0"></div>
                    
                    <!-- Progress Bar Active Colored Line -->
                    <div class="hidden sm:block absolute top-6 left-12 h-1 bg-emerald-600 z-0 transition-all duration-700"
                         style="width: {{ $progressWidth }};">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative z-10">
                        <!-- Step 1: Pengajuan Masuk -->
                        <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep1Done ? 'bg-emerald-700 text-white ring-4 ring-emerald-50' : 'bg-slate-100 text-slate-400' }}">
                                <i class="ri-file-text-line text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm text-slate-900">1. Pengaduan Masuk</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Tiket tercatat di antrian Helpdesk</p>
                                <span class="text-[10px] font-mono text-slate-400 block mt-0.5">{{ $ticket->created_at->format('d/m/Y H:i') }} WIB</span>
                            </div>
                        </div>

                        <!-- Step 2: Verifikasi & Diagnosa -->
                        <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep2Done ? ($ticket->status === 'diproses' && !$isStep3Done ? 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' : 'bg-emerald-700 text-white ring-4 ring-emerald-50') : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                                <i class="ri-search-eye-line text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm {{ $isStep2Done ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                                    2. Verifikasi & Diagnosa
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Penelaahan spesifikasi oleh tim IT</p>
                                @if($isStep2Done && $ticket->status === 'diproses')
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Sedang Ditelaah
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Step 3: Penanganan Teknis -->
                        <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep3Done ? ($ticket->status === 'diproses' ? 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' : 'bg-emerald-700 text-white ring-4 ring-emerald-50') : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                                <i class="ri-tools-line text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm {{ $isStep3Done ? 'text-slate-900 font-bold' : 'text-slate-400' }}">
                                    3. Penanganan Teknis
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Eksekusi perbaikan oleh teknisi</p>
                                @if($isStep3Done && $ticket->status === 'diproses')
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Proses Pengerjaan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Step 4: Selesai & Ditutup -->
                        <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all {{ $isStep4Done ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                                <i class="ri-checkbox-circle-line text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs sm:text-sm {{ $isStep4Done ? 'text-emerald-900 font-extrabold' : 'text-slate-400' }}">
                                    4. Tuntas & Selesai
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Kendala terselesaikan & tiket ditutup</p>
                                @if($isStep4Done)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Tuntas
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

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

                <!-- Card 2: Diskusi & Riwayat Tanggapan -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 print:hidden">
                    <div class="border-b border-slate-100 pb-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                                <i class="ri-chat-3-line text-sm"></i>
                                Komunikasi & Riwayat Penanganan
                            </span>
                            <h3 class="text-base font-bold text-slate-900 mt-0.5">
                                Kolom Diskusi Tiket
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Catatan resmi respon penanganan antara pemohon dan tim Helpdesk TIK BBPOM di Semarang
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 self-start sm:self-auto">
                            {{ $ticket->responses->count() }} Balasan
                        </span>
                    </div>

                    <!-- Conversation Stream -->
                    <div class="space-y-4 mb-8">
                        @forelse($ticket->responses as $response)
                            @php
                                $isAdmin = $response->user->isAdmin();
                            @endphp

                            <div class="rounded-2xl p-5 transition-all {{ $isAdmin ? 'bg-emerald-50/70 border border-emerald-200 shadow-xs' : 'bg-slate-50/80 border border-slate-200' }}">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shadow-xs {{ $isAdmin ? 'bg-emerald-700 text-white' : 'bg-slate-800 text-white' }}">
                                            @if($isAdmin)
                                                <i class="ri-customer-service-2-fill text-base"></i>
                                            @else
                                                {{ strtoupper(substr($response->user->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-xs sm:text-sm text-slate-900">{{ $response->user->name }}</span>
                                                @if($isAdmin)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                        <i class="ri-shield-check-fill text-emerald-700"></i>
                                                        Tim Helpdesk TIK BBPOM
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-200 text-slate-700">
                                                        <i class="ri-user-3-line text-slate-500"></i>
                                                        Pemohon Layanan
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] text-slate-500 block mt-0.5">
                                                {{ $response->created_at->translatedFormat('l, d F Y - H:i') }} WIB
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-mono text-slate-400 hidden sm:inline-block">
                                        {{ $response->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <div class="text-xs sm:text-sm text-slate-800 leading-relaxed ps-0 sm:ps-12 whitespace-pre-line bg-white/70 p-3.5 rounded-xl border border-slate-200/50">
                                    {{ $response->response_text }}
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center bg-slate-50/70 rounded-2xl border border-dashed border-slate-200">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                                    <i class="ri-chat-voice-line text-2xl text-slate-400"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Belum Ada Tanggapan</h4>
                                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 leading-relaxed">
                                    Tim Helpdesk TIK BBPOM di Semarang sedang meninjau pengaduan Anda. Anda juga dapat memberikan pesan tambahan atau informasi pelengkap melalui form di bawah.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Form Balasan / Kirim Tanggapan -->
                    <form method="POST" action="{{ route('tickets.reply', $ticket) }}" class="pt-6 border-t border-slate-100">
                        @csrf
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label for="response_text" class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="ri-reply-line text-emerald-700"></i>
                                    Berikan Tanggapan / Informasi Tambahan:
                                </label>
                                <span class="text-[11px] text-slate-400">Tercatat ke sistem Helpdesk</span>
                            </div>

                            <textarea 
                                id="response_text" 
                                name="response_text" 
                                rows="3" 
                                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs placeholder:text-slate-400"
                                placeholder="Tuliskan keterangan tambahan, konfirmasi status, atau pertanyaan untuk tim IT BBPOM..."
                                required
                            >{{ old('response_text') }}</textarea>
                            <x-input-error :messages="$errors->get('response_text')" />
                            
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-1">
                                <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                    <i class="ri-information-line text-slate-400"></i>
                                    Tanggapan Anda akan langsung dapat dibaca oleh staf penanggung jawab tiket.
                                </p>
                                <button 
                                    type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all"
                                >
                                    <i class="ri-send-plane-2-fill text-sm"></i>
                                    <span>Kirim Tanggapan</span>
                                </button>
                            </div>
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

                <!-- Card 3: Helpdesk & Layanan Dukungan BBPOM -->
                <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm space-y-4 print:hidden">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <i class="ri-customer-service-2-line text-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-xs font-bold uppercase tracking-wider text-emerald-400">Pusat Bantuan TIK</h5>
                            <p class="text-[11px] text-slate-400">Helpdesk & Tim Teknis</p>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs text-slate-300">
                        <div class="flex items-start gap-2">
                            <i class="ri-map-pin-2-line text-emerald-400 mt-0.5 shrink-0"></i>
                            <span>Subbag Tata Usaha / Tim TIK Lt. 2, BBPOM Semarang</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ri-phone-line text-emerald-400 shrink-0"></i>
                            <span>(024) 7612305 &bull; Ext. TIK</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="ri-time-line text-emerald-400 mt-0.5 shrink-0"></i>
                            <span>Senin - Kamis: 07.30 - 16.00 WIB<br>Jumat: 07.30 - 16.30 WIB</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-800 text-[11px] text-slate-400 leading-relaxed">
                        Jika kendala bersifat darurat yang menghentikan operasional pelayanan publik, segera hubungi kontak ekstensi TIK.
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

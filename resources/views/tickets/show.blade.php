<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('dashboard')],
                    ['label' => 'Daftar Pengaduan', 'url' => route('tickets.index')],
                    ['label' => $ticket->ticket_number, 'mono' => true]
                ]" />

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

                    <x-ticket-status-badge :status="$ticket->status" :handler="$ticket->handledBy" />

                    <x-ticket-category-badge :ticket="$ticket" :category="$ticket->category" :full="true" class="text-xs px-3 py-1" />
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

                @if($ticket->bsreDetail)
                    <!-- Card Khusus Formulir Sertifikat BSrE -->
                    <div class="bg-white rounded-2xl border-2 border-emerald-500/30 shadow-sm p-6 sm:p-7 relative overflow-hidden">
                        <div class="border-b border-gray-100 pb-4 mb-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center font-bold text-lg">
                                    <i class="ri-shield-keyhole-line"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Formulir Khusus Layanan</span>
                                    <h3 class="text-base font-extrabold text-gray-900 mt-0.5">Sertifikat BSrE / TTE</h3>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                {{ $ticket->bsreDetail->sub_category_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <span class="text-gray-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
                                <span class="font-bold text-gray-900 text-sm mt-0.5 block">
                                    {{ $ticket->bsreDetail->tanggal_pengajuan ? $ticket->bsreDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <span class="text-gray-400 block text-[11px] font-medium">Nama Pemohon:</span>
                                <span class="font-bold text-gray-900 text-sm mt-0.5 block">
                                    {{ $ticket->bsreDetail->nama }}
                                </span>
                            </div>

                            @if(in_array($ticket->bsreDetail->sub_category, ['reset_password', 'permintaan_otp']))
                                <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                    <span class="text-gray-400 block text-[11px] font-medium">NIP Pemohon:</span>
                                    <span class="font-bold text-gray-900 font-mono text-sm mt-0.5 block">
                                        {{ $ticket->bsreDetail->nip ?: '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                    <span class="text-gray-400 block text-[11px] font-medium">NIK Pemohon:</span>
                                    <span class="font-bold text-gray-900 font-mono text-sm mt-0.5 block">
                                        {{ $ticket->bsreDetail->nik ?: '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                    <span class="text-gray-400 block text-[11px] font-medium">Email Dinas Pemohon:</span>
                                    <span class="font-semibold text-gray-900 text-sm mt-0.5 block">
                                        {{ $ticket->bsreDetail->email ?: '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                    <span class="text-gray-400 block text-[11px] font-medium">Nomor Telepon / WhatsApp:</span>
                                    <span class="font-semibold text-gray-900 text-sm mt-0.5 block">
                                        {{ $ticket->bsreDetail->nomor_telepon ?: '-' }}
                                    </span>
                                </div>
                            @endif

                            @if($ticket->bsreDetail->deskripsi)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                    <span class="text-gray-400 block text-[11px] font-medium mb-1">Rincian Deskripsi / Kendala:</span>
                                    <p class="text-gray-800 leading-relaxed whitespace-pre-line text-xs sm:text-sm">{{ trim($ticket->bsreDetail->deskripsi) }}</p>
                                </div>
                            @endif

                            @if($ticket->bsreDetail->file_lampiran)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-gray-400 block text-[11px] font-medium">Lampiran Berkas BSrE:</span>
                                        <span class="font-semibold text-gray-900 text-xs mt-0.5 block">Dokumen pendukung pengajuan</span>
                                    </div>
                                    <a href="{{ route('tickets.attachment', ['ticket' => $ticket, 'type' => 'bsre', 'inline' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($ticket->zoomDetail)
                    <!-- Card Khusus Formulir Zoom -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 relative overflow-hidden">
                        <div class="border-b border-slate-100 pb-4 mb-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-700 flex items-center justify-center font-bold text-lg">
                                    <i class="ri-video-chat-line"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Formulir Khusus Layanan</span>
                                    <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Zoom Meeting & Webinar</h3>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200/60">
                                {{ $ticket->zoomDetail->sub_category_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->zoomDetail->tanggal_pengajuan ? $ticket->zoomDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Nama Pemohon:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->zoomDetail->nama }}
                                </span>
                            </div>

                            @if($ticket->zoomDetail->sub_category === 'permintaan_link_zoom')
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Nama Acara / Kegiatan:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->zoomDetail->nama_acara }}
                                    </span>
                                </div>

                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Jam / Waktu Pelaksanaan:</span>
                                    <span class="font-semibold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->zoomDetail->jam }}
                                    </span>
                                </div>
                            @endif

                            @if($ticket->zoomDetail->deskripsi)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium mb-1">Rincian Deskripsi / Kendala:</span>
                                    <p class="text-slate-800 leading-relaxed whitespace-pre-line text-xs sm:text-sm">{{ trim($ticket->zoomDetail->deskripsi) }}</p>
                                </div>
                            @endif

                            @if($ticket->zoomDetail->file_lampiran)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 block text-[11px] font-medium">Lampiran Berkas Zoom:</span>
                                        <span class="font-semibold text-slate-900 text-xs mt-0.5 block">Dokumen pendukung pengajuan</span>
                                    </div>
                                    <a href="{{ route('tickets.attachment', ['ticket' => $ticket, 'type' => 'zoom', 'inline' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($ticket->kegiatanDetail)
                    <!-- Card Khusus Formulir & Fasilitas Kegiatan -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 relative overflow-hidden">
                        <div class="border-b border-slate-100 pb-4 mb-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center font-bold text-lg">
                                    <i class="ri-survey-line"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Formulir Khusus Layanan</span>
                                    <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Formulir & Fasilitas Kegiatan</h3>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                {{ $ticket->kegiatanDetail->sub_category_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->kegiatanDetail->tanggal_pengajuan ? $ticket->kegiatanDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Nama Pemohon:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->kegiatanDetail->nama }}
                                </span>
                            </div>

                            @if($ticket->kegiatanDetail->sub_category === 'permintaan_daftar_hadir')
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Nama Acara / Kegiatan:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->kegiatanDetail->nama_acara }}
                                    </span>
                                </div>

                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Jam / Waktu Pelaksanaan:</span>
                                    <span class="font-semibold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->kegiatanDetail->jam }}
                                    </span>
                                </div>
                            @endif

                            @if($ticket->kegiatanDetail->sub_category === 'permintaan_qrcode')
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium mb-1">Tautan / Link Sasaran QR Code:</span>
                                    <a href="{{ $ticket->kegiatanDetail->link_qrcode }}" target="_blank" class="font-mono text-emerald-700 hover:text-emerald-800 underline text-xs break-all flex items-center gap-1.5">
                                        <span>{{ $ticket->kegiatanDetail->link_qrcode }}</span>
                                        <i class="ri-external-link-line"></i>
                                    </a>
                                </div>
                            @endif

                            @if($ticket->kegiatanDetail->sub_category === 'survey')
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Judul Survei:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->kegiatanDetail->judul_survey }}
                                    </span>
                                </div>

                                @if(!empty($ticket->kegiatanDetail->pertanyaan_data))
                                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                        <span class="text-slate-400 block text-[11px] font-medium mb-2">Daftar Pertanyaan Survei:</span>
                                        <div class="space-y-2">
                                            @foreach($ticket->kegiatanDetail->pertanyaan_data as $idx => $q)
                                                <div class="p-2.5 bg-white rounded-lg border border-slate-200 flex items-center justify-between gap-3 text-xs">
                                                    <span class="font-medium text-slate-800">
                                                        <strong>{{ $idx + 1 }}.</strong> {{ is_array($q) ? ($q['text'] ?? $q['pertanyaan'] ?? '') : $q }}
                                                    </span>
                                                    @if(is_array($q) && (!empty($q['tipe']) || !empty($q['tipe_jawaban'])))
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60 shrink-0">
                                                            {{ $q['tipe'] ?? $q['tipe_jawaban'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if($ticket->kegiatanDetail->sub_category === 'post_test_pretest')
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Judul Acara:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->kegiatanDetail->judul_acara }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Jenis Evaluasi:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->kegiatanDetail->jenis_test_label }}
                                    </span>
                                </div>

                                @if(!empty($ticket->kegiatanDetail->pertanyaan_data))
                                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                        <span class="text-slate-400 block text-[11px] font-medium mb-2">Daftar Soal Evaluasi:</span>
                                        <div class="space-y-2">
                                            @foreach($ticket->kegiatanDetail->pertanyaan_data as $idx => $q)
                                                <div class="p-2.5 bg-white rounded-lg border border-slate-200 flex items-center justify-between gap-3 text-xs">
                                                    <span class="font-medium text-slate-800">
                                                        <strong>{{ $idx + 1 }}.</strong> {{ is_array($q) ? ($q['text'] ?? $q['pertanyaan'] ?? '') : $q }}
                                                    </span>
                                                    @if(is_array($q) && (!empty($q['tipe']) || !empty($q['tipe_jawaban'])))
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-800 border border-blue-200/60 shrink-0">
                                                            {{ $q['tipe'] ?? $q['tipe_jawaban'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if($ticket->kegiatanDetail->deskripsi)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium mb-1">Rincian Deskripsi / Kendala:</span>
                                    <p class="text-slate-800 leading-relaxed whitespace-pre-line text-xs sm:text-sm">{{ trim($ticket->kegiatanDetail->deskripsi) }}</p>
                                </div>
                            @endif

                            @if($ticket->kegiatanDetail->file_lampiran)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 block text-[11px] font-medium">Berkas Dokumen Lampiran:</span>
                                        <span class="font-semibold text-slate-900 text-xs mt-0.5 block">File draf / kuesioner / soal kegiatan</span>
                                    </div>
                                    <a href="{{ route('tickets.attachment', ['ticket' => $ticket, 'type' => 'kegiatan', 'inline' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($ticket->windowsDetail)
                    <!-- Card Khusus Formulir Install Ulang Windows -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 relative overflow-hidden">
                        <div class="border-b border-slate-100 pb-4 mb-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-500/10 text-slate-700 flex items-center justify-center font-bold text-lg">
                                    <i class="ri-computer-line"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Formulir Khusus Layanan</span>
                                    <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Install Ulang Windows & OS</h3>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $ticket->windowsDetail->sub_category_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->windowsDetail->tanggal_pengajuan ? $ticket->windowsDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Nama Pemohon:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->windowsDetail->nama }}
                                </span>
                            </div>

                            @if($ticket->windowsDetail->sub_category === 'permintaan_install_windows')
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Bidang / Substansi:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->windowsDetail->bidang ?: '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Merk Laptop / Komputer:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->windowsDetail->merk_perangkat ?: '-' }}
                                    </span>
                                </div>

                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Kode BMN Perangkat:</span>
                                    <span class="font-bold text-slate-900 font-mono text-sm mt-0.5 block">
                                        {{ $ticket->windowsDetail->kode_bmn ?: '-' }}
                                    </span>
                                </div>
                            @endif

                            @if($ticket->windowsDetail->deskripsi)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium mb-1">Rincian Deskripsi / Kendala:</span>
                                    <p class="text-slate-800 leading-relaxed whitespace-pre-line text-xs sm:text-sm">{{ trim($ticket->windowsDetail->deskripsi) }}</p>
                                </div>
                            @endif

                            @if($ticket->windowsDetail->file_lampiran)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 block text-[11px] font-medium">Lampiran Berkas:</span>
                                        <span class="font-semibold text-slate-900 text-xs mt-0.5 block">Dokumen / Foto Perangkat</span>
                                    </div>
                                    <a href="{{ route('tickets.attachment', ['ticket' => $ticket, 'type' => 'windows', 'inline' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($ticket->officeDetail)
                    <!-- Card Khusus Formulir Lisensi Office -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 relative overflow-hidden">
                        <div class="border-b border-slate-100 pb-4 mb-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center font-bold text-lg">
                                    <i class="ri-key-line"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Formulir Khusus Layanan</span>
                                    <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Lisensi Microsoft Office</h3>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                {{ $ticket->officeDetail->sub_category_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->officeDetail->tanggal_pengajuan ? $ticket->officeDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Nama Pemohon:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->officeDetail->nama }}
                                </span>
                            </div>

                            @if($ticket->officeDetail->sub_category === 'permintaan_lisensi_office')
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Bidang / Substansi:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->officeDetail->bidang ?: '-' }}
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Merk Laptop / Komputer:</span>
                                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                        {{ $ticket->officeDetail->merk_perangkat ?: '-' }}
                                    </span>
                                </div>

                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium">Kode BMN Perangkat:</span>
                                    <span class="font-bold text-slate-900 font-mono text-sm mt-0.5 block">
                                        {{ $ticket->officeDetail->kode_bmn ?: '-' }}
                                    </span>
                                </div>
                            @endif

                            @if($ticket->officeDetail->deskripsi)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium mb-1">Rincian Deskripsi / Kendala:</span>
                                    <p class="text-slate-800 leading-relaxed whitespace-pre-line text-xs sm:text-sm">{{ trim($ticket->officeDetail->deskripsi) }}</p>
                                </div>
                            @endif

                            @if($ticket->officeDetail->file_lampiran)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 block text-[11px] font-medium">Lampiran Berkas:</span>
                                        <span class="font-semibold text-slate-900 text-xs mt-0.5 block">Dokumen / Bukti Lampiran</span>
                                    </div>
                                    <a href="{{ route('tickets.attachment', ['ticket' => $ticket, 'type' => 'office', 'inline' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($ticket->lainnyaDetail)
                    <!-- Card Khusus Formulir Layanan Lainnya -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 relative overflow-hidden">
                        <div class="border-b border-slate-100 pb-4 mb-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-500/10 text-slate-700 flex items-center justify-center font-bold text-lg">
                                    <i class="ri-tools-line"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Formulir Khusus Layanan</span>
                                    <h3 class="text-base font-extrabold text-slate-900 mt-0.5">Layanan TIK Umum / Lainnya</h3>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                Layanan Umum
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Tanggal Pengajuan:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->lainnyaDetail->tanggal_pengajuan ? $ticket->lainnyaDetail->tanggal_pengajuan->translatedFormat('d F Y') : '-' }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 block text-[11px] font-medium">Nama Pemohon:</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $ticket->lainnyaDetail->nama }}
                                </span>
                            </div>

                            @if($ticket->lainnyaDetail->deskripsi)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 block text-[11px] font-medium mb-1">Deskripsi Permintaan / Kendala:</span>
                                    <p class="text-slate-800 leading-relaxed whitespace-pre-line text-xs sm:text-sm">{{ trim($ticket->lainnyaDetail->deskripsi) }}</p>
                                </div>
                            @endif

                            @if($ticket->lainnyaDetail->file_lampiran)
                                <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 block text-[11px] font-medium">Lampiran Berkas:</span>
                                        <span class="font-semibold text-slate-900 text-xs mt-0.5 block">Foto / Dokumen Pendukung</span>
                                    </div>
                                    <a href="{{ route('tickets.attachment', ['ticket' => $ticket, 'type' => 'lainnya', 'inline' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                        Lihat Berkas &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                
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
                            <x-ticket-category-badge :ticket="$ticket" :category="$ticket->category" :full="true" class="text-xs px-3 py-1" />
                        </div>
                    </div>

                    <!-- Intelligent Structured Description Rendering -->
                    <x-ticket-description :description="$ticket->description" />

                    <!-- Attachment Section (Multiple Documents / Legacy) -->
                    <x-ticket-attachments-list :ticket="$ticket" />
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
                        class="bg-slate-50/70 rounded-2xl p-3.5 sm:p-5 border border-slate-200/70 min-h-[320px] max-h-[520px] overflow-y-auto space-y-2.5 scroll-smooth"
                    >
                        @php
                            $responses = $ticket->responses;
                        @endphp

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

                            <!-- Date Separator Pill (e.g. Kemarin / Hari Ini) -->
                            <div class="flex justify-center my-2.5">
                                <span class="px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-slate-200/80 text-slate-500 shadow-2xs">
                                    {{ $label }}
                                </span>
                            </div>

                            {{-- Always show Helpdesk TIK Bot greeting under the ticket's creation date --}}
                            @if($dateKey === $ticketDateKey)
                                <!-- Bot / IT Helpdesk Greeting Bubble (Right Side) -->
                                <div class="flex items-end justify-end gap-2 max-w-[80%] sm:max-w-[65%] ml-auto">
                                    <div class="bg-white rounded-2xl rounded-br-xs px-3.5 py-2 border border-slate-200 shadow-2xs">
                                        <div class="flex items-center justify-end gap-1.5 mb-0">
                                            <span class="text-[11px] font-bold text-slate-900 leading-tight">
                                                Helpdesk TIK
                                            </span>
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-blue-100 text-blue-800 border border-blue-200/60">
                                                {{ $ticket->handledBy ? $ticket->handledBy->name : 'Sistem' }}
                                            </span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5">
                                            Halo Bapak/Ibu <strong>{{ $ticket->user->name }}</strong>, pengaduan Anda terkait <em>"{{ $ticket->subject }}"</em> telah tercatat di sistem Helpdesk TIK BBPOM. Tim kami akan segera meninjau dan merespon pengaduan ini. Silakan sampaikan informasi tambahan di kolom chat ini jika diperlukan.
                                        </p>
                                        <div class="text-[10px] text-slate-400 text-right mt-0.5 font-mono">
                                            {{ $ticket->created_at->format('H:i') }} WIB
                                        </div>
                                    </div>
                                    <div class="w-7 h-7 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs">
                                        {{ $ticket->handledBy ? strtoupper(substr($ticket->handledBy->name, 0, 2)) : 'IT' }}
                                    </div>
                                </div>
                            @endif

                            @if($grouped->has($dateKey))
                                @foreach($grouped->get($dateKey) as $response)
                                    @php
                                        $isAdmin = $response->user->isAdmin();
                                    @endphp

                                    @if(!$isAdmin)
                                        <!-- Pelapor Bubble (LEFT SIDE) -->
                                        <div class="flex items-end gap-2 max-w-[80%] sm:max-w-[65%]">
                                            <!-- Avatar Circle with User Initials -->
                                            <div class="w-7 h-7 rounded-full bg-slate-200/90 border border-slate-300 text-slate-700 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs">
                                                {{ strtoupper(substr($response->user->name, 0, 2)) }}
                                            </div>

                                            <!-- Message Bubble -->
                                            <div class="bg-slate-100/95 rounded-2xl rounded-bl-xs px-3.5 py-2 border border-slate-200/60 shadow-2xs">
                                                <!-- Sender Name & Role Header -->
                                                <div class="flex items-center gap-1.5 mb-0">
                                                    <span class="text-[11px] font-bold text-slate-900 leading-tight">
                                                        {{ $response->user->name }}
                                                    </span>
                                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200/60">
                                                        Pemohon
                                                    </span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line">{{ trim($response->response_text) }}</p>
                                                <div class="text-[10px] text-slate-400 text-right mt-0.5 font-mono">
                                                    {{ $response->created_at->format('H:i') }} WIB
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Petugas IT Bubble (RIGHT SIDE) -->
                                        <div class="flex items-end justify-end gap-2 max-w-[80%] sm:max-w-[65%] ml-auto">
                                            <!-- Message Bubble -->
                                            <div class="bg-white rounded-2xl rounded-br-xs px-3.5 py-2 border border-slate-200 shadow-2xs">
                                                <!-- Role Pill & Admin Name inside bubble -->
                                                <div class="flex items-center justify-end gap-1.5 mb-0">
                                                    <span class="text-[11px] font-bold text-slate-900 leading-tight">
                                                        {{ $response->user->name }}
                                                    </span>
                                                    <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-blue-100 text-blue-800 border border-blue-200/60">
                                                        Petugas IT
                                                    </span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-slate-800 leading-snug mt-0.5 whitespace-pre-line">{{ trim($response->response_text) }}</p>
                                                <div class="text-[10px] text-slate-400 text-right mt-0.5 font-mono">
                                                    {{ $response->created_at->format('H:i') }} WIB
                                                </div>
                                            </div>

                                            <!-- Avatar Circle IT -->
                                            <div class="w-7 h-7 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center font-bold text-[11px] shrink-0 self-end mb-0.5 shadow-2xs">
                                                {{ strtoupper(substr($response->user->name, 0, 2)) }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                    <!-- WhatsApp Style Bottom Input Bar -->
                    <form 
                        id="chatReplyForm" 
                        method="POST" 
                        action="{{ route('tickets.reply', $ticket) }}" 
                        class="mt-4 pt-3 border-t border-slate-100"
                    >
                        @csrf
                        <div class="flex items-center gap-2.5">
                            <div class="flex-1 relative">
                                <textarea 
                                    id="response_text" 
                                    name="response_text" 
                                    rows="1" 
                                    class="w-full rounded-2xl border-slate-300 py-3 px-4 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs resize-none max-h-32 overflow-hidden"
                                    style="overflow-y: hidden;"
                                    placeholder="Ketik tanggapan Anda di sini..."
                                    required
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

                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100/80 gap-2">
                            <span class="text-slate-500 flex items-center gap-1.5 shrink-0">
                                <i class="ri-pulse-line text-slate-400"></i>
                                Status Saat Ini:
                            </span>
                            <x-ticket-status-badge :status="$ticket->status" :handler="$ticket->handledBy" class="text-[11px] sm:text-xs py-0.5" />
                        </div>

                        <div class="flex items-center justify-between py-1.5 border-b border-slate-100/80 gap-2">
                            <span class="text-slate-500 flex items-center gap-1.5 shrink-0">
                                <i class="ri-apps-2-line text-slate-400"></i>
                                Kategori Layanan:
                            </span>
                            <span class="font-semibold text-slate-900 text-right">
                                {{ $ticket->service_name ?: $ticket->category->name }}
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

                <!-- Card 3: Profil Petugas IT Penanggung Jawab -->
                <x-ticket-handler-card :handler="$ticket->handledBy" :status="$ticket->status" />
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
                    @if($ticket->handledBy)
                        <p class="font-bold text-slate-900 underline">{{ $ticket->handledBy->name }}</p>
                        <p class="text-[11px] text-slate-500">{{ $ticket->handledBy->nip ? 'NIP. ' . $ticket->handledBy->nip : 'Tim Teknologi Informasi BBPOM' }}</p>
                    @else
                        <p class="font-bold text-slate-900 underline">( ............................................ )</p>
                        <p class="text-[11px] text-slate-500">Tim Teknologi Informasi BBPOM</p>
                    @endif
                </div>
            </div>
            <div class="mt-12 text-center text-[10px] text-slate-400 italic">
                Dokumen ini dicetak secara otomatis melalui Sistem Manajemen Layanan Teknologi Informasi (ITSM) BBPOM di Semarang pada {{ now()->format('d/m/Y H:i') }} WIB.
            </div>
        </div>
    </div>

    <!-- Chat Textarea Keyboard Handling (Enter to Send, Shift+Enter for Newline) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.getElementById('response_text');
            const form = document.getElementById('chatReplyForm');

            if (!textarea || !form) return;

            // Auto-expand textarea height as user types (up to max-h-32 / 128px)
            function adjustHeight() {
                textarea.style.height = 'auto';
                if (textarea.scrollHeight > 128) {
                    textarea.style.height = '128px';
                    textarea.style.overflowY = 'auto';
                } else {
                    textarea.style.height = textarea.scrollHeight + 'px';
                    textarea.style.overflowY = 'hidden';
                }
            }

            adjustHeight();
            textarea.addEventListener('input', adjustHeight);

            // Handle keyboard shortcuts: Enter to submit, Shift + Enter for newline
            textarea.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    if (e.shiftKey) {
                        // Allow default behavior (insert newline)
                        return;
                    }

                    // Plain Enter: prevent default newline and submit if non-empty
                    e.preventDefault();

                    if (this.value.trim().length > 0) {
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

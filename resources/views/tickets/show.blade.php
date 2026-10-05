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

                <!-- Unified Enterprise Specification Card (Seluruh 12 Katalog Layanan Terpadu) -->
                <x-ticket-specification-card :ticket="$ticket" />

                <!-- Card 2: Percakapan WhatsApp Style -->
                <div class="print:hidden">
                    <x-ticket-conversation-stream :ticket="$ticket" />
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
</x-app-layout>

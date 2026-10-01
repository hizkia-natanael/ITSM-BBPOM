<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'icon' => 'ri-home-4-line']
            ]" />
            <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                Dashboard Layanan TI Pegawai
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                Balai Besar Pengawas Obat dan Makanan di Semarang
            </p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Hero Banner (Matching Buat Pengaduan / Gateway Palette) -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden border border-slate-700/50">
            <!-- Decorative Ambient Glows -->
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Portal Layanan ITSM BBPOM di Semarang
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Selamat Datang, {{ Auth::user()->name }}!
                    </h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Mengalami kendala koneksi jaringan, sertifikat BSrE, akun Zoom, perangkat PC/laptop, permohonan lisensi software, atau aplikasi dinas? Ajukan permohonan sekarang dan tim TIK BBPOM akan segera menanganinya.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold shadow-md shadow-emerald-900/30 transition-all duration-150 active:scale-[0.99]">
                            <i class="ri-add-circle-line text-base"></i>
                            <span>Laporkan Kendala TI</span>
                        </a>
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700/60 text-xs font-semibold transition">
                            <i class="ri-history-line text-sm"></i>
                            <span>Lihat Riwayat Tiket</span>
                        </a>
                    </div>
                </div>

                <!-- Illustrated Official BPOM Card Badge (Anti-clash, Soft Shadow & Highlight) -->
                <div class="hidden lg:flex items-center justify-center shrink-0">
                    <div class="relative group">
                        <!-- Delicate ambient aura -->
                        <div class="absolute -inset-1.5 bg-gradient-to-tr from-emerald-500/20 via-teal-400/20 to-emerald-300/10 rounded-3xl blur-md opacity-90 pointer-events-none"></div>

                        <!-- Illustrated Card Container -->
                        <div class="relative bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl p-5 border border-white/90 shadow-xl shadow-slate-950/20 ring-1 ring-emerald-500/15 flex flex-col items-center justify-center transition-all duration-300 hover:-translate-y-0.5">
                            <!-- Top delicate highlight line -->
                            <div class="absolute top-0 inset-x-4 h-px bg-gradient-to-r from-transparent via-emerald-400/35 to-transparent pointer-events-none"></div>

                            <!-- BPOM Logo on clean crisp white surface -->
                            <div class="p-1.5 flex items-center justify-center">
                                <img 
                                    src="{{ asset('images/logo-bpom.png') }}" 
                                    alt="Logo Resmi BPOM" 
                                    class="h-16 sm:h-20 w-auto object-contain filter drop-shadow-xs"
                                >
                            </div>

                            <!-- Illustrated label badge -->
                            <div class="mt-2.5 px-3 py-1 rounded-full bg-slate-50 border border-slate-200/80 flex items-center gap-1.5 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-bold tracking-wider text-slate-700 uppercase">BBPOM di Semarang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Card 1: Total -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-400/80 transition-all flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Pengaduan</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Keseluruhan tiket dibuat</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <i class="ri-file-list-3-line text-2xl"></i>
                </div>
            </div>

            <!-- Card 2: Pending (Kuning) -->
            <div class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-sm hover:border-amber-400 transition-all flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-amber-700">Tiket Pending</p>
                    <p class="text-2xl sm:text-3xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
                    <p class="text-[11px] text-amber-600/70 mt-1">Menunggu respon tim IT</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="ri-time-line text-2xl"></i>
                </div>
            </div>

            <!-- Card 3: Diproses (Biru) -->
            <div class="bg-white p-5 rounded-2xl border border-blue-200/80 shadow-sm hover:border-blue-400 transition-all flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-blue-700">Sedang Diproses</p>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-1">{{ $stats['diproses'] }}</p>
                    <p class="text-[11px] text-blue-600/70 mt-1">Dalam penanganan teknisi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="ri-tools-line text-2xl"></i>
                </div>
            </div>

            <!-- Card 4: Selesai (Hijau) -->
            <div class="bg-white p-5 rounded-2xl border border-emerald-200/80 shadow-sm hover:border-emerald-400 transition-all flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-emerald-700">Tiket Selesai</p>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-600 mt-1">{{ $stats['selesai'] }}</p>
                    <p class="text-[11px] text-emerald-600/70 mt-1">Telah terselesaikan</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="ri-checkbox-circle-line text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Recent Tickets Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 sm:px-6 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="ri-history-line text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pengaduan Terbaru Anda</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                {{ $recentTickets->count() }} Terkini
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar 5 tiket pengaduan terakhir yang Anda ajukan</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 hover:text-emerald-700 bg-slate-50 hover:bg-emerald-50/70 border border-slate-200 hover:border-emerald-300 transition duration-150">
                        <span>Lihat Semua Riwayat</span>
                        <i class="ri-arrow-right-line text-xs"></i>
                    </a>
                </div>
            </div>

            @if($recentTickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 font-bold border-b border-slate-200/80 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th scope="col" class="py-3 px-4 sm:px-5">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="ri-hashtag text-slate-400 text-xs"></i>
                                        <span>No. Tiket</span>
                                    </span>
                                </th>
                                <th scope="col" class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="ri-price-tag-3-line text-slate-400 text-xs"></i>
                                        <span>Kategori</span>
                                    </span>
                                </th>
                                <th scope="col" class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="ri-file-text-line text-slate-400 text-xs"></i>
                                        <span>Subjek Kendala</span>
                                    </span>
                                </th>
                                <th scope="col" class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="ri-pulse-line text-slate-400 text-xs"></i>
                                        <span>Status Progres</span>
                                    </span>
                                </th>
                                <th scope="col" class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="ri-calendar-line text-slate-400 text-xs"></i>
                                        <span>Waktu Pengajuan</span>
                                    </span>
                                </th>
                                <th scope="col" class="py-3 px-4 sm:px-5 text-right">
                                    <span class="inline-flex items-center gap-1.5 justify-end">
                                        <i class="ri-flashlight-line text-slate-400 text-xs"></i>
                                        <span>Aksi</span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentTickets as $ticket)
                                <tr class="hover:bg-slate-50/70 transition-colors group">
                                    <td class="py-3 px-4 sm:px-5 whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-slate-100/90 group-hover:bg-emerald-50 border border-slate-200/80 group-hover:border-emerald-300 transition shadow-2xs">
                                            <span class="font-mono font-bold text-xs text-slate-900 group-hover:text-emerald-800">
                                                {{ $ticket->ticket_number }}
                                            </span>
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                            {{ $ticket->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 max-w-xs sm:max-w-sm">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-semibold text-slate-900 truncate">{{ $ticket->subject }}</span>
                                            @if($ticket->attachment)
                                                <span class="inline-flex items-center gap-0.5 text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded shrink-0 border border-slate-200/60" title="Memiliki lampiran berkas">
                                                    <i class="ri-attachment-2 text-xs text-slate-500"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <x-ticket-status-badge :status="$ticket->status" />
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-xs text-slate-500">
                                        {{ $ticket->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </td>
                                    <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-emerald-600 text-slate-700 hover:text-white border border-slate-200 hover:border-emerald-600 shadow-2xs hover:shadow-xs transition-all duration-150">
                                            <span>Detail</span>
                                            <i class="ri-arrow-right-line text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 px-4 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center mb-3">
                        <i class="ri-inbox-archive-line text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Belum Ada Pengaduan</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        Anda belum pernah membuat tiket pengaduan kendala TI. Jika membutuhkan bantuan teknisi, silakan klik tombol di bawah.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-semibold shadow-sm transition">
                            <i class="ri-add-line text-base"></i>
                            <span>Buat Pengaduan Pertama Anda</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

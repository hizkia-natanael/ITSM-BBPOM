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
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
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
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
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
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
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
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Tickets Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 sm:px-6 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pengaduan Terbaru Anda</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar 5 tiket pengaduan terakhir yang Anda ajukan</p>
                </div>
                <div>
                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-800 transition">
                        Lihat Seluruh Tiket
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            @if($recentTickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 font-semibold border-b border-slate-200/60 uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="py-3 px-4 sm:px-6">No. Tiket</th>
                                <th scope="col" class="py-3 px-4">Kategori</th>
                                <th scope="col" class="py-3 px-4">Subjek / Masalah</th>
                                <th scope="col" class="py-3 px-4">Status</th>
                                <th scope="col" class="py-3 px-4">Tanggal Pengajuan</th>
                                <th scope="col" class="py-3 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentTickets as $ticket)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 sm:px-6 font-bold text-slate-900">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-emerald-700 hover:underline">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                            {{ $ticket->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-900 font-medium max-w-xs truncate">
                                        {{ $ticket->subject }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <x-ticket-status-badge :status="$ticket->status" />
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">
                                        {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200/50">
                                            Detail & Tracking
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 px-4 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Belum Ada Pengaduan</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        Anda belum pernah membuat tiket pengaduan kendala TI. Jika membutuhkan bantuan teknisi, silakan klik tombol di bawah.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Pengaduan Pertama Anda
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

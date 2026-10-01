<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    Dashboard Layanan TI Pegawai
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Pantau status tindak lanjut kendala teknologi informasi BBPOM di Semarang
                </p>
            </div>
            <div>
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Pengaduan Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
            <div class="relative z-10 max-w-2xl">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-900/60 text-emerald-200 border border-emerald-500/30 inline-block mb-3">
                    Portal Layanan ITSM BBPOM di Semarang
                </span>
                <h3 class="text-xl sm:text-2xl font-bold tracking-tight">
                    Selamat Datang, {{ Auth::user()->name }}!
                </h3>
                <p class="mt-2 text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                    Mengalami kendala koneksi jaringan, kerusakan PC/laptop, printer, email dinas, atau aplikasi perkantoran? Ajukan tiket pengaduan sekarang dan tim TI BBPOM akan segera membantu menanganinya.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-bold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Laporkan Kendala TI
                    </a>
                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-900/40 border border-emerald-400/30 text-white hover:bg-emerald-900/60 text-xs font-semibold transition">
                        Lihat Riwayat Tiket
                    </a>
                </div>
            </div>

            <!-- Background decorative shapes -->
            <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
                <svg class="w-72 h-72 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Card 1: Total -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500">Total Pengaduan</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Keseluruhan tiket dibuat</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Pending (Kuning) -->
            <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm flex items-center justify-between">
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
            <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm flex items-center justify-between">
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
            <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm flex items-center justify-between">
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
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 sm:px-6 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base">Pengaduan Terbaru Anda</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar 5 tiket pengaduan terakhir yang Anda ajukan</p>
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
                        <thead class="bg-gray-50/75 text-gray-600 font-semibold border-b border-gray-100 uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="py-3 px-4 sm:px-6">No. Tiket</th>
                                <th scope="col" class="py-3 px-4">Kategori</th>
                                <th scope="col" class="py-3 px-4">Subjek / Masalah</th>
                                <th scope="col" class="py-3 px-4">Status</th>
                                <th scope="col" class="py-3 px-4">Tanggal Pengajuan</th>
                                <th scope="col" class="py-3 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentTickets as $ticket)
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="py-3.5 px-4 sm:px-6 font-bold text-gray-900">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-emerald-700 hover:underline">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-600">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                            {{ $ticket->category->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-900 font-medium max-w-xs truncate">
                                        {{ $ticket->subject }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <x-ticket-status-badge :status="$ticket->status" />
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-500">
                                        {{ $ticket->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
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
                    <h4 class="font-bold text-gray-900 text-sm">Belum Ada Pengaduan</h4>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1">
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

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Helpdesk & Layanan TI | BBPOM di Semarang</title>

        <!-- Fonts (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-slate-50 flex flex-col min-h-screen">
        <!-- Header / Navigation -->
        <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center">
                    <x-application-logo />
                </div>
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            @if(Auth::user()->isAdmin())
                                <a href="/admin" class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition">
                                    Panel Admin
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-700 text-white hover:bg-emerald-800 transition shadow-sm">
                                    Buka Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-emerald-700 px-3 py-2 transition">
                                Masuk (Login)
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-700 text-white hover:bg-emerald-800 transition shadow-sm">
                                    Registrasi Pegawai
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1">
            <div class="relative overflow-hidden bg-gradient-to-b from-emerald-50/60 to-transparent py-16 sm:py-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 mb-6">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            IT Service Management (ITSM) BBPOM di Semarang
                        </span>
                        <h1 class="text-3xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
                            Solusi Cepat Kendala TI & Layanan Helpdesk Terpadu
                        </h1>
                        <p class="mt-5 text-base sm:text-lg text-gray-600 leading-relaxed">
                            Layanan dukungan teknologi informasi resmi untuk seluruh pegawai Balai Besar Pengawas Obat dan Makanan di Semarang. Ajukan kendala perangkat keras, jaringan, sistem informasi, dan pantau status penyelesaian secara real-time.
                        </p>
                        <div class="mt-8 flex flex-wrap justify-center gap-4">
                            @auth
                                <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-emerald-700 text-white hover:bg-emerald-800 shadow-md shadow-emerald-700/25 transition">
                                    <svg class="w-5 h-5 me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Buat Pengaduan Baru
                                </a>
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 shadow-sm transition">
                                    Lihat Dashboard Pengguna
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-emerald-700 text-white hover:bg-emerald-800 shadow-md shadow-emerald-700/25 transition">
                                    <svg class="w-5 h-5 me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Masuk untuk Buat Pengaduan
                                </a>
                                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 shadow-sm transition">
                                    Daftar Akun Pegawai
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layanan IT Yang Tersedia -->
            <div class="py-12 bg-white border-y border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-700">Kategori Layanan</h2>
                        <p class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">Kategori Dukungan Layanan TI BBPOM</p>
                        <p class="mt-2 text-sm text-gray-500">Tim IT BBPOM siap membantu berbagai kebutuhan dan kendala operasional Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Card 1 -->
                        <div class="p-6 rounded-2xl border border-gray-100 bg-white hover:border-emerald-200 hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-base mb-1">Jaringan & Internet</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">Kendala koneksi LAN, WiFi kantor, VPN, dan stabilitas akses internet di lingkungan BBPOM Semarang.</p>
                        </div>

                        <!-- Card 2 -->
                        <div class="p-6 rounded-2xl border border-gray-100 bg-white hover:border-emerald-200 hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-base mb-1">Hardware & Komputer</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">Perbaikan PC, laptop dinas, monitor, pergantian periferal, dan perawatan perangkat kerja operasional.</p>
                        </div>

                        <!-- Card 3 -->
                        <div class="p-6 rounded-2xl border border-gray-100 bg-white hover:border-emerald-200 hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-base mb-1">Aplikasi & Software</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">Dukungan sistem aplikasi internal BPOM, lisensi software resmi, instalasi OS, dan bug aplikasi.</p>
                        </div>

                        <!-- Card 4 -->
                        <div class="p-6 rounded-2xl border border-gray-100 bg-white hover:border-emerald-200 hover:shadow-md transition">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-base mb-1">Printer & Periferal</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">Pengaturan printer jaringan, scanner, kendala driver, dan sharing perangkat di unit kerja.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alur Layanan -->
            <div class="py-16 bg-slate-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-700">Alur Penanganan</h2>
                        <p class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">4 Langkah Mudah Pengajuan Tiket</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-subtle relative">
                            <span class="w-8 h-8 rounded-full bg-emerald-700 text-white font-bold text-sm flex items-center justify-center mb-4">1</span>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Buat Pengaduan</h4>
                            <p class="text-xs text-gray-500">Isi subjek kendala, pilih kategori, deskripsikan masalah, dan sertakan foto lampiran.</p>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-subtle relative">
                            <span class="w-8 h-8 rounded-full bg-amber-500 text-white font-bold text-sm flex items-center justify-center mb-4">2</span>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Verifikasi (Pending)</h4>
                            <p class="text-xs text-gray-500">Tiket otomatis terdaftar dengan nomor unik dan tim IT BBPOM memverifikasi antrian.</p>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-subtle relative">
                            <span class="w-8 h-8 rounded-full bg-blue-500 text-white font-bold text-sm flex items-center justify-center mb-4">3</span>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Diproses & Diskusi</h4>
                            <p class="text-xs text-gray-500">Teknisi merespons tiket, melakukan perbaikan, dan memberikan instruksi melalui kolom diskusi.</p>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-subtle relative">
                            <span class="w-8 h-8 rounded-full bg-emerald-500 text-white font-bold text-sm flex items-center justify-center mb-4">4</span>
                            <h4 class="font-bold text-gray-900 text-sm mb-1">Selesai & Riwayat</h4>
                            <p class="text-xs text-gray-500">Kendala terselesaikan dengan status resmi 'Selesai' dan tercatat dalam arsip histori.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-500">
                <div class="flex items-center gap-3">
                    <x-application-logo />
                </div>
                <div class="text-center md:text-right">
                    <p class="font-medium text-gray-700">Balai Besar Pengawas Obat dan Makanan di Semarang</p>
                    <p class="text-gray-400 mt-0.5">Jl. Sukun Raya No.41A, Srondol Wetan, Kec. Banyumanik, Kota Semarang, Jawa Tengah 50263</p>
                </div>
            </div>
        </footer>
    </body>
</html>

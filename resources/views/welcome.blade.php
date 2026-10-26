<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('images/logo-bpom.png') }}">
        <title>Helpdesk & Layanan TI | BBPOM di Semarang</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

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
                    <div class="hidden sm:block">
                        <x-ui.online-visitor-counter variant="navbar" />
                    </div>
                    @if (Route::has('login'))
                        @auth
                            @if(Auth::user()->isAdmin())
                                <a href="{{ url('/admin') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition">
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
            <div class="relative overflow-hidden bg-slate-50 py-16 sm:py-24">
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
                        <div class="mt-8 inline-grid grid-cols-1 sm:grid-cols-2 gap-4 w-full sm:w-fit mx-auto">
                            @auth
                                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-emerald-700 text-white hover:bg-emerald-800 shadow-md shadow-emerald-700/25 transition gap-2 w-full">
                                    <i class="ri-add-line text-lg"></i>
                                    <span>Buat Pengaduan Baru</span>
                                </a>
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 shadow-sm transition w-full">
                                    Lihat Dashboard Pengguna
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-emerald-700 text-white hover:bg-emerald-800 shadow-md shadow-emerald-700/25 transition gap-2 w-full">
                                    <i class="ri-login-box-line text-lg"></i>
                                    <span>Masuk untuk Buat Pengaduan</span>
                                </a>
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-sm font-semibold bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 shadow-sm transition w-full">
                                    Daftar Akun Pegawai
                                </a>
                            @endauth
                            <x-ui.whatsapp-contact-button variant="light" class="sm:col-span-2 w-full py-3.5 text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <x-welcome.service-categories />
        </main>
    </body>
</html>

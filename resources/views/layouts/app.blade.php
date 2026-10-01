<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Layanan TIK BBPOM di Semarang') }}</title>

        <!-- Fonts (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Remix Icon Library -->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-full flex flex-col">
        @include('layouts.navigation')

        <!-- Toast Notification: Welcome on Login (Card Ucapan Compact) -->
        @if (session('welcome_toast'))
            <div 
                x-data="{ show: true, percent: 100 }" 
                x-show="show" 
                x-init="
                    const interval = 50;
                    const duration = 5000;
                    const step = (interval / duration) * 100;
                    const timer = setInterval(() => {
                        percent = Math.max(0, percent - step);
                        if (percent <= 0) {
                            clearInterval(timer);
                            show = false;
                        }
                    }, interval);
                "
                x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-8"
                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="fixed top-20 right-4 sm:right-6 z-50 w-[calc(100%-2rem)] sm:w-80 pointer-events-auto"
                style="display: none;"
                role="status"
                aria-live="polite"
            >
                <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950 text-white shadow-xl shadow-slate-950/40 border border-slate-700/70 ring-1 ring-white/10 backdrop-blur-md">
                    <!-- Subtle ambient glow -->
                    <div class="absolute -top-8 -right-8 w-20 h-20 bg-emerald-500/15 rounded-full blur-xl pointer-events-none"></div>

                    <div class="p-3.5 relative z-10">
                        <div class="flex items-start gap-3">
                            <!-- Icon / Avatar -->
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20 ring-1 ring-white/20">
                                <i class="ri-user-smile-line text-lg"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 pr-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">Login Berhasil</span>
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-white tracking-tight mt-0.5 leading-snug break-words">
                                    Selamat Datang, {{ session('welcome_toast') }}!
                                </h4>
                                <p class="text-[11px] text-slate-300/80 mt-0.5">
                                    Layanan TIK BBPOM di Semarang
                                </p>
                            </div>

                            <!-- Close Button -->
                            <button 
                                type="button" 
                                @click="show = false" 
                                class="text-slate-400 hover:text-white p-1 rounded-md hover:bg-white/10 transition-colors shrink-0 -mr-1 -mt-1"
                                title="Tutup"
                                aria-label="Tutup"
                            >
                                <i class="ri-close-line text-base leading-none"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Progress Bar Countdown -->
                    <div class="h-0.5 w-full bg-slate-800/80 overflow-hidden">
                        <div 
                            class="h-full bg-gradient-to-r from-emerald-400 to-teal-300 transition-all duration-75 ease-linear" 
                            :style="'width: ' + percent + '%'"
                        ></div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm flex items-start gap-3">
                    <div class="text-emerald-700 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-sm font-medium text-emerald-800">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="rounded-xl bg-red-50 border border-red-200 p-4 shadow-sm flex items-start gap-3">
                    <div class="text-red-600 mt-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-gradient-to-b from-white via-white to-slate-50/70 border-b border-slate-200/80 shadow-xs relative">
                <div class="max-w-7xl mx-auto py-5 sm:py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1 pt-5 pb-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-2">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-emerald-800">Balai Besar POM di Semarang</span>
                    <span>&bull;</span>
                    <span>Layanan Bantuan Teknologi Informasi (ITSM)</span>
                </div>
                <div>
                    &copy; {{ date('Y') }} Tim Teknologi Informasi BBPOM Semarang. Hak Cipta Dilindungi.
                </div>
            </div>
        </footer>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/png" href="{{ asset('images/logo-bpom.png') }}">

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

        <!-- Toast Notification: Welcome on Login -->
        <x-ui.welcome-toast />

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm flex items-start gap-3">
                    <div class="text-emerald-700 mt-0.5">
                        <i class="ri-checkbox-circle-line text-lg"></i>
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
                        <i class="ri-error-warning-line text-lg"></i>
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
                <div class="max-w-7xl mx-auto pt-7 pb-6 sm:pt-8 sm:pb-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1 pt-6 pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        @stack('scripts')
    </body>
</html>

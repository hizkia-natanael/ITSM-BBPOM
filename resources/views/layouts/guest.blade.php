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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-50 min-h-full flex flex-col justify-center items-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <a href="/" class="inline-flex justify-center">
                <x-application-logo class="w-auto h-12" />
            </a>
            <h2 class="mt-4 text-center text-xl font-bold tracking-tight text-gray-900">
                Layanan TIK BBPOM di Semarang
            </h2>
            <p class="mt-1 text-center text-xs text-gray-500">
                Balai Besar Pengawas Obat dan Makanan di Semarang
            </p>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-sm border border-gray-100 sm:rounded-2xl sm:px-10">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} BBPOM Semarang. All rights reserved.
            </div>
        </div>
    </body>
</html>

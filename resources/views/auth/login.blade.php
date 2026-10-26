<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bpom.png') }}">
    <title>TIK BBPOM di Semarang - Portal Masuk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 min-h-screen select-none bg-slate-50 overflow-x-hidden">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
        <!-- Panoramic Showcase Panel -->
        <x-auth.panoramic-showcase />

        <!-- Authentication Panel -->
        <div class="lg:col-span-6 flex flex-col justify-center p-6 sm:p-10 lg:p-14 xl:p-16 bg-white min-h-screen select-text">
            <div class="w-full max-w-md mx-auto py-6">
                <!-- Mobile Header Logo -->
                <div class="lg:hidden text-center mb-6">
                    <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo Badan POM" class="h-14 w-auto mx-auto object-contain mb-3">
                    <h2 class="text-xl font-black text-slate-900">TIK BBPOM di Semarang</h2>
                    <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[11px] font-semibold text-emerald-800">
                            <i class="ri-pulse-fill text-emerald-600"></i>
                            <span>Inovasi Tata Kelola Layanan TI</span>
                        </span>
                        <x-ui.online-visitor-counter variant="navbar" />
                    </div>
                </div>

                <!-- Form Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900">Masuk Akun</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                        Pilih nama pegawai atau masukkan kredensial untuk mengakses sistem helpdesk.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800 flex items-center gap-2">
                        <i class="ri-checkbox-circle-fill text-emerald-600 text-base flex-shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-medium text-rose-700">
                        <div class="flex items-center gap-2 mb-1.5 font-semibold text-rose-800">
                            <i class="ri-error-warning-fill text-rose-600 text-base flex-shrink-0"></i>
                            <span>Autentikasi Gagal</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700/90 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="loginData()" @submit="onSubmit">
                    @csrf
                    <input type="hidden" name="email" x-model="selectedEmail">
                    <x-auth.user-searchable-select />
                    <x-auth.credentials-inputs />

                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-slate-900 focus:ring-slate-900 cursor-pointer">
                            <span class="ms-2 text-xs font-medium text-slate-600">Tetap masuk</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-slate-700 hover:text-emerald-700 transition">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white py-3.5 px-4 rounded-xl text-xs sm:text-sm font-bold tracking-wide shadow-lg shadow-slate-900/20 transition-all flex items-center justify-center gap-2 group cursor-pointer">
                            <span>Masuk Sekarang</span>
                            <i class="ri-arrow-right-line text-base transition-transform group-hover:translate-x-1"></i>
                        </button>
                    </div>
                </form>

                <!-- WhatsApp Contact Center Action Button on Home / Login -->
                <div class="mt-4">
                    <x-ui.whatsapp-contact-button variant="light" class="w-full py-3.5 sm:text-sm" />
                </div>
            </div>
        </div>
    </div>

    <x-auth.login-script :users="$users" />
</body>
</html>

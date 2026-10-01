<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-2" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition-colors flex items-center gap-1">
                        <i class="ri-home-4-line text-xs"></i>
                        <span>Dashboard</span>
                    </a>
                    <i class="ri-arrow-right-s-line text-slate-300 text-xs"></i>
                    <span class="font-semibold text-emerald-800">Profil Pengguna</span>
                </nav>
                <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Profil Pengguna & Akun Pegawai
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Pengaturan identitas pegawai, NIP, NIK, dan kredensial akun Layanan TIK BBPOM di Semarang
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold shadow-xs hover:shadow transition-all">
                    <i class="ri-arrow-left-line text-sm"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- User Overview Hero Card -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden border border-slate-700/50">
            <!-- Decorative Ambient Glows -->
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white flex items-center justify-center text-2xl sm:text-3xl font-extrabold shadow-xl shadow-emerald-950/50 ring-4 ring-white/10 shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                                {{ $user->name }}
                            </h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                {{ $user->role === 'admin' ? 'Administrator TIK' : 'Pegawai / Pemohon' }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 flex items-center gap-1.5">
                            <i class="ri-mail-line text-emerald-400"></i>
                            {{ $user->email }}
                        </p>
                        <p class="text-xs text-emerald-300/80 mt-1 flex items-center gap-1">
                            <i class="ri-shield-check-line text-emerald-400"></i>
                            Balai Besar Pengawas Obat dan Makanan di Semarang
                        </p>
                    </div>
                </div>

                <!-- Quick Metadata Chips -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs shrink-0">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">NIP</span>
                        <span class="font-mono text-slate-100 font-semibold mt-0.5 block truncate">{{ $user->nip ?: '-' }}</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">NIK</span>
                        <span class="font-mono text-slate-100 font-semibold mt-0.5 block truncate">{{ $user->nik ?: '-' }}</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/10 col-span-2 sm:col-span-1">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Telepon</span>
                        <span class="font-mono text-slate-100 font-semibold mt-0.5 block truncate">{{ $user->phone_number ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Informasi Profil & Data Kepegawaian -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Section 2: Keamanan Akun & Perbarui Password -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Section 3: Hapus Akun (Zona Berbahaya) -->
        <div class="bg-white rounded-2xl border border-rose-200/80 shadow-sm p-6 sm:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>

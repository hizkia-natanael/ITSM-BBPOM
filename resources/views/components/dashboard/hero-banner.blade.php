<div class="bg-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden border border-slate-800">
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="max-w-2xl">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Portal Layanan ITSM BBPOM di Semarang
                </span>
            </div>
            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ Auth::user()->name }}!
            </h3>
            <p class="mt-2 text-xs sm:text-sm text-slate-300 leading-relaxed">
                Mengalami kendala koneksi jaringan, sertifikat BSrE, akun Zoom, perangkat PC/laptop, permohonan lisensi software, atau aplikasi dinas? Ajukan permohonan sekarang dan tim TIK BBPOM akan segera menanganinya.
            </p>
            <div class="mt-5 inline-grid grid-cols-1 sm:grid-cols-2 gap-3 w-full sm:w-fit">
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold shadow-md shadow-emerald-900/30 transition-all duration-150 active:scale-[0.99] w-full">
                    <i class="ri-add-circle-line text-base"></i>
                    <span>Laporkan Kendala TI</span>
                </a>
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 text-xs font-semibold transition-all duration-150 w-full">
                    <i class="ri-history-line text-base"></i>
                    <span>Lihat Riwayat Tiket</span>
                </a>
                <x-ui.whatsapp-contact-button variant="hero" class="sm:col-span-2 w-full" />
            </div>
        </div>

        <!-- Illustrated Official BPOM Card Badge -->
        <div class="hidden lg:flex items-center justify-center shrink-0">
            <div class="relative bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200 shadow-xl flex flex-col items-center justify-center transition-all duration-300 hover:-translate-y-0.5">
                <div class="p-1.5 flex items-center justify-center">
                    <img
                        src="{{ asset('images/logo-bpom.png') }}"
                        alt="Logo Resmi BPOM"
                        class="h-16 sm:h-20 w-auto object-contain"
                    >
                </div>

                <div class="mt-2.5 px-3 py-1 rounded-full bg-slate-50 border border-slate-200/80 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-[10px] font-bold tracking-wider text-slate-700 uppercase">BBPOM di Semarang</span>
                </div>
            </div>
        </div>
    </div>
</div>

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
                @if(!Auth::user()?->isKetuaTim())
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold shadow-md shadow-emerald-900/30 transition-all duration-150 active:scale-[0.99] w-full">
                    <i class="ri-add-circle-line text-base"></i>
                    <span>Laporkan Kendala TI</span>
                </a>
                @endif
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 text-xs font-semibold transition-all duration-150 w-full">
                    <i class="ri-history-line text-base"></i>
                    <span>Lihat Riwayat Tiket</span>
                </a>
                <x-ui.whatsapp-contact-button variant="hero" class="sm:col-span-2 w-full" />
            </div>
        </div>

        <!-- Video Panduan Tutorial Player (Replacing static BPOM badge) -->
        <div x-data="{ isPlaying: false }" class="w-full lg:w-[400px] xl:w-[460px] shrink-0">
            <!-- Highlighted Label Placed ABOVE the Video -->
            <div class="mb-2.5 flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 shadow-xs shadow-emerald-500/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <i class="ri-play-circle-fill text-emerald-400 text-sm"></i>
                    Video Panduan
                </span>
                <span class="text-[11px] font-medium text-slate-400">
                    BBPOM di Semarang
                </span>
            </div>

            <!-- Video Frame (Clean Poster Facade to Hide YouTube Native Title Bar) -->
            <div class="relative w-full aspect-video rounded-2xl overflow-hidden border border-slate-700/80 shadow-2xl bg-black">
                <template x-if="!isPlaying">
                    <button
                        type="button"
                        x-on:click="isPlaying = true"
                        class="absolute inset-0 w-full h-full group focus:outline-none focus:ring-2 focus:ring-emerald-500 block"
                        aria-label="Putar Video Panduan"
                    >
                        <img
                            src="{{ asset('images/tutorial-poster-dash.jpg') }}"
                            alt="Poster Video Panduan"
                            class="w-full h-full object-cover brightness-[0.9] group-hover:brightness-100 group-hover:scale-105 transition-all duration-300"
                        >
                        <div class="absolute inset-0 bg-slate-950/25 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-emerald-600 group-hover:bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-950/50 group-hover:scale-110 active:scale-95 transition-all duration-200">
                                <i class="ri-play-fill text-2xl ms-0.5"></i>
                            </div>
                        </div>
                    </button>
                </template>

                <template x-if="isPlaying">
                    <iframe
                        class="w-full h-full"
                        src="https://www.youtube-nocookie.com/embed/Y-f6RuxzaGo?autoplay=1&rel=0&modestbranding=1"
                        title="Video Panduan ELITE BBPOM Semarang"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    ></iframe>
                </template>
            </div>
        </div>
    </div>
</div>

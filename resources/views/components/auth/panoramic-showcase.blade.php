@php
    $satisfactionIndex = app(\App\Services\Satisfaction\SatisfactionSurveyService::class)->getSatisfactionIndex();
@endphp

<div class="hidden lg:flex lg:col-span-6 relative overflow-hidden flex-col justify-between p-8 xl:p-12 text-white min-h-screen bg-slate-950">
    <!-- Full Height Background Image: Lobby & Tulisan BALAI BESAR POM SEMARANG Terhighlight Penuh -->
    <img
        src="{{ asset('images/elite-login-hero.jpg') }}"
        alt="Lobby Kantor Balai Besar Pengawas Obat dan Makanan di Semarang"
        loading="lazy"
        decoding="async"
        class="absolute inset-0 w-full h-full object-cover object-[center_48%] scale-[1.28] transition-transform duration-700 opacity-95 brightness-[0.95] contrast-[1.05] saturate-[1.05]"
    >

    <!-- Scrim Transparan Terarah: Gelap halus di atas (area plafon) & gelap di bawah (lantai), backdrop kayu bernuansa hangat di tengah tetap 100% terang dan tajam -->
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-transparent to-slate-950/85"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/30 via-transparent to-transparent"></div>

    <!-- Top Brand Bar (Mengambang di area plafon atas) -->
    <div class="relative z-10 flex items-center justify-between gap-4 p-3 rounded-2xl bg-slate-900/60 backdrop-blur-md border border-white/20 shadow-xl">
        <div class="flex items-center gap-3">
            <div class="p-1.5 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 shadow-xs">
                <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" decoding="async" class="h-7 w-auto">
            </div>
            <div>
                <p class="text-xs font-bold tracking-tight text-white drop-shadow-sm">Balai Besar POM di Semarang</p>
                <p class="text-[10px] text-emerald-300 font-medium">Pusat Layanan Teknologi Informasi &amp; Komunikasi</p>
            </div>
        </div>
        <x-ui.online-visitor-counter variant="dark" />
    </div>

    <!-- Area Tengah: DIBIARKAN KOSONG / CLEAR agar backdrop kayu, logo BPOM, dan tulisan 3D "BALAI BESAR POM SEMARANG" tampil utuh dan bebas tabrakan -->
    <div class="flex-1 min-h-[160px] pointer-events-none"></div>

    <!-- Bottom Content: Manifesto & Stats bertumpu rapi di area bawah (lantai) -->
    <div class="relative z-10 mt-auto space-y-3.5 max-w-xl">
        <!-- Card Manifesto -->
        <div class="p-5 sm:p-6 rounded-2xl bg-slate-900/75 backdrop-blur-md border border-white/20 shadow-2xl space-y-2.5">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-500/25 border border-emerald-400/40 text-emerald-300 text-[11px] font-semibold uppercase tracking-wider backdrop-blur-sm">
                <i class="ri-pulse-fill text-emerald-400"></i>
                <span>ELITE &bull; Inovasi Tata Kelola Layanan TI Terintegrasi</span>
            </div>
            <h1 class="text-xl xl:text-2xl font-black text-white leading-snug tracking-tight drop-shadow-md">
                Melayani dengan Integritas, Kecepatan, dan Akuntabilitas.
            </h1>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-normal">
                Portal terpadu untuk pencatatan tiket gangguan, permohonan fasilitas TI, serta monitoring berkala seluruh infrastruktur teknologi BBPOM di Semarang.
            </p>
        </div>

        <!-- Bottom Stats Strip -->
        <div class="p-3.5 sm:p-4 rounded-xl bg-slate-900/70 backdrop-blur-md border border-white/15 grid grid-cols-3 gap-3 shadow-xl text-center">
            <div>
                <p class="text-xl font-black text-emerald-400 drop-shadow-sm">12+</p>
                <p class="text-[10px] text-slate-300 mt-0.5 font-medium">Kategori Layanan TI</p>
            </div>
            <div>
                <p class="text-xl font-black text-teal-300 drop-shadow-sm">{{ $satisfactionIndex['formatted'] }}</p>
                <p class="text-[10px] text-slate-300 mt-0.5 font-medium">
                    Indeks Kepuasan
                    @if(!empty($satisfactionIndex['total_surveys']))
                        <span class="block text-[9px] text-teal-200/80 font-normal">({{ $satisfactionIndex['total_surveys'] }} ulasan)</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xl font-black text-cyan-300 drop-shadow-sm">100%</p>
                <p class="text-[10px] text-slate-300 mt-0.5 font-medium">Terkontrol Digital</p>
            </div>
        </div>
    </div>
</div>

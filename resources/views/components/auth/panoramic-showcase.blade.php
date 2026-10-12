<div class="hidden lg:flex lg:col-span-6 relative overflow-hidden flex-col justify-between p-12 xl:p-16 text-white min-h-screen">
    <!-- Full Height Background Image (Gedung Kantor BBPOM di Semarang) -->
    <img
        src="{{ asset('images/kantor-bbpom-semarang.jpg') }}"
        alt="Gedung Kantor Balai Besar POM di Semarang"
        class="absolute inset-0 w-full h-full object-cover object-center scale-105 brightness-[0.82] contrast-[1.05]"
    >
    <!-- Cinematic Dark & Emerald Gradients -->
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-emerald-950/30"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/70 via-slate-950/20 to-transparent"></div>

    <!-- Top Brand Bar -->
    <div class="relative z-10 flex items-center gap-3">
        <div class="p-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20">
            <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="h-8 w-auto">
        </div>
        <div>
            <p class="text-sm font-bold tracking-tight text-white">Balai Besar POM di Semarang</p>
            <p class="text-[11px] text-emerald-300 font-medium">Pusat Layanan Teknologi Informasi & Komunikasi</p>
        </div>
    </div>

    <!-- Center Manifesto -->
    <div class="relative z-10 my-auto py-8 max-w-lg">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-5 backdrop-blur-md">
            <i class="ri-pulse-fill text-emerald-400"></i>
            <span>Inovasi Tata Kelola TI</span>
        </div>
        <h1 class="text-4xl xl:text-5xl font-black text-white leading-tight tracking-tight">
            Melayani dengan Integritas, Kecepatan, dan Akuntabilitas.
        </h1>
        <p class="mt-5 text-sm xl:text-base text-slate-300 leading-relaxed font-normal">
            Portal terpadu untuk pencatatan tiket gangguan, permohonan fasilitas TI, serta monitoring berkala seluruh infrastruktur teknologi BBPOM di Semarang.
        </p>
    </div>

    <!-- Bottom Stats Strip -->
    <div class="relative z-10 pt-6 border-t border-white/15 grid grid-cols-3 gap-4">
        <div>
            <p class="text-2xl font-black text-emerald-400">12+</p>
            <p class="text-[11px] text-slate-300 mt-0.5">Kategori Layanan TI</p>
        </div>
        <div>
            <p class="text-2xl font-black text-teal-300">4.9 / 5</p>
            <p class="text-[11px] text-slate-300 mt-0.5">Indeks Kepuasan Layanan</p>
        </div>
        <div>
            <p class="text-2xl font-black text-cyan-300">100%</p>
            <p class="text-[11px] text-slate-300 mt-0.5">Terkontrol Digital</p>
        </div>
    </div>
</div>

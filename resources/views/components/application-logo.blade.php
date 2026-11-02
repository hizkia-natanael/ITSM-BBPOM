<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5 sm:gap-3']) }}>
    <img 
        src="{{ asset('images/logo-bpom.png') }}" 
        alt="Logo Badan POM Resmi - ELITE" 
        class="h-9 sm:h-10 w-auto object-contain shrink-0"
    >
    <div class="flex flex-col text-left justify-center">
        <span class="font-black text-slate-900 text-sm sm:text-base tracking-tight leading-tight flex items-center gap-1.5">
            <span class="bg-gradient-to-r from-emerald-800 to-emerald-600 bg-clip-text text-transparent font-black tracking-wider">ELITE</span>
            <span class="hidden sm:inline-block text-[10px] uppercase font-bold text-slate-400 border-l border-slate-200 pl-1.5">BBPOM</span>
        </span>
        <span class="text-[10px] sm:text-[11px] font-semibold text-emerald-700 leading-tight">
            E-Layanan IT Terintegrasi
        </span>
    </div>
</div>

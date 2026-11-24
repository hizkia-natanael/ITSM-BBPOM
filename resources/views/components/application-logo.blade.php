@php
    $currentUser = auth()->user();
    $roleBadge = match (true) {
        $currentUser?->isKetuaTim() => 'Ketua Tim',
        $currentUser?->isAdmin() => 'Admin',
        $currentUser !== null => 'User',
        default => 'BBPOM',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5 sm:gap-3']) }}>
    <img 
        src="{{ asset('images/logo-bpom.png') }}" 
        alt="Logo Badan POM Resmi - ELITE" 
        class="h-9 sm:h-10 w-auto object-contain shrink-0"
    >
    <div class="flex flex-col text-left justify-center">
        <span class="font-black text-slate-900 text-sm sm:text-base tracking-tight leading-tight flex items-center gap-1.5">
            <span class="bg-gradient-to-r from-emerald-800 to-emerald-600 bg-clip-text text-transparent font-black tracking-wider">ELITE</span>
            <span class="hidden sm:inline-block h-3 w-px bg-slate-200" aria-hidden="true"></span>
            <span class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded-full text-[9.5px] uppercase font-extrabold tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200/80">{{ $roleBadge }}</span>
        </span>
        <span class="text-[10px] sm:text-[11px] font-semibold text-emerald-700 leading-tight">
            E-Layanan IT Terintegrasi
        </span>
    </div>
</div>

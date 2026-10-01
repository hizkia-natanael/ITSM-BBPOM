<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5 sm:gap-3']) }}>
    <img 
        src="{{ asset('images/logo-bpom.png') }}" 
        alt="Logo Badan POM Resmi" 
        class="h-9 sm:h-10 w-auto object-contain shrink-0"
    >
    <div class="flex flex-col text-left justify-center">
        <span class="font-extrabold text-slate-900 text-sm sm:text-base tracking-tight leading-tight">
            Layanan TIK
        </span>
        <span class="text-[11px] font-semibold text-emerald-700 leading-tight mt-0.5">
            BBPOM di Semarang
        </span>
    </div>
</div>

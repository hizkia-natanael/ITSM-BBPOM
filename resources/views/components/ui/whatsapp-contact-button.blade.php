@props([
    'variant' => 'hero',
])

@if ($variant === 'hero')
    <a
        href="https://wa.me/6283866277489"
        target="_blank"
        rel="noopener noreferrer"
        {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 text-xs font-semibold transition-all duration-150 active:scale-[0.99]']) }}
        title="Hubungi Contact Center Layanan TI via WhatsApp (Windi - 083866277489)"
    >
        <i class="ri-whatsapp-fill text-base text-emerald-400"></i>
        <span>Contact Center : Windi (083866277489)</span>
    </a>
@else
    <a
        href="https://wa.me/6283866277489"
        target="_blank"
        rel="noopener noreferrer"
        {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 hover:text-emerald-700 border border-slate-200 text-xs font-semibold shadow-2xs transition-all duration-150 active:scale-[0.99]']) }}
        title="Hubungi Contact Center Layanan TI via WhatsApp (Windi - 083866277489)"
    >
        <i class="ri-whatsapp-fill text-base text-emerald-600"></i>
        <span>Contact Center : Windi (083866277489)</span>
    </a>
@endif

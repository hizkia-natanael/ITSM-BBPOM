@props(['status', 'handler' => null])

@php
    $handlerName = is_object($handler) ? ($handler->name ?? null) : (is_string($handler) ? $handler : null);

    $classes = match($status) {
        'pending' => 'bg-amber-50 text-amber-800 border-amber-300 ring-amber-500/20',
        'diproses' => 'bg-blue-50 text-blue-800 border-blue-300 ring-blue-500/20',
        'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-300 ring-emerald-500/20',
        'ditolak' => 'bg-rose-50 text-rose-800 border-rose-300 ring-rose-500/20',
        default => 'bg-slate-50 text-slate-800 border-slate-300 ring-slate-500/20',
    };

    $dotClasses = match($status) {
        'pending' => 'bg-amber-500',
        'diproses' => 'bg-blue-600',
        'selesai' => 'bg-emerald-600',
        'ditolak' => 'bg-rose-600',
        default => 'bg-slate-400',
    };

    $label = match($status) {
        'pending' => 'Menunggu (Pending)',
        'diproses' => $handlerName ? "Sedang diproses oleh {$handlerName}" : 'Sedang Diproses',
        'selesai' => $handlerName ? "Selesai ditangani oleh {$handlerName}" : 'Selesai',
        'ditolak' => 'Ditolak',
        default => ucfirst($status),
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border shadow-2xs {$classes}"]) }}>
    <span class="relative flex h-2 w-2 shrink-0">
        @if(in_array($status, ['pending', 'diproses']))
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $dotClasses }}"></span>
        @endif
        <span class="relative inline-flex rounded-full h-2 w-2 {{ $dotClasses }}"></span>
    </span>
    @if($handlerName && in_array($status, ['diproses', 'selesai']))
        <i class="ri-user-star-line text-xs shrink-0 {{ $status === 'diproses' ? 'text-blue-700' : 'text-emerald-700' }}"></i>
    @endif
    <span>{{ $label }}</span>
</span>

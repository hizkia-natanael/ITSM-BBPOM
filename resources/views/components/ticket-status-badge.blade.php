@props(['status'])

@php
    $classes = match($status) {
        'pending' => 'bg-amber-50 text-amber-800 border-amber-300 ring-amber-500/20',
        'diproses' => 'bg-blue-50 text-blue-800 border-blue-300 ring-blue-500/20',
        'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-300 ring-emerald-500/20',
        'ditolak' => 'bg-rose-50 text-rose-800 border-rose-300 ring-rose-500/20',
        default => 'bg-gray-50 text-gray-800 border-gray-300 ring-gray-500/20',
    };

    $dotClasses = match($status) {
        'pending' => 'bg-[#eab308]',
        'diproses' => 'bg-[#3b82f6]',
        'selesai' => 'bg-[#22c55e]',
        'ditolak' => 'bg-[#ef4444]',
        default => 'bg-gray-400',
    };

    $label = match($status) {
        'pending' => 'Menunggu (Pending)',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        default => ucfirst($status),
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border shadow-xs {{ $classes }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
    {{ $label }}
</span>

@props([
    'title' => 'SLA Tindak Lanjut TI',
    'rules' => [
        'Verifikasi pengaduan maksimal <strong>1 - 2 jam kerja</strong>.',
        'Reset password & pembukaan blokir akun diproses prioritas oleh admin sistem.',
        'Penanganan kendala hardware/jaringan ditangani sesuai antrian & lokasi.',
    ]
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-3']) }}>
    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
        <i class="ri-time-line text-emerald-700 text-base"></i>
        <span>{{ $title }}</span>
    </h4>
    <ul class="space-y-2 text-xs text-slate-600 leading-relaxed">
        @foreach($rules as $rule)
            <li class="flex items-start gap-2">
                <span class="text-emerald-700 font-bold text-sm leading-none mt-0.5">&bull;</span>
                <span>{!! $rule !!}</span>
            </li>
        @endforeach
    </ul>
</div>

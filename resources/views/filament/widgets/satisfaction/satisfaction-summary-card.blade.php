@props(['metrics'])

<div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white shadow-md border border-slate-700/60 flex flex-col justify-between h-full space-y-5">
    <div>
        <div class="flex items-center justify-between gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                <i class="ri-shield-star-line text-xs"></i>
                <span>Indeks Kepuasan Pengguna</span>
            </span>
            <span class="text-[10px] text-slate-400 font-medium">Skala 1 - 5</span>
        </div>

        <div class="flex items-baseline gap-2.5">
            <span class="text-4xl font-black tracking-tight text-white drop-shadow-sm">
                {{ number_format($metrics['score'], 1, '.', '') }}
            </span>
            <span class="text-base font-semibold text-emerald-400">/ 5.0</span>
        </div>

        <div class="flex items-center gap-1 mt-1.5">
            @for ($i = 1; $i <= 5; $i++)
                <i class="ri-star-fill text-sm {{ $i <= round($metrics['score']) ? 'text-amber-400' : 'text-slate-600' }}"></i>
            @endfor
            <span class="ml-2 text-[11px] text-slate-300 font-medium">
                {{ $metrics['total_surveys'] > 0 ? "Berdasarkan {$metrics['total_surveys']} ulasan pegawai" : 'Belum ada responden' }}
            </span>
        </div>
    </div>

    <!-- 4 Indikator Mutu Progress Bars -->
    <div class="space-y-2.5 pt-3 border-t border-slate-700/60">
        <div class="space-y-1">
            <div class="flex justify-between text-[11px]">
                <span class="text-slate-300 flex items-center gap-1"><i class="ri-flashlight-line text-emerald-400"></i> Kecepatan</span>
                <span class="font-bold text-emerald-300">{{ number_format($metrics['breakdown']['speed'], 1) }}/5</span>
            </div>
            <div class="w-full bg-slate-700/60 rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-1.5 rounded-full" style="width: {{ ($metrics['breakdown']['speed'] / 5) * 100 }}%"></div>
            </div>
        </div>

        <div class="space-y-1">
            <div class="flex justify-between text-[11px]">
                <span class="text-slate-300 flex items-center gap-1"><i class="ri-shield-check-line text-emerald-400"></i> Kualitas Solusi</span>
                <span class="font-bold text-emerald-300">{{ number_format($metrics['breakdown']['quality'], 1) }}/5</span>
            </div>
            <div class="w-full bg-slate-700/60 rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-1.5 rounded-full" style="width: {{ ($metrics['breakdown']['quality'] / 5) * 100 }}%"></div>
            </div>
        </div>

        <div class="space-y-1">
            <div class="flex justify-between text-[11px]">
                <span class="text-slate-300 flex items-center gap-1"><i class="ri-customer-service-2-line text-emerald-400"></i> Komunikasi</span>
                <span class="font-bold text-emerald-300">{{ number_format($metrics['breakdown']['communication'], 1) }}/5</span>
            </div>
            <div class="w-full bg-slate-700/60 rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-1.5 rounded-full" style="width: {{ ($metrics['breakdown']['communication'] / 5) * 100 }}%"></div>
            </div>
        </div>

        <div class="space-y-1">
            <div class="flex justify-between text-[11px]">
                <span class="text-slate-300 flex items-center gap-1"><i class="ri-device-line text-emerald-400"></i> Sistem ELITE</span>
                <span class="font-bold text-emerald-300">{{ number_format($metrics['breakdown']['system'], 1) }}/5</span>
            </div>
            <div class="w-full bg-slate-700/60 rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-1.5 rounded-full" style="width: {{ ($metrics['breakdown']['system'] / 5) * 100 }}%"></div>
            </div>
        </div>
    </div>
</div>

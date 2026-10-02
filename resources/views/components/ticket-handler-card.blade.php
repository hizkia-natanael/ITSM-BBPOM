@props(['handler' => null, 'status' => 'pending'])

@php
    $hasHandler = !is_null($handler);
    $initials = $hasHandler ? strtoupper(substr($handler->name, 0, 2)) : 'IT';
@endphp

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
            <i class="ri-shield-user-line text-emerald-700"></i>
            Petugas Penanggung Jawab
        </h4>
        @if($hasHandler)
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                Teknisi ITSM
            </span>
        @else
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                Antrian Masuk
            </span>
        @endif
    </div>

    @if($hasHandler)
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 text-white font-bold text-base flex items-center justify-center shrink-0 shadow-xs ring-4 ring-emerald-50">
                {{ $initials }}
            </div>
            <div class="overflow-hidden">
                <h5 class="font-bold text-sm text-slate-900 truncate" title="{{ $handler->name }}">
                    {{ $handler->name }}
                </h5>
                <p class="text-xs text-slate-500 truncate mt-0.5 flex items-center gap-1.5" title="{{ $handler->email }}">
                    <i class="ri-mail-line text-slate-400 shrink-0"></i>
                    <span>{{ $handler->email }}</span>
                </p>
                <div class="mt-1 flex items-center gap-1.5">
                    @if($status === 'diproses')
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-600"></span>
                            </span>
                            Sedang Aktif Menangani
                        </span>
                    @elseif($status === 'selesai')
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            <i class="ri-checkbox-circle-line text-emerald-600"></i>
                            Tuntas Diselesaikan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                            <i class="ri-user-star-line text-slate-500"></i>
                            Petugas Ditugaskan
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mx-auto mb-2">
                <i class="ri-user-shared-2-line text-lg"></i>
            </div>
            <h5 class="text-xs font-bold text-slate-800">Menunggu Penugasan Petugas IT</h5>
            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                Pengaduan Anda sedang dalam antrian. Tim Helpdesk TIK (Admin 1 / Admin 2) akan segera meninjau dan ditugaskan menangani kendala ini.
            </p>
        </div>
    @endif
</div>

@props([
    'variant' => 'light',
])

@php
    $presence = app(\App\Services\Presence\OnlineVisitorService::class)->recordAndCount(request());
    $initialCount = (int) ($presence['online_count'] ?? 1);
    $endpointUrl = route('presence.online');
@endphp

@if ($variant === 'stat')
    <div
        x-data="bbpomOnlineCounter({{ $initialCount }}, '{{ $endpointUrl }}')"
        class="bg-white p-5 rounded-2xl border border-teal-200/80 shadow-sm hover:border-teal-400 transition-all flex items-center justify-between col-span-2 lg:col-span-1"
    >
        <div>
            <div class="flex items-center gap-1.5">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <p class="text-xs font-medium text-teal-800">Pengunjung Online</p>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-teal-700 mt-1 tabular-nums" x-text="onlineCount">{{ $initialCount }}</p>
            <p class="text-[11px] text-teal-600/80 mt-1">Aktif secara realtime</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100">
            <i class="ri-radar-line text-2xl"></i>
        </div>
    </div>
@elseif ($variant === 'navbar')
    <div
        x-data="bbpomOnlineCounter({{ $initialCount }}, '{{ $endpointUrl }}')"
        class="inline-flex items-center gap-2 h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl bg-emerald-50/75 hover:bg-emerald-50 border border-emerald-200/80 text-xs text-slate-700 shadow-2xs transition-all select-none"
        style="display: inline-flex; align-items: center; gap: 0.45rem; height: 2.35rem; padding: 0 0.7rem; border-radius: 0.75rem; background-color: rgba(236, 253, 245, 0.85); border: 1px solid rgba(167, 243, 208, 0.85); font-size: 0.75rem; color: #334155; box-shadow: 0 1px 2px rgba(5, 150, 105, 0.06);"
        title="Jumlah pengunjung yang sedang online secara realtime"
    >
        <span class="relative flex h-2 w-2 shrink-0" style="position: relative; display: inline-flex; width: 0.5rem; height: 0.5rem; flex-shrink: 0;">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" style="position: absolute; inset: 0; border-radius: 9999px; background-color: #34d399; opacity: 0.75;"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600" style="position: relative; display: inline-flex; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #059669;"></span>
        </span>
        <i class="ri-radar-line text-emerald-700 text-sm" style="color: #047857; font-size: 0.875rem; line-height: 1;"></i>
        <span class="font-extrabold text-emerald-950 tabular-nums" style="font-weight: 800; color: #022c22; font-variant-numeric: tabular-nums;" x-text="onlineCount">{{ $initialCount }}</span>
        <span class="hidden md:inline text-slate-600 font-semibold" style="color: #475569; font-weight: 600;">Pengunjung Online</span>
        <span class="md:hidden text-slate-600 font-semibold" style="color: #475569; font-weight: 600;">Online</span>
    </div>
@elseif ($variant === 'dark')
    <div
        x-data="bbpomOnlineCounter({{ $initialCount }}, '{{ $endpointUrl }}')"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/65 backdrop-blur-md border border-emerald-400/30 text-xs text-slate-200 shadow-sm"
        title="Jumlah pengunjung yang sedang aktif mengakses sistem saat ini"
    >
        <span class="relative flex h-2 w-2 shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
        </span>
        <i class="ri-radar-line text-emerald-400 text-xs"></i>
        <span class="font-bold text-white tabular-nums" x-text="onlineCount">{{ $initialCount }}</span>
        <span class="text-slate-300 font-medium">Pengunjung Online</span>
        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/25">
            Live
        </span>
    </div>
@else
    <div
        x-data="bbpomOnlineCounter({{ $initialCount }}, '{{ $endpointUrl }}')"
        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50/90 border border-emerald-200/80 text-xs text-slate-700 shadow-2xs"
        style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.25rem 0.75rem; border-radius: 9999px; background-color: rgba(236, 253, 245, 0.9); border: 1px solid rgba(167, 243, 208, 0.85); font-size: 0.75rem; color: #334155;"
        title="Jumlah pengunjung yang sedang aktif mengakses sistem secara realtime"
    >
        <span class="relative flex h-2 w-2 shrink-0" style="position: relative; display: inline-flex; width: 0.5rem; height: 0.5rem; flex-shrink: 0;">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" style="position: absolute; inset: 0; border-radius: 9999px; background-color: #34d399; opacity: 0.75;"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600" style="position: relative; display: inline-flex; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #059669;"></span>
        </span>
        <i class="ri-user-shared-line text-emerald-700 text-xs" style="color: #047857; font-size: 0.75rem;"></i>
        <span class="font-extrabold text-emerald-900 tabular-nums" style="font-weight: 800; color: #064e3b; font-variant-numeric: tabular-nums;" x-text="onlineCount">{{ $initialCount }}</span>
        <span class="text-slate-600 font-medium" style="color: #475569; font-weight: 500;">Pengunjung Online</span>
        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-600/10 text-emerald-800" style="padding: 0.1rem 0.4rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; text-transform: uppercase; background-color: rgba(5, 150, 105, 0.12); color: #065f46;">
            Realtime
        </span>
    </div>
@endif

<x-ui.online-visitor-script />

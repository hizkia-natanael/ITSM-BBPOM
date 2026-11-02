@props([
    'stepNumber',
    'title',
    'subtitle',
    'icon',
    'actorName' => null,
    'actorRole' => null,
    'timestamp' => null,
    'isDone' => false,
    'isActive' => false,
    'isRejected' => false,
])

<div class="flex sm:flex-col items-start sm:items-center text-left sm:text-center gap-3 sm:gap-2.5 relative">
    {{-- Node Icon Indicator --}}
    <div class="relative shrink-0">
        <div 
            class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-all duration-300 {{ 
                $isRejected ? 'bg-rose-600 text-white ring-4 ring-rose-100' : (
                $isDone ? 'bg-emerald-700 text-white ring-4 ring-emerald-50' : (
                $isActive ? 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' : 'bg-slate-100 text-slate-400 border border-slate-200'
            )) }}"
        >
            <i class="{{ $icon }} text-xl"></i>
        </div>
        @if($isDone)
            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-600 text-white rounded-full flex items-center justify-center text-[10px] ring-2 ring-white">
                <i class="ri-check-line font-bold"></i>
            </span>
        @endif
    </div>

    {{-- Stage Text & Metadata Cardlet --}}
    <div class="flex-1 w-full sm:w-auto">
        <div class="flex items-center sm:justify-center gap-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahap {{ $stepNumber }}</span>
            @if($isActive)
                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-100 text-blue-700">Aktif</span>
            @endif
        </div>
        <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 mt-0.5">{{ $title }}</h4>
        <p class="text-[11px] text-slate-500 leading-tight hidden sm:block mt-0.5">{{ $subtitle }}</p>

        {{-- Multi-Admin Execution Cardlet --}}
        <div class="mt-2.5 p-2 rounded-xl bg-slate-50/80 border border-slate-200/80 backdrop-blur-xs text-left w-full sm:min-w-[160px] shadow-2xs">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg {{ $isDone ? 'bg-emerald-100 text-emerald-800' : ($isActive ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-500') }} flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="{{ $isDone ? 'ri-user-star-line' : ($isActive ? 'ri-user-follow-line' : 'ri-user-3-line') }}"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold text-slate-800 truncate" title="{{ $actorName ?? 'Belum Ditugaskan' }}">
                        {{ $actorName ?? 'Menunggu Petugas' }}
                    </p>
                    <p class="text-[10px] font-medium text-slate-400 truncate leading-tight">
                        {{ $actorRole ?? 'Helpdesk TIK' }}
                    </p>
                </div>
            </div>

            {{-- Timestamp Eksekusi --}}
            <div class="mt-1.5 pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-[10px] font-mono">
                <span class="text-slate-400 flex items-center gap-1">
                    <i class="ri-time-line text-[11px]"></i>
                    <span>Waktu:</span>
                </span>
                <span class="{{ $timestamp ? 'font-bold text-slate-700' : 'text-slate-400 italic' }}">
                    {{ $timestamp ?? '--:-- WIB' }}
                </span>
            </div>
        </div>
    </div>
</div>

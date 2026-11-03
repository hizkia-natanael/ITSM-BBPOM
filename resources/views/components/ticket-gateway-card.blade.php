@props(['svcKey', 'svc'])

@php
    $subCount = !empty($svc['subcategories']) ? count($svc['subcategories']) : 0;
    $colorClass = match($svc['color'] ?? 'slate') {
        'indigo' => 'from-indigo-600 to-indigo-700 shadow-indigo-500/25',
        'blue' => 'from-blue-600 to-cyan-600 shadow-blue-500/25',
        'emerald' => 'from-emerald-600 to-teal-600 shadow-emerald-500/25',
        'cyan' => 'from-cyan-600 to-blue-600 shadow-cyan-500/25',
        'sky' => 'from-sky-600 to-blue-700 shadow-sky-500/25',
        'teal' => 'from-teal-600 to-emerald-700 shadow-teal-500/25',
        'purple' => 'from-purple-600 to-indigo-600 shadow-purple-500/25',
        'violet' => 'from-violet-600 to-purple-700 shadow-violet-500/25',
        'amber' => 'from-amber-500 to-orange-600 shadow-amber-500/25',
        default => 'from-slate-700 to-slate-800 shadow-slate-500/25',
    };
@endphp

<div 
    x-show="isMatch('{{ $svcKey }}')"
    x-transition.opacity.duration.200ms
    @click="selectService('{{ $svcKey }}')"
    class="group relative bg-white rounded-2xl p-4 border border-slate-200/80 hover:border-emerald-500/60 shadow-xs hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center gap-3.5 sm:gap-4"
>
    <!-- Service Icon Badge with Vibrant Color Gradient -->
    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $colorClass }} flex items-center justify-center shrink-0 shadow-md transition-transform duration-200 group-hover:scale-105">
        <i class="{{ $svc['icon'] }} text-2xl text-white"></i>
    </div>

    <!-- Service Details -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <h4 class="font-bold text-sm sm:text-base text-slate-900 group-hover:text-emerald-800 transition truncate">
                {{ $svc['name'] }}
            </h4>
            @if($subCount > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border-emerald-200 transition shrink-0">
                    {{ $subCount }} Modul
                </span>
            @endif
        </div>
        <p class="text-xs text-slate-500 truncate mt-0.5">
            {{ $svc['long_name'] }}
        </p>
        <div class="mt-1 flex items-center gap-2">
            <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wide">
                {{ $svc['badge'] }}
            </span>
        </div>
    </div>

    <!-- Micro-interaction Action Indicator -->
    <div class="w-8 h-8 rounded-xl bg-slate-50 group-hover:bg-emerald-600 text-slate-400 group-hover:text-white flex items-center justify-center transition-all duration-200 shrink-0">
        <i class="ri-arrow-right-line text-sm transition-transform group-hover:translate-x-0.5"></i>
    </div>
</div>

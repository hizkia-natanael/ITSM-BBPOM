@props([
    'pageNumber' => 1,
    'totalPages' => 14,
    'chapter' => 'Bab I',
    'title' => '',
    'isCover' => false,
])

<div {{ $attributes->merge([
    'class' => 'relative flex-1 flex flex-col h-full w-full overflow-hidden ' . 
    ($isCover 
        ? 'bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white' 
        : 'bg-[#FCFBF8] text-slate-800 shadow-[inset_12px_0_24px_-10px_rgba(0,0,0,0.07)]'
    ) . ' transition-all duration-300 print:shadow-none print:border-none print:break-inside-avoid print:bg-white'
]) }}>
    @if(!$isCover)
        <!-- Top Running Header -->
        <header class="px-6 sm:px-8 lg:px-9 pl-9 sm:pl-11 lg:pl-12 pt-3 sm:pt-3.5 pb-2 border-b border-slate-200/90 text-xs sm:text-sm font-semibold tracking-wider text-slate-500 uppercase select-none shrink-0 flex items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-800 font-bold">
                <i class="ri-shield-check-line text-base text-emerald-600"></i>
                <span class="tracking-tight text-xs sm:text-sm">ITSM BBPOM SEMARANG</span>
            </div>
            <div class="text-slate-600 font-semibold truncate max-w-[280px] sm:max-w-md text-end text-xs sm:text-sm">
                {{ $chapter }} &bull; {{ $title }}
            </div>
        </header>

        <!-- Page Body Content (Expands to fill 100% height between header and footer) -->
        <main class="flex-1 flex flex-col px-6 sm:px-8 lg:px-9 pl-9 sm:pl-11 lg:pl-12 py-3 min-h-0 text-sm sm:text-base text-slate-700">
            {{ $slot }}
        </main>

        <!-- Bottom Running Footer (Anchored cleanly to the bottom edge of the book frame) -->
        <footer class="px-6 sm:px-8 lg:px-9 pl-9 sm:pl-11 lg:pl-12 py-2 sm:py-2.5 border-t border-slate-200/90 mt-auto text-xs sm:text-sm font-medium text-slate-500 select-none shrink-0 flex items-center justify-between bg-slate-50/70">
            <span class="text-slate-500 text-xs sm:text-sm truncate">
                Dokumen Resmi Dinas &bull; Balai Besar POM di Semarang
            </span>
            <div class="flex items-center gap-1.5 font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 text-xs sm:text-sm">
                <span>Halaman</span>
                <span class="text-emerald-700">{{ $pageNumber }}</span>
                <span class="text-slate-400">/</span>
                <span>{{ $totalPages }}</span>
            </div>
        </footer>
    @else
        <!-- Cover Content Container -->
        <div class="flex-1 flex flex-col justify-between h-full overflow-hidden p-6 sm:p-10 lg:p-12">
            {{ $slot }}
        </div>
    @endif
</div>

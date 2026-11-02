@props([
    'currentPage' => 0,
    'totalPages' => 13,
])

<div 
    class="relative w-full max-w-6xl xl:max-w-7xl mx-auto mt-0.5 sm:mt-1 mb-auto perspective-1000 print:m-0 print:p-0 print:max-w-none print:[zoom:1]"
    style="zoom: 0.8;"
>
    <!-- Ambient Table/Desk Glow Behind Book -->
    <div class="absolute -inset-4 sm:-inset-6 bg-gradient-to-r from-emerald-950/40 via-slate-900/60 to-emerald-950/40 rounded-[2.5rem] blur-2xl -z-10 pointer-events-none opacity-80 print:hidden"></div>

    <!-- Hanging Satin Ribbon Bookmark (Top Edge) -->
    <div class="absolute -top-3 sm:-top-5 right-12 sm:right-24 z-30 flex items-center print:hidden pointer-events-none">
        <div class="relative flex flex-col items-center">
            <div class="w-7 sm:w-9 h-12 sm:h-16 bg-gradient-to-b from-emerald-600 via-emerald-700 to-emerald-800 rounded-t-sm shadow-md flex items-center justify-center text-emerald-100 font-bold text-[10px] tracking-wider border-x border-emerald-500/40">
                <i class="ri-bookmark-3-fill text-lg sm:text-xl text-emerald-200 drop-shadow"></i>
            </div>
            <!-- Ribbon Swallowtail Cut -->
            <div class="w-0 h-0 border-l-[14px] sm:border-l-[18px] border-l-transparent border-r-[14px] sm:border-r-[18px] border-r-transparent border-t-[10px] sm:border-t-[14px] border-t-emerald-800 -mt-[1px]"></div>
        </div>
    </div>

    <!-- Outer Hardcover Book Case (Executive Folio Leatherette Texture & Stitched Borders) -->
    <div class="relative bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-3 sm:p-5 lg:p-6 rounded-3xl sm:rounded-[2.5rem] shadow-2xl shadow-slate-950/80 border-2 border-slate-800/90 ring-1 ring-emerald-500/20 transition-all duration-300 print:p-0 print:border-none print:shadow-none print:ring-0">
        
        <!-- Book Edge Gilding Accent (Top, Bottom, Edge Gutter) -->
        <div class="absolute inset-1.5 sm:inset-2.5 rounded-[1.8rem] sm:rounded-[2.2rem] border border-emerald-500/25 pointer-events-none opacity-60 print:hidden"></div>
        <div class="absolute top-4 left-4 sm:top-6 sm:left-6 w-8 h-8 border-t-2 border-l-2 border-emerald-500/40 rounded-tl-xl pointer-events-none print:hidden"></div>
        <div class="absolute top-4 right-4 sm:top-6 sm:right-6 w-8 h-8 border-t-2 border-r-2 border-emerald-500/40 rounded-tr-xl pointer-events-none print:hidden"></div>
        <div class="absolute bottom-4 left-4 sm:bottom-6 sm:left-6 w-8 h-8 border-b-2 border-l-2 border-emerald-500/40 rounded-bl-xl pointer-events-none print:hidden"></div>
        <div class="absolute bottom-4 right-4 sm:bottom-6 sm:right-6 w-8 h-8 border-b-2 border-r-2 border-emerald-500/40 rounded-br-xl pointer-events-none print:hidden"></div>

        <!-- The Book Pages Stage (Explicit Height so children fill 100% down to the bottom border) -->
        <div class="relative w-full bg-[#FAF9F6] rounded-2xl sm:rounded-3xl shadow-inner shadow-slate-950/20 overflow-hidden border border-amber-950/10 h-[580px] sm:h-[660px] lg:h-[720px] min-h-[580px] sm:min-h-[660px] lg:min-h-[720px] flex flex-col print:border-none print:shadow-none print:h-auto">
            
            <!-- Subtle BBPOM Motif Watermark Background Layer (Creative & Soft Translucency) -->
            <div 
                class="absolute inset-0 pointer-events-none z-0 opacity-[0.035] bg-center bg-no-repeat bg-contain mix-blend-multiply print:hidden" 
                style="background-image: url('{{ asset('images/logo-bpom.png') }}'); background-size: 380px auto;"
            ></div>

            <!-- Left Leather-Bound Executive Spine Crease -->
            <div class="absolute inset-y-0 left-0 w-8 pointer-events-none z-30 bg-gradient-to-r from-black/25 via-black/10 to-transparent border-r border-black/10 shadow-inner print:hidden"></div>

            {{ $slot }}

        </div>
    </div>
</div>

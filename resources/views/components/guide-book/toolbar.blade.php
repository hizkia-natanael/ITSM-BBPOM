<div class="fixed bottom-2.5 sm:bottom-3.5 inset-x-0 z-40 flex justify-center px-3 pointer-events-none print:hidden">
    <div class="pointer-events-auto bg-slate-900/90 backdrop-blur-xl border border-slate-700/80 p-1 sm:p-1.5 rounded-full shadow-xl shadow-black/60 flex items-center gap-1 sm:gap-1.5 text-white ring-1 ring-emerald-500/20">
        
        <!-- Table of Contents Trigger -->
        <button 
            type="button" 
            @click="showToc = true"
            class="inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1 rounded-full text-xs font-semibold bg-emerald-700/70 hover:bg-emerald-600 text-emerald-100 border border-emerald-500/30 transition shadow-xs active:scale-95"
            title="Buka Daftar Isi"
        >
            <i class="ri-list-check-3 text-sm"></i>
            <span class="hidden md:inline text-[11px] sm:text-xs">Daftar Isi</span>
        </button>

        <div class="h-4.5 w-px bg-slate-700/80"></div>

        <!-- Previous Page Button -->
        <button 
            type="button" 
            @click="prevPage()"
            :disabled="currentPage === 0"
            class="inline-flex items-center gap-1 px-2 py-1 sm:px-2.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-semibold transition active:scale-95 disabled:opacity-40 disabled:pointer-events-none hover:bg-slate-800 text-slate-200"
            title="Halaman Sebelumnya [Panah Kiri]"
        >
            <i class="ri-arrow-left-s-line text-base"></i>
            <span class="hidden sm:inline">Sebelumnya</span>
        </button>

        <!-- Current Page Pill -->
        <div class="px-2.5 py-1 bg-slate-800/80 rounded-full border border-slate-700/60 flex items-center gap-1.5">
            <span class="text-[11px] sm:text-xs font-mono font-bold text-emerald-400" x-text="'Hal ' + (currentPage + 1) + ' / ' + (totalPages + 1)"></span>
            <span class="text-[11px] text-slate-300 hidden md:inline" x-text="getChapterBadgeText()"></span>
        </div>

        <!-- Next Page Button -->
        <button 
            type="button" 
            @click="nextPage()"
            :disabled="currentPage === totalPages"
            class="inline-flex items-center gap-1 px-2.5 py-1 sm:px-3 sm:py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition active:scale-95 disabled:opacity-40 disabled:pointer-events-none shadow-sm shadow-emerald-950/40"
            title="Halaman Berikutnya [Panah Kanan]"
        >
            <span class="hidden sm:inline">Berikutnya</span>
            <i class="ri-arrow-right-s-line text-base"></i>
        </button>

        <div class="h-4.5 w-px bg-slate-700/80"></div>

        <!-- Print / Cetak Mode Button -->
        <button 
            type="button" 
            @click="printGuide()"
            class="inline-flex items-center p-1.5 rounded-full hover:bg-slate-800 text-slate-300 hover:text-white transition"
            title="Cetak Dokumen atau Simpan ke PDF"
        >
            <i class="ri-printer-line text-sm"></i>
        </button>

        <!-- Fullscreen Toggle -->
        <button 
            type="button" 
            @click="toggleFullscreen()"
            class="hidden sm:inline-flex items-center p-1.5 rounded-full hover:bg-slate-800 text-slate-300 hover:text-white transition"
            :title="isFullscreen ? 'Keluar Layar Penuh' : 'Mode Layar Penuh'"
        >
            <i :class="isFullscreen ? 'ri-fullscreen-exit-line text-sm text-emerald-400' : 'ri-fullscreen-line text-sm'"></i>
        </button>
    </div>
</div>

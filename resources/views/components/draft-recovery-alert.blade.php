@props([
    'draftTimeText' => 'draftTimeText',
])

<!-- Modal Pemulihan Draf Formulir (Centered Glassmorphism Modal - Atomic Component) -->
<div 
    x-show="hasDraft" 
    x-cloak 
    @keydown.escape.window="if (hasDraft) discardDraft()"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="draft-modal-title" 
    role="dialog" 
    aria-modal="true"
>
    <!-- Glassmorphism Backdrop with Smooth Fade -->
    <div 
        x-show="hasDraft"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity"
        @click="discardDraft()"
    ></div>

    <!-- Center Dialog Container -->
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
        <!-- Modal Card Panel with Spring Scale Animation -->
        <div 
            x-show="hasDraft"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.stop
            class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 p-6 sm:p-8"
        >
            <!-- Decorative Accent Gradient Glow -->
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Top Row: Icon with Pulse Ring + Dismiss (X) Button -->
            <div class="flex items-start justify-between">
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 flex items-center justify-center shadow-xs">
                        <i class="ri-history-line text-2xl"></i>
                    </div>
                    <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-600 border-2 border-white"></span>
                    </span>
                </div>

                <button 
                    type="button" 
                    @click="discardDraft()" 
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                    title="Abaikan & Tutup"
                >
                    <i class="ri-close-line text-xl leading-none"></i>
                </button>
            </div>

            <!-- Header Info -->
            <div class="mt-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight" id="draft-modal-title">
                        <span x-text="draftService ? ('Lanjutkan Pengisian ' + draftService.name + '?') : 'Lanjutkan Pengisian Formulir?'"></span>
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-200" x-text="draftTimeText"></span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                    Halaman baru saja dimuat ulang atau tertutup sebelumnya. Isian formulir Anda berhasil kami amankan otomatis. Ingin melanjutkan pengisian terakhir atau mulai baru dari awal?
                </p>
            </div>

            <!-- Mini Preview Card of Saved Content -->
            <div class="mt-5 rounded-2xl bg-slate-50/90 border border-slate-200/80 p-4 space-y-2.5">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <i class="ri-file-list-3-line text-slate-500"></i>
                    <span>Ringkasan Isian Sebelumnya</span>
                </div>

                <div class="flex items-center justify-between gap-3 pt-1 border-t border-slate-200/60">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <i :class="draftService ? draftService.icon : 'ri-customer-service-2-line'" class="text-lg"></i>
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-slate-900 truncate" x-text="draftService ? draftService.name : 'Layanan TI'"></p>
                            <p class="text-[11px] text-slate-500 truncate" x-text="draftSubcategoryLabel || 'Kategori Standar'"></p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/80">
                            <i class="ri-check-line"></i>
                            <span x-text="draftFieldsCount + ' isian tersimpan'"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="mt-6 flex flex-col sm:flex-row items-center gap-3">
                <button 
                    type="button" 
                    @click="restoreDraft()" 
                    class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-emerald-500/20"
                >
                    <i class="ri-play-circle-line text-base"></i>
                    <span>Lanjutkan Pengisian</span>
                </button>

                <button 
                    type="button" 
                    @click="discardDraft()" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-2xl text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 transition-colors"
                >
                    <i class="ri-delete-bin-line text-sm"></i>
                    <span>Buang & Mulai Baru</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Pemulihan Berhasil (Modern Floating Island) -->
<div 
    x-show="showRestoredToast" 
    x-cloak 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-3 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed bottom-6 right-6 z-50 rounded-2xl bg-slate-900/95 backdrop-blur-md text-white px-5 py-3.5 shadow-2xl border border-slate-700/80 flex items-center gap-3.5 text-xs font-semibold"
>
    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
        <i class="ri-checkbox-circle-fill text-lg"></i>
    </div>
    <div>
        <p class="font-bold text-white text-xs">Progress Berhasil Dilanjutkan</p>
        <p class="text-[11px] text-slate-300 font-normal">Seluruh data isian formulir Anda telah dikembalikan seperti semula.</p>
    </div>
</div>

@props([
    'draftTimeText' => 'draftTimeText',
])

<!-- Alert Pemulihan Draf Isian Formulir (Atomic Component) -->
<div 
    x-show="hasDraft" 
    x-cloak 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="rounded-2xl bg-amber-50/95 border-2 border-amber-300 p-4 sm:p-5 shadow-xs mb-6"
>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-100 border border-amber-200 text-amber-800 flex items-center justify-center shrink-0 shadow-2xs">
                <i class="ri-history-line text-xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="text-xs sm:text-sm font-bold text-amber-950">
                        Draf Isian Formulir Ditemukan
                    </h4>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 border border-amber-300" x-text="{{ $draftTimeText }}"></span>
                </div>
                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                    Halaman sempat termuat ulang atau tertutup sebelumnya. Pulihkan data dan pilihan layanan agar isian Anda tidak hilang.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
            <button 
                type="button" 
                @click="restoreDraft()" 
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition-all"
            >
                <i class="ri-restart-line text-sm"></i>
                <span>Pulihkan Progress</span>
            </button>

            <button 
                type="button" 
                @click="discardDraft()" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-amber-100 text-amber-900 border border-amber-300 text-xs font-semibold rounded-xl transition-colors shadow-2xs"
            >
                <i class="ri-delete-bin-line text-sm"></i>
                <span>Abaikan</span>
            </button>
        </div>
    </div>
</div>

<!-- Toast Pemulihan Berhasil -->
<div 
    x-show="showRestoredToast" 
    x-cloak 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed bottom-6 right-6 z-50 rounded-2xl bg-emerald-800 text-white px-5 py-3.5 shadow-xl border border-emerald-700 flex items-center gap-3 text-xs font-semibold"
>
    <i class="ri-checkbox-circle-fill text-emerald-400 text-lg"></i>
    <span>Progress isian formulir berhasil dipulihkan!</span>
</div>

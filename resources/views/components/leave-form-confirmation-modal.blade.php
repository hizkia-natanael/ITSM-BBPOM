<!-- Modal Konfirmasi Meninggalkan Halaman (Atomic Component) -->
<div 
    x-show="showLeaveConfirm" 
    x-cloak 
    @keydown.escape.window="if (showLeaveConfirm) cancelLeave()"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="leave-modal-title" 
    role="dialog" 
    aria-modal="true"
>
    <!-- Glassmorphism Backdrop with Smooth Fade -->
    <div 
        x-show="showLeaveConfirm"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity"
        @click="cancelLeave()"
    ></div>

    <!-- Center Dialog Container -->
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
        <!-- Modal Card Panel -->
        <div 
            x-show="showLeaveConfirm"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.stop
            class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 p-6 sm:p-7"
        >
            <!-- Decorative Accent Gradient Blob -->
            <div class="absolute -top-12 -right-12 w-36 h-36 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Top Row: Icon + Close Button -->
            <div class="flex items-start justify-between">
                <div class="w-13 h-13 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-700 flex items-center justify-center shadow-xs">
                    <i class="ri-error-warning-line text-2xl"></i>
                </div>

                <button 
                    type="button" 
                    @click="cancelLeave()" 
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                    title="Batal"
                >
                    <i class="ri-close-line text-xl leading-none"></i>
                </button>
            </div>

            <!-- Title & Context -->
            <div class="mt-4">
                <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight" id="leave-modal-title">
                    Tinggalkan Pengisian Formulir?
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                    Anda sedang mengisi formulir tiket. Tenang saja, isian Anda <strong class="text-slate-700 font-semibold">otomatis tersimpan sebagai draf</strong> dan dapat dipulihkan saat kembali ke halaman ini.
                </p>
            </div>

            <!-- Safe Status Badge -->
            <div class="mt-4 rounded-xl bg-emerald-50/80 border border-emerald-200/80 px-3.5 py-2.5 flex items-center gap-2.5 text-xs text-emerald-800">
                <i class="ri-shield-check-line text-base text-emerald-700 shrink-0"></i>
                <span class="font-medium text-[11px] leading-snug">
                    Draf Anda telah diamankan otomatis di perangkat ini.
                </span>
            </div>

            <!-- Action Buttons Footer -->
            <div class="mt-6 flex flex-col-reverse sm:flex-row items-center gap-2.5">
                <button 
                    type="button" 
                    @click="proceedLeave()" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 transition-colors"
                >
                    <i class="ri-logout-box-r-line text-sm"></i>
                    <span>Tinggalkan Halaman</span>
                </button>

                <button 
                    type="button" 
                    @click="cancelLeave()" 
                    class="w-full sm:flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 shadow-md transition-all focus:ring-4 focus:ring-emerald-500/20"
                >
                    <i class="ri-edit-line text-sm"></i>
                    <span>Lanjutkan Mengisi</span>
                </button>
            </div>
        </div>
    </div>
</div>

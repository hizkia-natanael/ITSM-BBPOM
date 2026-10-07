<div 
    x-show="isSubmitting" 
    x-cloak 
    class="fixed inset-0 z-[100] overflow-y-auto"
    aria-labelledby="submission-loading-title" 
    role="dialog" 
    aria-modal="true"
    @keydown.escape.window="resetSubmissionState()"
>
    <!-- Frosted Dark Glass Backdrop with Smooth Fade -->
    <div 
        x-show="isSubmitting"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity cursor-wait"
    ></div>

    <!-- Center Dialog Container -->
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
        <!-- Floating Glassmorphism Card Panel -->
        <div 
            x-show="isSubmitting"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-3xl bg-white/95 backdrop-blur-xl text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 p-8 sm:p-9"
        >
            <!-- Decorative Accent Gradient Blobs -->
            <div class="absolute -top-16 -right-16 w-44 h-44 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-44 h-44 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Animated Dual-Ring Pulse Orb -->
            <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                <!-- Ambient Pulsing Ping -->
                <span class="absolute inset-0 rounded-3xl bg-emerald-500/20 animate-ping"></span>
                
                <!-- Inner Badge with Spinner -->
                <div class="relative w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-50 to-teal-50/80 border border-emerald-200/80 flex items-center justify-center shadow-lg shadow-emerald-600/10">
                    <i class="ri-loader-4-line text-4xl text-emerald-600 animate-spin"></i>
                </div>
            </div>

            <!-- Subtitle Badge -->
            <div class="mt-6 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/60 text-emerald-800 text-[11px] font-bold uppercase tracking-wider">
                <i class="ri-shield-check-line text-xs text-emerald-600"></i>
                <span>Helpdesk Layanan TI BBPOM</span>
            </div>

            <!-- Title & Context -->
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight" id="submission-loading-title">
                    Mengirimkan Pengaduan Tiket
                </h3>
                
                <!-- Dynamic Status Message with Smooth Height Container -->
                <div class="min-h-[44px] flex items-center justify-center mt-2 px-2">
                    <p class="text-xs sm:text-sm font-medium text-slate-600 transition-all duration-300" x-text="currentSubmissionStepText">
                        Memvalidasi data dan menyiapkan berkas lampiran...
                    </p>
                </div>
            </div>

            <!-- Indeterminate Progress Shimmer Bar -->
            <div class="mt-4 w-full bg-slate-100 rounded-full h-2 overflow-hidden relative border border-slate-200/60">
                <div class="submission-progress-bar h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600"></div>
            </div>

            <!-- Footer Reassurance / SLA Notice -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-center gap-2 text-[11px] text-slate-500">
                <i class="ri-information-line text-emerald-600 text-sm"></i>
                <span>Mohon tidak menutup peramban saat proses berlangsung</span>
            </div>

            <!-- Emergency Escape Action -->
            <div class="mt-2.5 flex items-center justify-center">
                <button 
                    type="button" 
                    @click="resetSubmissionState()"
                    class="text-[10px] text-slate-400 hover:text-slate-600 underline transition cursor-pointer"
                >
                    Tutup Tampilan Loading
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes submissionProgressSlide {
        0% {
            width: 15%;
            margin-left: 0%;
        }
        50% {
            width: 55%;
            margin-left: 30%;
        }
        100% {
            width: 25%;
            margin-left: 80%;
        }
    }
    .submission-progress-bar {
        animation: submissionProgressSlide 2s ease-in-out infinite alternate;
    }
</style>

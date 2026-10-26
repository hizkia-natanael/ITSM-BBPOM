@if (session('welcome_toast'))
    <div
        x-data="bbpomWelcomeToastData()"
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-8"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed top-20 right-4 sm:right-6 z-50 w-[calc(100%-2rem)] sm:w-80 pointer-events-auto"
        style="display: none;"
        role="status"
        aria-live="polite"
    >
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950 text-white shadow-xl shadow-slate-950/40 border border-slate-700/70 ring-1 ring-white/10 backdrop-blur-md">
            <div class="absolute -top-8 -right-8 w-20 h-20 bg-emerald-500/15 rounded-full blur-xl pointer-events-none"></div>

            <div class="p-3.5 relative z-10">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20 ring-1 ring-white/20">
                        <i class="ri-user-smile-line text-lg"></i>
                    </div>

                    <div class="flex-1 min-w-0 pr-1">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">Login Berhasil</span>
                        </div>
                        <h4 class="text-xs sm:text-sm font-bold text-white tracking-tight mt-0.5 leading-snug break-words">
                            Selamat Datang, {{ session('welcome_toast') }}!
                        </h4>
                        <p class="text-[11px] text-slate-300/80 mt-0.5">
                            Layanan TIK BBPOM di Semarang
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="show = false"
                        class="text-slate-400 hover:text-white p-1 rounded-md hover:bg-white/10 transition-colors shrink-0 -mr-1 -mt-1"
                        title="Tutup"
                        aria-label="Tutup"
                    >
                        <i class="ri-close-line text-base leading-none"></i>
                    </button>
                </div>
            </div>

            <div class="h-0.5 w-full bg-slate-800/80 overflow-hidden">
                <div
                    class="h-full bg-gradient-to-r from-emerald-400 to-teal-300 transition-all duration-75 ease-linear"
                    :style="'width: ' + percent + '%'"
                ></div>
            </div>
        </div>
    </div>

    @once
        <script>
            window.bbpomWelcomeToastData = function bbpomWelcomeToastData() {
                return {
                    show: true,
                    percent: 100,
                    init() {
                        const interval = 50;
                        const duration = 5000;
                        const step = (interval / duration) * 100;
                        const timer = setInterval(() => {
                            this.percent = Math.max(0, this.percent - step);
                            if (this.percent === 0) {
                                clearInterval(timer);
                                this.show = false;
                            }
                        }, interval);
                    }
                };
            };
        </script>
    @endonce
@endif

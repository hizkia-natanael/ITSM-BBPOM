                <!-- ================= HALAMAN 0: SAMPUL DEPAN (COVER) ================= -->
                <div 
                    x-show="currentPage === 0" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                >
                    <x-guide-book.page-sheet :isCover="true">
                        <div class="h-full flex flex-col justify-between p-6 sm:p-10 lg:p-12 text-center relative overflow-hidden">
                            <!-- Ambient Radial Glow -->
                            <div class="absolute -top-16 -left-16 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute -bottom-16 -right-16 w-72 h-72 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>

                            <!-- Top Emblems & Header (Tanpa kata "RI") -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/10 backdrop-blur-md p-3.5 sm:p-4 border border-white/20 shadow-2xl mb-3 flex items-center justify-center">
                                    <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="w-full h-auto drop-shadow-md">
                                </div>
                                <span class="text-xs sm:text-sm font-bold tracking-widest text-emerald-300 uppercase">
                                    Badan Pengawas Obat dan Makanan
                                </span>
                                <span class="text-sm sm:text-base font-semibold tracking-wider text-slate-300 uppercase mt-0.5">
                                    Balai Besar POM di Semarang
                                </span>
                            </div>

                            <!-- Main Title with Gold/Emerald Border -->
                            <div class="relative z-10 my-4 py-6 border-y border-emerald-500/30 max-w-3xl mx-auto w-full">
                                <span class="inline-block px-3.5 py-1 rounded-full text-xs font-mono tracking-widest uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-2.5">
                                    PETUNJUK OPERASIONAL SISTEM RESMI
                                </span>
                                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight uppercase leading-tight font-serif drop-shadow-sm">
                                    BUKU PANDUAN PENGOPERASIAN SISTEM
                                </h2>
                                <p class="text-sm sm:text-lg font-medium text-emerald-200 mt-2">
                                    Layanan & Pengaduan Teknologi Informasi (ITSM)
                                </p>
                            </div>

                            <!-- Metadata Badges & Start Button -->
                            <div class="relative z-10 flex flex-col items-center gap-4">
                                <div class="flex flex-wrap justify-center gap-2 text-xs sm:text-sm font-semibold text-slate-300">
                                    <span class="px-3 py-1 rounded-lg bg-white/10 border border-white/15">12 Katalog Layanan Terpadu</span>
                                    <span class="px-3 py-1 rounded-lg bg-white/10 border border-white/15">Panduan Pengguna & Admin</span>
                                    <span class="px-3 py-1 rounded-lg bg-white/10 border border-white/15">Edisi Tata Kelola 2026</span>
                                </div>

                                <button 
                                    type="button" 
                                    @click="nextPage()"
                                    class="inline-flex items-center gap-2.5 px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-sm sm:text-base shadow-lg shadow-emerald-950/50 hover:shadow-xl transition-all active:scale-95 group"
                                >
                                    <span>Buka Buku Panduan</span>
                                    <i class="ri-arrow-right-line text-base group-hover:translate-x-1.5 transition-transform"></i>
                                </button>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


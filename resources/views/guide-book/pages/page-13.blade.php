                <!-- ================= HALAMAN 13: SAMPUL BELAKANG (BACK COVER) ================= -->
                <div 
                    x-show="currentPage === 13" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :isCover="true">
                        <div class="h-full flex flex-col justify-between p-6 sm:p-10 text-center relative overflow-hidden">
                            <!-- Top BPOM Emblem (Enlarged) -->
                            <div class="flex flex-col items-center">
                                <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="w-16 sm:w-20 h-auto drop-shadow-md mb-2">
                                <h4 class="text-sm sm:text-base font-bold text-emerald-300 uppercase tracking-widest">
                                    Balai Besar POM di Semarang
                                </h4>
                                <span class="text-xs sm:text-sm text-slate-400">
                                    Badan Pengawas Obat dan Makanan
                                </span>
                            </div>

                            <!-- Middle Slogan & Integrity Quote -->
                            <div class="my-4 py-4 border-y border-emerald-500/20 space-y-2 max-w-xl mx-auto">
                                <span class="text-sm sm:text-lg font-serif italic text-emerald-200 block">
                                    "Melayani dengan Integritas, Profesionalitas, dan Komitmen Transformasi Digital."
                                </span>
                                <div class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                                    Panduan resmi ini berlaku untuk seluruh unit kerja, laboratorium pengujian, dan tata usaha di lingkungan BBPOM Semarang.
                                </div>
                            </div>

                            <!-- Footer Address & Return Button -->
                            <div class="space-y-3">
                                <div class="text-xs sm:text-sm text-slate-400 space-y-0.5">
                                    <p class="font-semibold text-slate-200">Jl. Sukun Raya No. 41A Banyumanik, Semarang 50264</p>
                                    <p>Telp: 024 - 7612324 (ULPK) &bull; Fax: 024 - 7613633</p>
                                    <p>Email: bpom_semarang@pom.go.id &bull; Website: www.pom.go.id</p>
                                </div>

                                <button 
                                    type="button" 
                                    @click="goToPage(0)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white text-xs sm:text-sm font-semibold transition active:scale-95"
                                >
                                    <i class="ri-arrow-up-line"></i>
                                    <span>Kembali ke Sampul Depan</span>
                                </button>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


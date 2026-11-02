                <!-- ================= HALAMAN 3: BAB I - MAKSUD, TUJUAN, & MANFAAT ================= -->
                <div 
                    x-show="currentPage === 3" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="3" chapter="Bab I" title="Maksud, Tujuan, & Manfaat Sistem">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">2</span>
                                <h3>Maksud, Tujuan, & Manfaat Sistem</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Sistem Manajemen Layanan TI BBPOM di Semarang dibangun untuk menciptakan ekosistem layanan teknologi informasi yang fleksibel, transparan, dan berorientasi pada kemudahan pengguna.
                            </p>

                            <!-- 4 Pilar Strategis (Grid 2x2 Wide Cards, Expands to fill vertical room) -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 py-1.5">
                                <div class="p-5 sm:p-6 rounded-2xl bg-emerald-50/70 border border-emerald-200/90 flex flex-col justify-between shadow-2xs">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                            <i class="ri-compass-3-line text-2xl"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Fleksibilitas Layanan</h4>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                        Lapor kendala kapan saja dari ponsel atau komputer tanpa perlu tatap muka langsung, memudahkan pegawai di ruang uji maupun kantor.
                                    </p>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-blue-50/70 border border-blue-200/90 flex flex-col justify-between shadow-2xs">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                            <i class="ri-eye-line text-2xl"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Transparansi Penuh</h4>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                        Pantau perkembangan status penanganan dan nama teknisi yang bertanggung jawab secara realtime dari meja kerja Anda.
                                    </p>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-indigo-50/70 border border-indigo-200/90 flex flex-col justify-between shadow-2xs">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center shrink-0">
                                            <i class="ri-bubble-chart-line text-2xl"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Sentralisasi Bantuan</h4>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                        Satu pintu gerbang resmi untuk seluruh 12 jenis kebutuhan TI dinas: hardware, software, jaringan, email, hingga permohonan Zoom.
                                    </p>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-purple-50/70 border border-purple-200/90 flex flex-col justify-between shadow-2xs">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center shrink-0">
                                            <i class="ri-file-chart-line text-2xl"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Arsip & Rekapitulasi Kinerja</h4>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                        Setiap tindakan tercatat untuk evaluasi berkala, laporan akuntabilitas pimpinan, dan standar mutu pelayanan operasional.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


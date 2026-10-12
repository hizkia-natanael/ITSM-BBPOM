                <!-- ================= HALAMAN 8: BAB IV - PANDUAN ADMIN TRIAGE ================= -->
                <div 
                    x-show="currentPage === 8" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="8" chapter="Bab IV" title="Panduan Admin: Triage & Manajemen Tiket">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">1</span>
                                <h3>Panduan Administrator: Triage & Manajemen Tiket</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Petunjuk operasional bagi tim pengelola helpdesk dan teknisi TI dalam mengelola alur penanganan tiket dinas:
                            </p>

                            <!-- 3 Aksi Utama Administrator (Expands to fill vertical room) -->
                            <div class="flex-1 flex flex-col justify-between gap-3.5 py-1.5">
                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-3xl shrink-0 border border-emerald-100">
                                        <i class="ri-dashboard-3-line"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">1. Akses Portal Administrator Filament</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                            Administrator login ke panel khusus <strong>/admin</strong> untuk melihat metrik total aduan, tiket belum tertangani, dan memonitor antrean tiket dari seluruh unit kerja BBPOM Semarang.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-3xl shrink-0 border border-blue-100">
                                        <i class="ri-user-add-line"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">2. Mekanisme Klaim Tiket Mandiri</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                            Teknisi mengambil alih tiket dengan mengklik tombol <strong>Klaim Tiket</strong>. Nama teknisi otomatis tersemat sebagai penanggung jawab resmi, menghindari pengerjaan ganda antarteknisi.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-3xl shrink-0 border border-purple-100">
                                        <i class="ri-checkbox-line"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">3. Pembaruan Status & Catatan Solusi</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                            Setelah perbaikan fisik atau remote tuntas, teknisi wajib menuliskan ringkasan solusi teknis dan mengubah status menjadi Selesai sebagai rekam jejak akuntabilitas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


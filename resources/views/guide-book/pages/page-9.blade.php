                <!-- ================= HALAMAN 9: BAB IV - PANDUAN ADMIN EKSPOR ================= -->
                <div 
                    x-show="currentPage === 9" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="9" chapter="Bab IV" title="Panduan Admin: Ekspor PDF & Excel Resmi">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">2</span>
                                <h3>Fitur Ekspor Dokumen Resmi Kedinasan</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Fasilitas rekapitulasi data pengaduan untuk keperluan audit kinerja, evaluasi pimpinan, dan pelaporan berkala:
                            </p>

                            <!-- 2 Format Ekspor Utama (Expands to fill vertical room) -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 py-1.5">
                                <div class="p-6 sm:p-7 rounded-2xl bg-red-50/70 border border-red-200/90 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-2xl">
                                                <i class="ri-file-pdf-2-line"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-base sm:text-lg text-slate-900">Laporan Resmi PDF (Landscape)</h4>
                                                <span class="text-xs text-rose-700 font-semibold uppercase">Dokumen Cetak Pimpinan</span>
                                            </div>
                                        </div>
                                        <ul class="text-xs sm:text-sm text-slate-600 space-y-2 mt-3 list-disc list-inside leading-relaxed">
                                            <li>Header resmi Balai Besar POM di Semarang dengan tipografi Humanist & Tschichold.</li>
                                            <li>Format landscape profesional siap cetak.</li>
                                            <li>Kolom tanda tangan resmi Pejabat Pengelola Helpdesk TI.</li>
                                        </ul>
                                    </div>
                                    <div class="pt-3 border-t border-rose-200/60 text-xs text-rose-800 font-medium">
                                        Cocok untuk lampiran rapat pimpinan dan audit ISO
                                    </div>
                                </div>

                                <div class="p-6 sm:p-7 rounded-2xl bg-emerald-50/70 border border-emerald-200/90 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl">
                                                <i class="ri-file-excel-2-line"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-base sm:text-lg text-slate-900">Rekapitulasi Excel Kustom</h4>
                                                <span class="text-xs text-emerald-700 font-semibold uppercase">Olah Data & Statistik</span>
                                            </div>
                                        </div>
                                        <ul class="text-xs sm:text-sm text-slate-600 space-y-2 mt-3 list-disc list-inside leading-relaxed">
                                            <li>Header sel tunggal yang rapi dan terstandar.</li>
                                            <li>Filter fleksibel berdasarkan kategori katalog, status, teknisi, atau rentang tanggal.</li>
                                            <li>Data bersih untuk analisis beban kerja dan tren insiden.</li>
                                        </ul>
                                    </div>
                                    <div class="pt-3 border-t border-emerald-200/60 text-xs text-emerald-800 font-medium">
                                        Dapat diolah lebih lanjut menggunakan rumus spreadsheet
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


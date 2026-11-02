                <!-- ================= HALAMAN 12: PENUTUP - IKHTISAR BUDAYA LAYANAN ================= -->
                <div 
                    x-show="currentPage === 12" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="12" chapter="Penutup" title="Ikhtisar Budaya Layanan TIK">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <i class="ri-checkbox-circle-fill text-emerald-600 text-2xl"></i>
                                <h3>Ikhtisar Budaya Layanan TIK</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Penerapan Sistem Manajemen Layanan TI (ITSM) menuntut sinergi antara pemohon dan penangan. Kunci keberhasilan implementasi terletak pada:
                            </p>

                            <div class="flex-1 flex flex-col justify-between gap-3.5 py-1.5">
                                <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-lg">
                                        1
                                    </div>
                                    <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                        <strong class="font-bold text-slate-900 block text-base">Disiplin Tiket:</strong>
                                        Mengajukan setiap kebutuhan melalui sistem resmi agar tercatat dan memiliki kepastian serta rekam jejak penanganan yang dapat diaudit.
                                    </div>
                                </div>

                                <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-lg">
                                        2
                                    </div>
                                    <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                        <strong class="font-bold text-slate-900 block text-base">Keterbukaan Informasi:</strong>
                                        Memberikan deskripsi kendala yang jelas serta melampirkan bukti agar teknisi dapat bergerak cepat dan tepat sasaran.
                                    </div>
                                </div>

                                <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0 text-lg">
                                        3
                                    </div>
                                    <div class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                        <strong class="font-bold text-slate-900 block text-base">Umpan Balik Positif:</strong>
                                        Mengonfirmasi penyelesaian kendala saat tindakan tuntas dilakukan untuk menjaga kualitas layanan secara berkesinambungan.
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 text-xs sm:text-sm text-emerald-950 font-semibold text-center shrink-0">
                                "Mewujudkan Pelayanan Publik Berkelas Dunia yang Akuntabel dan Bebas dari Maladministrasi."
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


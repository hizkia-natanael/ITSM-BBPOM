                <!-- ================= HALAMAN 7: BAB III - PANDUAN PENGGUNA PELACAKAN ================= -->
                <div 
                    x-show="currentPage === 7" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="7" chapter="Bab III" title="Panduan Pengguna: Pelacakan & Komunikasi">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">2</span>
                                <h3>Fitur Pelacakan Status & Chat Tiket</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Kemudahan memantau tiket yang sedang berjalan dan berkomunikasi langsung dengan teknisi yang menangani:
                            </p>

                            <!-- 2 Kolom Fitur Lebar (Expands to fill vertical room) -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 py-1.5">
                                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 text-slate-900 font-bold text-base mb-2">
                                        <i class="ri-time-line text-emerald-600 text-xl"></i>
                                        <span>4 Status Penanganan Tiket:</span>
                                    </div>
                                    <div class="space-y-2.5 text-xs sm:text-sm">
                                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                                            <strong class="text-amber-900 block font-bold">1. Menunggu:</strong>
                                            <span class="text-amber-800">Tiket berhasil terkirim, menanti verifikasi & penugasan teknisi.</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                                            <strong class="text-blue-900 block font-bold">2. Diproses:</strong>
                                            <span class="text-blue-800">Teknisi telah mengklaim tiket dan sedang melakukan perbaikan.</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-purple-50 border border-purple-200">
                                            <strong class="text-purple-900 block font-bold">3. Tertunda:</strong>
                                            <span class="text-purple-800">Menunggu konfirmasi pemohon atau suku cadang tambahan.</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                                            <strong class="text-emerald-900 block font-bold">4. Selesai:</strong>
                                            <span class="text-emerald-800">Kendala tuntas diselesaikan dan solusi perbaikan dicatat resmi.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 rounded-2xl bg-emerald-50/60 border border-emerald-200 flex flex-col justify-between shadow-2xs">
                                    <div class="flex items-center gap-2 text-emerald-950 font-bold text-base mb-2">
                                        <i class="ri-chat-smile-2-line text-emerald-700 text-xl"></i>
                                        <span>Fitur Komunikasi & Kolaborasi:</span>
                                    </div>
                                    <div class="space-y-3 text-xs sm:text-sm text-slate-700">
                                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-2xs">
                                            <strong class="text-emerald-900 block font-bold text-sm">Live Chat Terintegrasi:</strong>
                                            <span class="text-slate-600">Berinteraksi secara dua arah dengan teknisi penanggung jawab langsung pada tiket.</span>
                                        </div>
                                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-2xs">
                                            <strong class="text-emerald-900 block font-bold text-sm">Unggah Bukti Susulan:</strong>
                                            <span class="text-slate-600">Dapat mengirimkan foto error tambahan kapan saja melalui tombol lampiran chat.</span>
                                        </div>
                                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-2xs">
                                            <strong class="text-emerald-900 block font-bold text-sm">Lonceng Notifikasi Realtime:</strong>
                                            <span class="text-slate-600">Menerima alert visual di navbar saat ada pesan atau perubahan status tiket baru.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


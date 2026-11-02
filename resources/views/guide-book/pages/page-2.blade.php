                <!-- ================= HALAMAN 2: BAB I - URGENSI DIGITALISASI ================= -->
                <div 
                    x-show="currentPage === 2" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="2" chapter="Bab I" title="Urgensi Digitalisasi Layanan TI">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">1</span>
                                <h3>Urgensi Digitalisasi Layanan TI</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Dalam penyelenggaraan tugas pengawasan obat dan makanan sehari-hari, kelancaran perangkat komputer, jaringan, dan aplikasi dinas merupakan faktor penentu produktivitas. Pendekatan lama melalui pesan obrolan informal sering kali menghadirkan kendala yang tidak terduga:
                            </p>

                            <!-- Perbandingan Masalah vs Solusi (Expands to fill vertical room) -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 py-1.5">
                                <div class="p-5 sm:p-6 rounded-2xl bg-rose-50/80 border border-rose-200 flex flex-col justify-between text-rose-950 shadow-2xs">
                                    <div>
                                        <div class="flex items-center gap-2.5 text-rose-900 font-bold text-base sm:text-lg mb-2">
                                            <i class="ri-close-circle-fill text-rose-600 text-2xl"></i>
                                            <span>Kendala Komunikasi Informal:</span>
                                        </div>
                                        <p class="text-sm text-rose-800 leading-relaxed">
                                            Pesan rawan tertimbun di obrolan pribadi WhatsApp, antrean tidak transparan, ketiadaan bukti penanganan resmi, dan teknisi kesulitan memprioritaskan kendala mendesak.
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider mt-4 block">
                                        Kekurangan Metode Manual
                                    </span>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-emerald-50/80 border border-emerald-200 flex flex-col justify-between text-emerald-950 shadow-2xs">
                                    <div>
                                        <div class="flex items-center gap-2.5 text-emerald-900 font-bold text-base sm:text-lg mb-2">
                                            <i class="ri-checkbox-circle-fill text-emerald-600 text-2xl"></i>
                                            <span>Solusi Melalui ITSM BBPOM:</span>
                                        </div>
                                        <p class="text-sm text-emerald-800 leading-relaxed">
                                            Setiap aduan tercatat dengan Nomor Tiket resmi, terdistribusi otomatis kepada teknisi yang bertugas, dapat dipantau dari meja kerja, dan memiliki rekam jejak penyelesaian yang jelas.
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider mt-4 block">
                                        Keunggulan Sistem Baru
                                    </span>
                                </div>
                            </div>

                            <!-- Nilai Tambah Praktis -->
                            <div class="p-4 bg-slate-50/90 rounded-2xl border border-slate-200 text-sm sm:text-base text-slate-700 flex items-center gap-3 shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <i class="ri-thumb-up-line text-xl"></i>
                                </div>
                                <span class="leading-relaxed">
                                    Sistem ini hadir untuk mempermudah pekerjaan Anda, bukan menambah beban administrasi. Cukup klik kategori dan kendala langsung tertangani oleh teknisi yang bertugas.
                                </span>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


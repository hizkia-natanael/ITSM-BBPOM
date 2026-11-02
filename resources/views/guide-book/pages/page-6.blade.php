                <!-- ================= HALAMAN 6: BAB III - PANDUAN PENGGUNA PENGAJUAN ================= -->
                <div 
                    x-show="currentPage === 6" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="6" chapter="Bab III" title="Panduan Pengguna: Cara Mengajukan Tiket">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">1</span>
                                <h3>Panduan Pengguna: Alur Pengajuan Tiket</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Langkah praktis bagi pegawai untuk menyampaikan kendala perangkat atau permohonan layanan TI secara terstruktur:
                            </p>

                            <!-- 3 Langkah Panduan Praktis (Expands to fill vertical room) -->
                            <div class="flex-1 flex flex-col justify-between gap-3.5 py-1.5">
                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-3xl shrink-0 border border-emerald-100">
                                        <i class="ri-grid-fill"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Langkah 1: Buka Dashboard & Pilih Katalog</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                            Masuk ke portal ITSM, klik tombol <strong>Buat Tiket Baru</strong>, lalu pilih 1 dari 12 kategori layanan yang paling relevan dengan kebutuhan Anda.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-3xl shrink-0 border border-blue-100">
                                        <i class="ri-save-line"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Langkah 2: Lengkapi Formulir & Fitur Autosave Senyap</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                            Isi deskripsi kendala dan lokasi kerja. Fitur <strong>Autosave Senyap</strong> otomatis mengamankan ketikan Anda ke peramban jika terjadi gangguan jaringan mendadak.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-3xl shrink-0 border border-purple-100">
                                        <i class="ri-attachment-line"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900">Langkah 3: Unggah Lampiran Bukti Kendala</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                                            Sertakan tangkapan layar (screenshot), foto fisik perangkat, atau berkas pendukung (PDF, JPG, PNG) untuk mempercepat diagnosis teknisi tanpa perlu survei awal.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


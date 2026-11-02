                <!-- ================= HALAMAN 5: BAB II - DETAIL ALUR KERJA ================= -->
                <div 
                    x-show="currentPage === 5" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="5" chapter="Bab II" title="Detail Alur Kerja Aplikasi ITSM">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">2</span>
                                <h3>Detail Alur Kerja Aplikasi ITSM</h3>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed shrink-0">
                                Setiap pengajuan aduan melewati siklus transparan yang dikelola secara terpadu oleh <strong>Tim TIK BBPOM Semarang</strong>:
                            </p>

                            <!-- 4 Tahapan Siklus (Expands to fill vertical room) -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 py-1.5">
                                <div class="p-5 sm:p-6 rounded-2xl bg-emerald-50/80 border border-emerald-200/90 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">
                                                Tahap 1 &bull; Pemohon
                                            </span>
                                            <i class="ri-edit-line text-2xl text-emerald-600"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900 mt-1">Pengajuan & Tiket Diterbitkan</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                                            Pemohon memilih 1 dari 12 katalog, menjelaskan kendala, melampirkan foto bukti, dan sistem otomatis menerbitkan Nomor Tiket resmi unik.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-blue-50/80 border border-blue-200/90 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-mono font-bold text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                                                Tahap 2 &bull; Triage
                                            </span>
                                            <i class="ri-user-shared-line text-2xl text-blue-600"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900 mt-1">Verifikasi & Klaim Teknisi</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                                            Tim TIK BBPOM Semarang menelaah tiket masuk, menetapkan tingkat urgensi, dan mengklaim tiket untuk penanganan mandiri secara transparan.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-amber-50/80 border border-amber-200/90 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-mono font-bold text-amber-700 bg-amber-100 px-3 py-1 rounded-full">
                                                Tahap 3 &bull; Penanganan
                                            </span>
                                            <i class="ri-tools-line text-2xl text-amber-600"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900 mt-1">Tindakan Teknis & Live Chat</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                                            Teknisi mendatangi lokasi atau melakukan perbaikan remote, berkoordinasi langsung dengan pemohon via live chat, dan memperbarui progres.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-5 sm:p-6 rounded-2xl bg-purple-50/80 border border-purple-200/90 flex flex-col justify-between shadow-2xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-mono font-bold text-purple-700 bg-purple-100 px-3 py-1 rounded-full">
                                                Tahap 4 &bull; Tuntas
                                            </span>
                                            <i class="ri-check-double-line text-2xl text-purple-600"></i>
                                        </div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900 mt-1">Solusi Dicatat & Tiket Selesai</h4>
                                        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                                            Teknisi mencatat rangkuman solusi perbaikan, status berganti menjadi Selesai, dan rekam jejak tersimpan rapi untuk evaluasi berkala.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


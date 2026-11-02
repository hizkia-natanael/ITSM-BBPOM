                <!-- ================= HALAMAN 10: BAB V - FAQ INTERAKTIF ================= -->
                <div 
                    x-show="currentPage === 10" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="10" chapter="Bab V" title="Tanya Jawab Umum (FAQ)">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">1</span>
                                <h3>Tanya Jawab Umum (FAQ)</h3>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 shrink-0">
                                Klik pertanyaan di bawah untuk membuka penjelasan solusi dari kendala yang sering dialami:
                            </p>

                            <!-- Accordion FAQ List (Expands to fill vertical room) -->
                            <div class="flex-1 flex flex-col justify-between gap-2 sm:gap-2.5 py-1">
                                <!-- FAQ 1 -->
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                                    <button 
                                        type="button" 
                                        @click="toggleFaq(1)" 
                                        class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition"
                                    >
                                        <span class="text-xs sm:text-sm font-bold text-slate-800">1. Bagaimana jika saya lupa password akun SSO atau email dinas?</span>
                                        <i class="ri-arrow-down-s-line text-slate-400 text-base transition-transform" :class="{ 'rotate-180 text-emerald-600': isFaqOpen(1) }"></i>
                                    </button>
                                    <div x-show="isFaqOpen(1)" x-transition class="p-4 pt-0 text-xs sm:text-sm text-slate-600 bg-emerald-50/40 border-t border-slate-100 leading-relaxed">
                                        Anda dapat mengajukan tiket dengan memilih kategori <strong>Reset Password Akun</strong> atau menghubungi Tim TIK secara langsung melalui nomor telepon internal balai (ext. ULPK).
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                                    <button 
                                        type="button" 
                                        @click="toggleFaq(2)" 
                                        class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition"
                                    >
                                        <span class="text-xs sm:text-sm font-bold text-slate-800">2. Apakah pembuatan tiket wajib menggunakan akun pribadi?</span>
                                        <i class="ri-arrow-down-s-line text-slate-400 text-base transition-transform" :class="{ 'rotate-180 text-emerald-600': isFaqOpen(2) }"></i>
                                    </button>
                                    <div x-show="isFaqOpen(2)" x-transition class="p-4 pt-0 text-xs sm:text-sm text-slate-600 bg-emerald-50/40 border-t border-slate-100 leading-relaxed">
                                        Ya, setiap pegawai wajib login menggunakan akun dinas masing-masing agar histori kepemilikan aset dan komunikasi dapat terlacak dengan akurat.
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                                    <button 
                                        type="button" 
                                        @click="toggleFaq(3)" 
                                        class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition"
                                    >
                                        <span class="text-xs sm:text-sm font-bold text-slate-800">3. Apakah ketikan saya akan hilang jika koneksi internet terputus saat mengisi form?</span>
                                        <i class="ri-arrow-down-s-line text-slate-400 text-base transition-transform" :class="{ 'rotate-180 text-emerald-600': isFaqOpen(3) }"></i>
                                    </button>
                                    <div x-show="isFaqOpen(3)" x-transition class="p-4 pt-0 text-xs sm:text-sm text-slate-600 bg-emerald-50/40 border-t border-slate-100 leading-relaxed">
                                        Tidak. Fitur <strong>Autosave Senyap</strong> otomatis menyimpan draf ke penyimpanan peramban lokal. Saat Anda membuka kembali formulir, sistem akan menampilkan dialog pemulihan draf.
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                                    <button 
                                        type="button" 
                                        @click="toggleFaq(4)" 
                                        class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition"
                                    >
                                        <span class="text-xs sm:text-sm font-bold text-slate-800">4. Bagaimana cara menambahkan bukti foto susulan?</span>
                                        <i class="ri-arrow-down-s-line text-slate-400 text-base transition-transform" :class="{ 'rotate-180 text-emerald-600': isFaqOpen(4) }"></i>
                                    </button>
                                    <div x-show="isFaqOpen(4)" x-transition class="p-4 pt-0 text-xs sm:text-sm text-slate-600 bg-emerald-50/40 border-t border-slate-100 leading-relaxed">
                                        Buka halaman detail tiket Anda, gunakan fitur live chat untuk mengirim pesan tambahan, lalu klik ikon lampiran untuk mengunggah berkas foto atau dokumen PDF baru.
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                                    <button 
                                        type="button" 
                                        @click="toggleFaq(5)" 
                                        class="w-full text-left p-3.5 sm:p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition"
                                    >
                                        <span class="text-xs sm:text-sm font-bold text-slate-800">5. Kapan waktu operasional pelayanan aduan?</span>
                                        <i class="ri-arrow-down-s-line text-slate-400 text-base transition-transform" :class="{ 'rotate-180 text-emerald-600': isFaqOpen(5) }"></i>
                                    </button>
                                    <div x-show="isFaqOpen(5)" x-transition class="p-4 pt-0 text-xs sm:text-sm text-slate-600 bg-emerald-50/40 border-t border-slate-100 leading-relaxed">
                                        Pengajuan tiket dapat dilakukan 24/7. Pemrosesan dan penanganan aktif oleh tim teknisi dilaksanakan pada hari kerja dinas: Senin s.d. Jumat pukul 07.30 - 16.00 WIB.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>


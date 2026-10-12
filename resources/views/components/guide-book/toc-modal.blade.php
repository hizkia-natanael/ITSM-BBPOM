<div 
    x-show="showToc" 
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md print:hidden"
    style="display: none;"
    @keydown.escape.window="showToc = false"
>
    <!-- Modal Card -->
    <div 
        @click.outside="showToc = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[88vh]"
    >
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white flex items-center justify-between border-b border-emerald-500/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl">
                    <i class="ri-list-ordered-2"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold tracking-tight">Daftar Isi Buku Panduan ITSM</h3>
                    <p class="text-xs text-emerald-300">Pilih halaman untuk langsung berpindah ke materi yang diinginkan</p>
                </div>
            </div>
            <button 
                type="button" 
                @click="showToc = false"
                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition"
                title="Tutup Daftar Isi"
            >
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>

        <!-- TOC Chapters List (14 Single Pages) -->
        <div class="p-4 sm:p-6 overflow-y-auto space-y-2 divide-y divide-slate-100 max-h-[60vh]">
            <!-- Hal 0: Cover -->
            <button 
                type="button"
                @click="goToPage(0)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 0 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="ri-book-mark-line"></i>
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Sampul Depan (Executive Hardcover)</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Judul resmi, logo BPOM, & identitas dokumen pedoman</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Cover
                </span>
            </button>

            <!-- Hal 1: Kata Sambutan -->
            <button 
                type="button"
                @click="goToPage(1)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 1 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="ri-chat-quote-line"></i>
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Kata Sambutan & Pengantar Pimpinan</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Visi transformasi digital Tim TIK BBPOM Semarang</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 1
                </span>
            </button>

            <!-- Hal 2: Urgensi -->
            <button 
                type="button"
                @click="goToPage(2)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 2 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        I
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab I: Urgensi Digitalisasi Layanan TI</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Perbandingan kendala komunikasi informal vs solusi ITSM terpusat</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 2
                </span>
            </button>

            <!-- Hal 3: Maksud Tujuan -->
            <button 
                type="button"
                @click="goToPage(3)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 3 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        I
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab I: Maksud, Tujuan, & Manfaat Sistem</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Fleksibilitas layanan, transparansi penuh, & sentralisasi bantuan</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 3
                </span>
            </button>

            <!-- Hal 4: 12 Katalog -->
            <button 
                type="button"
                @click="goToPage(4)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 4 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        II
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab II: 12 Katalog Layanan TI Terpadu</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Daftar lengkap 12 kategori layanan kedinasan resmi</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 4
                </span>
            </button>

            <!-- Hal 5: Alur Kerja -->
            <button 
                type="button"
                @click="goToPage(5)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 5 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        II
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab II: Detail Alur Kerja Aplikasi ITSM</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">4 tahapan penanganan oleh Tim TIK BBPOM Semarang</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 5
                </span>
            </button>

            <!-- Hal 6: Panduan User Pengajuan -->
            <button 
                type="button"
                @click="goToPage(6)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 6 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs shrink-0">
                        III
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab III: Panduan Pengguna - Alur Pengajuan Tiket</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Pemilihan katalog, formulir cerdas, & fitur autosave senyap</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 6
                </span>
            </button>

            <!-- Hal 7: Panduan User Pelacakan -->
            <button 
                type="button"
                @click="goToPage(7)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 7 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs shrink-0">
                        III
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab III: Panduan Pengguna - Pelacakan & Komunikasi</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">4 status tiket, live chat teknisi, & notifikasi realtime</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 7
                </span>
            </button>

            <!-- Hal 8: Panduan Admin Triage -->
            <button 
                type="button"
                @click="goToPage(8)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 8 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0">
                        IV
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab IV: Panduan Admin - Triage & Manajemen Tiket</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Panel Filament, klaim tiket teknisi, & penulisan solusi</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 8
                </span>
            </button>

            <!-- Hal 9: Panduan Admin Ekspor -->
            <button 
                type="button"
                @click="goToPage(9)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 9 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0">
                        IV
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab IV: Panduan Admin - Ekspor PDF & Excel Resmi</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Laporan landscape dinas & rekapitulasi data kustom</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 9
                </span>
            </button>

            <!-- Hal 10: FAQ -->
            <button 
                type="button"
                @click="goToPage(10)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 10 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs shrink-0">
                        V
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab V: Tanya Jawab Umum (FAQ Interaktif)</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">6 pertanyaan & jawaban dropdown kendala umum pengguna</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 10
                </span>
            </button>

            <!-- Hal 11: Kontak -->
            <button 
                type="button"
                @click="goToPage(11)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 11 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs shrink-0">
                        V
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Bab V: Meja Bantuan & Kontak Tim TIK</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Saluran bantuan Tim TIK BBPOM Di semarang & tips pelaporan</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 11
                </span>
            </button>

            <!-- Hal 12: Penutup -->
            <button 
                type="button"
                @click="goToPage(12)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 12 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Penutup: Ikhtisar Budaya Layanan TIK</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">3 pilar budaya pelayanan dan integritas publik</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Hal. 12
                </span>
            </button>

            <!-- Hal 13: Back Cover -->
            <button 
                type="button"
                @click="goToPage(13)"
                class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === 13 }"
            >
                <div class="flex items-center gap-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="ri-shield-keyhole-line"></i>
                    </div>
                    <div>
                        <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">Sampul Belakang (Back Cover)</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Alamat dinas, kontak ULPK, & komitmen integritas</div>
                    </div>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    Cover Akhir
                </span>
            </button>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs sm:text-sm text-slate-500">
            <span class="flex items-center gap-1.5">
                <i class="ri-information-line text-emerald-600"></i>
                Gunakan tombol panah keyboard [←] dan [→] untuk membalik halaman.
            </span>
            <button 
                type="button" 
                @click="showToc = false" 
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

<x-app-layout>
    <!-- Guide Book Creative Menu Book Interactive Stage (Locked to Exactly 1 Viewport Height) -->
    <div 
        x-data="guideBookViewer()" 
        class="relative h-[calc(100vh-4.25rem)] max-h-[calc(100vh-4.25rem)] w-full pt-1 pb-12 sm:pb-14 px-3 sm:px-6 flex flex-col justify-start text-slate-100 select-none overflow-hidden print:h-auto print:max-h-none print:p-0 print:m-0 print:bg-white print:text-slate-900"
    >
        <!-- Translucent BBPOM Scenic Photo Background (Creative & Warm Atmosphere, Low Opacity) -->
        <div 
            class="absolute inset-0 bg-cover bg-center pointer-events-none -z-20 opacity-[0.055] blur-[2px] scale-105 print:hidden"
            style="background-image: url('{{ asset('images/footer-bbpom-semarang.jpg') }}');"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/95 via-slate-900/90 to-slate-950/95 -z-10 pointer-events-none print:hidden"></div>

        <!-- Accessible Semantic Screen-Reader Title (Visually Clean Stage) -->
        <h1 class="sr-only">Buku Panduan Resmi ITSM BBPOM di Semarang</h1>

        <!-- The Hardcover 3D Book Frame Component (Single-Page Executive Folio Stage) -->
        <div class="flex-1 flex flex-col justify-start items-center min-h-0 pt-0.5 sm:pt-1">
            <x-guide-book.book-frame>

                <!-- ================= HALAMAN 0: SAMPUL DEPAN (COVER) ================= -->
                <div 
                    x-show="currentPage === 0" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                >
                    <x-guide-book.page-sheet :isCover="true">
                        <div class="h-full flex flex-col justify-between p-6 sm:p-10 lg:p-12 text-center relative overflow-hidden">
                            <!-- Ambient Radial Glow -->
                            <div class="absolute -top-16 -left-16 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute -bottom-16 -right-16 w-72 h-72 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>

                            <!-- Top Emblems & Header (Tanpa kata "RI") -->
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/10 backdrop-blur-md p-3.5 sm:p-4 border border-white/20 shadow-2xl mb-3 flex items-center justify-center">
                                    <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="w-full h-auto drop-shadow-md">
                                </div>
                                <span class="text-xs sm:text-sm font-bold tracking-widest text-emerald-300 uppercase">
                                    Badan Pengawas Obat dan Makanan
                                </span>
                                <span class="text-sm sm:text-base font-semibold tracking-wider text-slate-300 uppercase mt-0.5">
                                    Balai Besar POM di Semarang
                                </span>
                            </div>

                            <!-- Main Title with Gold/Emerald Border -->
                            <div class="relative z-10 my-4 py-6 border-y border-emerald-500/30 max-w-3xl mx-auto w-full">
                                <span class="inline-block px-3.5 py-1 rounded-full text-xs font-mono tracking-widest uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-2.5">
                                    PETUNJUK OPERASIONAL SISTEM RESMI
                                </span>
                                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight uppercase leading-tight font-serif drop-shadow-sm">
                                    BUKU PANDUAN PENGOPERASIAN SISTEM
                                </h2>
                                <p class="text-sm sm:text-lg font-medium text-emerald-200 mt-2">
                                    Layanan & Pengaduan Teknologi Informasi (ITSM)
                                </p>
                            </div>

                            <!-- Metadata Badges & Start Button -->
                            <div class="relative z-10 flex flex-col items-center gap-4">
                                <div class="flex flex-wrap justify-center gap-2 text-xs sm:text-sm font-semibold text-slate-300">
                                    <span class="px-3 py-1 rounded-lg bg-white/10 border border-white/15">12 Katalog Layanan Terpadu</span>
                                    <span class="px-3 py-1 rounded-lg bg-white/10 border border-white/15">Panduan Pengguna & Admin</span>
                                    <span class="px-3 py-1 rounded-lg bg-white/10 border border-white/15">Edisi Tata Kelola 2026</span>
                                </div>

                                <button 
                                    type="button" 
                                    @click="nextPage()"
                                    class="inline-flex items-center gap-2.5 px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-sm sm:text-base shadow-lg shadow-emerald-950/50 hover:shadow-xl transition-all active:scale-95 group"
                                >
                                    <span>Buka Buku Panduan</span>
                                    <i class="ri-arrow-right-line text-base group-hover:translate-x-1.5 transition-transform"></i>
                                </button>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>

                <!-- ================= HALAMAN 1: KATA SAMBUTAN & PENGANTAR ================= -->
                <div 
                    x-show="currentPage === 1" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="1" chapter="Pengantar" title="Kata Sambutan Pimpinan">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <!-- Official Header with Enlarged Logo -->
                            <div class="flex items-center gap-4 pb-2.5 border-b border-slate-200 shrink-0">
                                <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="w-14 sm:w-16 h-auto drop-shadow-sm">
                                <div>
                                    <h4 class="text-sm sm:text-base font-bold text-slate-900 uppercase tracking-tight">
                                        Balai Besar Pengawas Obat dan Makanan di Semarang
                                    </h4>
                                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Tim Pengelola Teknologi Informasi & Komunikasi</p>
                                </div>
                            </div>

                            <!-- Executive Quote Card -->
                            <div class="bg-emerald-50/90 p-5 sm:p-6 rounded-2xl border border-emerald-200 text-sm sm:text-base text-emerald-950 leading-relaxed shadow-xs">
                                <div class="font-bold text-emerald-900 mb-1.5 flex items-center gap-2">
                                    <i class="ri-chat-quote-line text-emerald-700 text-xl"></i>
                                    <span class="text-sm sm:text-base">Kata Sambutan & Pengantar Pimpinan</span>
                                </div>
                                "Transformasi digital bukan sekadar mengganti kertas dengan layar, melainkan menjamin setiap kebutuhan dan kendala operasional pegawai terselesaikan dengan cepat, transparan, dan terukur. Sistem ITSM ini adalah sarana kolaborasi bersama untuk mewujudkan tata kelola TI yang responsif, mudah diakses, dan berintegritas."
                            </div>

                            <!-- Main Guidance Paragraphs & Cards -->
                            <div class="flex-1 flex flex-col justify-between py-1 text-sm sm:text-base text-slate-600">
                                <p class="leading-relaxed">
                                    <strong class="text-slate-900 font-bold block mb-0.5">Selamat Datang di Panduan Pengoperasian:</strong>
                                    Buku pedoman resmi ini dirancang untuk memandu seluruh pegawai (ASN dan PPNPN) serta tim teknisi TI dalam memanfaatkan Sistem Manajemen Layanan TI (ITSM) BBPOM di Semarang secara optimal.
                                </p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <i class="ri-compass-3-line text-lg"></i>
                                        </div>
                                        <div class="text-xs sm:text-sm text-slate-600">
                                            <strong class="font-bold text-slate-800 block text-sm">Alur Layanan Terstandar:</strong>
                                            Pahami tahapan pengajuan, penugasan teknisi, dan pemantauan tiket dinas Anda.
                                        </div>
                                    </div>
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <i class="ri-user-star-line text-lg"></i>
                                        </div>
                                        <div class="text-xs sm:text-sm text-slate-600">
                                            <strong class="font-bold text-slate-800 block text-sm">Kolaborasi Cepat & Akuntabel:</strong>
                                            Gunakan fitur live chat dan notifikasi untuk interaksi langsung dengan teknisi TI.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Stamp -->
                            <div class="pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs sm:text-sm text-slate-500 shrink-0">
                                <span class="flex items-center gap-1.5 text-emerald-800 font-semibold">
                                    <i class="ri-map-pin-line text-emerald-600 text-base"></i>
                                    <span>Semarang, Jawa Tengah</span>
                                </span>
                                <span class="font-bold text-slate-700">Tim TIK BBPOM Semarang</span>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>

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

                <!-- ================= HALAMAN 4: BAB II - 12 KATALOG LAYANAN ================= -->
                <div 
                    x-show="currentPage === 4" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="4" chapter="Bab II" title="12 Katalog Layanan TI Terpadu">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">1</span>
                                <h3>12 Katalog Layanan TI Terpadu</h3>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 shrink-0">
                                Pilih kategori layanan berikut pada portal formulir tiket agar permohonan Anda langsung terarah ke penanganan yang tepat:
                            </p>

                            <!-- 12 Katalog Grid (Expands to fill vertical room) -->
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-3 py-1">
                                @php
                                    $catalogs = [
                                        ['icon' => 'ri-mail-line', 'title' => 'Email Dinas @pom.go.id', 'desc' => 'Pembuatan & pemulihan akun surel'],
                                        ['icon' => 'ri-lock-password-line', 'title' => 'Reset Password Akun', 'desc' => 'Pemulihan akses SSO & portal dinas'],
                                        ['icon' => 'ri-video-chat-line', 'title' => 'Permohonan Zoom Meeting', 'desc' => 'Jadwal & akun rapat dinas virtual'],
                                        ['icon' => 'ri-computer-line', 'title' => 'Perbaikan Komputer / PC', 'desc' => 'Troubleshooting PC & laptop dinas'],
                                        ['icon' => 'ri-printer-line', 'title' => 'Perbaikan Printer / Scanner', 'desc' => 'Kendala cetak dokumen & tinta'],
                                        ['icon' => 'ri-wifi-line', 'title' => 'Jaringan Internet & WiFi', 'desc' => 'Koneksi lambat & login hotspot'],
                                        ['icon' => 'ri-folder-info-line', 'title' => 'Permohonan Data TI', 'desc' => 'Informasi publik & database balai'],
                                        ['icon' => 'ri-router-line', 'title' => 'Akses Jaringan Lokal (LAN)', 'desc' => 'Kabel LAN & sharing folder kerja'],
                                        ['icon' => 'ri-install-line', 'title' => 'Instalasi Software Dinas', 'desc' => 'Antivirus, office, & aplikasi lab'],
                                        ['icon' => 'ri-shield-flash-line', 'title' => 'Penanganan Insiden TI', 'desc' => 'Virus, malware, & serangan siber'],
                                        ['icon' => 'ri-server-line', 'title' => 'Pemeliharaan Server', 'desc' => 'Backup rutin & pemeliharaan berkala'],
                                        ['icon' => 'ri-customer-service-line', 'title' => 'Konsultasi TI Lainnya', 'desc' => 'Bantuan teknis umum & koordinasi'],
                                    ];
                                @endphp

                                @foreach($catalogs as $c)
                                    <div class="p-3 sm:p-3.5 rounded-xl border border-slate-200/90 bg-white hover:bg-emerald-50/50 hover:border-emerald-300 transition flex items-center gap-3 shadow-2xs group">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition">
                                            <i class="{{ $c['icon'] }} text-xl"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 truncate group-hover:text-emerald-950">{{ $c['title'] }}</h4>
                                            <p class="text-[11px] text-slate-500 truncate">{{ $c['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>

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

                <!-- ================= HALAMAN 11: BAB V - MEJA BANTUAN & KONTAK ================= -->
                <div 
                    x-show="currentPage === 11" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :pageNumber="11" chapter="Bab V" title="Meja Bantuan & Kontak Tim TIK">
                        <div class="h-full flex-1 flex flex-col justify-between py-1">
                            <div class="flex items-center gap-3 text-emerald-800 font-bold text-base sm:text-xl shrink-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-extrabold">2</span>
                                <h3>Saluran Bantuan & Tips Cepat</h3>
                            </div>

                            <!-- 2 Kolom Kontak & Tips Lebar (Expands to fill vertical room) -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 py-1.5">
                                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between text-xs sm:text-sm shadow-2xs">
                                    <div>
                                        <strong class="text-slate-900 flex items-center gap-2 text-base font-bold mb-2">
                                            <i class="ri-customer-service-2-line text-emerald-600 text-2xl"></i>
                                            <span>Saluran Bantuan Tim TIK BBPOM Di semarang:</span>
                                        </strong>
                                        <div class="text-slate-600 space-y-2 pt-1 leading-relaxed">
                                            <p>&bull; <strong>Lokasi:</strong> Tim TIK BBPOM Di semarang</p>
                                            <p>&bull; <strong>Telepon Kantor:</strong> 024 - 7612324 (ULPK)</p>
                                            <p>&bull; <strong>Faksimili:</strong> 024 - 7613633</p>
                                            <p>&bull; <strong>Surel Resmi:</strong> bpom_semarang@pom.go.id</p>
                                            <p>&bull; <strong>Laman Web:</strong> www.pom.go.id &bull; semarang.pom.go.id</p>
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-slate-200 text-xs text-slate-500 font-medium">
                                        Layanan tatap muka bertempat di Gedung Subbagian Tata Usaha
                                    </div>
                                </div>

                                <div class="p-6 bg-emerald-50/80 rounded-2xl border border-emerald-200 flex flex-col justify-between text-xs sm:text-sm text-emerald-950 shadow-2xs">
                                    <div>
                                        <strong class="text-emerald-900 flex items-center gap-2 text-base font-bold mb-2">
                                            <i class="ri-flashlight-line text-amber-600 text-2xl"></i>
                                            <span>Tips Pelaporan Agar Cepat Ditangani:</span>
                                        </strong>
                                        <ul class="text-emerald-800 space-y-2 list-disc list-inside leading-relaxed pt-1">
                                            <li>Sertakan tangkapan layar (screenshot) pesan error atau foto perangkat kendala.</li>
                                            <li>Pada kendala hardware, sebutkan nomor inventaris BMN atau lokasi ruangan kerja.</li>
                                            <li>Untuk permohonan Zoom Meeting dinas, ajukan minimal 1 hari sebelum agenda rapat.</li>
                                        </ul>
                                    </div>
                                    <div class="pt-3 border-t border-emerald-200 text-xs text-emerald-800 font-medium">
                                        Deskripsi yang akurat mempercepat diagnosa hingga 50%
                                    </div>
                                </div>
                            </div>

                            <!-- Jam Layanan Helpdesk -->
                            <div class="p-4 bg-slate-100 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 flex items-center justify-between font-medium shrink-0">
                                <span>Jam Layanan Meja Bantuan Kedinasan:</span>
                                <span class="font-bold text-slate-900">Senin - Jumat &bull; 07.30 - 16.00 WIB</span>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>

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

                <!-- ================= HALAMAN 13: SAMPUL BELAKANG (BACK COVER) ================= -->
                <div 
                    x-show="currentPage === 13" 
                    x-transition:enter="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    x-transition:enter-start="opacity-0 [transform:perspective(2400px)_rotateY(-12deg)_scale(0.99)] origin-left"
                    x-transition:enter-end="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave="transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] absolute inset-0 pointer-events-none"
                    x-transition:leave-start="opacity-100 [transform:perspective(2400px)_rotateY(0deg)_scale(1)] origin-left"
                    x-transition:leave-end="opacity-0 [transform:perspective(2400px)_rotateY(12deg)_scale(0.99)] origin-left"
                    class="h-full w-full flex flex-col [transform-style:preserve-3d] [backface-visibility:hidden] will-change-transform"
                    style="display: none;"
                >
                    <x-guide-book.page-sheet :isCover="true">
                        <div class="h-full flex flex-col justify-between p-6 sm:p-10 text-center relative overflow-hidden">
                            <!-- Top BPOM Emblem (Enlarged) -->
                            <div class="flex flex-col items-center">
                                <img src="{{ asset('images/logo-bpom.png') }}" alt="Logo BPOM" class="w-16 sm:w-20 h-auto drop-shadow-md mb-2">
                                <h4 class="text-sm sm:text-base font-bold text-emerald-300 uppercase tracking-widest">
                                    Balai Besar POM di Semarang
                                </h4>
                                <span class="text-xs sm:text-sm text-slate-400">
                                    Badan Pengawas Obat dan Makanan
                                </span>
                            </div>

                            <!-- Middle Slogan & Integrity Quote -->
                            <div class="my-4 py-4 border-y border-emerald-500/20 space-y-2 max-w-xl mx-auto">
                                <span class="text-sm sm:text-lg font-serif italic text-emerald-200 block">
                                    "Melayani dengan Integritas, Profesionalitas, dan Komitmen Transformasi Digital."
                                </span>
                                <div class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                                    Panduan resmi ini berlaku untuk seluruh unit kerja, laboratorium pengujian, dan tata usaha di lingkungan BBPOM Semarang.
                                </div>
                            </div>

                            <!-- Footer Address & Return Button -->
                            <div class="space-y-3">
                                <div class="text-xs sm:text-sm text-slate-400 space-y-0.5">
                                    <p class="font-semibold text-slate-200">Jl. Sukun Raya No. 41A Banyumanik, Semarang 50264</p>
                                    <p>Telp: 024 - 7612324 (ULPK) &bull; Fax: 024 - 7613633</p>
                                    <p>Email: bpom_semarang@pom.go.id &bull; Website: www.pom.go.id</p>
                                </div>

                                <button 
                                    type="button" 
                                    @click="goToPage(0)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white text-xs sm:text-sm font-semibold transition active:scale-95"
                                >
                                    <i class="ri-arrow-up-line"></i>
                                    <span>Kembali ke Sampul Depan</span>
                                </button>
                            </div>
                        </div>
                    </x-guide-book.page-sheet>
                </div>

            </x-guide-book.book-frame>
        </div>

        <!-- Floating Executive Toolbar Component (Docked Neatly at Bottom) -->
        <x-guide-book.toolbar />

        <!-- Table of Contents Modal Drawer -->
        <x-guide-book.toc-modal />
    </div>

    <!-- Alpine.js Dedicated Controller Script (Parser-Safe: No comparison operators in HTML attributes) -->
    <script>
        function guideBookViewer() {
            return {
                currentPage: 0,
                totalPages: 13,
                isFullscreen: false,
                showToc: false,
                activeFaq: 1,
                isFlipping: false,

                init: function () {
                    var self = this;

                    // Keyboard shortcuts
                    window.addEventListener('keydown', function (e) {
                        if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                            self.nextPage();
                        } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                            self.prevPage();
                        } else if (e.key === 'Escape') {
                            self.showToc = false;
                        }
                    });
                },

                nextPage: function () {
                    if (this.currentPage < this.totalPages && !this.isFlipping) {
                        var self = this;
                        this.isFlipping = true;
                        this.currentPage = this.currentPage + 1;
                        setTimeout(function () {
                            self.isFlipping = false;
                        }, 700);
                    }
                },

                prevPage: function () {
                    if (this.currentPage > 0 && !this.isFlipping) {
                        var self = this;
                        this.isFlipping = true;
                        this.currentPage = this.currentPage - 1;
                        setTimeout(function () {
                            self.isFlipping = false;
                        }, 700);
                    }
                },

                goToPage: function (index) {
                    if (index >= 0 && index <= this.totalPages && !this.isFlipping) {
                        var self = this;
                        this.isFlipping = true;
                        this.currentPage = index;
                        this.showToc = false;
                        setTimeout(function () {
                            self.isFlipping = false;
                        }, 700);
                    }
                },

                toggleFaq: function (faqId) {
                    this.activeFaq = this.activeFaq === faqId ? null : faqId;
                },

                isFaqOpen: function (faqId) {
                    return this.activeFaq === faqId;
                },

                toggleFullscreen: function () {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(function () {});
                        this.isFullscreen = true;
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                            this.isFullscreen = false;
                        }
                    }
                },

                printGuide: function () {
                    window.print();
                },

                getChapterBadgeText: function () {
                    switch (this.currentPage) {
                        case 0:
                            return 'Sampul Depan (Cover)';
                        case 1:
                            return 'Pengantar: Kata Sambutan';
                        case 2:
                            return 'Bab I: Urgensi Digitalisasi TI';
                        case 3:
                            return 'Bab I: Maksud, Tujuan, & Manfaat';
                        case 4:
                            return 'Bab II: 12 Katalog Layanan Terpadu';
                        case 5:
                            return 'Bab II: Detail Alur Kerja Aplikasi';
                        case 6:
                            return 'Bab III: Panduan Pengguna (Alur Tiket)';
                        case 7:
                            return 'Bab III: Panduan Pengguna (Pelacakan)';
                        case 8:
                            return 'Bab IV: Panduan Admin (Triage Tiket)';
                        case 9:
                            return 'Bab IV: Panduan Admin (Ekspor Laporan)';
                        case 10:
                            return 'Bab V: Tanya Jawab Umum (FAQ)';
                        case 11:
                            return 'Bab V: Meja Bantuan & Saluran TIK';
                        case 12:
                            return 'Penutup: Ikhtisar Budaya Layanan';
                        case 13:
                            return 'Sampul Belakang (Back Cover)';
                        default:
                            return '';
                    }
                }
            };
        }
    </script>
</x-app-layout>

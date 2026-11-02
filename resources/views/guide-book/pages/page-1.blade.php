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


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


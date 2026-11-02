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


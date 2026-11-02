@php
$chapters = [
    ['page' => 0, 'badge' => 'Cover', 'title' => 'Sampul Depan (Executive Hardcover)', 'desc' => 'Judul resmi, logo BPOM, & identitas dokumen pedoman', 'icon' => 'ri-book-mark-line', 'theme' => 'bg-slate-900 text-emerald-400'],
    ['page' => 1, 'badge' => 'Hal. 1', 'title' => 'Kata Sambutan & Pengantar Pimpinan', 'desc' => 'Visi transformasi digital Tim TIK BBPOM Semarang', 'icon' => 'ri-chat-quote-line', 'theme' => 'bg-emerald-100 text-emerald-800'],
    ['page' => 2, 'badge' => 'Hal. 2', 'title' => 'Bab I: Urgensi Digitalisasi Layanan TI', 'desc' => 'Perbandingan kendala komunikasi informal vs solusi ITSM terpusat', 'label' => 'I', 'theme' => 'bg-emerald-100 text-emerald-800'],
    ['page' => 3, 'badge' => 'Hal. 3', 'title' => 'Bab I: Maksud, Tujuan, & Manfaat Sistem', 'desc' => 'Fleksibilitas layanan, transparansi penuh, & sentralisasi bantuan', 'label' => 'I', 'theme' => 'bg-emerald-100 text-emerald-800'],
    ['page' => 4, 'badge' => 'Hal. 4', 'title' => 'Bab II: 12 Katalog Layanan TI Terpadu', 'desc' => 'Daftar lengkap 12 kategori layanan kedinasan resmi', 'label' => 'II', 'theme' => 'bg-emerald-100 text-emerald-800'],
    ['page' => 5, 'badge' => 'Hal. 5', 'title' => 'Bab II: Detail Alur Kerja Aplikasi ITSM', 'desc' => '4 tahapan penanganan oleh Tim TIK BBPOM Semarang', 'label' => 'II', 'theme' => 'bg-emerald-100 text-emerald-800'],
    ['page' => 6, 'badge' => 'Hal. 6', 'title' => 'Bab III: Panduan Pengguna - Alur Pengajuan Tiket', 'desc' => 'Pemilihan katalog, formulir cerdas, & fitur autosave senyap', 'label' => 'III', 'theme' => 'bg-blue-100 text-blue-800'],
    ['page' => 7, 'badge' => 'Hal. 7', 'title' => 'Bab III: Panduan Pengguna - Pelacakan & Komunikasi', 'desc' => '4 status tiket, live chat teknisi, & notifikasi realtime', 'label' => 'III', 'theme' => 'bg-blue-100 text-blue-800'],
    ['page' => 8, 'badge' => 'Hal. 8', 'title' => 'Bab IV: Panduan Admin - Triage & Manajemen Tiket', 'desc' => 'Panel Filament, klaim tiket teknisi, & penulisan solusi', 'label' => 'IV', 'theme' => 'bg-amber-100 text-amber-800'],
    ['page' => 9, 'badge' => 'Hal. 9', 'title' => 'Bab IV: Panduan Admin - Ekspor PDF & Excel Resmi', 'desc' => 'Laporan landscape dinas & rekapitulasi data kustom', 'label' => 'IV', 'theme' => 'bg-amber-100 text-amber-800'],
    ['page' => 10, 'badge' => 'Hal. 10', 'title' => 'Bab V: Tanya Jawab Umum (FAQ Interaktif)', 'desc' => '6 pertanyaan & jawaban dropdown kendala umum pengguna', 'label' => 'V', 'theme' => 'bg-purple-100 text-purple-800'],
    ['page' => 11, 'badge' => 'Hal. 11', 'title' => 'Bab V: Meja Bantuan & Kontak Tim TIK', 'desc' => 'Saluran bantuan Tim TIK BBPOM Di semarang & tips pelaporan', 'label' => 'V', 'theme' => 'bg-purple-100 text-purple-800'],
    ['page' => 12, 'badge' => 'Hal. 12', 'title' => 'Penutup: Ikhtisar Budaya Layanan TIK', 'desc' => '3 pilar budaya pelayanan dan integritas publik', 'icon' => 'ri-checkbox-circle-line', 'theme' => 'bg-emerald-100 text-emerald-800'],
    ['page' => 13, 'badge' => 'Cover Akhir', 'title' => 'Sampul Belakang (Back Cover)', 'desc' => 'Alamat dinas, kontak ULPK, & komitmen integritas', 'icon' => 'ri-shield-keyhole-line', 'theme' => 'bg-slate-900 text-emerald-400'],
];
@endphp

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

        <!-- TOC Chapters List -->
        <div class="p-4 sm:p-6 overflow-y-auto space-y-2 divide-y divide-slate-100 max-h-[60vh]">
            @foreach($chapters as $c)
                <button 
                    type="button"
                    @click="goToPage({{ $c['page'] }})"
                    class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition flex items-center justify-between group pt-3"
                    :class="{ 'bg-emerald-50/70 border-emerald-200': currentPage === {{ $c['page'] }} }"
                >
                    <div class="flex items-center gap-3.5">
                        <div class="w-8 h-8 rounded-xl {{ $c['theme'] }} flex items-center justify-center font-bold text-xs shrink-0">
                            @if(isset($c['icon']))
                                <i class="{{ $c['icon'] }}"></i>
                            @else
                                {{ $c['label'] }}
                            @endif
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-900">{{ $c['title'] }}</div>
                            <div class="text-[11px] sm:text-xs text-slate-500">{{ $c['desc'] }}</div>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-emerald-700 bg-slate-100 px-2 py-0.5 rounded-md">
                        {{ $c['badge'] }}
                    </span>
                </button>
            @endforeach
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs sm:text-sm text-slate-500">
            <span class="flex items-center gap-1.5">
                <i class="ri-information-line text-emerald-600"></i>
                Gunakan tombol panah keyboard [&larr;] dan [&rarr;] untuk membalik halaman.
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

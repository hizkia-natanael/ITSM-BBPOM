<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
                        </li>
                        <li>
                            <span class="mx-1 text-gray-400">/</span>
                        </li>
                        <li>
                            <a href="{{ route('tickets.index') }}" class="hover:text-emerald-700">Daftar Pengaduan</a>
                        </li>
                        <li>
                            <span class="mx-1 text-gray-400">/</span>
                        </li>
                        <li class="font-semibold text-emerald-800">Buat Pengaduan</li>
                    </ol>
                </nav>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">
                    Formulir Pengaduan Kendala TI
                </h2>
            </div>
            <div>
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Kolom Kiri / Utama -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="border-b border-gray-100 pb-4 mb-6">
                    <h3 class="text-base font-bold text-gray-900">Rincian Laporan Kendala</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Sampaikan kendala perangkat keras, software, jaringan, atau akun Anda dengan jelas agar dapat segera ditangani oleh staf IT BBPOM.
                    </p>
                </div>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Subjek / Judul -->
                    <div>
                        <x-input-label for="subject" :value="__('Subjek / Judul Masalah')" class="font-semibold text-xs text-gray-700" />
                        <span class="text-[11px] text-gray-400 block mb-1">Contoh: Komputer Ruang Laboratorium Tidak Bisa Terhubung Internet</span>
                        <x-text-input 
                            id="subject" 
                            name="subject" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('subject')" 
                            placeholder="Tuliskan ringkasan kendala yang dialami..."
                            required 
                            autofocus 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('subject')" />
                    </div>

                    <!-- Kategori Pengaduan -->
                    <div>
                        <x-input-label for="category_id" :value="__('Kategori Layanan TI')" class="font-semibold text-xs text-gray-700" />
                        <span class="text-[11px] text-gray-400 block mb-1">Pilih kategori yang paling sesuai dengan kendala Anda</span>
                        <select 
                            id="category_id" 
                            name="category_id" 
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm text-gray-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                            required
                        >
                            <option value="">-- Pilih Kategori Kendala --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                    </div>

                    <!-- Deskripsi Masalah -->
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi Rinci Kendala')" class="font-semibold text-xs text-gray-700" />
                        <span class="text-[11px] text-gray-400 block mb-1">Sertakan detail seperti lokasi ruangan, nama perangkat, kode error jika ada, dan kronologi kejadian.</span>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5" 
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm text-gray-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-gray-400"
                            placeholder="Jelaskan kendala secara lengkap..."
                            required
                        >{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <!-- Upload Lampiran -->
                    <div>
                        <x-input-label for="attachment" :value="__('Lampiran Bukti / Screenshot (Opsional)')" class="font-semibold text-xs text-gray-700" />
                        <span class="text-[11px] text-gray-400 block mb-2">Unggah tangkapan layar (screenshot), foto error, atau dokumen pendukung (Format: JPG, PNG, PDF, DOCX, ZIP | Maks: 5MB).</span>
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-200 hover:border-emerald-500 rounded-xl transition bg-gray-50/50">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-gray-600 justify-center">
                                    <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-semibold text-emerald-700 hover:text-emerald-800 focus-within:outline-none">
                                        <span>Pilih berkas dari perangkat Anda</span>
                                        <input id="attachment" name="attachment" type="file" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-[11px] text-gray-400" id="file-chosen-text">Belum ada file yang dipilih</p>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                            Kirim Pengaduan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Panduan & Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h4 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Petunjuk Pengisian
                </h4>
                <ul class="mt-4 space-y-3 text-xs text-gray-600 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-700 font-bold">•</span>
                        <span>Pastikan informasi yang Anda berikan spesifik agar staf teknisi dapat menyiapkan suku cadang atau perbaikan yang tepat.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-700 font-bold">•</span>
                        <span>Nomor tiket unik (misal: <strong>TIK-BBPOM-001</strong>) akan otomatis terbit begitu formulir dikirim.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-700 font-bold">•</span>
                        <span>Anda dapat memantau respons atau balasan teknisi melalui fitur <strong>Detail & Tracking</strong>.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-emerald-50 rounded-2xl border border-emerald-100 p-6 text-emerald-900">
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800">Kontak Cepat Staf TI</h4>
                <p class="text-xs text-emerald-700 mt-1">Jika kendala bersifat sangat darurat (misal: ruang server padam atau sistem krusial terhenti), hubungi langsung tim IT BBPOM Semarang di Ruang Pengelola TI.</p>
                <div class="mt-4 text-xs font-semibold text-emerald-800">
                    Ekstensi Internal: 104 / 105
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('attachment')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const textElem = document.getElementById('file-chosen-text');
            if (fileName && textElem) {
                textElem.textContent = 'File terpilih: ' + fileName;
                textElem.classList.remove('text-gray-400');
                textElem.classList.add('text-emerald-700', 'font-medium');
            }
        });
    </script>
</x-app-layout>

<!-- 6. FORMULIR & KEGIATAN (Daftar Hadir, QR Code, Survey, Pretest/Posttest, Linktree) -->
<div class="space-y-6" x-data="formulirKegiatanData()">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Formulir & Kegiatan:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($services['formulir_kegiatan']['subcategories'] as $key => $label)
                <button 
                    type="button" 
                    @click="selectedSubcategory = '{{ $key }}'; selectedSubCategory = '{{ $key }}'"
                    :class="selectedSubcategory === '{{ $key }}' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                    class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
                >
                    <span>{{ $label }}</span>
                    <i x-show="selectedSubcategory === '{{ $key }}'" class="ri-check-line text-sm"></i>
                </button>
            @endforeach
        </div>
    </div>

    <!-- A. Permintaan Daftar Hadir -->
    <template x-if="selectedSubcategory === 'permintaan_daftar_hadir'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="kegiatan_nama_acara" :value="__('Nama Acara')" class="font-semibold text-xs text-slate-700" />
                <x-text-input 
                    type="text" 
                    id="kegiatan_nama_acara" 
                    name="nama_acara" 
                    value="{{ old('nama_acara') }}" 
                    placeholder="Contoh: Bimbingan Teknis CPPOB Bagi Pelaku Usaha UMKM..." 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                    required 
                />
            </div>

            <div>
                <x-input-label for="kegiatan_jam" :value="__('Jam / Waktu Pelaksanaan')" class="font-semibold text-xs text-slate-700" />
                <x-text-input 
                    type="text" 
                    id="kegiatan_jam" 
                    name="jam" 
                    value="{{ old('jam') }}" 
                    placeholder="Contoh: 08:30 - 15:00 WIB..." 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                    required 
                />
            </div>
        </div>
    </template>

    <!-- B. Permintaan QR Code -->
    <template x-if="selectedSubcategory === 'permintaan_qrcode'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="kegiatan_link_qrcode" :value="__('Link yang Akan Dijadikan QR Code')" class="font-semibold text-xs text-slate-700" />
                <x-text-input 
                    type="text" 
                    id="kegiatan_link_qrcode" 
                    name="link_qrcode" 
                    value="{{ old('link_qrcode') }}" 
                    placeholder="Contoh: https://bbpomsemarang.pom.go.id/materi-bimtek atau tautan Google Drive..." 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" 
                    required 
                />
                <p class="text-[11px] text-slate-400 mt-1">Teknisi TIK akan men-generate QR Code resmi beresolusi tinggi dengan logo BBPOM.</p>
            </div>
        </div>
    </template>

    <!-- C. Survey -->
    <template x-if="selectedSubcategory === 'survey'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ surveyFileName: '' }">
            <div>
                <x-input-label for="survey_judul" :value="__('Judul Survey')" class="font-semibold text-xs text-slate-700" />
                <x-text-input 
                    type="text" 
                    id="survey_judul" 
                    name="judul_survey" 
                    value="{{ old('judul_survey') }}" 
                    placeholder="Contoh: Survei Kepuasan Pelanggan Layanan Pengujian Semester II..." 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                    required 
                />
            </div>

            <!-- Pertanyaan Dinamis & Kategori Jawaban -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="ri-questionnaire-line text-emerald-700"></i>
                        <span>Daftar Pertanyaan Survey</span>
                    </label>
                    <button 
                        type="button" 
                        @click="addSurveyQuestion()" 
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-semibold transition"
                    >
                        <i class="ri-add-line"></i>
                        <span>Tambah Pertanyaan</span>
                    </button>
                </div>

                <template x-for="(q, idx) in surveyQuestions" :key="idx">
                    <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-emerald-700" x-text="'Pertanyaan #' + (idx + 1)"></span>
                            <button 
                                type="button" 
                                @click="removeSurveyQuestion(idx)" 
                                class="text-rose-500 hover:text-rose-700 text-xs flex items-center gap-0.5"
                                title="Hapus pertanyaan"
                            >
                                <i class="ri-delete-bin-line"></i>
                                <span>Hapus</span>
                            </button>
                        </div>
                        <input 
                            type="text" 
                            x-model="q.text" 
                            placeholder="Tuliskan butir pertanyaan survey..." 
                            class="w-full rounded-xl border-slate-300 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs"
                        >
                        <div class="flex items-center gap-2 pt-1">
                            <span class="text-[11px] font-medium text-slate-500 shrink-0">Kategori Jawaban:</span>
                            <select 
                                x-model="q.tipe" 
                                class="text-xs rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white py-1 px-2.5 shadow-2xs"
                            >
                                <option value="Pilihan Skala 4">Pilihan Skala 4 (Sangat Baik / Baik / Cukup / Kurang)</option>
                                <option value="Yes / No">Yes / No</option>
                                <option value="Pilihan Opsi">Pilihan Opsi (Multiple Choice)</option>
                                <option value="Essay / Teks">Essay / Teks Bebas</option>
                                <option value="Rating Bintang">Rating Bintang (1 - 5)</option>
                            </select>
                        </div>
                    </div>
                </template>

                <input type="hidden" name="pertanyaan_json" :value="JSON.stringify(surveyQuestions)">
            </div>

            <!-- Alternatif Dokumen Pertanyaan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Atau Unggah Berkas Draf Pertanyaan (PDF / Word) <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                    <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                        <span>Pilih Dokumen</span>
                        <input 
                            type="file" 
                            name="attachment" 
                            class="sr-only"
                            @change="surveyFileName = $event.target.files[0]?.name"
                        >
                    </label>
                    <span class="text-xs text-slate-500 truncate" x-text="surveyFileName ? 'File: ' + surveyFileName : 'Format: PDF, DOC, DOCX (Maks 5MB)'"></span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Jika mengunggah berkas kuesioner draf, kolom daftar pertanyaan di atas boleh dikosongkan.</p>
            </div>
        </div>
    </template>

    <!-- D. Pretest / Post Test -->
    <template x-if="selectedSubcategory === 'post_test_pretest'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ testFileName: '' }">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="kegiatan_judul_test" :value="__('Judul Acara')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input 
                        type="text" 
                        id="kegiatan_judul_test" 
                        name="judul_acara" 
                        value="{{ old('judul_acara') }}" 
                        placeholder="Contoh: Sosialisasi Pedoman Registrasi Pangan Olahan..." 
                        class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                        required 
                    />
                </div>

                <div>
                    <x-input-label for="kegiatan_jenis_test" :value="__('Jenis Evaluasi')" class="font-semibold text-xs text-slate-700" />
                    <select 
                        id="kegiatan_jenis_test" 
                        name="jenis_test" 
                        class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                        required
                    >
                        <option value="pretest" {{ old('jenis_test') == 'pretest' ? 'selected' : '' }}>Pretest</option>
                        <option value="post_test" {{ old('jenis_test') == 'post_test' ? 'selected' : '' }}>Post Test</option>
                    </select>
                </div>
            </div>

            <!-- Pertanyaan Evaluasi Dinamis -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="ri-file-list-3-line text-emerald-700"></i>
                        <span>Butir Soal Evaluasi</span>
                    </label>
                    <button 
                        type="button" 
                        @click="addTestQuestion()" 
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-semibold transition"
                    >
                        <i class="ri-add-line"></i>
                        <span>Tambah Soal</span>
                    </button>
                </div>

                <template x-for="(q, idx) in testQuestions" :key="idx">
                    <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-emerald-700" x-text="'Soal #' + (idx + 1)"></span>
                            <button 
                                type="button" 
                                @click="removeTestQuestion(idx)" 
                                class="text-rose-500 hover:text-rose-700 text-xs flex items-center gap-0.5"
                                title="Hapus soal"
                            >
                                <i class="ri-delete-bin-line"></i>
                                <span>Hapus</span>
                            </button>
                        </div>
                        <input 
                            type="text" 
                            x-model="q.text" 
                            placeholder="Tuliskan naskah soal pertanyaan..." 
                            class="w-full rounded-xl border-slate-300 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs"
                        >
                        <div class="flex items-center gap-2 pt-1">
                            <span class="text-[11px] font-medium text-slate-500 shrink-0">Tipe Jawaban:</span>
                            <select 
                                x-model="q.tipe" 
                                class="text-xs rounded-xl border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 bg-white py-1 px-2.5 shadow-2xs"
                            >
                                <option value="Multiple Answer">Multiple Answer (Pilihan Ganda A/B/C/D)</option>
                                <option value="Essay / Text">Essay / Isian Singkat</option>
                                <option value="File Upload">Upload Berkas Jawaban</option>
                            </select>
                        </div>
                    </div>
                </template>

                <input type="hidden" name="pertanyaan_json" :value="JSON.stringify(testQuestions)">
            </div>

            <!-- Alternatif Berkas Soal -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Atau Unggah Naskah Soal Lengkap (PDF / Word) <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                    <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                        <span>Pilih Naskah Soal</span>
                        <input 
                            type="file" 
                            name="attachment" 
                            class="sr-only"
                            @change="testFileName = $event.target.files[0]?.name"
                        >
                    </label>
                    <span class="text-xs text-slate-500 truncate" x-text="testFileName ? 'File: ' + testFileName : 'Format: PDF, DOC, DOCX (Maks 5MB)'"></span>
                </div>
            </div>
        </div>
    </template>

    <!-- E. Lainnya Formulir & Kegiatan -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ kegiatanOtherFileName: '' }">
            <div>
                <x-input-label for="kegiatan_deskripsi" :value="__('Deskripsi')" class="font-semibold text-xs text-slate-700" />
                <textarea 
                    id="kegiatan_deskripsi" 
                    name="deskripsi" 
                    rows="4" 
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                    placeholder="Jelaskan kebutuhan pembuatan linktree tautan materi acara, sertifikat elektronik kegiatan, atau form khusus lainnya..." 
                    required
                >{{ old('deskripsi') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Upload File Lampiran <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                    <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                        <span>Pilih Berkas</span>
                        <input 
                            type="file" 
                            name="attachment" 
                            class="sr-only"
                            @change="kegiatanOtherFileName = $event.target.files[0]?.name"
                        >
                    </label>
                    <span class="text-xs text-slate-500 truncate" x-text="kegiatanOtherFileName ? 'File: ' + kegiatanOtherFileName : 'Format: JPG, PNG, PDF (Maks 5MB)'"></span>
                </div>
            </div>
        </div>
    </template>
</div>

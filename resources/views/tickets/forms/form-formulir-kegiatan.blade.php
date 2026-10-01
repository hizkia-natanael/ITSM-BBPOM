<!-- 6. FORMULIR & KEGIATAN (Daftar Hadir, QR Code, Survey, Pretest/Posttest, Linktree) -->
<div 
    class="space-y-6"
    x-data="{
        surveyQuestions: [{ text: '', tipe: 'Pilihan Skala 4' }],
        addSurveyQuestion() {
            this.surveyQuestions.push({ text: '', tipe: 'Pilihan Skala 4' });
        },
        removeSurveyQuestion(index) {
            if (this.surveyQuestions.length > 1) {
                this.surveyQuestions.splice(index, 1);
            } else {
                this.surveyQuestions[0].text = '';
            }
        },
        testQuestions: [{ text: '', tipe: 'Multiple Answer' }],
        addTestQuestion() {
            this.testQuestions.push({ text: '', tipe: 'Multiple Answer' });
        },
        removeTestQuestion(index) {
            if (this.testQuestions.length > 1) {
                this.testQuestions.splice(index, 1);
            } else {
                this.testQuestions[0].text = '';
            }
        }
    }"
>
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Formulir & Kegiatan:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <button 
                type="button" 
                @click="selectedSubCategory = 'permintaan_daftar_hadir'; selectedSubcategory = 'permintaan_daftar_hadir'"
                :class="selectedSubCategory === 'permintaan_daftar_hadir' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-user-follow-line text-emerald-600"></i>
                    <span>Permintaan Daftar Hadir</span>
                </span>
                <i x-show="selectedSubCategory === 'permintaan_daftar_hadir'" class="ri-check-line text-sm"></i>
            </button>
            <button 
                type="button" 
                @click="selectedSubCategory = 'permintaan_qrcode'; selectedSubcategory = 'permintaan_qrcode'"
                :class="selectedSubCategory === 'permintaan_qrcode' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-qr-code-line text-blue-600"></i>
                    <span>Permintaan QR Code</span>
                </span>
                <i x-show="selectedSubCategory === 'permintaan_qrcode'" class="ri-check-line text-sm"></i>
            </button>
            <button 
                type="button" 
                @click="selectedSubCategory = 'survey'; selectedSubcategory = 'survey'"
                :class="selectedSubCategory === 'survey' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-survey-line text-indigo-600"></i>
                    <span>Survey</span>
                </span>
                <i x-show="selectedSubCategory === 'survey'" class="ri-check-line text-sm"></i>
            </button>
            <button 
                type="button" 
                @click="selectedSubCategory = 'post_test_pretest'; selectedSubcategory = 'post_test_pretest'"
                :class="selectedSubCategory === 'post_test_pretest' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-file-list-3-line text-amber-600"></i>
                    <span>Pretest / Post Test</span>
                </span>
                <i x-show="selectedSubCategory === 'post_test_pretest'" class="ri-check-line text-sm"></i>
            </button>
            <button 
                type="button" 
                @click="selectedSubCategory = 'lainnya'; selectedSubcategory = 'lainnya'"
                :class="selectedSubCategory === 'lainnya' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-more-line text-slate-500"></i>
                    <span>Lainnya (Linktree, dll)</span>
                </span>
                <i x-show="selectedSubCategory === 'lainnya'" class="ri-check-line text-sm"></i>
            </button>
        </div>
    </div>

    <!-- A. Permintaan Daftar Hadir -->
    <div x-show="selectedSubCategory === 'permintaan_daftar_hadir'" class="space-y-4 pt-4 border-t border-slate-100">
        <div>
            <label for="kegiatan_nama_acara" class="block text-xs font-semibold text-slate-700 mb-1">
                Nama Acara <span class="text-rose-500">*</span>
            </label>
            <input 
                type="text" 
                id="kegiatan_nama_acara" 
                name="nama_acara" 
                value="{{ old('nama_acara') }}" 
                placeholder="Contoh: Sosialisasi Pengawasan Pangan Jajanan Anak Sekolah" 
                :required="selectedSubCategory === 'permintaan_daftar_hadir'"
                :disabled="selectedSubCategory !== 'permintaan_daftar_hadir'"
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
            >
            <x-input-error class="mt-1" :messages="$errors->get('nama_acara')" />
        </div>

        <div>
            <label for="kegiatan_jam" class="block text-xs font-semibold text-slate-700 mb-1">
                Jam <span class="text-rose-500">*</span>
            </label>
            <input 
                type="text" 
                id="kegiatan_jam" 
                name="jam" 
                value="{{ old('jam') }}" 
                placeholder="Contoh: 08:30 - 12:00 WIB" 
                :required="selectedSubCategory === 'permintaan_daftar_hadir'"
                :disabled="selectedSubCategory !== 'permintaan_daftar_hadir'"
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
            >
            <x-input-error class="mt-1" :messages="$errors->get('jam')" />
        </div>
    </div>

    <!-- B. Permintaan QR Code -->
    <div x-show="selectedSubCategory === 'permintaan_qrcode'" class="space-y-4 pt-4 border-t border-slate-100">
        <div>
            <label for="kegiatan_link_qrcode" class="block text-xs font-semibold text-slate-700 mb-1">
                Link yang akan dijadikan QR Code <span class="text-rose-500">*</span>
            </label>
            <input 
                type="url" 
                id="kegiatan_link_qrcode" 
                name="link_qrcode" 
                value="{{ old('link_qrcode') }}" 
                placeholder="https://..." 
                :required="selectedSubCategory === 'permintaan_qrcode'"
                :disabled="selectedSubCategory !== 'permintaan_qrcode'"
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
            >
            <p class="text-[11px] text-slate-400 mt-1">Masukkan URL lengkap tautan tujuan (Google Drive, survei, materi, dsb.).</p>
            <x-input-error class="mt-1" :messages="$errors->get('link_qrcode')" />
        </div>
    </div>

    <!-- C. Survey -->
    <div x-show="selectedSubCategory === 'survey'" class="space-y-4 pt-4 border-t border-slate-100" x-data="{ surveyFileName: '' }">
        <div>
            <label for="survey_judul" class="block text-xs font-semibold text-slate-700 mb-1">
                Judul Survey <span class="text-rose-500">*</span>
            </label>
            <input 
                type="text" 
                id="survey_judul" 
                name="judul_survey" 
                value="{{ old('judul_survey') }}" 
                placeholder="Contoh: Survei Kepuasan Pelanggan Pelayanan Informasi Obat dan Makanan 2026" 
                :required="selectedSubCategory === 'survey'"
                :disabled="selectedSubCategory !== 'survey'"
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
            >
            <x-input-error class="mt-1" :messages="$errors->get('judul_survey')" />
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                File Upload (PDF/Word) <span class="text-slate-400 font-normal">(Jika upload file digunakan, kolom pertanyaan di bawah boleh dikosongkan)</span>
            </label>
            <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                    <span>Pilih Berkas</span>
                    <input 
                        type="file" 
                        name="file_lampiran" 
                        class="sr-only"
                        @change="surveyFileName = $event.target.files[0]?.name"
                        :disabled="selectedSubCategory !== 'survey'"
                    >
                </label>
                <span class="text-xs text-slate-500 truncate" x-text="surveyFileName ? 'File: ' + surveyFileName : 'Format: PDF, DOC, DOCX (Maks 5MB)'"></span>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
        </div>

        <!-- Dynamic Questions Builder for Survey -->
        <div class="pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-semibold text-slate-700">
                    Pertanyaan Survei (Dapat menambahkan lebih dari 1 pertanyaan)
                </label>
                <button 
                    type="button" 
                    @click="addSurveyQuestion()"
                    class="text-xs text-emerald-700 hover:text-emerald-800 font-bold flex items-center gap-1"
                >
                    <i class="ri-add-line text-sm"></i>
                    <span>Tambah Pertanyaan</span>
                </button>
            </div>

            <input 
                type="hidden" 
                name="pertanyaan_json" 
                :value="JSON.stringify(surveyQuestions.filter(q => q.text.trim() !== ''))"
                :disabled="selectedSubCategory !== 'survey'"
            >

            <div class="space-y-3">
                <template x-for="(item, idx) in surveyQuestions" :key="idx">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 w-6" x-text="(idx + 1) + '.'"></span>
                        <input 
                            type="text" 
                            x-model="item.text"
                            placeholder="Tuliskan butir pertanyaan survei..." 
                            class="flex-1 w-full rounded-lg border-slate-300 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                        >
                        <select 
                            x-model="item.tipe" 
                            class="w-full sm:w-48 rounded-lg border-slate-300 text-xs text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                        >
                            <option value="Pilihan Skala 4">Pilihan Skala 4</option>
                            <option value="Yes / No">Yes / No</option>
                            <option value="Pilihan Opsi">Pilihan Opsi</option>
                            <option value="Essay / Teks">Essay / Teks</option>
                            <option value="Rating Bintang">Rating Bintang</option>
                        </select>
                        <button 
                            type="button" 
                            @click="removeSurveyQuestion(idx)" 
                            class="p-1.5 text-slate-400 hover:text-rose-600 transition"
                            title="Hapus butir pertanyaan"
                        >
                            <i class="ri-delete-bin-line text-sm"></i>
                        </button>
                    </div>
                </template>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('pertanyaan_json')" />
        </div>
    </div>

    <!-- D. Pretest / Post Test -->
    <div x-show="selectedSubCategory === 'post_test_pretest'" class="space-y-4 pt-4 border-t border-slate-100" x-data="{ testFileName: '' }">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label for="test_judul_acara" class="block text-xs font-semibold text-slate-700 mb-1">
                    Judul Acara <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="test_judul_acara" 
                    name="judul_acara" 
                    value="{{ old('judul_acara') }}" 
                    placeholder="Contoh: Bimtek Internal Pengujian Mikrobiologi Laboratorium" 
                    :required="selectedSubCategory === 'post_test_pretest'"
                    :disabled="selectedSubCategory !== 'post_test_pretest'"
                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                >
                <x-input-error class="mt-1" :messages="$errors->get('judul_acara')" />
            </div>

            <div>
                <label for="test_jenis" class="block text-xs font-semibold text-slate-700 mb-1">
                    Jenis <span class="text-rose-500">*</span>
                </label>
                <select 
                    id="test_jenis" 
                    name="jenis_test" 
                    :required="selectedSubCategory === 'post_test_pretest'"
                    :disabled="selectedSubCategory !== 'post_test_pretest'"
                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                >
                    <option value="pretest" {{ old('jenis_test') === 'pretest' ? 'selected' : '' }}>Pretest</option>
                    <option value="post_test" {{ old('jenis_test') === 'post_test' ? 'selected' : '' }}>Post Test</option>
                </select>
                <x-input-error class="mt-1" :messages="$errors->get('jenis_test')" />
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                File Upload (PDF/Word) <span class="text-slate-400 font-normal">(Jika upload file digunakan, kolom pertanyaan di bawah boleh dikosongkan)</span>
            </label>
            <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                    <span>Pilih Berkas</span>
                    <input 
                        type="file" 
                        name="file_lampiran" 
                        class="sr-only"
                        @change="testFileName = $event.target.files[0]?.name"
                        :disabled="selectedSubCategory !== 'post_test_pretest'"
                    >
                </label>
                <span class="text-xs text-slate-500 truncate" x-text="testFileName ? 'File: ' + testFileName : 'Format: PDF, DOC, DOCX (Maks 5MB)'"></span>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
        </div>

        <!-- Dynamic Questions Builder for Test -->
        <div class="pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-semibold text-slate-700">
                    Pertanyaan (Dapat menambahkan lebih dari 1 pertanyaan)
                </label>
                <button 
                    type="button" 
                    @click="addTestQuestion()"
                    class="text-xs text-emerald-700 hover:text-emerald-800 font-bold flex items-center gap-1"
                >
                    <i class="ri-add-line text-sm"></i>
                    <span>Tambah Soal</span>
                </button>
            </div>

            <input 
                type="hidden" 
                name="pertanyaan_json" 
                :value="JSON.stringify(testQuestions.filter(q => q.text.trim() !== ''))"
                :disabled="selectedSubCategory !== 'post_test_pretest'"
            >

            <div class="space-y-3">
                <template x-for="(item, idx) in testQuestions" :key="idx">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 w-6" x-text="(idx + 1) + '.'"></span>
                        <input 
                            type="text" 
                            x-model="item.text"
                            placeholder="Tuliskan butir soal evaluasi..." 
                            class="flex-1 w-full rounded-lg border-slate-300 text-xs text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                        >
                        <select 
                            x-model="item.tipe" 
                            class="w-full sm:w-48 rounded-lg border-slate-300 text-xs text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                        >
                            <option value="Multiple Answer">Multiple Answer</option>
                            <option value="Essay / Text">Essay / Text</option>
                            <option value="File Upload">File Upload</option>
                        </select>
                        <button 
                            type="button" 
                            @click="removeTestQuestion(idx)" 
                            class="p-1.5 text-slate-400 hover:text-rose-600 transition"
                            title="Hapus butir soal"
                        >
                            <i class="ri-delete-bin-line text-sm"></i>
                        </button>
                    </div>
                </template>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('pertanyaan_json')" />
        </div>
    </div>

    <!-- E. Lainnya Formulir & Kegiatan -->
    <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4 pt-4 border-t border-slate-100" x-data="{ fileName: '' }">
        <div>
            <label for="kegiatan_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                Deskripsi <span class="text-rose-500">*</span>
            </label>
            <textarea 
                id="kegiatan_deskripsi" 
                name="deskripsi" 
                rows="4" 
                :required="selectedSubCategory === 'lainnya'"
                :disabled="selectedSubCategory !== 'lainnya'"
                placeholder="Jelaskan kebutuhan pembuatan Linktree, Google Form khusus, atau format formulir lainnya..." 
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
            >{{ old('deskripsi') }}</textarea>
            <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                Upload File <span class="text-slate-400 font-normal">(Opsional)</span>
            </label>
            <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                    <span>Pilih Berkas</span>
                    <input 
                        type="file" 
                        name="file_lampiran" 
                        class="sr-only"
                        @change="fileName = $event.target.files[0]?.name"
                        :disabled="selectedSubCategory !== 'lainnya'"
                    >
                </label>
                <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: PDF, DOC, DOCX, ZIP (Maks 5MB)'"></span>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
        </div>
    </div>
</div>

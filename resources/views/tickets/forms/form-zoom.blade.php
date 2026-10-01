<!-- 5. ZOOM (Layanan Zoom Meeting / Webinar) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Zoom:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <button 
                type="button" 
                @click="selectedSubCategory = 'permintaan_link_zoom'; selectedSubcategory = 'permintaan_link_zoom'"
                :class="selectedSubCategory === 'permintaan_link_zoom' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-video-chat-line text-blue-500"></i>
                    <span>Permintaan Link Zoom</span>
                </span>
                <i x-show="selectedSubCategory === 'permintaan_link_zoom'" class="ri-check-line text-sm"></i>
            </button>
            <button 
                type="button" 
                @click="selectedSubCategory = 'lainnya'; selectedSubcategory = 'lainnya'"
                :class="selectedSubCategory === 'lainnya' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-more-line text-slate-500"></i>
                    <span>Lainnya</span>
                </span>
                <i x-show="selectedSubCategory === 'lainnya'" class="ri-check-line text-sm"></i>
            </button>
        </div>
    </div>

    <!-- A. Permintaan Link Zoom -->
    <div x-show="selectedSubCategory === 'permintaan_link_zoom'" class="space-y-4 pt-4 border-t border-slate-100">
        <div>
            <label for="zoom_nama_acara" class="block text-xs font-semibold text-slate-700 mb-1">
                Nama Acara <span class="text-rose-500">*</span>
            </label>
            <input 
                type="text" 
                id="zoom_nama_acara" 
                name="nama_acara" 
                value="{{ old('nama_acara') }}" 
                placeholder="Contoh: Rapat Koordinasi Evaluasi Pengawasan Obat Semester I" 
                :required="selectedSubCategory === 'permintaan_link_zoom'"
                :disabled="selectedSubCategory !== 'permintaan_link_zoom'"
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
            >
            <x-input-error class="mt-1" :messages="$errors->get('nama_acara')" />
        </div>

        <div>
            <label for="zoom_jam" class="block text-xs font-semibold text-slate-700 mb-1">
                Jam <span class="text-rose-500">*</span>
            </label>
            <input 
                type="text" 
                id="zoom_jam" 
                name="jam" 
                value="{{ old('jam') }}" 
                placeholder="Contoh: 09:00 - 12:00 WIB atau 13:30 WIB s/d Selesai" 
                :required="selectedSubCategory === 'permintaan_link_zoom'"
                :disabled="selectedSubCategory !== 'permintaan_link_zoom'"
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
            >
            <p class="text-[11px] text-slate-400 mt-1">Sebutkan perkiraan jam mulai hingga selesai kegiatan rapat.</p>
            <x-input-error class="mt-1" :messages="$errors->get('jam')" />
        </div>
    </div>

    <!-- B. Lainnya Zoom -->
    <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4 pt-4 border-t border-slate-100" x-data="{ fileName: '' }">
        <div>
            <label for="zoom_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                Deskripsi <span class="text-rose-500">*</span>
            </label>
            <textarea 
                id="zoom_deskripsi" 
                name="deskripsi" 
                rows="4" 
                :required="selectedSubCategory === 'lainnya'"
                :disabled="selectedSubCategory !== 'lainnya'"
                placeholder="Jelaskan kebutuhan pengaturan khusus Zoom, pendampingan teknisi, kapasitas besar, atau kendala lainnya..." 
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
                    <span>Pilih File</span>
                    <input 
                        type="file" 
                        name="file_lampiran" 
                        class="sr-only"
                        @change="fileName = $event.target.files[0]?.name"
                        :disabled="selectedSubCategory !== 'lainnya'"
                    >
                </label>
                <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF, DOCX (Maks 5MB)'"></span>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
        </div>
    </div>
</div>

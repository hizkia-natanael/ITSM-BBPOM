<!-- 10. INSTALL ULANG WINDOWS -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Install Windows:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <button 
                type="button" 
                @click="selectedSubCategory = 'permintaan_install_windows'; selectedSubcategory = 'permintaan_install_windows'"
                :class="selectedSubCategory === 'permintaan_install_windows' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-windows-line text-blue-500"></i>
                    <span>Permintaan Install Ulang Windows</span>
                </span>
                <i x-show="selectedSubCategory === 'permintaan_install_windows'" class="ri-check-line text-sm"></i>
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

    <!-- A. Permintaan Install Ulang Windows -->
    <div x-show="selectedSubCategory === 'permintaan_install_windows'" class="space-y-4 pt-4 border-t border-slate-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="win_bidang" class="block text-xs font-semibold text-slate-700 mb-1">
                    Bidang / Substansi <span class="text-rose-500">*</span>
                </label>
                <select 
                    id="win_bidang" 
                    name="bidang" 
                    :required="selectedSubCategory === 'permintaan_install_windows'"
                    :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                >
                    <option value="">-- Pilih Bidang / Substansi --</option>
                    <option value="Tata Usaha" {{ old('bidang') == 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                    <option value="Pengujian" {{ old('bidang') == 'Pengujian' ? 'selected' : '' }}>Pengujian</option>
                    <option value="Penindakan" {{ old('bidang') == 'Penindakan' ? 'selected' : '' }}>Penindakan</option>
                    <option value="Infokom" {{ old('bidang') == 'Infokom' ? 'selected' : '' }}>Infokom</option>
                    <option value="Pemeriksaan" {{ old('bidang') == 'Pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                </select>
                <x-input-error class="mt-1" :messages="$errors->get('bidang')" />
            </div>

            <div>
                <label for="win_merk_perangkat" class="block text-xs font-semibold text-slate-700 mb-1">
                    Merk Laptop / Komputer <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="win_merk_perangkat" 
                    name="merk_perangkat" 
                    value="{{ old('merk_perangkat') }}" 
                    placeholder="Contoh: Lenovo ThinkPad L14 / HP ProBook 440..." 
                    :required="selectedSubCategory === 'permintaan_install_windows'"
                    :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                >
                <x-input-error class="mt-1" :messages="$errors->get('merk_perangkat')" />
            </div>
        </div>

        <div>
            <label for="win_kode_bmn" class="block text-xs font-semibold text-slate-700 mb-1">
                Kode BMN <span class="text-rose-500">*</span>
            </label>
            <input 
                type="text" 
                id="win_kode_bmn" 
                name="kode_bmn" 
                value="{{ old('kode_bmn') }}" 
                placeholder="Contoh: 3.05.02.01.002.XXXXX (tercantum pada stiker BMN perangkat)" 
                :required="selectedSubCategory === 'permintaan_install_windows'"
                :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
            >
            <p class="text-[11px] text-slate-400 mt-1">Kode Barang Milik Negara yang tertempel pada perangkat dinas BBPOM Semarang.</p>
            <x-input-error class="mt-1" :messages="$errors->get('kode_bmn')" />
        </div>

        <div>
            <label for="win_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                Deskripsi Kendala / Alasan Install Ulang <span class="text-rose-500">*</span>
            </label>
            <textarea 
                id="win_deskripsi" 
                name="deskripsi" 
                rows="4" 
                :required="selectedSubCategory === 'permintaan_install_windows'"
                :disabled="selectedSubCategory !== 'permintaan_install_windows'"
                placeholder="Jelaskan alasan pengajuan install ulang (misal: Windows corrupt/blue screen, terkena virus/ransomware, sistem sangat lambat, atau upgrade OS)..." 
                class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm placeholder:text-slate-400"
            >{{ old('deskripsi') }}</textarea>
            <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
        </div>
    </div>

    <!-- B. Lainnya -->
    <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4 pt-4 border-t border-slate-100" x-data="{ winOtherFileName: '' }">
        <div>
            <label for="win_lainnya_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                Deskripsi <span class="text-rose-500">*</span>
            </label>
            <textarea 
                id="win_lainnya_deskripsi" 
                name="deskripsi" 
                rows="4" 
                :required="selectedSubCategory === 'lainnya'"
                :disabled="selectedSubCategory !== 'lainnya'"
                placeholder="Jelaskan kendala sistem operasi atau software dinas lainnya yang dialami..." 
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
                        @change="winOtherFileName = $event.target.files[0]?.name"
                        :disabled="selectedSubCategory !== 'lainnya'"
                    >
                </label>
                <span class="text-xs text-slate-500 truncate" x-text="winOtherFileName ? 'File: ' + winOtherFileName : 'Format: JPG, PNG, PDF (Maks 5MB)'"></span>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
        </div>
    </div>
</div>

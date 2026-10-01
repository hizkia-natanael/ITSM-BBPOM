<!-- 10. INSTALL ULANG WINDOWS -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Install Windows:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($services['install_windows']['subcategories'] as $key => $label)
                <button 
                    type="button" 
                    @click="selectedSubcategory = '{{ $key }}'; selectedSubCategory = '{{ $key }}'"
                    :class="selectedSubcategory === '{{ $key }}' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                    class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
                >
                    <span class="flex items-center gap-1.5">
                        @if($key === 'permintaan_install_windows')
                            <i class="ri-windows-line text-blue-500"></i>
                        @else
                            <i class="ri-more-line text-slate-500"></i>
                        @endif
                        <span>{{ $label }}</span>
                    </span>
                    <i x-show="selectedSubcategory === '{{ $key }}'" class="ri-check-line text-sm"></i>
                </button>
            @endforeach
        </div>
    </div>

    <!-- A. Permintaan Install Ulang Windows -->
    <template x-if="selectedSubcategory === 'permintaan_install_windows'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="win_bidang" :value="__('Bidang / Substansi')" class="font-semibold text-xs text-slate-700" />
                    <select 
                        id="win_bidang" 
                        name="bidang" 
                        class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600"
                        required
                    >
                        <option value="">-- Pilih Bidang / Substansi --</option>
                        <option value="Tata Usaha" {{ old('bidang') == 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                        <option value="Pengujian" {{ old('bidang') == 'Pengujian' ? 'selected' : '' }}>Pengujian</option>
                        <option value="Penindakan" {{ old('bidang') == 'Penindakan' ? 'selected' : '' }}>Penindakan</option>
                        <option value="Infokom" {{ old('bidang') == 'Infokom' ? 'selected' : '' }}>Infokom</option>
                        <option value="Pemeriksaan" {{ old('bidang') == 'Pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="win_merk_perangkat" :value="__('Merk Laptop / Komputer')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input 
                        type="text" 
                        id="win_merk_perangkat" 
                        name="merk_perangkat" 
                        value="{{ old('merk_perangkat') }}" 
                        placeholder="Contoh: Lenovo ThinkPad L14 / HP ProBook 440..." 
                        class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                        required 
                    />
                </div>
            </div>

            <div>
                <x-input-label for="win_kode_bmn" :value="__('Kode BMN Perangkat')" class="font-semibold text-xs text-slate-700" />
                <x-text-input 
                    type="text" 
                    id="win_kode_bmn" 
                    name="kode_bmn" 
                    value="{{ old('kode_bmn') }}" 
                    placeholder="Contoh: 3.05.02.01.002.XXXXX (tercantum pada stiker BMN)..." 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" 
                    required 
                />
                <p class="text-[11px] text-slate-400 mt-1">Kode Barang Milik Negara yang tertempel pada perangkat dinas BBPOM Semarang.</p>
            </div>

            <div>
                <x-input-label for="win_deskripsi" :value="__('Deskripsi Kendala / Alasan Install Ulang')" class="font-semibold text-xs text-slate-700" />
                <textarea 
                    id="win_deskripsi" 
                    name="deskripsi" 
                    rows="4" 
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                    placeholder="Jelaskan alasan pengajuan install ulang (misal: Windows corrupt/blue screen, terkena virus, sistem lambat, atau upgrade OS)..." 
                    required
                >{{ old('deskripsi') }}</textarea>
            </div>
        </div>
    </template>

    <!-- B. Lainnya -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ winOtherFileName: '' }">
            <div>
                <x-input-label for="win_lainnya_deskripsi" :value="__('Deskripsi Kendala')" class="font-semibold text-xs text-slate-700" />
                <textarea 
                    id="win_lainnya_deskripsi" 
                    name="deskripsi" 
                    rows="4" 
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                    placeholder="Jelaskan kendala sistem operasi atau software dinas lainnya yang dialami..." 
                    required
                >{{ old('deskripsi') }}</textarea>
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
                            name="attachment" 
                            class="sr-only"
                            @change="winOtherFileName = $event.target.files[0]?.name"
                        >
                    </label>
                    <span class="text-xs text-slate-500 truncate" x-text="winOtherFileName ? 'File: ' + winOtherFileName : 'Format: JPG, PNG, PDF (Maks 5MB)'"></span>
                </div>
            </div>
        </div>
    </template>
</div>

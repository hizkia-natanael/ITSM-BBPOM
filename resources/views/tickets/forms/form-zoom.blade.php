<!-- 5. ZOOM (Layanan Zoom Meeting / Webinar) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills (Standard SIPT & SRIKANDI Pattern) -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Zoom:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($services['zoom']['subcategories'] as $key => $label)
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

    <!-- A. Permintaan Link Zoom -->
    <template x-if="selectedSubcategory === 'permintaan_link_zoom'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="zoom_nama_acara" class="font-semibold text-xs text-slate-700">
                    <span>Nama Acara / Kegiatan</span> <span class="text-rose-500">*</span>
                </x-input-label>
                <x-text-input 
                    id="zoom_nama_acara" 
                    name="nama_acara" 
                    type="text" 
                    value="{{ old('nama_acara') }}" 
                    placeholder="Contoh: Rapat Koordinasi Evaluasi Pengawasan Obat Semester I" 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                    required 
                />
                <p class="text-[11px] text-slate-400 mt-1">Nama kegiatan yang akan dijadikan judul ruang rapat virtual Zoom.</p>
                <x-input-error class="mt-1" :messages="$errors->get('nama_acara')" />
            </div>

            <div>
                <x-input-label for="zoom_jam" class="font-semibold text-xs text-slate-700">
                    <span>Jam / Waktu Pelaksanaan</span> <span class="text-rose-500">*</span>
                </x-input-label>
                <x-text-input 
                    id="zoom_jam" 
                    name="jam" 
                    type="text" 
                    value="{{ old('jam') }}" 
                    placeholder="Contoh: 09:00 - 12:00 WIB atau 13:30 WIB s/d Selesai" 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                    required 
                />
                <p class="text-[11px] text-slate-400 mt-1">Sebutkan estimasi waktu mulai dan selesai pelaksanaan acara.</p>
                <x-input-error class="mt-1" :messages="$errors->get('jam')" />
            </div>
        </div>
    </template>

    <!-- B. Lainnya Zoom -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ zoomOtherFileName: '' }">
            <div>
                <x-input-label for="zoom_deskripsi" class="font-semibold text-xs text-slate-700">
                    <span>Deskripsi Kebutuhan / Kendala</span> <span class="text-rose-500">*</span>
                </x-input-label>
                <textarea 
                    id="zoom_deskripsi" 
                    name="deskripsi" 
                    rows="4" 
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                    placeholder="Jelaskan kebutuhan pengaturan khusus Zoom, breakout room, kapasitas besar, atau kendala lainnya..." 
                    required
                >{{ old('deskripsi') }}</textarea>
                <x-input-error class="mt-1" :messages="$errors->get('deskripsi')" />
            </div>

            <div>
                <x-file-dropzone-uploader 
                    id="dropzone_zoom" 
                    name="attachments[]" 
                    label="Unggah Berkas Lampiran Zoom" 
                    helperText="Format didukung: JPG, PNG, PDF, DOCX (Maks. 5MB per berkas, dapat memilih beberapa berkas)"
                />
                <x-input-error class="mt-1" :messages="$errors->get('attachments')" />
            </div>
        </div>
    </template>
</div>

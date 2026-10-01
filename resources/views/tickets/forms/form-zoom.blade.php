<!-- 5. ZOOM (Layanan Zoom Meeting / Webinar) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
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
                    <span class="flex items-center gap-1.5">
                        @if($key === 'permintaan_link_zoom')
                            <i class="ri-video-chat-line text-blue-500"></i>
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

    <!-- A. Permintaan Link Zoom -->
    <template x-if="selectedSubcategory === 'permintaan_link_zoom'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="zoom_nama_acara" :value="__('Nama Acara')" class="font-semibold text-xs text-slate-700" />
                <x-text-input 
                    id="zoom_nama_acara" 
                    name="nama_acara" 
                    type="text" 
                    value="{{ old('nama_acara') }}" 
                    placeholder="Contoh: Rapat Koordinasi Evaluasi Pengawasan Obat Semester I..." 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                    required 
                />
                <p class="text-[11px] text-slate-400 mt-1">Nama kegiatan yang akan dijadikan judul ruang rapat virtual Zoom.</p>
            </div>

            <div>
                <x-input-label for="zoom_jam" :value="__('Jam / Waktu Pelaksanaan')" class="font-semibold text-xs text-slate-700" />
                <x-text-input 
                    id="zoom_jam" 
                    name="jam" 
                    type="text" 
                    value="{{ old('jam') }}" 
                    placeholder="Contoh: 09:00 - 12:00 WIB..." 
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                    required 
                />
                <p class="text-[11px] text-slate-400 mt-1">Sebutkan estimasi waktu mulai dan selesai pelaksanaan acara.</p>
            </div>
        </div>
    </template>

    <!-- B. Lainnya Zoom -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ zoomOtherFileName: '' }">
            <div>
                <x-input-label for="zoom_deskripsi" :value="__('Deskripsi')" class="font-semibold text-xs text-slate-700" />
                <textarea 
                    id="zoom_deskripsi" 
                    name="deskripsi" 
                    rows="4" 
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                    placeholder="Jelaskan kebutuhan pendampingan teknis rapat, pengaturan co-host, breakout rooms, atau live streaming YouTube..." 
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
                            @change="zoomOtherFileName = $event.target.files[0]?.name"
                        >
                    </label>
                    <span class="text-xs text-slate-500 truncate" x-text="zoomOtherFileName ? 'File: ' + zoomOtherFileName : 'Format: JPG, PNG, PDF (Maks 5MB)'"></span>
                </div>
            </div>
        </div>
    </template>
</div>

<!-- 9. INFOGRAFIS & MEDIA -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Infografis:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($services['infografis']['subcategories'] as $key => $label)
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

    <!-- A. Pembuatan Infografis -->
    <template x-if="selectedSubcategory === 'pembuatan_infografis'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="infographic_title" :value="__('Judul Infografis')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="infographic_title" name="infographic_title" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: Infografis Waspada Obat Ilegal & Bahan Kimia Berbahaya..." required />
            </div>

            <div>
                <x-input-label for="infographic_content" :value="__('Isi Materi / Pesan Utama')" class="font-semibold text-xs text-slate-700" />
                <textarea id="infographic_content" name="infographic_content" rows="6" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Rincikan teks materi, narasi poin-poin penting, ketentuan warna/logo, atau referensi pesan utama yang ingin disampaikan..." required></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Uraikan konsep atau alur pesan materi secara sistematis.</p>
            </div>

            <div>
                <x-file-dropzone-uploader 
                    id="dropzone_infografis" 
                    name="attachments[]" 
                    label="Unggah File Materi Naskah / Dokumen Pendukung" 
                    :optional="true"
                    helperText="Format: PDF, Word, PPT, ZIP, JPG, PNG (Maks 5MB per berkas, dapat memilih beberapa berkas)"
                />
            </div>
        </div>
    </template>
</div>

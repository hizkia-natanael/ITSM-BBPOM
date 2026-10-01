<!-- 8. SUBSITE BBPOM -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Subsite:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($services['subsite']['subcategories'] as $key => $label)
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

    <!-- A. Upload Berita Internal -->
    <template x-if="selectedSubcategory === 'upload_berita'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="subsite_news_title" :value="__('Judul Berita')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="subsite_news_title" name="news_title" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: BBPOM Semarang Gelar Sosialisasi Keamanan Pangan di Pasar Tradisional..." required />
            </div>

            <div>
                <x-input-label for="subsite_news_content" :value="__('Isi Berita')" class="font-semibold text-xs text-slate-700" />
                <textarea id="subsite_news_content" name="news_content" rows="6" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Tuliskan naskah/isi berita publikasi lengkap di sini..." required></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Dapat menyertakan poin 5W + 1H kegiatan kedinasan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Upload Foto Dokumentasi <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition" x-data="{ fileName: '' }">
                        <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                            <span>Pilih Berkas</span>
                            <input type="file" name="attachment" class="sr-only" @change="fileName = $event.target.files[0]?.name">
                        </label>
                        <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG (Maks 5MB)'"></span>
                    </div>
                </div>

                <div>
                    <x-input-label for="subsite_target_date" :value="__('Request Tanggal Upload')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="subsite_target_date" name="target_date" type="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" required />
                    <p class="text-[11px] text-slate-400 mt-1">Target tanggal publikasi di portal subsite.</p>
                </div>
            </div>
        </div>
    </template>

    <!-- B. Upload Dokumen -->
    <template x-if="selectedSubcategory === 'upload_dokumen'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="subsite_doc_name" :value="__('Nama Dokumen (untuk tampilan subsite)')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="subsite_doc_name" name="doc_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: SK Pelayanan Publik Tahun 2026 / Laporan Tahunan 2025..." required />
            </div>

            <div>
                <x-input-label for="subsite_doc_location" :value="__('Lokasi Upload / Kategori Menu')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="subsite_doc_location" name="doc_location" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: Menu Transparansi Kinerja / Standar Pelayanan / PPID..." required />
                <p class="text-[11px] text-slate-400 mt-1">Sebutkan nama kategori halaman atau menu tujuan di subsite.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Upload File Dokumen <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition" x-data="{ fileName: '' }">
                    <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                        <span>Pilih Dokumen</span>
                        <input type="file" name="attachment" class="sr-only" @change="fileName = $event.target.files[0]?.name">
                    </label>
                    <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: PDF, Word, Excel, ZIP (Maks 5MB)'"></span>
                </div>
            </div>
        </div>
    </template>

    <!-- C. Hapus Dokumen -->
    <template x-if="selectedSubcategory === 'hapus_dokumen'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="subsite_del_name" :value="__('Judul Dokumen yang Akan Dihapus')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="subsite_del_name" name="doc_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: Pengumuman Tender Pengadaan Alat Lab 2024..." required />
            </div>

            <div>
                <x-input-label for="subsite_del_url" :value="__('Link Dokumen yang Ingin Dihapus')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="subsite_del_url" name="doc_url" type="url" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="https://semarang.pom.go.id/storage/dokumen/..." required />
                <p class="text-[11px] text-slate-400 mt-1">Salin URL tautan unduhan dokumen yang ada di website subsite saat ini.</p>
            </div>
        </div>
    </template>
</div>

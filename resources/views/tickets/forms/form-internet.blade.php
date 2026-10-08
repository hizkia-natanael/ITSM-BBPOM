<!-- 7. INTERNET & JARINGAN -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Internet:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($services['internet']['subcategories'] as $key => $label)
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

    <!-- A. Keluhan Internet Lemot / Mati -->
    <template x-if="selectedSubcategory === 'lemot_mati'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="internet_ap_location_lemot" :value="__('Lokasi Titik Access Point / Ruangan')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="internet_ap_location_lemot" name="ap_location" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: Gedung A Lantai 2, Ruang Subbag TU..." required />
                <p class="text-[11px] text-slate-400 mt-1">Sebutkan nama ruangan, lantai, atau identitas perangkat Access Point terdekat.</p>
            </div>
            <div>
                <x-input-label for="internet_complaint_lemot" :value="__('Keluhan')" class="font-semibold text-xs text-slate-700" />
                <textarea id="internet_complaint_lemot" name="complaint_details" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Jelaskan detail kendala (misal: sering RTO, sinyal lemah, SSID tidak muncul, atau putus-putus)..." required></textarea>
            </div>
        </div>
    </template>

    <!-- B. Request Penambahan Access Point -->
    <template x-if="selectedSubcategory === 'tambah_ap'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="internet_ap_location_req" :value="__('Request Lokasi Titik Access Point')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="internet_ap_location_req" name="ap_location" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Contoh: Selasar Aula Timur, Ruang Rapat Baru Lantai 3..." required />
                <p class="text-[11px] text-slate-400 mt-1">Tuliskan titik penempatan AP yang diusulkan untuk dilakukan survei teknis jaringan.</p>
            </div>
            <div>
                <x-input-label for="internet_complaint_req" :value="__('Keluhan / Alasan Kebutuhan')" class="font-semibold text-xs text-slate-700" />
                <textarea id="internet_complaint_req" name="complaint_details" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Jelaskan alasan pengajuan penambahan titik AP (misal: area blank spot, penambahan personil, atau perluasan area kerja)..." required></textarea>
            </div>
        </div>
    </template>

    <!-- C. Lainnya Internet -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="internet_description" :value="__('Deskripsi')" class="font-semibold text-xs text-slate-700" />
                <textarea id="internet_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Jelaskan kebutuhan jaringan internet, kendala kabel LAN, atau konsultasi jaringan lainnya..." required></textarea>
            </div>
            <div>
                <x-file-dropzone-uploader 
                    id="dropzone_internet" 
                    name="attachments[]" 
                    label="Unggah Foto / Berkas Kendala Jaringan" 
                    helperText="Format didukung: JPG, PNG, PDF (Maksimal 10 dokumen hingga 100MB per berkas)"
                />
            </div>
        </div>
    </template>
</div>

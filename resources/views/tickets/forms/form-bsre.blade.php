<!-- 4. SERTIFIKAT BSRE (Balai Sertifikasi Elektronik / TTE) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan BSrE:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($services['sertifikat_bse']['subcategories'] as $key => $label)
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

    <!-- A. Reset Password BSrE & B. Permintaan OTP -->
    <template x-if="selectedSubcategory === 'reset_password' || selectedSubcategory === 'permintaan_otp'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="bsre_nip" :value="__('NIP (Nomor Induk Pegawai)')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input 
                        id="bsre_nip" 
                        name="nip" 
                        type="text" 
                        value="{{ old('nip', $user->nip ?? '') }}" 
                        placeholder="18 digit NIP pegawai..." 
                        class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" 
                        required 
                    />
                    <p class="text-[11px] text-slate-400 mt-1">Otomatis terisi dari profil Anda.</p>
                </div>
                <div>
                    <x-input-label for="bsre_nik" :value="__('NIK (Nomor Induk Kependudukan)')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input 
                        id="bsre_nik" 
                        name="nik" 
                        type="text" 
                        value="{{ old('nik', $user->nik ?? '') }}" 
                        placeholder="16 digit NIK sesuai KTP..." 
                        class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" 
                        maxlength="20" 
                        required 
                    />
                    <p class="text-[11px] text-slate-400 mt-1">Wajib sesuai data KTP terdaftar di BSrE.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="bsre_email" :value="__('Email Dinas / Terdaftar di BSrE')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input 
                        id="bsre_email" 
                        name="email" 
                        type="email" 
                        value="{{ old('email', $user->email) }}" 
                        placeholder="nama@bbpom.go.id" 
                        class="mt-1 block w-full rounded-xl text-xs sm:text-sm" 
                        required 
                    />
                </div>
                <div>
                    <x-input-label for="bsre_phone" :value="__('Nomor Telepon / WhatsApp')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input 
                        id="bsre_phone" 
                        name="nomor_telepon" 
                        type="tel" 
                        value="{{ old('nomor_telepon', $user->phone_number ?? '') }}" 
                        placeholder="Contoh: 081234567890" 
                        class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" 
                        required 
                    />
                    <p class="text-[11px] text-slate-400 mt-1">Untuk pengiriman tautan aktivasi / kode OTP verifikasi.</p>
                </div>
            </div>
        </div>
    </template>

    <!-- C. Lainnya BSrE -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ bsreOtherFileName: '' }">
            <div>
                <x-input-label for="bsre_deskripsi" :value="__('Deskripsi Kendala / Permohonan')" class="font-semibold text-xs text-slate-700" />
                <textarea 
                    id="bsre_deskripsi" 
                    name="deskripsi" 
                    rows="4" 
                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                    placeholder="Jelaskan kebutuhan sertifikat elektronik, perpanjangan masa berlaku, atau kendala teknis lainnya..." 
                    required
                >{{ old('deskripsi') }}</textarea>
            </div>

            <div>
                <x-file-dropzone-uploader 
                    id="dropzone_bsre" 
                    name="attachments[]" 
                    label="Unggah Berkas Lampiran BSrE" 
                    helperText="Format didukung: PDF, DOCX, JPG, PNG (Maks. 5MB per berkas, dapat memilih beberapa berkas)"
                />
            </div>
        </div>
    </template>
</div>

<!-- 2. SRIKANDI (Sistem Kearsipan Dinamis Terintegrasi) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Srikandi:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($services['srikandi']['subcategories'] as $key => $label)
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

    <!-- A. Reset Password Srikandi -->
    <template x-if="selectedSubcategory === 'reset_password'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="srikandi_username_reset" :value="__('Username Srikandi')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="srikandi_username_reset" name="srikandi_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Username akun Srikandi Anda..." required />
            </div>
            <div>
                <x-input-label for="srikandi_password_reset" :value="__('Request Password Baru')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="srikandi_password_reset" name="srikandi_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="Password baru yang diinginkan..." required />
            </div>
        </div>
    </template>

    <!-- B. Pembuatan Akun Baru Srikandi -->
    <template x-if="selectedSubcategory === 'buat_akun'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="srikandi_fullname" :value="__('Nama Lengkap')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="srikandi_fullname" name="srikandi_fullname" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Nama lengkap pegawai..." required />
                </div>
                <div>
                    <x-input-label for="srikandi_nik" :value="__('NIK (Nomor Induk Kependudukan)')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="srikandi_nik" name="srikandi_nik" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="16 digit NIK..." maxlength="20" required />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="srikandi_phone" :value="__('No Telp / WhatsApp')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="srikandi_phone" name="srikandi_phone" type="tel" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="08xxxxxxxxxx..." required />
                </div>
                <div>
                    <x-input-label for="srikandi_email" :value="__('Email')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="srikandi_email" name="srikandi_email" type="email" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="nama@bbpom.go.id..." required />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="srikandi_req_username" :value="__('Request Username')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="srikandi_req_username" name="srikandi_req_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Usulan username..." required />
                </div>
                <div>
                    <x-input-label for="srikandi_req_password" :value="__('Request Password')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="srikandi_req_password" name="srikandi_req_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="Usulan password awal..." required />
                </div>
            </div>
        </div>
    </template>

    <!-- C. Penonaktifan Akun Srikandi -->
    <template x-if="selectedSubcategory === 'nonaktif_akun'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="srikandi_nonaktif_user" :value="__('Username Srikandi')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="srikandi_nonaktif_user" name="srikandi_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Username akun yang akan dinonaktifkan..." required />
            </div>
            <div>
                <x-input-label for="srikandi_disable_reason" :value="__('Alasan Dinonaktifkan')" class="font-semibold text-xs text-slate-700" />
                <textarea id="srikandi_disable_reason" name="srikandi_disable_reason" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Contoh: Pensiun, mutasi unit kerja lain, alih tugas..." required></textarea>
            </div>
        </div>
    </template>

    <!-- D. Reset MFA Srikandi -->
    <template x-if="selectedSubcategory === 'reset_mfa'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="srikandi_mfa_username" :value="__('Username Srikandi')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="srikandi_mfa_username" name="srikandi_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Username akun Srikandi yang terkendala MFA..." required />
            </div>
            <div class="p-3 bg-blue-50/80 rounded-xl border border-blue-200/60 text-xs text-blue-900 flex items-start gap-2">
                <i class="ri-information-line text-blue-600 text-sm mt-0.5 shrink-0"></i>
                <p>Teknisi IT BBPOM akan mereset otentikasi ganda (MFA) sehingga Anda dapat memindai ulang barcode autentikator di perangkat baru.</p>
            </div>
        </div>
    </template>

    <!-- E. Lainnya Srikandi -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ srikandiOtherFileName: '' }">
            <div>
                <x-input-label for="srikandi_description" class="font-semibold text-xs text-slate-700">
                    <span>Deskripsi Kendala Srikandi</span> <span class="text-rose-500">*</span>
                </x-input-label>
                <textarea id="srikandi_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Jelaskan kendala Srikandi yang dihadapi secara rinci..." required>{{ old('service_description') }}</textarea>
                <x-input-error class="mt-1" :messages="$errors->get('service_description')" />
            </div>

            <div>
                <x-file-dropzone-uploader 
                    id="dropzone_srikandi" 
                    name="attachments[]" 
                    label="Unggah Foto / Berkas Kendala Srikandi" 
                    helperText="Format didukung: JPG, PNG, PDF, DOCX, ZIP (Maks. 5MB per berkas, dapat memilih beberapa berkas)"
                />
                <x-input-error class="mt-1" :messages="$errors->get('attachments')" />
            </div>
        </div>
    </template>
</div>

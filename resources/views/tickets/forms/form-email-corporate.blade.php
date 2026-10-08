<!-- 3. EMAIL CORPORATE (@bbpom.go.id) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan Email Corporate:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($services['email_corporate']['subcategories'] as $key => $label)
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

    <!-- A. Reset Password Email Corporate -->
    <template x-if="selectedSubcategory === 'reset_password'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="email_nip" :value="__('NIP (Nomor Induk Pegawai)')" class="font-semibold text-xs text-slate-700" />
                <div class="relative mt-1">
                    <x-text-input 
                        id="email_nip" 
                        name="email_nip" 
                        type="text" 
                        class="block w-full rounded-xl text-xs sm:text-sm font-mono pr-28" 
                        value="{{ old('email_nip', $user->nip ?? '') }}" 
                        placeholder="Contoh: 198501012010121001" 
                        required 
                    />
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">
                        Otomatis Akun
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">NIP pegawai digunakan untuk verifikasi pemilik akun email resmi dinas.</p>
            </div>
            <div>
                <x-input-label for="email_new_password" :value="__('Request Password Baru')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="email_new_password" name="email_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="Ketik usulan password baru..." required />
                <p class="text-[11px] text-slate-400 mt-1">Gunakan kombinasi minimal 8 karakter dengan huruf kapital dan angka.</p>
            </div>
        </div>
    </template>

    <!-- B. Lainnya Email Corporate -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ emailOtherFileName: '' }">
            <div>
                <x-input-label for="email_description" class="font-semibold text-xs text-slate-700">
                    <span>Deskripsi Kendala Email</span> <span class="text-rose-500">*</span>
                </x-input-label>
                <textarea id="email_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Jelaskan kendala email (misal: kuota penuh, gagal kirim webmail, integrasi outlook)..." required>{{ old('service_description') }}</textarea>
                <x-input-error class="mt-1" :messages="$errors->get('service_description')" />
            </div>

            <div>
                <x-file-dropzone-uploader 
                    id="dropzone_email_corporate" 
                    name="attachments[]" 
                    label="Unggah Berkas / Tangkapan Layar Kendala Email" 
                    helperText="Format didukung: JPG, PNG, PDF, DOCX, ZIP (Maksimal 10 dokumen hingga 100MB per berkas)"
                />
                <x-input-error class="mt-1" :messages="$errors->get('attachments')" />
            </div>
        </div>
    </template>
</div>

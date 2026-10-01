<!-- 4. SERTIFIKAT BSRE (Balai Sertifikasi Elektronik / TTE) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan BSrE:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            <button 
                type="button" 
                @click="selectedSubCategory = 'reset_password'; selectedSubcategory = 'reset_password'"
                :class="selectedSubCategory === 'reset_password' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-lock-password-line text-amber-500"></i>
                    <span>Reset Password BSrE</span>
                </span>
                <i x-show="selectedSubCategory === 'reset_password'" class="ri-check-line text-sm"></i>
            </button>
            <button 
                type="button" 
                @click="selectedSubCategory = 'permintaan_otp'; selectedSubcategory = 'permintaan_otp'"
                :class="selectedSubCategory === 'permintaan_otp' ? 'bg-emerald-700 text-white font-bold border-emerald-700 shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200 font-medium'"
                class="px-3.5 py-2.5 rounded-xl border text-xs text-left transition flex items-center justify-between gap-2"
            >
                <span class="flex items-center gap-1.5">
                    <i class="ri-shield-keyhole-line text-blue-500"></i>
                    <span>Permintaan OTP</span>
                </span>
                <i x-show="selectedSubCategory === 'permintaan_otp'" class="ri-check-line text-sm"></i>
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

    <!-- A. Reset Password BSrE & B. Permintaan OTP -->
    <div x-show="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'" class="space-y-4 pt-4 border-t border-slate-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="bsre_nip" class="block text-xs font-semibold text-slate-700 mb-1">
                    NIP (Nomor Induk Pegawai) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="bsre_nip" 
                        name="nip" 
                        value="{{ old('nip', $user->nip ?? '') }}" 
                        placeholder="Contoh: 198501012010121001" 
                        maxlength="30" 
                        :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                        :disabled="selectedSubCategory === 'lainnya'"
                        class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono pr-24"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">
                        Otomatis
                    </span>
                </div>
                <x-input-error class="mt-1" :messages="$errors->get('nip')" />
            </div>

            <div>
                <label for="bsre_nik" class="block text-xs font-semibold text-slate-700 mb-1">
                    NIK (Nomor Induk Kependudukan) <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="bsre_nik" 
                    name="nik" 
                    value="{{ old('nik', $user->nik ?? '') }}" 
                    placeholder="Masukkan 16 digit NIK sesuai KTP..." 
                    maxlength="20" 
                    :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                    :disabled="selectedSubCategory === 'lainnya'"
                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
                >
                <x-input-error class="mt-1" :messages="$errors->get('nik')" />
            </div>

            <div>
                <label for="bsre_email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Email <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="bsre_email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}" 
                    placeholder="nama@bbpom.go.id" 
                    :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                    :disabled="selectedSubCategory === 'lainnya'"
                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm"
                >
                <x-input-error class="mt-1" :messages="$errors->get('email')" />
            </div>

            <div>
                <label for="bsre_nomor_telepon" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nomor Telepon <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="tel" 
                    id="bsre_nomor_telepon" 
                    name="nomor_telepon" 
                    value="{{ old('nomor_telepon', $user->phone_number ?? '') }}" 
                    placeholder="Contoh: 081234567890" 
                    :required="selectedSubCategory === 'reset_password' || selectedSubCategory === 'permintaan_otp'"
                    :disabled="selectedSubCategory === 'lainnya'"
                    class="w-full rounded-xl border-slate-300 text-xs sm:text-sm text-slate-900 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-mono"
                >
                <x-input-error class="mt-1" :messages="$errors->get('nomor_telepon')" />
            </div>
        </div>
    </div>

    <!-- C. Lainnya BSrE -->
    <div x-show="selectedSubCategory === 'lainnya'" class="space-y-4 pt-4 border-t border-slate-100" x-data="{ fileName: '' }">
        <div>
            <label for="bsre_deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                Deskripsi <span class="text-rose-500">*</span>
            </label>
            <textarea 
                id="bsre_deskripsi" 
                name="deskripsi" 
                rows="4" 
                :required="selectedSubCategory === 'lainnya'"
                :disabled="selectedSubCategory !== 'lainnya'"
                placeholder="Jelaskan kebutuhan sertifikat elektronik, perpanjangan masa berlaku, atau kendala teknis lainnya..." 
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
                        @change="fileName = $event.target.files[0]?.name"
                        :disabled="selectedSubCategory !== 'lainnya'"
                    >
                </label>
                <span class="text-xs text-slate-500 truncate" x-text="fileName ? 'File: ' + fileName : 'Format: JPG, PNG, PDF, DOCX (Maks 5MB)'"></span>
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('file_lampiran')" />
        </div>
    </div>
</div>

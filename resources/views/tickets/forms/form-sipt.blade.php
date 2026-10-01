<!-- 1. SIPT (Sistem Informasi Pelaporan Terpadu) -->
<div class="space-y-6">
    <!-- Subkategori Selector Pills -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2.5 flex items-center gap-1.5">
            <i class="ri-list-check-2 text-emerald-700"></i>
            <span>Pilih Sub Kategori Layanan SIPT:</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($services['sipt']['subcategories'] as $key => $label)
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

    <!-- A. Reset Password SIPT -->
    <template x-if="selectedSubcategory === 'reset_password'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="sipt_username" :value="__('Username SIPT')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="sipt_username" name="sipt_username" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Masukkan username akun SIPT Anda..." required />
                <p class="text-[11px] text-slate-400 mt-1">Username akun pegawai yang terdaftar pada sistem SIPT BPOM.</p>
            </div>
            <div>
                <x-input-label for="sipt_password" :value="__('Request Password Baru')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="sipt_password" name="sipt_password" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="Masukkan usulan password baru..." required />
                <p class="text-[11px] text-slate-400 mt-1">Kombinasi minimal 8 karakter huruf dan angka disarankan.</p>
            </div>
        </div>
    </template>

    <!-- B. Rollback Sampel SIPT -->
    <template x-if="selectedSubcategory === 'rollback_sampel'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="sipt_sample_number" :value="__('Nomor Sampel')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="sipt_sample_number" name="sipt_sample_number" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="Contoh: SMP-2026-X123..." required />
            </div>
            <div>
                <x-input-label for="sipt_commodity_rollback" :value="__('Komoditi')" class="font-semibold text-xs text-slate-700" />
                <select id="sipt_commodity_rollback" name="sipt_commodity" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" required>
                    <option value="">-- Pilih Komoditi --</option>
                    <option value="Pangan">Pangan</option>
                    <option value="Obat">Obat</option>
                    <option value="Obat Tradisional">Obat Tradisional</option>
                    <option value="Kosmetik">Kosmetik</option>
                    <option value="Suplemen Kesehatan">Suplemen Kesehatan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
        </div>
    </template>

    <!-- C. Perbaikan Data Sampel SIPT -->
    <template x-if="selectedSubcategory === 'perbaikan_sampel'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="sipt_sample_code_repair" :value="__('Kode Sampel')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="sipt_sample_code_repair" name="sipt_sample_code" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="Kode sampel..." required />
                </div>
                <div>
                    <x-input-label for="sipt_commodity_repair" :value="__('Komoditi')" class="font-semibold text-xs text-slate-700" />
                    <select id="sipt_commodity_repair" name="sipt_commodity" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" required>
                        <option value="">-- Pilih Komoditi --</option>
                        <option value="Pangan">Pangan</option>
                        <option value="Obat">Obat</option>
                        <option value="Obat Tradisional">Obat Tradisional</option>
                        <option value="Kosmetik">Kosmetik</option>
                        <option value="Suplemen Kesehatan">Suplemen Kesehatan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div>
                <x-input-label for="sipt_sample_name_repair" :value="__('Nama Sampel')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="sipt_sample_name_repair" name="sipt_sample_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Nama produk / sampel..." required />
            </div>
            <div>
                <x-input-label for="sipt_before_repair" :value="__('Sebelum Perbaikan')" class="font-semibold text-xs text-slate-700" />
                <textarea id="sipt_before_repair" name="sipt_before_repair" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Tuliskan data sebelum perbaikan (data lama)..." required></textarea>
            </div>
            <div>
                <x-input-label for="sipt_after_repair" :value="__('Perbaikan')" class="font-semibold text-xs text-slate-700" />
                <textarea id="sipt_after_repair" name="sipt_after_repair" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Tuliskan data perbaikan yang benar (data baru)..." required></textarea>
            </div>
        </div>
    </template>

    <!-- D. Cek Posisi Sampel SIPT -->
    <template x-if="selectedSubcategory === 'cek_posisi_sampel'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="sipt_sample_code_track" :value="__('Kode Sampel')" class="font-semibold text-xs text-slate-700" />
                    <x-text-input id="sipt_sample_code_track" name="sipt_sample_code" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono" placeholder="Kode sampel yang dilacak..." required />
                </div>
                <div>
                    <x-input-label for="sipt_commodity_track" :value="__('Komoditi')" class="font-semibold text-xs text-slate-700" />
                    <select id="sipt_commodity_track" name="sipt_commodity" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" required>
                        <option value="">-- Pilih Komoditi --</option>
                        <option value="Pangan">Pangan</option>
                        <option value="Obat">Obat</option>
                        <option value="Obat Tradisional">Obat Tradisional</option>
                        <option value="Kosmetik">Kosmetik</option>
                        <option value="Suplemen Kesehatan">Suplemen Kesehatan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div>
                <x-input-label for="sipt_sample_name_track" :value="__('Nama Sampel')" class="font-semibold text-xs text-slate-700" />
                <x-text-input id="sipt_sample_name_track" name="sipt_sample_name" type="text" class="mt-1 block w-full rounded-xl text-xs sm:text-sm" placeholder="Nama sampel produk..." required />
            </div>
        </div>
    </template>

    <!-- E. Lainnya SIPT -->
    <template x-if="selectedSubcategory === 'lainnya'">
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div>
                <x-input-label for="sipt_description" :value="__('Deskripsi Kendala SIPT')" class="font-semibold text-xs text-slate-700" />
                <textarea id="sipt_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm" placeholder="Jelaskan kendala SIPT yang Anda alami..." required></textarea>
            </div>
        </div>
    </template>
</div>

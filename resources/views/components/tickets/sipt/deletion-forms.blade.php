<!-- E. Hapus Sampel SIPT -->
<template x-if="selectedSubcategory === 'hapus_sampel'">
    <div class="space-y-4 pt-4 border-t border-slate-100">
        <x-tickets.sipt.destructive-notice
            title="Konfirmasi Penghapusan Data Sampel SIPT"
            message="Pastikan Nomor Sampel dan Komoditi telah diperiksa dan sesuai dengan data di aplikasi SIPT sebelum mengirimkan permohonan penghapusan."
        />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="sipt_sample_number_delete" class="font-semibold text-xs text-slate-700">
                    <span>No. Sampel</span> <span class="text-rose-600">*</span>
                </x-input-label>
                <x-text-input
                    id="sipt_sample_number_delete"
                    name="sipt_sample_number"
                    type="text"
                    :value="old('sipt_sample_number')"
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono"
                    placeholder="Contoh: SMP-2026-X123..."
                    required
                />
                <p class="text-[11px] text-slate-500 mt-1">Masukkan nomor sampel lengkap sesuai tercatat pada SIPT.</p>
            </div>

            <x-tickets.sipt.commodity-select
                id="sipt_commodity_delete"
                helperText="Pilih kelompok komoditi sesuai data sampel yang akan dihapus."
            />
        </div>
    </div>
</template>

<!-- F. Hapus Sarana SIPT -->
<template x-if="selectedSubcategory === 'hapus_sarana'">
    <div class="space-y-4 pt-4 border-t border-slate-100">
        <x-tickets.sipt.destructive-notice
            title="Konfirmasi Penghapusan Data Sarana SIPT"
            message="Pastikan No. ID Sarana dan Nama Sarana sudah diverifikasi pada aplikasi SIPT agar penghapusan data tepat sasaran."
        />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="sipt_facility_id" class="font-semibold text-xs text-slate-700">
                    <span>No. ID Sarana</span> <span class="text-rose-600">*</span>
                </x-input-label>
                <x-text-input
                    id="sipt_facility_id"
                    name="sipt_facility_id"
                    type="text"
                    :value="old('sipt_facility_id')"
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm font-mono"
                    placeholder="Contoh: SRN-3374-00123..."
                    required
                />
                <p class="text-[11px] text-slate-500 mt-1">Nomor identitas sarana terdaftar pada aplikasi SIPT.</p>
            </div>

            <div>
                <x-input-label for="sipt_facility_name" class="font-semibold text-xs text-slate-700">
                    <span>Nama Sarana</span> <span class="text-rose-600">*</span>
                </x-input-label>
                <x-text-input
                    id="sipt_facility_name"
                    name="sipt_facility_name"
                    type="text"
                    :value="old('sipt_facility_name')"
                    class="mt-1 block w-full rounded-xl text-xs sm:text-sm"
                    placeholder="Masukkan nama lengkap sarana..."
                    required
                />
                <p class="text-[11px] text-slate-500 mt-1">Nama sarana sesuai data yang tercatat pada SIPT.</p>
            </div>
        </div>
    </div>
</template>

<!-- G. Lainnya SIPT -->
<template x-if="selectedSubcategory === 'lainnya'">
    <div class="space-y-4 pt-4 border-t border-slate-100" x-data="{ siptOtherFileName: '' }">
        <div>
            <x-input-label for="sipt_description" class="font-semibold text-xs text-slate-700">
                <span>Deskripsi Kendala SIPT</span> <span class="text-rose-600">*</span>
            </x-input-label>
            <textarea id="sipt_description" name="service_description" rows="4" class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Jelaskan kendala SIPT yang Anda alami..." required>{{ old('service_description') }}</textarea>
            <x-input-error class="mt-1" :messages="$errors->get('service_description')" />
        </div>

        <div>
            <x-file-dropzone-uploader
                id="dropzone_sipt"
                name="attachments[]"
                label="Unggah Foto / Berkas Kendala SIPT"
                helperText="Format didukung: JPG, PNG, PDF, DOCX, ZIP (Maks. 5MB per berkas, dapat memilih beberapa berkas)"
            />
            <x-input-error class="mt-1" :messages="$errors->get('attachments')" />
        </div>
    </div>
</template>

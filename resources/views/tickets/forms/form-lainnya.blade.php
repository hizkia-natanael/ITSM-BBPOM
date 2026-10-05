<!-- 12. LAYANAN LAINNYA (UMUM) -->
<div class="space-y-6">
    <div class="space-y-4">
        <div>
            <x-input-label for="lainnya_deskripsi" :value="__('Deskripsi Permintaan / Kendala TIK')" class="font-semibold text-xs text-slate-700" />
            <textarea 
                id="lainnya_deskripsi" 
                name="deskripsi" 
                rows="5" 
                class="mt-1 block w-full rounded-xl border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-emerald-600" 
                placeholder="Jelaskan kebutuhan dukungan teknis TIK, kendala printer, scanner, proyektor, audio ruang rapat, atau permohonan TI lainnya..." 
                required
            >{{ old('deskripsi') }}</textarea>
            <p class="text-[11px] text-slate-400 mt-1">Berikan informasi selengkap mungkin mengenai kebutuhan perangkat atau kendala yang dihadapi.</p>
        </div>

        <div>
            <x-file-dropzone-uploader 
                id="dropzone_lainnya" 
                name="attachments[]" 
                label="Unggah Foto / Dokumen Pendukung" 
                helperText="Format didukung: JPG, PNG, PDF, DOCX, ZIP, TXT (Maks. 5MB per berkas, dapat memilih beberapa berkas)"
            />
        </div>
    </div>
</div>

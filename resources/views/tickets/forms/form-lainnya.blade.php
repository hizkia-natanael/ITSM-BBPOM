<!-- 12. LAYANAN LAINNYA (UMUM) -->
<div class="space-y-6" x-data="{ lainnyaFileName: '' }">
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
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                Upload File Pendukung <span class="text-slate-400 font-normal">(Opsional)</span>
            </label>
            <div class="flex items-center gap-3 p-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 hover:bg-white transition">
                <label class="cursor-pointer px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-sm shrink-0">
                    <span>Pilih Berkas</span>
                    <input 
                        type="file" 
                        name="attachment" 
                        class="sr-only"
                        @change="lainnyaFileName = $event.target.files[0]?.name"
                    >
                </label>
                <span class="text-xs text-slate-500 truncate" x-text="lainnyaFileName ? 'File: ' + lainnyaFileName : 'Format: JPG, PNG, PDF, DOCX, ZIP (Maks 5MB)'"></span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Unggah foto kendala perangkat atau dokumen pendukung permohonan jika ada.</p>
        </div>
    </div>
</div>

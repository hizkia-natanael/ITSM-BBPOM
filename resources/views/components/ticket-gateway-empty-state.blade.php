<div x-show="isEmptyCatalog" class="text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3">
        <i class="ri-error-warning-line text-2xl"></i>
    </div>
    <h4 class="font-bold text-slate-800 text-base">Layanan Tidak Ditemukan</h4>
    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        Tidak ada layanan yang cocok dengan kata kunci "<span class="font-semibold text-slate-700" x-text="search"></span>". Anda dapat memilih menu <strong>Lainnya</strong> untuk kebutuhan TI umum.
    </p>
    <div class="mt-4 flex justify-center gap-2">
        <button type="button" @click="search = ''; activeGroup = 'all'" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
            Reset Pencarian
        </button>
        <button type="button" @click="selectService('lainnya')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-700 text-white hover:bg-emerald-800 transition">
            Pilih Menu "Lainnya"
        </button>
    </div>
</div>

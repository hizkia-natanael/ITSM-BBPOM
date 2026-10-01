<!-- Auto-filled Metadata Header (Sesuai Spesifikasi: Tanggal & Nama Otomatis) -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-700 shrink-0">
            <i class="ri-calendar-line text-lg"></i>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Tanggal Pengajuan
            </label>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-900 mt-0.5">
                <span>{{ $todayFormatted }}</span>
                <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                    Otomatis
                </span>
            </div>
            <input type="hidden" name="tanggal_pengajuan" value="{{ $todayDate }}">
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-700 shrink-0">
            <i class="ri-user-line text-lg"></i>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                Nama Pemohon
            </label>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-900 mt-0.5">
                <span class="truncate max-w-[180px] sm:max-w-xs" title="{{ $user->name }}">{{ $user->name }}</span>
                <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60">
                    Akun Login
                </span>
            </div>
            <input type="hidden" name="nama" value="{{ old('nama', $user->name) }}">
        </div>
    </div>
</div>

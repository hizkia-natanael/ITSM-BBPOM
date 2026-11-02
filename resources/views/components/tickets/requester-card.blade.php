@props(['user'])

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
            <i class="ri-user-shared-line text-emerald-700"></i>
            Identitas Pemohon
        </h4>
        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
            Pegawai
        </span>
    </div>

    <div class="flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 text-white font-bold text-base flex items-center justify-center shrink-0 shadow-xs">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="overflow-hidden">
            <h5 class="font-bold text-sm text-slate-900 truncate" title="{{ $user->name }}">
                {{ $user->name }}
            </h5>
            <p class="text-xs text-slate-500 truncate mt-0.5" title="{{ $user->email }}">
                {{ $user->email }}
            </p>
            <span class="inline-block mt-1 text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                BBPOM di Semarang
            </span>
        </div>
    </div>
</div>

@props(['ticket'])

@if($ticket->canBeRatedByUser(auth()->user()))
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 text-white shadow-lg border border-emerald-500/30 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-xs flex items-center justify-center text-amber-300 border border-white/20 shrink-0 shadow-xs">
                <i class="ri-star-smile-fill text-2xl"></i>
            </div>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-700/60 border border-emerald-400/30 text-[10px] font-bold uppercase tracking-wider text-emerald-200 mb-1">
                    <i class="ri-shield-check-fill text-emerald-300"></i>
                    <span>Aduan Berhasil Diselesaikan</span>
                </div>
                <h3 class="text-sm sm:text-base font-extrabold tracking-tight text-white">
                    Bagaimana Pengalaman Anda dengan Layanan TI Ini?
                </h3>
                <p class="text-xs text-emerald-100/90 mt-0.5 max-w-xl">
                    Partisipasi Anda sangat berarti untuk evaluasi dan peningkatan mutu pelayanan teknologi informasi BBPOM di Semarang.
                </p>
            </div>
        </div>

        <div class="shrink-0 self-start md:self-center">
            <button
                type="button"
                @click="$dispatch('open-survey-modal')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95"
            >
                <i class="ri-star-fill text-slate-950"></i>
                <span>Beri Penilaian (1-5 Bintang)</span>
            </button>
        </div>
    </div>

    <!-- Active Survey Modal -->
    <x-tickets.survey.survey-modal :ticket="$ticket" />
@endif

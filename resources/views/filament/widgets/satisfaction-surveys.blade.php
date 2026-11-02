<x-filament-widgets::widget>
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 sm:p-6 space-y-5">
        <!-- Widget Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center justify-center text-lg shadow-2xs">
                    <i class="ri-star-smile-fill"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                        Evaluasi &amp; Ulasan Kepuasan Layanan TI
                    </h3>
                    <p class="text-xs text-slate-500">
                        Hasil survei kepuasan pelanggan dan audit mutu pelayanan helpdesk ELITE BBPOM di Semarang
                    </p>
                </div>
            </div>

            <div class="shrink-0 self-start sm:self-auto">
                <a
                    href="{{ \App\Filament\Resources\SatisfactionSurveys\SatisfactionSurveyResource::getUrl('index') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-2xs"
                >
                    <span>Lihat Semua Ulasan</span>
                    <i class="ri-arrow-right-line text-xs"></i>
                </a>
            </div>
        </div>

        <!-- 2-Column Split Body -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            <div class="lg:col-span-5">
                @include('filament.widgets.satisfaction.satisfaction-summary-card', ['metrics' => $this->getMetrics()])
            </div>
            <div class="lg:col-span-7">
                @include('filament.widgets.satisfaction.satisfaction-recent-feed', ['surveys' => $this->getRecentSurveys()])
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

<x-filament-widgets::widget>
    @include('filament.widgets.satisfaction.satisfaction-styles')

    <div class="bbpom-survey-container">
        <div class="bbpom-survey-header">
            <div class="bbpom-survey-header-left">
                <div class="bbpom-survey-icon-box">
                    <i class="ri-star-smile-fill"></i>
                </div>
                <div>
                    <h3 class="bbpom-survey-title">
                        Evaluasi &amp; Ulasan Kepuasan Layanan TI
                    </h3>
                    <p class="bbpom-survey-subtitle">
                        Hasil survei kepuasan pelanggan dan audit mutu pelayanan helpdesk ELITE BBPOM di Semarang
                    </p>
                </div>
            </div>

            <div>
                <a
                    href="{{ \App\Filament\Resources\SatisfactionSurveys\SatisfactionSurveyResource::getUrl('index') }}"
                    class="bbpom-survey-view-all-btn"
                >
                    <span>Lihat Semua Ulasan</span>
                    <i class="ri-arrow-right-line" style="font-size: 0.75rem;"></i>
                </a>
            </div>
        </div>

        <div class="bbpom-survey-grid">
            <div>
                @include('filament.widgets.satisfaction.satisfaction-summary-card', ['metrics' => $this->getMetrics()])
            </div>
            <div>
                @include('filament.widgets.satisfaction.satisfaction-recent-feed', ['surveys' => $this->getRecentSurveys()])
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

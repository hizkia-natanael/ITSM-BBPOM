@php
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Filament\Widgets\View\Components\ChartWidgetComponent;
    use Illuminate\Contracts\Support\Htmlable;

    $color = $this->getColor();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $isCollapsible = $this->isCollapsible();
    $type = $this->getType();
    $maxHeight = $this->getMaxHeight();
    $hasMaxHeight = filled($maxHeight) && $maxHeight !== '100%';
    $isEmpty = $this->isEmpty();
    $statusCounts = $this->getStatusCounts();
    $currentFilter = $this->filter ?? 'all';
    $currentPeriod = $this->period ?? 'bulan';

    $chartAccessibleLabel = trim(implode('. ', array_filter([
        $heading instanceof Htmlable ? strip_tags($heading->toHtml()) : $heading,
        $description instanceof Htmlable ? strip_tags($description->toHtml()) : $description,
    ], fn ($value): bool => filled($value))));
@endphp

<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section
        :description="$description"
        :heading="$heading"
        :collapsible="$isCollapsible"
    >
        {{-- Toolbar 1: Filter Periode Waktu (Per Bulan, Per Tahun, Rentang Tertentu) - Hanya Onclick --}}
        <div style="display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.65rem; padding-bottom: 0.65rem; border-bottom: 1px solid #f1f5f9;">
            {{-- Segmented Controls Waktu --}}
            <div style="display: inline-flex; align-items: center; gap: 0.25rem; background-color: #f1f5f9; padding: 0.2rem; border-radius: 9999px; border: 1px solid #e2e8f0;">
                {{-- Tombol: Per Bulan --}}
                <button
                    type="button"
                    wire:click="setPeriod('bulan')"
                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentPeriod === 'bulan' ? 'background-color: #059669; color: #ffffff; box-shadow: 0 1px 2px rgba(5,150,105,0.25); border: 1px solid #059669;' : 'background-color: transparent; color: #64748b; border: 1px solid transparent;' }}"
                    title="Klik untuk melihat grafik bulanan"
                >
                    <i class="ri-calendar-line" style="font-size: 0.8125rem;"></i>
                    <span>Per Bulan</span>
                </button>

                {{-- Tombol: Per Tahun --}}
                <button
                    type="button"
                    wire:click="setPeriod('tahun')"
                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentPeriod === 'tahun' ? 'background-color: #059669; color: #ffffff; box-shadow: 0 1px 2px rgba(5,150,105,0.25); border: 1px solid #059669;' : 'background-color: transparent; color: #64748b; border: 1px solid transparent;' }}"
                    title="Klik untuk melihat grafik tahunan"
                >
                    <i class="ri-calendar-2-line" style="font-size: 0.8125rem;"></i>
                    <span>Per Tahun</span>
                </button>

                {{-- Tombol: Rentang Waktu --}}
                <button
                    type="button"
                    wire:click="setPeriod('custom')"
                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentPeriod === 'custom' ? 'background-color: #059669; color: #ffffff; box-shadow: 0 1px 2px rgba(5,150,105,0.25); border: 1px solid #059669;' : 'background-color: transparent; color: #64748b; border: 1px solid transparent;' }}"
                    title="Klik untuk menentukan rentang tanggal kustom"
                >
                    <i class="ri-calendar-event-line" style="font-size: 0.8125rem;"></i>
                    <span>Rentang Waktu</span>
                </button>
            </div>

            {{-- Input Rentang Waktu Kustom (Aktif saat mode 'custom') --}}
            @if ($currentPeriod === 'custom')
                <div style="display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; gap: 0.4rem; background-color: #ffffff; padding: 0.3rem 0.65rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                        <span style="font-size: 0.6875rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Dari:</span>
                        <input
                            type="date"
                            wire:model.defer="startDate"
                            style="border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.2rem 0.45rem; font-size: 0.75rem; font-weight: 500; color: #1e293b; background-color: #f8fafc; outline: none;"
                        />
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                        <span style="font-size: 0.6875rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Sampai:</span>
                        <input
                            type="date"
                            wire:model.defer="endDate"
                            style="border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.2rem 0.45rem; font-size: 0.75rem; font-weight: 500; color: #1e293b; background-color: #f8fafc; outline: none;"
                        />
                    </div>
                    <button
                        type="button"
                        wire:click="applyCustomRange"
                        style="display: inline-flex; align-items: center; gap: 0.25rem; background-color: #0f172a; color: #ffffff; border: none; border-radius: 0.375rem; padding: 0.25rem 0.6rem; font-size: 0.7125rem; font-weight: 600; cursor: pointer; transition: background-color 0.15s ease;"
                        title="Terapkan rentang tanggal"
                    >
                        <i class="ri-check-line" style="font-size: 0.8125rem;"></i>
                        <span>Terapkan</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Toolbar 2: Filter Status Tiket - Hover & Click Beranimasi Halus Tanpa Reload (Option 2 Minimalist Border-Only) --}}
        <div
            x-data="bbpomTrendStatusController('{{ $currentFilter }}')"
            class="bbpom-status-chips-wrap"
        >
            {{-- Tombol: Semua Status --}}
            <button
                type="button"
                @mouseenter.debounce.40ms="onHover('all')"
                @click="onClick('all')"
                class="bbpom-status-chip bbpom-chip-all"
                :class="{ 'is-active': activeFilter === 'all' }"
                title="Arahkan kursor atau klik untuk menampilkan semua status aduan"
            >
                <i class="ri-apps-2-line" style="font-size: 0.8125rem;"></i>
                <span>Semua Status</span>
                <span class="chip-count">{{ $statusCounts['total'] }}</span>
            </button>

            {{-- Tombol: Pending --}}
            <button
                type="button"
                @mouseenter.debounce.40ms="onHover('pending')"
                @click="onClick('pending')"
                class="bbpom-status-chip bbpom-chip-pending"
                :class="{ 'is-active': activeFilter === 'pending' }"
                title="Arahkan kursor atau klik untuk melihat tiket Pending. Klik kembali untuk reset."
            >
                <span class="chip-dot"></span>
                <i class="ri-time-line" style="font-size: 0.8125rem;"></i>
                <span>Pending</span>
                <span class="chip-count">{{ $statusCounts['pending'] }}</span>
            </button>

            {{-- Tombol: Diproses --}}
            <button
                type="button"
                @mouseenter.debounce.40ms="onHover('diproses')"
                @click="onClick('diproses')"
                class="bbpom-status-chip bbpom-chip-diproses"
                :class="{ 'is-active': activeFilter === 'diproses' }"
                title="Arahkan kursor atau klik untuk melihat tiket Diproses. Klik kembali untuk reset."
            >
                <span class="chip-dot"></span>
                <i class="ri-tools-line" style="font-size: 0.8125rem;"></i>
                <span>Diproses</span>
                <span class="chip-count">{{ $statusCounts['diproses'] }}</span>
            </button>

            {{-- Tombol: Selesai --}}
            <button
                type="button"
                @mouseenter.debounce.40ms="onHover('selesai')"
                @click="onClick('selesai')"
                class="bbpom-status-chip bbpom-chip-selesai"
                :class="{ 'is-active': activeFilter === 'selesai' }"
                title="Arahkan kursor atau klik untuk melihat tiket Selesai. Klik kembali untuk reset."
            >
                <span class="chip-dot"></span>
                <i class="ri-checkbox-circle-line" style="font-size: 0.8125rem;"></i>
                <span>Selesai</span>
                <span class="chip-count">{{ $statusCounts['selesai'] }}</span>
            </button>

            {{-- Tombol: Ditolak --}}
            <button
                type="button"
                @mouseenter.debounce.40ms="onHover('ditolak')"
                @click="onClick('ditolak')"
                class="bbpom-status-chip bbpom-chip-ditolak"
                :class="{ 'is-active': activeFilter === 'ditolak' }"
                title="Arahkan kursor atau klik untuk melihat tiket Ditolak. Klik kembali untuk reset."
            >
                <span class="chip-dot"></span>
                <i class="ri-close-circle-line" style="font-size: 0.8125rem;"></i>
                <span>Ditolak</span>
                <span class="chip-count">{{ $statusCounts['ditolak'] }}</span>
            </button>
        </div>

        {{-- Area Canvas Grafik Bawaan Filament --}}
        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}="updateChartData"
            @endif
            @if ($isEmpty)
                style="display: none"
            @endif
        >
            <div
                x-load
                x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                data-chart-type="{{ $type }}"
                x-data="chart({
                            cachedData: @js($this->getCachedData()),
                            options: @js($this->getOptions()),
                            type: @js($type),
                        })"
                {{
                    (new FilamentComponentAttributeBag)
                        ->color(ChartWidgetComponent::class, $color)
                        ->class([
                            'fi-wi-chart-frame',
                            'fi-wi-chart-canvas-ctn',
                            'fi-wi-chart-frame-no-aspect-ratio' => $hasMaxHeight,
                        ])
                }}
            >
                <canvas
                    x-ref="canvas"
                    @if (filled($chartAccessibleLabel))
                        role="img"
                        aria-label="{{ $chartAccessibleLabel }}"
                    @endif
                    @style([
                        'width: 100%',
                        'height: 100%; max-height: 100%' => ! $hasMaxHeight,
                        ('max-height: ' . e($maxHeight)) => $hasMaxHeight,
                    ])
                ></canvas>

                <span
                    aria-hidden="true"
                    x-ref="backgroundColorElement"
                    class="fi-wi-chart-bg-color"
                ></span>

                <span
                    aria-hidden="true"
                    x-ref="borderColorElement"
                    class="fi-wi-chart-border-color"
                ></span>

                <span
                    aria-hidden="true"
                    x-ref="gridColorElement"
                    class="fi-wi-chart-grid-color"
                ></span>

                <span
                    aria-hidden="true"
                    x-ref="textColorElement"
                    class="fi-wi-chart-text-color"
                ></span>

                <span
                    aria-hidden="true"
                    x-ref="tooltipBackgroundColorElement"
                    class="fi-wi-chart-tooltip-bg-color"
                ></span>

                <span
                    aria-hidden="true"
                    x-ref="tooltipTextColorElement"
                    class="fi-wi-chart-tooltip-text-color"
                ></span>

                <span
                    aria-hidden="true"
                    x-ref="tooltipBorderColorElement"
                    class="fi-wi-chart-tooltip-border-color"
                ></span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<script>
function bbpomTrendStatusController(initialFilter) {
    return {
        activeFilter: initialFilter || 'all',
        getChart() {
            if (window.__bbpomChart) return window.__bbpomChart;
            const canvas = document.querySelector('.fi-wi-chart canvas');
            if (canvas && canvas.__bbpomChart) return canvas.__bbpomChart;
            if (typeof Chart !== 'undefined' && Chart.getChart && canvas) {
                return Chart.getChart(canvas);
            }
            return null;
        },
        onHover(status) {
            if (this.activeFilter === status) return;
            this.activeFilter = status;
            this.applyToChart(status);
        },
        onClick(status) {
            if (this.activeFilter === status && status !== 'all') {
                this.activeFilter = 'all';
                this.applyToChart('all');
            } else {
                this.activeFilter = status;
                this.applyToChart(status);
            }
        },
        applyToChart(status) {
            const chart = this.getChart();
            if (!chart) return;
            const map = { 'pending': 0, 'diproses': 1, 'selesai': 2, 'ditolak': 3 };
            if (status === 'all') {
                for (let i = 0; i < 4; i++) {
                    chart.setDatasetVisibility(i, true);
                }
            } else if (map[status] !== undefined) {
                for (let i = 0; i < 4; i++) {
                    chart.setDatasetVisibility(i, i === map[status]);
                }
            }
            chart.update();
        }
    };
}
window.bbpomTrendStatusController = bbpomTrendStatusController;
</script>

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
        {{-- Baris 1: Filter Periode Waktu (Per Bulan, Per Tahun, Rentang Tertentu) --}}
        <div style="display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 0.875rem; border-bottom: 1px solid #f1f5f9;">
            {{-- Segmented Controls Waktu --}}
            <div style="display: inline-flex; align-items: center; gap: 0.35rem; background-color: #f8fafc; padding: 0.25rem; border-radius: 9999px; border: 1px solid #e2e8f0;">
                {{-- Tombol: Per Bulan --}}
                <button
                    type="button"
                    wire:click="setPeriod('bulan')"
                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentPeriod === 'bulan' ? 'background-color: #059669; color: #ffffff; box-shadow: 0 1px 3px rgba(5,150,105,0.3); border: 1px solid #059669;' : 'background-color: transparent; color: #64748b; border: 1px solid transparent;' }}"
                    title="Tampilkan grafik tren bulanan"
                >
                    <i class="ri-calendar-line" style="font-size: 0.875rem;"></i>
                    <span>Per Bulan</span>
                </button>

                {{-- Tombol: Per Tahun --}}
                <button
                    type="button"
                    wire:click="setPeriod('tahun')"
                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentPeriod === 'tahun' ? 'background-color: #059669; color: #ffffff; box-shadow: 0 1px 3px rgba(5,150,105,0.3); border: 1px solid #059669;' : 'background-color: transparent; color: #64748b; border: 1px solid transparent;' }}"
                    title="Tampilkan grafik tren tahunan"
                >
                    <i class="ri-calendar-2-line" style="font-size: 0.875rem;"></i>
                    <span>Per Tahun</span>
                </button>

                {{-- Tombol: Rentang Waktu --}}
                <button
                    type="button"
                    wire:click="setPeriod('custom')"
                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentPeriod === 'custom' ? 'background-color: #059669; color: #ffffff; box-shadow: 0 1px 3px rgba(5,150,105,0.3); border: 1px solid #059669;' : 'background-color: transparent; color: #64748b; border: 1px solid transparent;' }}"
                    title="Atur rentang tanggal kustom"
                >
                    <i class="ri-calendar-event-line" style="font-size: 0.875rem;"></i>
                    <span>Rentang Waktu</span>
                </button>
            </div>

            {{-- Drawer Input Rentang Waktu Kustom (Aktif saat mode 'custom') --}}
            @if ($currentPeriod === 'custom')
                <div style="display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; gap: 0.5rem; background-color: #ffffff; padding: 0.35rem 0.75rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Dari:</span>
                        <input
                            type="date"
                            wire:model.defer="startDate"
                            style="border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 500; color: #1e293b; background-color: #f8fafc; outline: none;"
                        />
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Sampai:</span>
                        <input
                            type="date"
                            wire:model.defer="endDate"
                            style="border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 500; color: #1e293b; background-color: #f8fafc; outline: none;"
                        />
                    </div>
                    <button
                        type="button"
                        wire:click="applyCustomRange"
                        style="display: inline-flex; align-items: center; gap: 0.25rem; background-color: #0f172a; color: #ffffff; border: none; border-radius: 0.5rem; padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: background-color 0.15s ease;"
                        title="Terapkan rentang tanggal"
                    >
                        <i class="ri-check-line" style="font-size: 0.875rem;"></i>
                        <span>Terapkan</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Baris 2: Filter Status Tiket Sejajar Horizontal & Berwarna --}}
        <div style="display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; padding-bottom: 0.875rem; border-bottom: 1px solid #f1f5f9;">
            {{-- Tombol: Semua Status --}}
            <button
                type="button"
                wire:click="selectFilter('all')"
                style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentFilter === 'all' ? 'background-color: #0f172a; color: #ffffff; border: 1px solid #0f172a; box-shadow: 0 1px 3px rgba(15,23,42,0.25);' : 'background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0;' }}"
                title="Tampilkan semua status tiket"
            >
                <i class="ri-apps-2-line" style="font-size: 0.875rem;"></i>
                <span>Semua Status</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.4rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; {{ $currentFilter === 'all' ? 'background-color: rgba(255,255,255,0.2); color: #ffffff;' : 'background-color: #e2e8f0; color: #475569;' }}">
                    {{ $statusCounts['total'] }}
                </span>
            </button>

            {{-- Tombol: Pending --}}
            <button
                type="button"
                wire:click="selectFilter('pending')"
                style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentFilter === 'pending' ? 'background-color: #d97706; color: #ffffff; border: 1px solid #b45309; box-shadow: 0 2px 4px rgba(217,119,6,0.3);' : 'background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a;' }}"
                title="Klik untuk melihat hanya tiket Pending. Klik kembali untuk reset."
            >
                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 9999px; {{ $currentFilter === 'pending' ? 'background-color: #ffffff;' : 'background-color: #d97706;' }}"></span>
                <i class="ri-time-line" style="font-size: 0.875rem;"></i>
                <span>Pending</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.4rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; {{ $currentFilter === 'pending' ? 'background-color: rgba(255,255,255,0.25); color: #ffffff;' : 'background-color: #fef3c7; color: #b45309;' }}">
                    {{ $statusCounts['pending'] }}
                </span>
            </button>

            {{-- Tombol: Diproses --}}
            <button
                type="button"
                wire:click="selectFilter('diproses')"
                style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentFilter === 'diproses' ? 'background-color: #2563eb; color: #ffffff; border: 1px solid #1d4ed8; box-shadow: 0 2px 4px rgba(37,99,235,0.3);' : 'background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;' }}"
                title="Klik untuk melihat hanya tiket Diproses. Klik kembali untuk reset."
            >
                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 9999px; {{ $currentFilter === 'diproses' ? 'background-color: #ffffff;' : 'background-color: #2563eb;' }}"></span>
                <i class="ri-tools-line" style="font-size: 0.875rem;"></i>
                <span>Diproses</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.4rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; {{ $currentFilter === 'diproses' ? 'background-color: rgba(255,255,255,0.25); color: #ffffff;' : 'background-color: #dbeafe; color: #1d4ed8;' }}">
                    {{ $statusCounts['diproses'] }}
                </span>
            </button>

            {{-- Tombol: Selesai --}}
            <button
                type="button"
                wire:click="selectFilter('selesai')"
                style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentFilter === 'selesai' ? 'background-color: #059669; color: #ffffff; border: 1px solid #047857; box-shadow: 0 2px 4px rgba(5,150,105,0.3);' : 'background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;' }}"
                title="Klik untuk melihat hanya tiket Selesai. Klik kembali untuk reset."
            >
                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 9999px; {{ $currentFilter === 'selesai' ? 'background-color: #ffffff;' : 'background-color: #059669;' }}"></span>
                <i class="ri-checkbox-circle-line" style="font-size: 0.875rem;"></i>
                <span>Selesai</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.4rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; {{ $currentFilter === 'selesai' ? 'background-color: rgba(255,255,255,0.25); color: #ffffff;' : 'background-color: #d1fae5; color: #047857;' }}">
                    {{ $statusCounts['selesai'] }}
                </span>
            </button>

            {{-- Tombol: Ditolak --}}
            <button
                type="button"
                wire:click="selectFilter('ditolak')"
                style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.9rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; {{ $currentFilter === 'ditolak' ? 'background-color: #e11d48; color: #ffffff; border: 1px solid #be123c; box-shadow: 0 2px 4px rgba(225,29,72,0.3);' : 'background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3;' }}"
                title="Klik untuk melihat hanya tiket Ditolak. Klik kembali untuk reset."
            >
                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 9999px; {{ $currentFilter === 'ditolak' ? 'background-color: #ffffff;' : 'background-color: #e11d48;' }}"></span>
                <i class="ri-close-circle-line" style="font-size: 0.875rem;"></i>
                <span>Ditolak</span>
                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 0.125rem 0.4rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; {{ $currentFilter === 'ditolak' ? 'background-color: rgba(255,255,255,0.25); color: #ffffff;' : 'background-color: #ffe4e6; color: #be123c;' }}">
                    {{ $statusCounts['ditolak'] }}
                </span>
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

@props(['metrics'])

<div class="bbpom-survey-summary-card">
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
            <span class="bbpom-survey-badge-kpi">
                <i class="ri-shield-star-line" style="font-size: 0.75rem;"></i>
                <span>Indeks Kepuasan Pengguna</span>
            </span>
            <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 600;">Skala 1 - 5</span>
        </div>

        <div class="bbpom-survey-score-display">
            <span class="bbpom-survey-score-value">
                {{ number_format($metrics['score'], 1, '.', '') }}
            </span>
            <span class="bbpom-survey-score-max">/ 5.0</span>
        </div>

        <div class="bbpom-survey-stars-row">
            @for ($i = 1; $i <= 5; $i++)
                <i class="ri-star-fill {{ $i <= round($metrics['score']) ? 'bbpom-survey-star-active' : 'bbpom-survey-star-inactive' }}"></i>
            @endfor
            <span class="bbpom-survey-count-pill">
                {{ $metrics['total_surveys'] > 0 ? "Berdasarkan {$metrics['total_surveys']} ulasan pegawai" : 'Belum ada responden' }}
            </span>
        </div>
    </div>

    <div class="bbpom-survey-metrics-list">
        @php
            $indicators = [
                ['key' => 'speed', 'label' => 'Kecepatan', 'icon' => 'ri-flashlight-line'],
                ['key' => 'quality', 'label' => 'Kualitas Solusi', 'icon' => 'ri-shield-check-line'],
                ['key' => 'communication', 'label' => 'Komunikasi', 'icon' => 'ri-customer-service-2-line'],
                ['key' => 'system', 'label' => 'Sistem ELITE', 'icon' => 'ri-device-line'],
            ];
        @endphp

        @foreach($indicators as $ind)
            @php
                $val = (float) ($metrics['breakdown'][$ind['key']] ?? 0);
                $pct = min(100, max(0, ($val / 5) * 100));
            @endphp
            <div class="bbpom-survey-metric-row">
                <div class="bbpom-survey-metric-header">
                    <span class="bbpom-survey-metric-label">
                        <i class="{{ $ind['icon'] }}"></i>
                        <span>{{ $ind['label'] }}</span>
                    </span>
                    <span class="bbpom-survey-metric-val">{{ number_format($val, 1) }}/5</span>
                </div>
                <div class="bbpom-survey-progress-track">
                    <div class="bbpom-survey-progress-fill" style="width: {{ $pct }}%;"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

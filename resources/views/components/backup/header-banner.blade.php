@props(['summary', 'settings'])

<div class="bk-header-card">
    <div style="display: flex; align-items: center; gap: 0.875rem; flex: 1 1 auto; min-width: 0;">
        <div style="width: 2.75rem; height: 2.75rem; border-radius: 0.75rem; background: #ecfdf5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 1px 2px 0 rgba(4, 120, 87, 0.1);">
            <i class="ri-database-2-line"></i>
        </div>
        <div style="min-width: 0;">
            <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.25;">
                Sistem Cadangan ELITE (Helpdesk IT BBPOM)
            </h2>
            <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0; line-height: 1.4;">
                Dump SQL database terkompresi ZIP, sinkronisasi Google Drive API otomatis, dan retensi lokal {{ $settings['retention_days'] }} hari.
            </p>
        </div>
    </div>

    <div class="bk-header-actions">
        {{-- Tombol 1: Backup Sekarang --}}
        <form action="{{ route('admin.backup.run') }}" method="POST" onsubmit="return confirm('Mulai proses backup database dan berkas sekarang?');">
            @csrf
            <button type="submit" class="bk-btn bk-btn-primary" {{ $summary['has_running_job'] ? 'disabled style=opacity:0.6;cursor:not-allowed;' : '' }}>
                <i class="{{ $summary['has_running_job'] ? 'ri-loader-4-line bk-spin' : 'ri-refresh-line' }}"></i>
                <span>{{ $summary['has_running_job'] ? 'Sedang Berjalan...' : 'Backup Sekarang' }}</span>
            </button>
        </form>

        {{-- Tombol 2: Backup .env Terenkripsi --}}
        <button type="button" @click="$dispatch('open-env-modal')" class="bk-btn bk-btn-secondary">
            <i class="ri-lock-password-line"></i>
            <span>Backup .env (ZIP AES-256)</span>
        </button>
    </div>
</div>

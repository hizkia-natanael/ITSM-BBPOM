@props(['summary', 'settings'])

<div class="bk-header-bar">
    <div style="display: flex; align-items: flex-start; gap: 0.875rem; min-width: 0;">
        <div class="bk-icon-box">
            <i class="ri-database-2-line"></i>
        </div>
        <div style="min-width: 0;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.25; letter-spacing: -0.01em;">
                    Backup Database &amp; Google Drive
                </h2>
                <span class="bk-badge bk-badge-emerald">
                    <i class="ri-shield-user-line"></i>
                    Administrator ELITE
                </span>
            </div>
            <p style="font-size: 0.8125rem; color: #64748b; margin: 0.25rem 0 0; line-height: 1.45;">
                Backup harian otomatis ke ZIP, upload ke Google Drive, dan pembersihan file lokal &gt; {{ $settings['retention_days'] }} hari.
            </p>
        </div>
    </div>

    <div class="bk-header-actions">
        @if ($settings['can_download_env'] ?? true)
            <button type="button" @click="$dispatch('open-env-modal')" class="bk-btn bk-btn-secondary">
                <i class="ri-key-2-line" style="color: #047857;"></i>
                <span>Backup .env</span>
            </button>
        @endif

        <button type="button" wire:click="$refresh" class="bk-btn bk-btn-secondary">
            <i class="ri-refresh-line {{ $summary['has_running_job'] ? 'bk-spin' : '' }}"></i>
            <span>Muat Ulang</span>
        </button>

        <form action="{{ route('admin.backup.run') }}" method="POST">
            @csrf
            <button type="submit" class="bk-btn bk-btn-primary" {{ $summary['has_running_job'] ? 'disabled' : '' }}>
                <i class="{{ $summary['has_running_job'] ? 'ri-loader-4-line bk-spin' : 'ri-play-fill' }}"></i>
                <span>{{ $summary['has_running_job'] ? 'Backup Berjalan...' : 'Backup Sekarang' }}</span>
            </button>
        </form>
    </div>
</div>

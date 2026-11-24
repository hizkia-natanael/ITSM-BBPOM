@props(['summary', 'settings'])

@php
    $bytes = (int) ($summary['local_size_bytes'] ?? 0);
    if ($bytes <= 0) {
        $displaySize = '0 MB';
    } elseif ($bytes < 1048576) {
        $displaySize = round($bytes / 1024, 1).' KB';
    } else {
        $displaySize = number_format($bytes / 1048576, 2).' MB';
    }
@endphp

<div class="bk-grid-4">
    <div class="bk-stat-card">
        <div style="min-width: 0;">
            <p style="font-size: 0.6875rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                Jadwal Harian
            </p>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin: 0.25rem 0 0;">
                {{ $settings['auto_backup_enabled'] ? $settings['schedule_time'].' WIB' : 'Nonaktif' }}
            </h3>
            <p style="font-size: 0.6875rem; color: #047857; font-weight: 600; margin: 0.35rem 0 0; display: flex; align-items: center; gap: 0.375rem;">
                <span style="width: 0.375rem; height: 0.375rem; border-radius: 9999px; display: inline-block; background: {{ $settings['auto_backup_enabled'] ? '#10b981' : '#94a3b8' }};"></span>
                {{ $settings['auto_backup_enabled'] ? 'Berjalan otomatis tiap hari' : 'Hanya pemicu manual' }}
            </p>
        </div>
        <div class="bk-stat-icon" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857;">
            <i class="ri-shield-check-line"></i>
        </div>
    </div>

    <div class="bk-stat-card">
        <div style="min-width: 0;">
            <p style="font-size: 0.6875rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                Arsip Lokal ({{ $settings['retention_days'] }} Hari)
            </p>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin: 0.25rem 0 0;">
                {{ $summary['local_count'] }} File
            </h3>
            <p style="font-size: 0.6875rem; color: #64748b; margin: 0.35rem 0 0; display: flex; align-items: center; gap: 0.25rem;">
                <i class="ri-hard-drive-2-line" style="color: #94a3b8;"></i>
                Total ukuran: {{ $displaySize }}
            </p>
        </div>
        <div class="bk-stat-icon" style="background: #f1f5f9; border: 1px solid #e2e8f0; color: #334155;">
            <i class="ri-hard-drive-2-line"></i>
        </div>
    </div>

    <div class="bk-stat-card">
        <div style="min-width: 0;">
            <p style="font-size: 0.6875rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                Arsip Google Drive
            </p>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin: 0.25rem 0 0;">
                {{ $summary['uploaded_count'] }} File
            </h3>
            <p style="font-size: 0.6875rem; color: #059669; font-weight: 600; margin: 0.35rem 0 0; display: flex; align-items: center; gap: 0.25rem;">
                <i class="ri-checkbox-circle-line"></i>
                {{ $settings['gdrive_connected'] ? 'Tersimpan permanen di Drive' : 'Menunggu koneksi GDrive' }}
            </p>
        </div>
        <div class="bk-stat-icon" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669;">
            <i class="ri-upload-cloud-2-line"></i>
        </div>
    </div>

    <div class="bk-stat-card">
        <div style="min-width: 0;">
            <p style="font-size: 0.6875rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                Backup Sukses Terakhir
            </p>
            <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0.35rem 0 0;">
                {{ $summary['last_success_at'] ?? 'Belum Pernah' }}
            </h3>
            <p style="font-size: 0.6875rem; color: #2563eb; font-weight: 600; margin: 0.35rem 0 0; display: flex; align-items: center; gap: 0.25rem;">
                <i class="ri-time-line"></i>
                Total {{ $summary['total_backups'] }} riwayat tercatat
            </p>
        </div>
        <div class="bk-stat-icon" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb;">
            <i class="ri-database-2-line"></i>
        </div>
    </div>
</div>

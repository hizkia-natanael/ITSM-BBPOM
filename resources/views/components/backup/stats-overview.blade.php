@props(['summary', 'settings'])

<div class="bk-grid-4">
    <div class="bk-stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Jadwal Backup Harian</span>
            <span class="bk-badge {{ $settings['auto_backup_enabled'] ? 'bk-badge-emerald' : 'bk-badge-slate' }}">
                <i class="{{ $settings['auto_backup_enabled'] ? 'ri-checkbox-circle-line' : 'ri-close-circle-line' }}"></i>
                {{ $settings['auto_backup_enabled'] ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        <div style="margin-top: 0.75rem;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                {{ $settings['schedule_time'] }} <span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">WIB</span>
            </div>
            <div style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.375rem; display: flex; align-items: center; gap: 0.25rem;">
                <i class="ri-time-line"></i> Eksekusi otomatis setiap hari
            </div>
        </div>
    </div>

    <div class="bk-stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Total Riwayat Backup</span>
            <div style="width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                <i class="ri-archive-line"></i>
            </div>
        </div>
        <div style="margin-top: 0.75rem;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                {{ $summary['total_backups'] }} <span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">arsip</span>
            </div>
            <div style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.375rem;">
                Terakhir: {{ $summary['last_success_at'] ?? 'Belum pernah' }}
            </div>
        </div>
    </div>

    <div class="bk-stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Penyimpanan Lokal</span>
            <div style="width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                <i class="ri-hard-drive-2-line"></i>
            </div>
        </div>
        <div style="margin-top: 0.75rem;">
            @php
                $mb = round($summary['local_size_bytes'] / 1048576, 2);
                $displaySize = $mb >= 1 ? "{$mb} MB" : round($summary['local_size_bytes'] / 1024, 1) . ' KB';
            @endphp
            <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                {{ $displaySize }}
            </div>
            <div style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.375rem;">
                {{ $summary['local_count'] }} file (retensi {{ $settings['retention_days'] }} hari)
            </div>
        </div>
    </div>

    <div class="bk-stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Google Drive Cloud</span>
            <span class="bk-badge {{ $settings['gdrive_connected'] ? 'bk-badge-blue' : 'bk-badge-amber' }}">
                <i class="{{ $settings['gdrive_connected'] ? 'ri-cloud-line' : 'ri-cloud-off-line' }}"></i>
                {{ $settings['gdrive_connected'] ? 'Terhubung' : 'Belum Terhubung' }}
            </span>
        </div>
        <div style="margin-top: 0.75rem;">
            <div style="font-size: 0.875rem; font-weight: 800; color: #0f172a; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ $settings['gdrive_account_email'] ?: ($settings['gdrive_connected'] ? 'Akun Google Aktif' : 'Belum Terhubung') }}
            </div>
            <div style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.375rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                Folder: {{ $settings['gdrive_folder_name'] }} ({{ $summary['uploaded_count'] }} terunggah)
            </div>
        </div>
    </div>
</div>

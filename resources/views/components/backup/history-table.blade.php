@props(['backups', 'retentionDays' => 14])

<div class="bk-card" x-data>
    <div class="bk-card-header">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="bk-icon-box">
                <i class="ri-hard-drive-2-line"></i>
            </div>
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0;">
                    Riwayat Backup Database (30 Terakhir)
                </h3>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0;">
                    File lokal server otomatis terhapus setelah {{ $retentionDays }} hari, sedangkan file di Google Drive tetap tersimpan.
                </p>
            </div>
        </div>
    </div>

    <div class="bk-table-wrap">
        <table class="bk-table">
            <thead>
                <tr>
                    <th>Nama File ZIP</th>
                    <th>Pemicu</th>
                    <th>Ukuran</th>
                    <th>Status Lokal</th>
                    <th>Status GDrive</th>
                    <th>Waktu Selesai</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($backups as $backup)
                    <x-backup.history-row :backup="$backup" />
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                            <div style="width: 2.75rem; height: 2.75rem; margin: 0 auto 0.5rem; border-radius: 0.75rem; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                <i class="ri-hard-drive-2-line"></i>
                            </div>
                            <div style="font-size: 0.875rem; font-weight: 600; color: #334155;">Belum ada riwayat backup</div>
                            <div style="font-size: 0.75rem; margin-top: 0.25rem;">
                                Klik tombol <strong style="color: #047857;">Backup Sekarang</strong> di kanan atas untuk membuat backup pertama.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

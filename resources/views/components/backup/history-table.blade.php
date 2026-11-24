@props(['backups'])

<div class="bk-card" style="padding: 1.25rem 1.5rem;">
    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
        <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ri-history-line" style="color: #059669;"></i>
            Riwayat Cadangan Database
        </h3>
        <p style="font-size: 0.6875rem; color: #64748b; margin: 0.25rem 0 0 0;">
            Daftar arsip cadangan database lokal dan status unggahan ke Google Drive Cloud.
        </p>
    </div>

    <div class="bk-table-container">
        <table class="bk-table">
            <thead>
                <tr>
                    <th>Nama Berkas ZIP</th>
                    <th>Ukuran</th>
                    <th>Pemicu</th>
                    <th>Status Backup</th>
                    <th>Google Drive</th>
                    <th>Waktu Selesai (WIB)</th>
                    <th>Pembuat</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($backups as $backup)
                    <tr>
                        <td style="font-weight: 600; color: #0f172a; max-width: 16rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $backup->filename }}
                            @if ($backup->error_message)
                                <div style="font-size: 0.625rem; color: #e11d48; margin-top: 0.125rem;" title="{{ $backup->error_message }}">
                                    {{ Str::limit($backup->error_message, 40) }}
                                </div>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            @php
                                $kb = round($backup->size_bytes / 1024, 1);
                                $mb = round($backup->size_bytes / 1048576, 2);
                            @endphp
                            {{ $backup->size_bytes > 0 ? ($mb >= 1 ? "{$mb} MB" : "{$kb} KB") : '-' }}
                        </td>
                        <td style="white-space: nowrap;">
                            <span class="bk-badge {{ $backup->trigger_type === 'scheduled' ? 'bk-badge-slate' : 'bk-badge-blue' }}">
                                <i class="{{ $backup->trigger_type === 'scheduled' ? 'ri-calendar-schedule-line' : 'ri-user-line' }}"></i>
                                {{ $backup->trigger_type === 'scheduled' ? 'Terjadwal' : 'Manual' }}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            @if ($backup->status === 'success')
                                <span class="bk-badge bk-badge-emerald"><i class="ri-checkbox-circle-line"></i> Sukses</span>
                            @elseif ($backup->status === 'running')
                                <span class="bk-badge bk-badge-amber"><i class="ri-loader-4-line bk-spin"></i> Berjalan</span>
                            @elseif ($backup->status === 'failed')
                                <span class="bk-badge bk-badge-rose"><i class="ri-close-circle-line"></i> Gagal</span>
                            @else
                                <span class="bk-badge bk-badge-slate"><i class="ri-time-line"></i> Pending</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            @if ($backup->gdrive_status === 'uploaded')
                                <span class="bk-badge bk-badge-blue"><i class="ri-cloud-line"></i> Terunggah</span>
                            @elseif ($backup->gdrive_status === 'skipped')
                                <span class="bk-badge bk-badge-slate"><i class="ri-skip-forward-line"></i> Dilewati</span>
                            @elseif ($backup->gdrive_status === 'failed')
                                <span class="bk-badge bk-badge-rose"><i class="ri-error-warning-line"></i> Gagal</span>
                            @else
                                <span class="bk-badge bk-badge-slate"><i class="ri-time-line"></i> Pending</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap; font-size: 0.6875rem; color: #64748b;">
                            {{ $backup->completed_at ? $backup->completed_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') : ($backup->started_at ? $backup->started_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') : '-') }}
                        </td>
                        <td style="white-space: nowrap; font-size: 0.6875rem; color: #475569;">
                            {{ $backup->createdByUser?->name ?? 'Sistem Otomatis' }}
                        </td>
                        <td style="white-space: nowrap; text-align: right;">
                            <div style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                @if ($backup->hasLocalFile())
                                    <a href="{{ route('admin.backup.download', $backup) }}" class="bk-btn-icon bk-btn-icon-emerald" title="Unduh Berkas ZIP Lokal">
                                        <i class="ri-download-2-line"></i>
                                    </a>
                                @endif
                                @if ($backup->gdrive_web_url)
                                    <a href="{{ $backup->gdrive_web_url }}" target="_blank" class="bk-btn-icon bk-btn-icon-blue" title="Buka di Google Drive">
                                        <i class="ri-external-link-line"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.backup.destroy', $backup) }}" method="POST" onsubmit="return confirm('Hapus riwayat dan berkas backup lokal ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bk-btn-icon bk-btn-icon-rose" title="Hapus Riwayat & File Lokal">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8;">
                            <i class="ri-inbox-line" style="font-size: 1.5rem; display: block; margin-bottom: 0.375rem; color: #cbd5e1;"></i>
                            Belum ada riwayat backup database. Klik <strong>Backup Sekarang</strong> untuk membuat cadangan pertama.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@props(['backup'])

@php
    $bytes = (int) ($backup->size_bytes ?? 0);
    $sizeText = $bytes <= 0 ? '0 B' : ($bytes < 1024 ? $bytes.' B' : ($bytes < 1048576 ? round($bytes / 1024, 1).' KB' : number_format($bytes / 1048576, 2).' MB'));
    $timeText = ($backup->completed_at ?? $backup->created_at)?->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') ?? '-';
@endphp

<tr>
    <td class="bk-input-mono" style="font-weight: 600; color: #1e293b;">
        <div>{{ $backup->filename }}</div>
        @if ($backup->error_message)
            <div class="bk-error-pill" style="font-family: inherit;">
                <i class="ri-alert-line" style="color: #e11d48;"></i>
                <span class="bk-error-truncate" title="{{ $backup->error_message }}">{{ $backup->error_message }}</span>
                <button type="button" class="bk-error-detail-btn"
                    data-filename="{{ $backup->filename }}"
                    data-message="{{ $backup->error_message }}"
                    @click="$dispatch('open-backup-error', { filename: $el.dataset.filename, message: $el.dataset.message })">
                    Detail
                </button>
            </div>
        @endif
    </td>
    <td style="white-space: nowrap;">
        <span class="bk-badge {{ $backup->trigger_type === 'scheduled' ? 'bk-badge-blue' : 'bk-badge-slate' }}">
            {{ $backup->trigger_type === 'scheduled' ? 'Otomatis' : 'Manual' }}
        </span>
        <div style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.25rem;">
            {{ $backup->createdByUser?->name ?? 'Sistem (Otomatis)' }}
        </div>
    </td>
    <td style="white-space: nowrap;">{{ $sizeText }}</td>
    <td style="white-space: nowrap;">
        @if ($backup->status === 'success')
            <span class="bk-badge bk-badge-emerald">Berhasil</span>
        @elseif ($backup->status === 'running' || $backup->status === 'pending')
            <span class="bk-badge bk-badge-amber">{{ $backup->status === 'running' ? 'Memproses...' : 'Antrean' }}</span>
        @else
            <span class="bk-badge bk-badge-rose">Gagal</span>
        @endif
        <div style="font-size: 0.6875rem; color: #64748b; margin-top: 0.25rem;">
            @if ($backup->hasLocalFile()) Tersedia di server
            @elseif ($backup->local_deleted_at) Dihapus ({{ $backup->local_deleted_at->copy()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }})
            @else Tidak ada file lokal @endif
        </div>
    </td>
    <td style="white-space: nowrap;">
        @if ($backup->gdrive_status === 'uploaded')
            <span class="bk-badge bk-badge-emerald"><i class="ri-checkbox-circle-line"></i> Terunggah</span>
        @elseif ($backup->gdrive_status === 'skipped')
            <span class="bk-badge bk-badge-slate">Dilewati</span>
        @elseif ($backup->gdrive_status === 'pending')
            <span class="bk-badge bk-badge-amber">Menunggu</span>
        @else
            <span class="bk-badge bk-badge-rose">Upload Gagal</span>
        @endif
    </td>
    <td style="white-space: nowrap; color: #475569;">{{ $timeText }}</td>
    <td style="white-space: nowrap; text-align: right;">
        <div style="display: inline-flex; align-items: center; gap: 0.375rem;">
            @if ($backup->hasLocalFile())
                <a href="{{ route('admin.backup.download', $backup) }}" class="bk-btn bk-btn-secondary bk-btn-sm">
                    <i class="ri-download-2-line"></i><span>Unduh</span>
                </a>
            @endif
            @if ($backup->gdrive_web_url)
                <a href="{{ $backup->gdrive_web_url }}" target="_blank" rel="noreferrer" class="bk-btn bk-btn-emerald-soft bk-btn-sm">
                    <i class="ri-external-link-line"></i><span>GDrive</span>
                </a>
            @endif
            <button type="button" title="Hapus riwayat & file lokal" class="bk-btn-icon-rose"
                data-url="{{ route('admin.backup.destroy', $backup) }}"
                data-filename="{{ $backup->filename }}"
                data-size="{{ $sizeText }}"
                data-trigger="{{ $backup->trigger_type === 'scheduled' ? 'Otomatis' : 'Manual' }}"
                data-time="{{ $timeText }}"
                @click="$dispatch('open-backup-delete', { url: $el.dataset.url, filename: $el.dataset.filename, size: $el.dataset.size, trigger: $el.dataset.trigger, time: $el.dataset.time })">
                <i class="ri-delete-bin-line" style="font-size: 1rem;"></i>
            </button>
        </div>
    </td>
</tr>

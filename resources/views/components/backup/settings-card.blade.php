@props(['settings'])

<div class="bk-card" x-data="backupSettingsState(@js($settings))">
    <div class="bk-card-header-hero">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="bk-hero-icon">
                <i class="ri-cloud-line"></i>
            </div>
            <div>
                <h3>Jadwal Otomatis, Retensi &amp; Google Drive</h3>
                <p>Atur jam backup harian, retensi lokal server (default 14 hari), dan koneksi Google Drive pribadi.</p>
            </div>
        </div>

        <div>
            @if ($settings['gdrive_connected'])
                <span class="bk-badge bk-badge-emerald">
                    <i class="ri-checkbox-circle-line"></i>
                    GDrive Terhubung{{ $settings['gdrive_account_email'] ? ' ('.$settings['gdrive_account_email'].')' : '' }}
                </span>
            @else
                <span class="bk-badge bk-badge-amber">
                    <i class="ri-error-warning-line"></i>
                    GDrive Belum Terhubung
                </span>
            @endif
        </div>
    </div>

    <div class="bk-card-body">
        <form action="{{ route('admin.backup.settings.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')

            <div class="bk-grid-5">
                <div>
                    <label class="bk-label" for="auto_backup_enabled">Backup Otomatis Harian</label>
                    <input type="hidden" name="auto_backup_enabled" :value="autoEnabled ? '1' : '0'">
                    <button id="auto_backup_enabled" type="button" @click="autoEnabled = !autoEnabled"
                        :class="autoEnabled ? 'bk-pill-toggle bk-pill-toggle-active' : 'bk-pill-toggle'">
                        <span x-text="autoEnabled ? 'Aktif Setiap Hari' : 'Nonaktif (Manual)'"></span>
                        <i class="ri-time-line"></i>
                    </button>
                </div>

                <div>
                    <label class="bk-label" for="include_documents">Cakupan Isi Backup</label>
                    <input type="hidden" name="include_documents" :value="includeDocuments ? '1' : '0'">
                    <button id="include_documents" type="button" @click="includeDocuments = !includeDocuments"
                        :class="includeDocuments ? 'bk-pill-toggle bk-pill-toggle-active' : 'bk-pill-toggle'">
                        <span x-text="includeDocuments ? 'DB + Dokumen' : 'Database Saja'"></span>
                        <i class="ri-folder-zip-line"></i>
                    </button>
                    <p class="bk-hint">Menyertakan lampiran tiket di <span style="font-family: monospace;">storage/app</span>.</p>
                </div>

                <div>
                    <label class="bk-label" for="schedule_time">Jam Eksekusi (WIB)</label>
                    <input id="schedule_time" name="schedule_time" type="time" required
                        value="{{ old('schedule_time', $settings['schedule_time']) }}" class="bk-input">
                    @error('schedule_time')
                        <p class="bk-error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="bk-label" for="retention_days">Hapus File Lokal (Hari)</label>
                    <input id="retention_days" name="retention_days" type="number" min="1" max="365" required
                        value="{{ old('retention_days', $settings['retention_days']) }}" class="bk-input">
                    <p class="bk-hint">Default 14 hari. Arsip di GDrive tetap disimpan.</p>
                    @error('retention_days')
                        <p class="bk-error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="bk-label" for="filename_prefix">Awalan Nama File ZIP</label>
                    <input id="filename_prefix" name="filename_prefix" type="text" required maxlength="50"
                        x-model="filenamePrefix" placeholder="backup-elite" class="bk-input bk-input-mono">
                    <p class="bk-hint">
                        Format: <span style="font-family: monospace;" x-text="(filenamePrefix || 'backup-elite') + '-YYYY-MM-DD.zip'"></span>
                    </p>
                    @error('filename_prefix')
                        <p class="bk-error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <x-backup.oauth-credentials-panel :settings="$settings" />

            <div style="display: flex; justify-content: flex-end; padding-top: 0.25rem;">
                <button type="submit" class="bk-btn bk-btn-primary">
                    <i class="ri-save-3-line"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function backupSettingsState(settings) {
        return {
            autoEnabled: Boolean(settings.auto_backup_enabled),
            includeDocuments: settings.include_documents !== false,
            filenamePrefix: @js(old('filename_prefix', $settings['filename_prefix'])),
        };
    }
</script>

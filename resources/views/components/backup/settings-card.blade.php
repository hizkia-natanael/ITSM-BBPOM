@props(['settings'])

<div class="bk-card">
    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.875rem; margin-bottom: 1.25rem;">
        <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ri-settings-4-line" style="color: #059669;"></i>
            Pengaturan Backup & Google Drive
        </h3>
        <p style="font-size: 0.6875rem; color: #64748b; margin: 0.25rem 0 0 0;">
            Konfigurasi jadwal otomatis, retensi arsip lokal, dan integrasi Google Cloud Platform.
        </p>
    </div>

    <form action="{{ route('admin.backup.settings.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
        @csrf
        @method('PUT')

        {{-- Row 1: Dua Toggle Switch Bersebelahan --}}
        <div class="bk-grid-2">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; background: #f8fafc; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #0f172a; display: block;">Backup Otomatis Harian</span>
                    <span style="font-size: 0.6875rem; color: #64748b; display: block; margin-top: 0.125rem;">Scheduler Laravel cron di server setiap hari.</span>
                </div>
                <input type="hidden" name="auto_backup_enabled" value="0">
                <input type="checkbox" name="auto_backup_enabled" value="1" {{ $settings['auto_backup_enabled'] ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: #059669; cursor: pointer;">
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; background: #f8fafc; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #0f172a; display: block;">Sertakan Berkas & Lampiran Tiket</span>
                    <span style="font-size: 0.6875rem; color: #64748b; display: block; margin-top: 0.125rem;">Menyimpan seluruh lampiran tiket di storage/app.</span>
                </div>
                <input type="hidden" name="include_documents" value="0">
                <input type="checkbox" name="include_documents" value="1" {{ $settings['include_documents'] ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: #059669; cursor: pointer;">
            </div>
        </div>

        {{-- Row 2: Empat Kolom Parameter Operasional --}}
        <div class="bk-grid-4">
            <div class="bk-form-group">
                <label class="bk-label">Waktu Eksekusi (WIB)</label>
                <input type="text" name="schedule_time" value="{{ old('schedule_time', $settings['schedule_time']) }}" required pattern="^([01]\d|2[0-3]):([0-5]\d)$" placeholder="02:00" class="bk-input">
                <span class="bk-hint">Format 24 jam (contoh: 02:00).</span>
            </div>

            <div class="bk-form-group">
                <label class="bk-label">Retensi Lokal (Hari)</label>
                <input type="number" name="retention_days" value="{{ old('retention_days', $settings['retention_days']) }}" required min="1" max="365" class="bk-input">
                <span class="bk-hint">File lokal kadaluwarsa otomatis dihapus.</span>
            </div>

            <div class="bk-form-group">
                <label class="bk-label">Prefix Nama File ZIP</label>
                <input type="text" name="filename_prefix" value="{{ old('filename_prefix', $settings['filename_prefix']) }}" required maxlength="50" class="bk-input">
                <span class="bk-hint">Contoh: backup-elite</span>
            </div>

            <div class="bk-form-group">
                <label class="bk-label">Nama Folder Google Drive</label>
                <input type="text" name="gdrive_folder_name" value="{{ old('gdrive_folder_name', $settings['gdrive_folder_name']) }}" required maxlength="128" class="bk-input">
                <span class="bk-hint">Folder otomatis dibuat jika belum ada.</span>
            </div>
        </div>

        {{-- Row 3: Dua Kolom Kredensial OAuth --}}
        <div class="bk-grid-2" style="padding-top: 0.5rem; border-top: 1px solid #f1f5f9;">
            <div class="bk-form-group">
                <label class="bk-label">Google OAuth 2.0 Client ID</label>
                <input type="text" name="gdrive_client_id" value="{{ old('gdrive_client_id', $settings['gdrive_client_id']) }}" placeholder="xxxx.apps.googleusercontent.com" class="bk-input">
            </div>

            <div class="bk-form-group">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <label class="bk-label">Google OAuth 2.0 Client Secret</label>
                    @if ($settings['has_client_secret'])
                        <span class="bk-badge bk-badge-emerald">Tersimpan Terenkripsi</span>
                    @endif
                </div>
                <input type="password" name="gdrive_client_secret" placeholder="{{ $settings['has_client_secret'] ? '•••••••••••••••• (Kosongkan jika tidak diubah)' : 'Masukkan Client Secret GCP' }}" class="bk-input">
            </div>
        </div>

        {{-- Row 4: Tombol Aksi Simpan --}}
        <div style="display: flex; justify-content: flex-end; padding-top: 0.5rem;">
            <button type="submit" class="bk-btn bk-btn-primary">
                <i class="ri-save-3-line"></i>
                <span>Simpan Pengaturan</span>
            </button>
        </div>
    </form>
</div>

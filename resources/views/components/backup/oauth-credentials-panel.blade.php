@props(['settings'])

<div class="bk-oauth-panel" x-data="backupOauthPanelState(@js($settings['oauth_redirect_uri']))">
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="ri-key-2-line" style="color: #047857; font-size: 1rem;"></i>
            <h4 style="font-size: 0.875rem; font-weight: 600; color: #1e293b; margin: 0;">
                Kredensial OAuth Google Drive (Gratis via Google Cloud Console)
            </h4>
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            @if ($settings['has_oauth_client'])
                <a href="{{ route('admin.backup.google.connect') }}" class="bk-btn bk-btn-secondary bk-btn-sm">
                    <i class="ri-link" style="color: #047857;"></i>
                    <span>{{ $settings['gdrive_connected'] ? 'Hubungkan Ulang GDrive' : 'Hubungkan Akun GDrive' }}</span>
                </a>
            @endif

            @if ($settings['gdrive_connected'])
                <button type="button" @click="$dispatch('open-disconnect-modal')" class="bk-btn bk-btn-rose-outline bk-btn-sm">
                    <i class="ri-link-unlink"></i>
                    <span>Putus Koneksi</span>
                </button>
            @endif
        </div>
    </div>

    <div class="bk-uri-box">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.45rem; min-width: 0;">
            <span style="font-weight: 600; color: #065f46;">Authorized Redirect URI:</span>
            <code class="bk-uri-code">{{ $settings['oauth_redirect_uri'] }}</code>
        </div>
        <button type="button" @click="copyUri()" class="bk-btn bk-btn-secondary bk-btn-sm">
            <i :class="copiedUri ? 'ri-check-line' : 'ri-file-copy-line'"></i>
            <span x-text="copiedUri ? 'Tersalin' : 'Salin URI'"></span>
        </button>
    </div>

    <div class="bk-grid-2">
        <div>
            <label class="bk-label" for="gdrive_client_id">Google Client ID</label>
            <input id="gdrive_client_id" name="gdrive_client_id" type="text"
                value="{{ old('gdrive_client_id', $settings['gdrive_client_id']) }}"
                placeholder="xxxx.apps.googleusercontent.com" class="bk-input bk-input-mono">
            @error('gdrive_client_id')
                <p class="bk-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="bk-label" for="gdrive_client_secret">
                Google Client Secret {{ $settings['has_client_secret'] ? '(Sudah Tersimpan)' : '' }}
            </label>
            <input id="gdrive_client_secret" name="gdrive_client_secret" type="password"
                placeholder="{{ $settings['has_client_secret'] ? 'Kosongkan jika tidak ingin mengganti' : 'GOCSPX-...' }}"
                class="bk-input bk-input-mono">
            @error('gdrive_client_secret')
                <p class="bk-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="bk-label" for="gdrive_folder_name">Nama Folder Tujuan di Google Drive</label>
            <input id="gdrive_folder_name" name="gdrive_folder_name" type="text" required maxlength="128"
                value="{{ old('gdrive_folder_name', $settings['gdrive_folder_name']) }}"
                placeholder="Backup-ELITE" class="bk-input">
            <p class="bk-hint">Otomatis dibuat di root My Drive jika Folder ID di samping dikosongkan.</p>
            @error('gdrive_folder_name')
                <p class="bk-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="bk-label" for="gdrive_folder_id">
                <i class="ri-folder-open-line" style="color: #94a3b8;"></i>
                <span>Folder ID Google Drive (Opsional)</span>
            </label>
            <input id="gdrive_folder_id" name="gdrive_folder_id" type="text"
                value="{{ old('gdrive_folder_id', $settings['gdrive_folder_id']) }}"
                placeholder="Kosongkan untuk auto-detect folder" class="bk-input bk-input-mono">
            @error('gdrive_folder_id')
                <p class="bk-error-text">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<script>
    function backupOauthPanelState(redirectUri) {
        return {
            copiedUri: false,
            async copyUri() {
                try {
                    await navigator.clipboard.writeText(redirectUri);
                    this.copiedUri = true;
                    setTimeout(() => { this.copiedUri = false; }, 2000);
                } catch (_) {}
            },
        };
    }
</script>

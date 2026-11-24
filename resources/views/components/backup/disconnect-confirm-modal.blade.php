<div x-data="backupDisconnectModalState()" x-show="isOpen" @open-disconnect-modal.window="openModal()"
    @keydown.escape.window="closeModal()" x-cloak class="bk-modal-overlay" style="display: none;" @click="closeModal()">
    <div class="bk-modal-card" @click.stop>
        <div class="bk-modal-header">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div class="bk-hero-icon" style="width: 2.25rem; height: 2.25rem; background: rgba(244, 63, 94, 0.2); border-color: rgba(251, 113, 133, 0.45); color: #fda4af; font-size: 1rem;">
                    <i class="ri-link-unlink"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #0f172a; margin: 0;">Putuskan Koneksi Google Drive</h3>
                    <p style="font-size: 0.6875rem; color: #64748b; margin: 0;">Token otorisasi Google Drive akan dihapus</p>
                </div>
            </div>
            <button type="button" @click="closeModal()" class="bk-modal-close"><i class="ri-close-line"></i></button>
        </div>

        <div class="bk-modal-body">
            <div class="bk-alert-amber">
                <i class="ri-alert-line" style="color: #d97706; font-size: 1rem; margin-top: 0.1rem;"></i>
                <p style="margin: 0;">Putuskan koneksi akun Google Drive saat ini? Backup tetap berjalan secara lokal tanpa upload ke Drive.</p>
            </div>
        </div>

        <form action="{{ route('admin.backup.google.disconnect') }}" method="POST" style="margin: 0;">
            @csrf
            <div class="bk-modal-footer">
                <button type="button" @click="closeModal()" class="bk-btn bk-btn-secondary bk-btn-sm">Batal</button>
                <button type="submit" class="bk-btn bk-btn-destructive bk-btn-sm">
                    <i class="ri-link-unlink"></i>
                    <span>Ya, Putus Koneksi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function backupDisconnectModalState() {
        return {
            isOpen: false,
            openModal() { this.isOpen = true; },
            closeModal() { this.isOpen = false; },
        };
    }
</script>

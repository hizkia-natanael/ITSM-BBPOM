<div x-data="backupDeleteModalState()" x-show="isOpen" @open-backup-delete.window="openModal($event.detail)"
    @keydown.escape.window="closeModal()" x-cloak class="bk-modal-overlay" style="display: none;" @click="closeModal()">
    <div class="bk-modal-card" @click.stop>
        <div class="bk-modal-header">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div class="bk-icon-box" style="width: 2.25rem; height: 2.25rem; background: #fff1f2; border-color: #fecdd3; color: #e11d48; font-size: 1rem;">
                    <i class="ri-delete-bin-line"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #0f172a; margin: 0;">Hapus Riwayat Backup</h3>
                    <p style="font-size: 0.6875rem; color: #64748b; margin: 0;">Konfirmasi penghapusan riwayat &amp; arsip lokal</p>
                </div>
            </div>
            <button type="button" @click="closeModal()" class="bk-modal-close"><i class="ri-close-line"></i></button>
        </div>

        <div class="bk-modal-body">
            <div style="border-radius: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.875rem;">
                <p class="bk-input-mono" style="font-weight: 700; color: #0f172a; margin: 0; word-break: break-all;" x-text="target.filename"></p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; font-size: 0.6875rem; color: #64748b; margin-top: 0.375rem;">
                    <span x-text="'Ukuran: ' + target.size"></span>
                    <span>&bull;</span>
                    <span x-text="target.trigger"></span>
                    <span>&bull;</span>
                    <span x-text="target.time"></span>
                </div>
            </div>

            <div class="bk-alert-amber">
                <i class="ri-alert-line" style="color: #d97706; font-size: 1rem; margin-top: 0.1rem;"></i>
                <p style="margin: 0;">Riwayat dan file ZIP lokal di server akan dihapus permanen. File yang sudah ada di Google Drive tidak ikut dihapus.</p>
            </div>
        </div>

        <form :action="target.url" method="POST" style="margin: 0;">
            @csrf
            @method('DELETE')
            <div class="bk-modal-footer">
                <button type="button" @click="closeModal()" class="bk-btn bk-btn-secondary bk-btn-sm">Batal</button>
                <button type="submit" class="bk-btn bk-btn-destructive bk-btn-sm">
                    <i class="ri-delete-bin-line"></i>
                    <span>Hapus Backup</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function backupDeleteModalState() {
        return {
            isOpen: false,
            target: { url: '', filename: '', size: '', trigger: '', time: '' },
            openModal(detail) {
                this.target = {
                    url: detail?.url || '',
                    filename: detail?.filename || '',
                    size: detail?.size || '-',
                    trigger: detail?.trigger || 'Manual',
                    time: detail?.time || '-',
                };
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
            },
        };
    }
</script>

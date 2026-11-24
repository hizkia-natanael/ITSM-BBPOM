<div x-data="backupErrorModalState()" x-show="isOpen" @open-backup-error.window="openModal($event.detail)"
    @keydown.escape.window="closeModal()" x-cloak class="bk-modal-overlay" style="display: none;" @click="closeModal()">
    <div class="bk-modal-card bk-modal-card-lg" @click.stop>
        <div class="bk-modal-header">
            <div style="display: flex; align-items: center; gap: 0.625rem; min-width: 0;">
                <div class="bk-icon-box" style="width: 2.25rem; height: 2.25rem; background: #fff1f2; border-color: #fecdd3; color: #e11d48; font-size: 1rem;">
                    <i class="ri-alert-line"></i>
                </div>
                <div style="min-width: 0;">
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #0f172a; margin: 0;">Detail Kegagalan Backup</h3>
                    <p class="bk-input-mono" style="font-size: 0.6875rem; color: #64748b; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="filename"></p>
                </div>
            </div>
            <button type="button" @click="closeModal()" class="bk-modal-close"><i class="ri-close-line"></i></button>
        </div>

        <div class="bk-modal-body">
            <div style="font-size: 0.75rem; font-weight: 600; color: #475569;">Pesan error teknis dari sistem:</div>
            <div class="bk-error-pre" x-text="message"></div>
        </div>

        <div class="bk-modal-footer">
            <button type="button" @click="copyError()" class="bk-btn bk-btn-secondary bk-btn-sm">
                <i :class="copied ? 'ri-check-line' : 'ri-file-copy-line'"></i>
                <span x-text="copied ? 'Tersalin' : 'Salin Pesan'"></span>
            </button>
            <button type="button" @click="closeModal()" class="bk-btn bk-btn-primary bk-btn-sm">Tutup</button>
        </div>
    </div>
</div>

<script>
    function backupErrorModalState() {
        return {
            isOpen: false,
            filename: '',
            message: '',
            copied: false,
            openModal(detail) {
                this.filename = detail?.filename || '';
                this.message = detail?.message || '';
                this.copied = false;
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
            },
            async copyError() {
                try {
                    await navigator.clipboard.writeText(this.message);
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                } catch (_) {}
            },
        };
    }
</script>

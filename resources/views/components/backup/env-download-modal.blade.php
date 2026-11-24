<div x-data="backupEnvModalState(@js(route('admin.backup.env-download')), @js(csrf_token()))"
    x-show="isOpen" @open-env-modal.window="openModal()" @keydown.escape.window="closeModal()"
    x-cloak class="bk-modal-overlay" style="display: none;">
    <div class="bk-modal-card" @click.stop>
        <div class="bk-modal-header">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div class="bk-icon-box" style="width: 2.25rem; height: 2.25rem; font-size: 1rem;">
                    <i class="ri-key-2-line"></i>
                </div>
                <div>
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: #0f172a; margin: 0;">
                        Unduh Backup .env Terenkripsi
                    </h3>
                    <p style="font-size: 0.6875rem; color: #64748b; margin: 0;">
                        Dikemas menjadi ZIP AES-256 langsung ke perangkat Anda
                    </p>
                </div>
            </div>
            <button type="button" @click="closeModal()" class="bk-modal-close"><i class="ri-close-line"></i></button>
        </div>

        <form @submit.prevent="submitForm()" style="margin: 0;">
            <div class="bk-modal-body">
                <div class="bk-alert-emerald">
                    <i class="ri-shield-keyhole-line" style="color: #047857; font-size: 1rem; margin-top: 0.1rem;"></i>
                    <p style="margin: 0;">
                        File <span style="font-family: monospace;">.env</span> berisi <span style="font-family: monospace;">APP_KEY</span> dan password database. Salinan ini tidak disimpan di server maupun Google Drive.
                    </p>
                </div>

                <div x-show="errorMessage" class="bk-alert-rose" x-text="errorMessage"></div>

                <div>
                    <label class="bk-label" for="env_admin_password">Password Akun Administrator (Verifikasi)</label>
                    <input id="env_admin_password" type="password" required x-model="adminPassword"
                        placeholder="Masukkan password login Anda" class="bk-input">
                    <p x-show="fieldErrors.admin_password" class="bk-error-text" x-text="fieldErrors.admin_password"></p>
                </div>

                <div>
                    <label class="bk-label" for="env_zip_password">Password Kunci File ZIP (Min. 8 Karakter)</label>
                    <input id="env_zip_password" type="password" required minlength="8" x-model="zipPassword"
                        placeholder="Buat password untuk membuka file ZIP" class="bk-input">
                    <p x-show="fieldErrors.zip_password" class="bk-error-text" x-text="fieldErrors.zip_password"></p>
                </div>
            </div>

            <div class="bk-modal-footer">
                <button type="button" @click="closeModal()" :disabled="submitting" class="bk-btn bk-btn-secondary bk-btn-sm">Batal</button>
                <button type="submit" :disabled="submitting" class="bk-btn bk-btn-primary bk-btn-sm">
                    <i class="ri-lock-2-line"></i>
                    <span x-text="submitting ? 'Mengenkripsi...' : 'Unduh ZIP .env'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function backupEnvModalState(endpointUrl, csrfToken) {
        return {
            isOpen: false, adminPassword: '', zipPassword: '', submitting: false, errorMessage: null, fieldErrors: {},
            openModal() { this.isOpen = true; },
            closeModal() {
                if (this.submitting) { return; }
                this.isOpen = false; this.adminPassword = ''; this.zipPassword = '';
                this.errorMessage = null; this.fieldErrors = {};
            },
            async submitForm() {
                this.submitting = true; this.errorMessage = null; this.fieldErrors = {};
                try {
                    const res = await fetch(endpointUrl, {
                        method: 'POST', credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json, application/zip', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ admin_password: this.adminPassword, zip_password: this.zipPassword }),
                    });
                    if (!res.ok) {
                        const parsed = await res.json().catch(() => null);
                        this.errorMessage = parsed?.message || 'Gagal mengunduh arsip .env.';
                        this.fieldErrors = { admin_password: parsed?.errors?.admin_password?.[0], zip_password: parsed?.errors?.zip_password?.[0] };
                        return;
                    }
                    const blob = await res.blob();
                    const cd = String(res.headers.get('content-disposition') || '');
                    const match = cd.match(/filename="?([^"]+)"?/i);
                    const url = window.URL.createObjectURL(new Blob([blob], { type: 'application/zip' }));
                    const link = document.createElement('a');
                    link.href = url; link.setAttribute('download', match?.[1] || 'backup-env-elite.zip');
                    document.body.appendChild(link); link.click(); link.remove();
                    window.URL.revokeObjectURL(url);
                    this.submitting = false; this.closeModal();
                } catch (_) {
                    this.errorMessage = 'Terjadi kesalahan saat memproses unduhan .env.';
                } finally {
                    this.submitting = false;
                }
            },
        };
    }
</script>

<div x-data="backupEnvModalState()" x-show="isOpen" @open-env-modal.window="openModal()" @keydown.escape.window="closeModal()" x-cloak class="bk-modal-overlay" style="display: none;">
    <div class="bk-modal-card" @click.stop>
        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ri-shield-keyhole-line" style="color: #059669; font-size: 1.15rem;"></i>
                Unduh Konfigurasi .env Terenkripsi
            </h3>
            <button type="button" @click="closeModal()" style="background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.25rem; line-height: 1; padding: 0.25rem;">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <form action="{{ route('admin.backup.env-download') }}" method="POST" style="margin-top: 1rem; display: flex; flex-direction: column; gap: 1rem;">
            @csrf

            <div class="bk-form-group">
                <label class="bk-label">Password Akun Administrator</label>
                <input type="password" name="admin_password" required placeholder="Masukkan password login Anda" class="bk-input">
                <span class="bk-hint">Konfirmasi identitas admin untuk keamanan kredensial.</span>
            </div>

            <div class="bk-form-group">
                <label class="bk-label">Password Kunci ZIP (AES-256)</label>
                <input type="password" name="zip_password" required minlength="8" placeholder="Minimal 8 karakter unik" class="bk-input">
                <span class="bk-hint">Password ini akan digunakan untuk membuka file ZIP hasil download.</span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding-top: 0.5rem;">
                <button type="button" @click="closeModal()" class="bk-btn bk-btn-secondary">
                    Batal
                </button>
                <button type="submit" class="bk-btn bk-btn-primary">
                    <i class="ri-download-cloud-line"></i>
                    <span>Unduh ZIP Terenkripsi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function backupEnvModalState() {
        return {
            isOpen: false,
            openModal() {
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
            }
        };
    }
</script>

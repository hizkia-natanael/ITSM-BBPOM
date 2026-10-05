<x-user-push-helper />

<div
    x-data="bbpomPushOptInCard()"
    x-init="init()"
    x-show="visible"
    x-cloak
    class="mb-4 rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-50/90 via-white to-slate-50 p-3.5 sm:p-4 shadow-sm"
>
    <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-xl bg-emerald-100 border border-emerald-200/70 text-emerald-700 flex items-center justify-center shrink-0">
            <i class="ri-notification-badge-line text-lg"></i>
        </div>

        <div class="min-w-0 flex-1">
            <h4 class="text-sm font-bold text-slate-900 leading-tight">Aktifkan notifikasi balasan</h4>
            <p class="text-xs text-slate-600 mt-0.5 leading-relaxed" x-text="description()"></p>
            <p class="text-[11px] text-rose-600 mt-1.5 leading-snug" x-show="errorMessage !== ''" x-cloak x-text="errorMessage"></p>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button
                type="button"
                x-show="state === 'off'"
                x-cloak
                @click="enable()"
                :disabled="loading"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition-colors disabled:opacity-60"
            >
                <i class="ri-notification-3-line text-sm"></i>
                <span x-text="loading ? 'Memproses...' : 'Aktifkan'"></span>
            </button>

            <button
                type="button"
                @click="dismiss()"
                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                title="Sembunyikan"
                aria-label="Sembunyikan"
            >
                <i class="ri-close-line text-base"></i>
            </button>
        </div>
    </div>
</div>

<script>
function bbpomPushOptInCard() {
    return {
        state: 'unsupported',
        loading: false,
        dismissed: sessionStorage.getItem('bbpom_push_optin_dismissed') === '1',
        errorMessage: '',

        async init() {
            this.state = await window.bbpomUserPush.getState();
            window.bbpomUserPush.onChange((next) => { this.state = next; });
        },

        get visible() {
            if (this.dismissed) return false;
            return this.state === 'off' || this.state === 'denied';
        },

        description() {
            if (this.state === 'denied') {
                return 'Notifikasi diblokir browser. Izinkan lewat ikon gembok di bilah alamat, lalu muat ulang halaman.';
            }
            return 'Dapatkan pop-up desktop saat tim IT membalas pengaduan ini, tanpa perlu membuka halaman berulang kali.';
        },

        async enable() {
            if (this.loading) return;
            this.loading = true;
            this.errorMessage = '';
            try {
                this.state = await window.bbpomUserPush.enable();
            } catch (e) {
                this.errorMessage = 'Gagal mengaktifkan notifikasi. Coba lagi beberapa saat.';
                this.state = await window.bbpomUserPush.getState();
            } finally {
                this.loading = false;
            }
        },

        dismiss() {
            this.dismissed = true;
            sessionStorage.setItem('bbpom_push_optin_dismissed', '1');
        }
    };
}
</script>

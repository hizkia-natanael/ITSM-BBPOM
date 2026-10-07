@php
    $initialLatestId = (int) (\App\Models\Ticket::max('id') ?? 0);
    $initialTotalCount = (int) \App\Models\Ticket::count();
@endphp

<div
    class="bbpom-tickets-live-sync-wrapper"
    wire:ignore
    x-data="bbpomAdminTicketsPoller({
        checkUrl: '{{ route('admin.tickets.realtime-check') }}',
        initialLatestId: {{ $initialLatestId }},
        initialTotalCount: {{ $initialTotalCount }}
    })"
    x-init="init()"
>
    {{-- Floating Toast Notifikasi Real-time Aduan Baru --}}
    <template x-teleport="body">
        <div
            x-show="hasNewAlert"
            x-cloak
            x-transition:enter="bbpom-toast-enter"
            x-transition:enter-start="bbpom-toast-enter-start"
            x-transition:enter-end="bbpom-toast-enter-end"
            x-transition:leave="bbpom-toast-leave"
            x-transition:leave-start="bbpom-toast-leave-start"
            x-transition:leave-end="bbpom-toast-leave-end"
            class="bbpom-live-toast-container"
            role="alert"
            aria-live="assertive"
        >
            <div class="bbpom-toast-content">
                {{-- Header Toast --}}
                <div class="bbpom-toast-header">
                    <div class="bbpom-toast-icon-wrap">
                        <i class="ri-notification-3-line bbpom-toast-icon"></i>
                    </div>
                    <div class="bbpom-toast-title-box">
                        <span class="bbpom-toast-title">Aduan Baru Masuk</span>
                        <span class="bbpom-toast-badge">Real-time Sync</span>
                    </div>
                    <button
                        type="button"
                        @click="dismissAlert()"
                        class="bbpom-toast-close"
                        title="Tutup notifikasi"
                    >
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                {{-- Body Toast --}}
                <div class="bbpom-toast-body">
                    <div class="bbpom-toast-meta" x-show="latestTicketNumber">
                        <span class="bbpom-toast-code" x-text="latestTicketNumber"></span>
                        <span class="bbpom-toast-sender" x-text="latestSenderName"></span>
                    </div>
                    <p class="bbpom-toast-subject" x-text="alertSubject"></p>
                </div>

                {{-- Footer / Actions --}}
                <div class="bbpom-toast-footer">
                    <template x-if="latestEditUrl">
                        <a
                            :href="latestEditUrl"
                            class="bbpom-toast-btn-primary"
                        >
                            <span>Buka Tiket</span>
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </template>
                    <button
                        type="button"
                        @click="triggerManualRefresh()"
                        class="bbpom-toast-btn-secondary"
                    >
                        <i class="ri-refresh-line" :class="{ 'bbpom-spin': isRefreshing }"></i>
                        <span>Segarkan</span>
                    </button>
                </div>

                {{-- Auto Dismiss Progress Bar (3 Detik) --}}
                <div class="bbpom-toast-progress-track">
                    <div
                        class="bbpom-toast-progress-bar"
                        :style="hasNewAlert ? 'animation: bbpomProgressCountdown 3s linear forwards;' : ''"
                    ></div>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
/* Scoped Standalone Styling untuk Toast Notifikasi Aduan Admin (Filament-safe) */
.bbpom-live-toast-container {
    position: fixed !important;
    top: 1.25rem !important;
    right: 1.25rem !important;
    z-index: 999999 !important;
    width: 380px !important;
    max-width: calc(100vw - 2.5rem) !important;
    font-family: inherit !important;
    pointer-events: auto !important;
}

.bbpom-toast-enter {
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.bbpom-toast-enter-start {
    opacity: 0 !important;
    transform: translateY(-1rem) scale(0.96) !important;
}
.bbpom-toast-enter-end {
    opacity: 1 !important;
    transform: translateY(0) scale(1) !important;
}
.bbpom-toast-leave {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.bbpom-toast-leave-start {
    opacity: 1 !important;
    transform: translateY(0) scale(1) !important;
}
.bbpom-toast-leave-end {
    opacity: 0 !important;
    transform: translateY(-0.75rem) scale(0.96) !important;
}

.bbpom-toast-content {
    position: relative !important;
    overflow: hidden !important;
    background: rgba(15, 23, 42, 0.97) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border: 1px solid rgba(16, 185, 129, 0.4) !important;
    border-radius: 1rem !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3), 0 0 20px rgba(16, 185, 129, 0.2) !important;
    padding: 1rem 1.15rem 1.15rem 1.15rem !important;
    color: #ffffff !important;
}

.bbpom-toast-header {
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    margin-bottom: 0.65rem !important;
}

.bbpom-toast-icon-wrap {
    width: 2.25rem !important;
    height: 2.25rem !important;
    border-radius: 0.65rem !important;
    background: rgba(16, 185, 129, 0.18) !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important;
    color: #34d399 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

.bbpom-toast-icon {
    font-size: 1.15rem !important;
    line-height: 1 !important;
}

.bbpom-toast-title-box {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    flex: 1 1 0% !important;
    min-width: 0 !important;
}

.bbpom-toast-title {
    font-size: 0.825rem !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    letter-spacing: -0.01em !important;
    white-space: nowrap !important;
}

.bbpom-toast-badge {
    display: inline-flex !important;
    align-items: center !important;
    padding: 0.125rem 0.5rem !important;
    border-radius: 9999px !important;
    font-size: 0.625rem !important;
    font-weight: 700 !important;
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6ee7b7 !important;
    border: 1px solid rgba(16, 185, 129, 0.35) !important;
    letter-spacing: 0.02em !important;
    white-space: nowrap !important;
}

.bbpom-toast-close {
    width: 1.75rem !important;
    height: 1.75rem !important;
    border-radius: 0.5rem !important;
    background: transparent !important;
    border: none !important;
    color: #94a3b8 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    flex-shrink: 0 !important;
}
.bbpom-toast-close:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.1) !important;
}
.bbpom-toast-close i {
    font-size: 1rem !important;
}

.bbpom-toast-body {
    margin-bottom: 0.85rem !important;
}

.bbpom-toast-meta {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    margin-bottom: 0.35rem !important;
}

.bbpom-toast-code {
    display: inline-flex !important;
    align-items: center !important;
    padding: 0.15rem 0.45rem !important;
    border-radius: 0.375rem !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 0.6875rem !important;
    font-weight: 700 !important;
    background: rgba(16, 185, 129, 0.22) !important;
    color: #a7f3d0 !important;
    border: 1px solid rgba(16, 185, 129, 0.4) !important;
}

.bbpom-toast-sender {
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    color: #94a3b8 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

.bbpom-toast-subject {
    font-size: 0.8125rem !important;
    line-height: 1.4 !important;
    color: #e2e8f0 !important;
    font-weight: 500 !important;
    margin: 0 !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
}

.bbpom-toast-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 0.5rem !important;
    padding-top: 0.65rem !important;
    border-top: 1px solid rgba(148, 163, 184, 0.15) !important;
}

.bbpom-toast-btn-primary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
    padding: 0.375rem 0.85rem !important;
    border-radius: 0.55rem !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    background: #059669 !important;
    color: #ffffff !important;
    text-decoration: none !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
    transition: background-color 0.15s ease !important;
}
.bbpom-toast-btn-primary:hover {
    background: #047857 !important;
}
.bbpom-toast-btn-primary i {
    font-size: 0.85rem !important;
}

.bbpom-toast-btn-secondary {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
    padding: 0.375rem 0.75rem !important;
    border-radius: 0.55rem !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}
.bbpom-toast-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
}

.bbpom-toast-progress-track {
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 3px !important;
    background: rgba(16, 185, 129, 0.15) !important;
    border-bottom-left-radius: 1rem !important;
    border-bottom-right-radius: 1rem !important;
    overflow: hidden !important;
}

.bbpom-toast-progress-bar {
    height: 100% !important;
    background: #10b981 !important;
    width: 100% !important;
}

@keyframes bbpomProgressCountdown {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

.bbpom-spin {
    animation: bbpomSpinAnim 1s linear infinite !important;
}
@keyframes bbpomSpinAnim {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
window.bbpomAdminTicketsPoller = function bbpomAdminTicketsPoller(config) {
    const cfg = config || {};
    return {
        checkUrl: cfg.checkUrl || '',
        latestId: Number(cfg.initialLatestId) || 0,
        totalCount: Number(cfg.initialTotalCount) || 0,
        pollTimer: null,
        isPolling: false,
        isRefreshing: false,
        hasNewAlert: false,
        latestTicketNumber: '',
        latestSenderName: '',
        alertSubject: '',
        latestEditUrl: '',
        dismissTimer: null,

        init() {
            // Pulihkan toast jika masih dalam jendela 3 detik aktif
            if (window.__bbpomActiveAlert && window.__bbpomActiveAlert.expiresAt > Date.now()) {
                this.hasNewAlert = true;
                this.latestTicketNumber = window.__bbpomActiveAlert.latestTicketNumber || '';
                this.latestSenderName = window.__bbpomActiveAlert.latestSenderName || '';
                this.alertSubject = window.__bbpomActiveAlert.alertSubject || '';
                this.latestEditUrl = window.__bbpomActiveAlert.latestEditUrl || '';
                const remaining = Math.max(100, window.__bbpomActiveAlert.expiresAt - Date.now());
                this.dismissTimer = setTimeout(() => {
                    this.dismissAlert();
                }, remaining);
            }

            this.startPolling();
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.stopPolling();
                } else {
                    this.startPolling();
                    this.pollCheck();
                }
            });
        },

        startPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(() => {
                this.pollCheck();
            }, 3500);
        },

        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },

        playChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const now = ctx.currentTime;

                // Nada 1: E5 (659.25Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now);
                gain1.gain.setValueAtTime(0.15, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                // Nada 2: B5 (987.77Hz)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(987.77, now + 0.1);
                gain2.gain.setValueAtTime(0.18, now + 0.1);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.1);
                osc2.stop(now + 0.55);
            } catch (e) {
                // AudioContext silent fallback
            }
        },

        async pollCheck() {
            if (this.isPolling || !this.checkUrl) return;
            this.isPolling = true;

            try {
                const res = await fetch(`${this.checkUrl}?latest_id=${this.latestId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) {
                    this.isPolling = false;
                    return;
                }

                const data = await res.json();
                if (data && data.success) {
                    const newMaxId = Number(data.latest_id) || 0;
                    const isNewDetected = Boolean(data.has_new && newMaxId > this.latestId);

                    if (isNewDetected) {
                        this.latestId = newMaxId;
                        this.totalCount = Number(data.total_count) || this.totalCount;

                        const firstTicket = Array.isArray(data.new_tickets) && data.new_tickets.length > 0
                            ? data.new_tickets[0]
                            : null;

                        if (firstTicket) {
                            this.latestTicketNumber = firstTicket.ticket_number || '';
                            this.latestSenderName = firstTicket.user_name ? `oleh ${firstTicket.user_name}` : '';
                            this.alertSubject = firstTicket.subject || 'Pengaduan baru diajukan';
                            this.latestEditUrl = firstTicket.edit_url || '';
                        } else {
                            this.latestTicketNumber = '';
                            this.latestSenderName = '';
                            this.alertSubject = 'Pengaduan baru telah masuk ke sistem ITSM.';
                            this.latestEditUrl = '';
                        }

                        this.hasNewAlert = true;
                        this.playChime();

                        // Catat aktif di window agar card tahan selama 3 detik penuh tanpa hilang cepat
                        window.__bbpomActiveAlert = {
                            hasNewAlert: true,
                            latestTicketNumber: this.latestTicketNumber,
                            latestSenderName: this.latestSenderName,
                            alertSubject: this.alertSubject,
                            latestEditUrl: this.latestEditUrl,
                            expiresAt: Date.now() + 3000,
                        };

                        // Refresh tabel secara otomatis jika admin tidak sedang fokus mengetik
                        const activeEl = document.activeElement;
                        const isTyping = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA');

                        if (!isTyping) {
                            this.refreshTableComponent();
                        }

                        // Tahan kartu selama tepat 3 detik
                        if (this.dismissTimer) clearTimeout(this.dismissTimer);
                        this.dismissTimer = setTimeout(() => {
                            this.dismissAlert();
                        }, 3000);
                    } else if (newMaxId > 0 && this.latestId === 0) {
                        this.latestId = newMaxId;
                    }
                }
            } catch (e) {
                // Ignore transient network errors
            } finally {
                this.isPolling = false;
            }
        },

        refreshTableComponent() {
            this.isRefreshing = true;

            // 1. Coba refresh via Livewire component instance ($wire)
            if (typeof this.$wire !== 'undefined' && this.$wire && typeof this.$wire.$refresh === 'function') {
                this.$wire.$refresh().then(() => {
                    this.isRefreshing = false;
                }).catch(() => {
                    this.isRefreshing = false;
                });
                return;
            }

            // 2. Dispatch event global Livewire
            if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                window.Livewire.dispatch('refresh-tickets');
            }

            setTimeout(() => {
                this.isRefreshing = false;
            }, 600);
        },

        triggerManualRefresh() {
            this.refreshTableComponent();
            this.pollCheck();
        },

        dismissAlert() {
            this.hasNewAlert = false;
            window.__bbpomActiveAlert = null;
            if (this.dismissTimer) {
                clearTimeout(this.dismissTimer);
                this.dismissTimer = null;
            }
        }
    };
};
</script>

window.bbpomNotificationBell = function bbpomNotificationBell(config) {
    return {
        isOpen: false,
        count: 0,
        items: [],
        statusUrl: config.statusUrl,
        readItemUrl: config.readItemUrl,
        readAllUrl: config.readAllUrl,
        ticketsUrl: config.ticketsUrl,
        csrfToken: config.csrfToken,
        soundEnabled: localStorage.getItem('bbpom_notif_sound') !== 'disabled',
        isClaiming: false,
        claimToast: '',
        pollTimer: null,

        init() {
            this.fetchStatus(false);
            this.startPolling();
            this.registerServiceWorkerGlobal();
        },

        hasCount() {
            return Number(this.count) > 0;
        },

        hasItems() {
            return Array.isArray(this.items) && this.items.length > 0;
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchStatus(false);
            }
        },

        closeDropdown() {
            this.isOpen = false;
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('bbpom_notif_sound', this.soundEnabled ? 'enabled' : 'disabled');
            if (this.soundEnabled) {
                this.playChime();
            }
        },

        playChime() {
            if (!this.soundEnabled) return;
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const now = ctx.currentTime;

                // Note 1: E5 (659.25Hz)
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

                // Note 2: B5 (987.77Hz)
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

        async fetchStatus(triggerSound) {
            try {
                const res = await fetch(this.statusUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();
                const previousCount = Number(this.count);
                const newCount = Number(data.count);

                this.count = newCount;
                this.items = data.items || [];

                if (triggerSound && newCount > previousCount) {
                    this.playChime();
                }
            } catch (e) {
                // Polling fail silent
            }
        },

        startPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(() => {
                this.fetchStatus(true);
            }, 15000);
        },

        async onCardClick(item, event) {
            const url = item.url || item.edit_url;
            if (!url) return;

            await this.clickItem(item, event);

            if (event && (event.ctrlKey || event.metaKey || event.button === 1)) {
                window.open(url, '_blank');
                return;
            }

            window.location.href = url;
        },

        async clickItem(item, event) {
            if (!item.is_read) {
                item.is_read = true;
                if (Number(this.count) > 0) {
                    this.count = Number(this.count) - 1;
                }
                try {
                    fetch(this.readItemUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({
                            item_type: item.item_type,
                            id: item.raw_id
                        }),
                        keepalive: true
                    });
                } catch (e) {
                    // Abaikan kesalahan koneksi
                }
            }
        },

        async markAllRead() {
            try {
                await fetch(this.readAllUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });
            } catch (e) {
                // Abaikan kesalahan koneksi
            }
            await this.fetchStatus(false);
        },

        async claimTicketDirect(ticketId) {
            if (this.isClaiming) return;
            this.isClaiming = true;
            this.claimToast = '';

            try {
                const res = await fetch('/admin/tickets/' + ticketId + '/claim', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.claimToast = data.message;
                    await this.fetchStatus(false);
                    setTimeout(() => {
                        if (data.edit_url) {
                            window.location.href = data.edit_url;
                        }
                    }, 600);
                } else {
                    alert(data.message || 'Gagal mengambil alih tiket.');
                    await this.fetchStatus(false);
                }
            } catch (e) {
                alert('Terjadi kesalahan saat memproses klaim tiket.');
            } finally {
                this.isClaiming = false;
            }
        },

        async registerServiceWorkerGlobal() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
            try {
                const reg = await navigator.serviceWorker.register('/sw.js');
                await navigator.serviceWorker.ready;
                if (('Notification' in window) && Notification.permission === 'granted') {
                    const sub = await reg.pushManager.getSubscription();
                    if (sub) {
                        const subData = JSON.parse(JSON.stringify(sub));
                        await fetch('/admin/push/subscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                endpoint: sub.endpoint,
                                publicKey: subData.keys ? subData.keys.p256dh : null,
                                authToken: subData.keys ? subData.keys.auth : null,
                                contentEncoding: (window.PushManager && PushManager.supportedContentEncodings || ['aes128gcm'])[0]
                            })
                        });
                    }
                }
            } catch (err) {
                // Service worker registration silent fallback
            }
        }
    };
};

@props([
    'unhandledTickets' => collect(),
    'unhandledCount' => 0,
])

<style>
.bbpom-alert-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.bbpom-alert-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 9999px;
}
.bbpom-alert-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
.bbpom-alert-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<div style="flex: 1 1 0%; display: flex; flex-direction: column; justify-content: space-between; min-height: 0;">
    {{-- Card Body: Ticket List / Empty State --}}
    <div style="flex: 1 1 0%; max-height: 255px; overflow-y: auto; padding-right: 0.25rem;" class="bbpom-alert-scrollbar">
        @if ($unhandledCount > 0)
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach ($unhandledTickets as $ticket)
                    <div
                        style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.625rem; padding: 0.65rem 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03); transition: all 0.15s ease;"
                        onmouseover="this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';"
                        onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 1px 2px rgba(0,0,0,0.03)';"
                    >
                        {{-- Baris 1: No Tiket & Kategori --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.4rem; margin-bottom: 0.35rem;">
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <span style="font-family: monospace; font-size: 0.725rem; font-weight: 700; color: #065f46; background-color: #ecfdf5; padding: 0.1rem 0.4rem; border-radius: 0.35rem; border: 1px solid #a7f3d0;">
                                    {{ $ticket->ticket_number }}
                                </span>
                                <span style="font-size: 0.65rem; background-color: #f1f5f9; color: #475569; padding: 0.1rem 0.35rem; border-radius: 0.25rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $ticket->category?->name ?? 'Layanan TI' }}
                                </span>
                            </div>
                        </div>

                        {{-- Baris 2: Subjek Kendala --}}
                        <h4 style="margin: 0 0 0.3rem 0; font-size: 0.775rem; font-weight: 700; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $ticket->subject }}">
                            {{ $ticket->subject }}
                        </h4>

                        {{-- Baris 3: Pelapor --}}
                        <div style="display: flex; align-items: center; gap: 0.3rem; font-size: 0.6875rem; color: #64748b; margin-bottom: 0.45rem;">
                            <i class="ri-user-3-line" style="color: #94a3b8; font-size: 0.75rem;"></i>
                            <span style="font-weight: 500; color: #475569; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $ticket->user?->name ?? 'Pegawai' }}</span>
                        </div>

                        {{-- Baris 4: Tombol Aksi --}}
                        <div style="display: flex; gap: 0.35rem; border-top: 1px solid #f8fafc; padding-top: 0.4rem;">
                            <button
                                type="button"
                                wire:click="claimTicket({{ $ticket->id }})"
                                style="flex: 1 1 0%; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; background-color: #059669; color: #ffffff; padding: 0.3rem 0.5rem; border: none; border-radius: 0.4rem; font-size: 0.7125rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;"
                                onmouseover="this.style.backgroundColor='#047857';"
                                onmouseout="this.style.backgroundColor='#059669';"
                                title="Ambil alih aduan ini langsung ke akun Anda"
                            >
                                <i class="ri-hand-coin-line" style="font-size: 0.8125rem;"></i>
                                <span>Tangani Sekarang</span>
                            </button>
                            <a
                                href="{{ url('/admin/tickets/' . $ticket->id . '/edit') }}"
                                style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1; border-radius: 0.4rem; text-decoration: none; transition: all 0.15s ease;"
                                onmouseover="this.style.backgroundColor='#e2e8f0'; this.style.color='#0f172a';"
                                onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#475569';"
                                title="Buka rincian tiket di tab editor"
                            >
                                <i class="ri-external-link-line" style="font-size: 0.8125rem;"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="height: 100%; min-height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 1.5rem 0.5rem;">
                <div style="width: 3rem; height: 3rem; border-radius: 9999px; background-color: #ecfdf5; border: 1px solid #a7f3d0; display: flex; align-items: center; justify-content: center; margin-bottom: 0.65rem; color: #059669;">
                    <i class="ri-shield-check-line" style="font-size: 1.65rem;"></i>
                </div>
                <h4 style="margin: 0 0 0.2rem 0; font-size: 0.8125rem; font-weight: 700; color: #064e3b;">
                    Antrean Aduan Terkendali
                </h4>
                <p style="margin: 0; font-size: 0.7125rem; color: #64748b; max-width: 14rem; line-height: 1.4;">
                    Tidak ada aduan yang menunggu penanganan Admin TIK saat ini.
                </p>
            </div>
        @endif
    </div>

    {{-- Card Footer: Desktop OS Web Push Controller --}}
    <div
        id="bbpom-desktop-push-banner"
        x-data="desktopPushController()"
        x-init="initPush()"
        style="padding: 0.5rem 0.65rem; border-top: 1px solid #f1f5f9; background-color: #f8fafc; border-radius: 0.5rem; margin-top: 0.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.4rem;"
    >
        <div style="display: flex; align-items: center; gap: 0.35rem;">
            <div
                style="display: flex; align-items: center; justify-content: center; width: 1.25rem; height: 1.25rem; border-radius: 0.35rem;"
                :style="isSubscribed ? 'background-color: #d1fae5; color: #047857;' : 'background-color: #e2e8f0; color: #64748b;'"
            >
                <i :class="isSubscribed ? 'ri-notification-3-fill' : 'ri-notification-off-line'" style="font-size: 0.75rem;"></i>
            </div>
            <div style="font-size: 0.6875rem;">
                <span style="font-weight: 600; color: #475569;">Push Desktop:</span>
                <span
                    style="font-weight: 700; margin-left: 0.15rem;"
                    :style="isSubscribed ? 'color: #047857;' : 'color: #64748b;'"
                    x-text="isSubscribed ? 'Aktif' : 'Nonaktif'"
                ></span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.3rem;">
            <span
                x-show="testMessage"
                x-text="testMessage"
                style="font-size: 0.65rem; color: #047857; font-weight: 700; margin-right: 0.15rem;"
            ></span>

            <button
                type="button"
                @click="triggerTestPush()"
                :disabled="isTesting"
                style="display: inline-flex; align-items: center; gap: 0.2rem; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.18rem 0.45rem; font-size: 0.65rem; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.15s ease;"
                onmouseover="this.style.backgroundColor='#f1f5f9';"
                onmouseout="this.style.backgroundColor='#ffffff';"
            >
                <i class="ri-send-plane-line" style="font-size: 0.6875rem;"></i>
                <span x-text="isTesting ? 'Menguji...' : 'Uji Pop-up'"></span>
            </button>

            <button
                type="button"
                @click="togglePush()"
                :disabled="isLoading"
                style="display: inline-flex; align-items: center; gap: 0.2rem; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.18rem 0.45rem; font-size: 0.65rem; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.15s ease;"
                onmouseover="this.style.backgroundColor='#f1f5f9';"
                onmouseout="this.style.backgroundColor='#ffffff';"
            >
                <i :class="isSubscribed ? 'ri-close-line' : 'ri-checkbox-circle-line'" style="font-size: 0.6875rem;"></i>
                <span x-text="isLoading ? '...' : (isSubscribed ? 'Nonaktifkan' : 'Aktifkan')"></span>
            </button>
        </div>
    </div>
</div>

<script>
function desktopPushController() {
    return {
        isSubscribed: false,
        isLoading: false,
        isTesting: false,
        testMessage: '',
        swRegistration: null,
        vapidPublicKey: '{{ config('webpush.vapid.public_key') }}',

        async initPush() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                return;
            }

            try {
                this.swRegistration = await navigator.serviceWorker.register('/sw.js');
                await navigator.serviceWorker.ready;
                const subscription = await this.swRegistration.pushManager.getSubscription();
                this.isSubscribed = Boolean(subscription);
                if (subscription && ('Notification' in window) && Notification.permission === 'granted') {
                    await this.syncSubscription(subscription);
                }
            } catch (err) {
                console.error('Service worker registration failed:', err);
            }
        },

        async syncSubscription(sub) {
            try {
                const subData = JSON.parse(JSON.stringify(sub));
                await fetch('/admin/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        endpoint: sub.endpoint,
                        publicKey: subData.keys ? subData.keys.p256dh : null,
                        authToken: subData.keys ? subData.keys.auth : null,
                        contentEncoding: (window.PushManager && PushManager.supportedContentEncodings || ['aes128gcm'])[0]
                    })
                });
            } catch (e) {
                console.warn('Sync subscription warning:', e);
            }
        },

        async togglePush() {
            if (this.isSubscribed) {
                await this.unsubscribePush();
            } else {
                await this.subscribePush();
            }
        },

        async subscribePush() {
            this.isLoading = true;
            try {
                if (!('Notification' in window)) {
                    alert('Browser Anda tidak mendukung notifikasi desktop.');
                    this.isLoading = false;
                    return;
                }

                if (Notification.permission === 'denied') {
                    alert('Izin notifikasi diblokir oleh browser. Silakan klik ikon gembok / setelan situs di sebelah kiri bilah URL browser Anda untuk mengubah Notifikasi menjadi "Izinkan".');
                    this.isLoading = false;
                    return;
                }

                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    alert('Izin notifikasi ditolak oleh browser. Mohon izinkan notifikasi pada pengaturan browser Anda.');
                    this.isLoading = false;
                    return;
                }

                if (!this.swRegistration) {
                    this.swRegistration = await navigator.serviceWorker.register('/sw.js');
                }
                const reg = await navigator.serviceWorker.ready;
                this.swRegistration = reg;

                let sub = await reg.pushManager.getSubscription();
                if (!sub) {
                    sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
                    });
                }

                const subData = JSON.parse(JSON.stringify(sub));
                const response = await fetch('/admin/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        endpoint: sub.endpoint,
                        publicKey: subData.keys ? subData.keys.p256dh : null,
                        authToken: subData.keys ? subData.keys.auth : null,
                        contentEncoding: (window.PushManager && PushManager.supportedContentEncodings || ['aes128gcm'])[0]
                    })
                });

                const result = await response.json();
                if (result.success) {
                    this.isSubscribed = true;
                    if (reg && reg.showNotification) {
                        reg.showNotification('Notifikasi Desktop BBPOM Aktif', {
                            body: 'Anda akan menerima pop-up desktop saat ada aduan baru yang belum ditangani.',
                            icon: '/images/logo-bpom.png',
                            badge: '/images/logo-bpom.png',
                            data: { url: '/admin' }
                        });
                    }
                }
            } catch (err) {
                console.error('Subscription error:', err);
                alert('Gagal mengaktifkan notifikasi: ' + err.message);
            } finally {
                this.isLoading = false;
            }
        },

        async unsubscribePush() {
            this.isLoading = true;
            try {
                if (this.swRegistration && this.swRegistration.pushManager) {
                    const subscription = await this.swRegistration.pushManager.getSubscription();
                    if (subscription) {
                        await fetch('/admin/push/unsubscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ endpoint: subscription.endpoint })
                        });
                        await subscription.unsubscribe();
                    }
                }
                this.isSubscribed = false;
            } catch (err) {
                console.error('Unsubscribe error:', err);
            } finally {
                this.isLoading = false;
            }
        },

        async triggerTestPush() {
            if (!('Notification' in window)) {
                alert('Browser Anda tidak mendukung notifikasi desktop.');
                return;
            }

            if (Notification.permission === 'denied') {
                alert('Izin notifikasi diblokir oleh browser. Silakan klik ikon gembok / setelan situs di sebelah kiri bilah URL browser Anda dan ubah Notifikasi menjadi "Izinkan", lalu coba lagi.');
                return;
            }

            this.isTesting = true;
            this.testMessage = '';

            try {
                if (Notification.permission !== 'granted') {
                    const requested = await Notification.requestPermission();
                    if (requested !== 'granted') {
                        alert('Izin notifikasi belum diizinkan. Mohon pilih "Izinkan" saat browser meminta izin notifikasi.');
                        this.isTesting = false;
                        return;
                    }
                }

                let reg = this.swRegistration;
                if (!reg && ('serviceWorker' in navigator)) {
                    await navigator.serviceWorker.register('/sw.js');
                    reg = await navigator.serviceWorker.ready;
                    this.swRegistration = reg;
                }

                let sub = null;
                if (reg && reg.pushManager) {
                    sub = await reg.pushManager.getSubscription();
                    if (!sub && this.vapidPublicKey) {
                        try {
                            sub = await reg.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
                            });
                        } catch (subErr) {
                            console.warn('Subscription subscribe warning:', subErr);
                        }
                    }
                    if (sub) {
                        this.isSubscribed = true;
                        await this.syncSubscription(sub);
                    }
                }

                const notifTitle = 'Uji Notifikasi Pop-up Desktop BBPOM';
                const notifOptions = {
                    body: 'Tes notifikasi pop-up desktop BBPOM berhasil diterima! Sistem siap menampilkan aduan masuk.',
                    icon: '/images/logo-bpom.png',
                    badge: '/images/logo-bpom.png',
                    tag: 'bbpom-test-popup-' + Date.now(),
                    renotify: true,
                    requireInteraction: true,
                    data: { url: '/admin' }
                };

                // Play audio chime for instant sensory confirmation
                this.playChime();

                // 1. Service Worker Notification (routes through OS Notification Center)
                if (reg && typeof reg.showNotification === 'function') {
                    try {
                        await reg.showNotification(notifTitle, notifOptions);
                    } catch (swErr) {
                        console.warn('SW showNotification warning:', swErr);
                    }
                }

                // 2. Direct Window Notification (routes through browser window/desktop toast)
                if (typeof Notification === 'function' && Notification.permission === 'granted') {
                    try {
                        new Notification(notifTitle, {
                            body: notifOptions.body,
                            icon: notifOptions.icon,
                            requireInteraction: true,
                        });
                    } catch (winErr) {
                        console.warn('Window Notification fallback error:', winErr);
                    }
                }

                const payload = sub ? {
                    endpoint: sub.endpoint,
                    publicKey: sub.toJSON().keys ? sub.toJSON().keys.p256dh : null,
                    authToken: sub.toJSON().keys ? sub.toJSON().keys.auth : null,
                    contentEncoding: (window.PushManager && PushManager.supportedContentEncodings || ['aes128gcm'])[0]
                } : {};

                const res = await fetch('/admin/push/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                console.log('Push test response:', data);

                if (data.sentCount > 0) {
                    this.testMessage = 'API Sukses (' + data.sentCount + ' perangkat)!';
                } else {
                    this.testMessage = 'API merespons (0 perangkat)';
                }
                setTimeout(() => { this.testMessage = ''; }, 6000);
            } catch (err) {
                console.error('Test push error:', err);
                alert('Gagal menguji pop-up desktop: ' + err.message);
            } finally {
                this.isTesting = false;
            }
        },

        playChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {
                // Ignore audio autoplay restrictions if any
            }
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    };
}
</script>

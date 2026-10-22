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
                this.swRegistration = await navigator.serviceWorker.register('{{ asset('sw.js') }}');
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
                await fetch('{{ url('/admin/push/subscribe') }}', {
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
                    this.swRegistration = await navigator.serviceWorker.register('{{ asset('sw.js') }}');
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
                const response = await fetch('{{ url('/admin/push/subscribe') }}', {
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

                if (response.ok) {
                    this.isSubscribed = true;
                }
            } catch (err) {
                console.error('Subscribe push error:', err);
                alert('Gagal mengaktifkan notifikasi: ' + err.message);
            } finally {
                this.isLoading = false;
            }
        },

        async unsubscribePush() {
            this.isLoading = true;
            try {
                if (!this.swRegistration) {
                    this.swRegistration = await navigator.serviceWorker.register('{{ asset('sw.js') }}');
                }
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.getSubscription();
                if (sub) {
                    await fetch('{{ url('/admin/push/unsubscribe') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ endpoint: sub.endpoint })
                    });
                    await sub.unsubscribe();
                }
                this.isSubscribed = false;
            } catch (err) {
                console.error('Unsubscribe push error:', err);
                alert('Gagal menonaktifkan notifikasi: ' + err.message);
            } finally {
                this.isLoading = false;
            }
        },

        async triggerTestPush() {
            this.isTesting = true;
            this.testMessage = '';

            try {
                if (!('Notification' in window)) {
                    alert('Browser Anda tidak mendukung notifikasi desktop.');
                    this.isTesting = false;
                    return;
                }

                if (Notification.permission !== 'granted') {
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') {
                        alert('Izin notifikasi belum diberikan. Silakan aktifkan izin notifikasi.');
                        this.isTesting = false;
                        return;
                    }
                }

                if (!this.swRegistration) {
                    this.swRegistration = await navigator.serviceWorker.register('{{ asset('sw.js') }}');
                }
                const reg = await navigator.serviceWorker.ready;
                this.swRegistration = reg;

                let sub = await reg.pushManager.getSubscription();
                if (!sub) {
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

                const notifTitle = 'Uji Notifikasi Pop-up Desktop BBPOM';
                const notifOptions = {
                    body: 'Tes notifikasi pop-up desktop BBPOM berhasil diterima! Sistem siap menampilkan aduan masuk.',
                    icon: '{{ asset('images/logo-bpom.png') }}',
                    badge: '{{ asset('images/logo-bpom.png') }}',
                    tag: 'bbpom-test-popup-' + Date.now(),
                    renotify: true,
                    requireInteraction: true,
                    data: { url: '{{ url('/admin') }}' }
                };

                this.playChime();

                if (reg && typeof reg.showNotification === 'function') {
                    try {
                        await reg.showNotification(notifTitle, notifOptions);
                    } catch (swErr) {
                        console.warn('SW showNotification warning:', swErr);
                    }
                }

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

                const res = await fetch('{{ url('/admin/push/test') }}', {
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

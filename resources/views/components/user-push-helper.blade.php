{{-- Shared Web Push opt-in helper for employee portal (dipakai lonceng navbar dan kartu aktivasi). --}}
@once
<script>
window.bbpomUserPush = window.bbpomUserPush || {
    configUrl: '{{ route('user.push.config') }}',
    subscribeUrl: '{{ route('user.push.subscribe') }}',
    unsubscribeUrl: '{{ route('user.push.unsubscribe') }}',
    csrfToken: '{{ csrf_token() }}',
    listeners: [],

    onChange(callback) {
        this.listeners.push(callback);
    },

    notify(state) {
        this.listeners.forEach(function (callback) { callback(state); });
    },

    supported() {
        return ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
    },

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const output = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            output[i] = rawData.charCodeAt(i);
        }
        return output;
    },

    async post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify(body)
        });
    },

    async syncToServer(sub) {
        const data = JSON.parse(JSON.stringify(sub));
        await this.post(this.subscribeUrl, {
            endpoint: sub.endpoint,
            publicKey: data.keys ? data.keys.p256dh : null,
            authToken: data.keys ? data.keys.auth : null,
            contentEncoding: (window.PushManager && PushManager.supportedContentEncodings || ['aes128gcm'])[0]
        });
    },

    /** Returns one of: unsupported, denied, on, off. */
    async getState() {
        if (!this.supported()) return 'unsupported';
        if (Notification.permission === 'denied') return 'denied';
        try {
            const reg = await navigator.serviceWorker.register('{{ asset('sw.js') }}');
            await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            if (sub && Notification.permission === 'granted') {
                await this.syncToServer(sub);
                return 'on';
            }
        } catch (e) {
            return 'off';
        }
        return 'off';
    },

    async enable() {
        if (!this.supported()) return 'unsupported';
        if (Notification.permission === 'denied') return 'denied';

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return permission === 'denied' ? 'denied' : 'off';

        const configRes = await fetch(this.configUrl, { headers: { 'Accept': 'application/json' } });
        const config = await configRes.json();

        await navigator.serviceWorker.register('{{ asset('sw.js') }}');
        const reg = await navigator.serviceWorker.ready;

        let sub = await reg.pushManager.getSubscription();
        if (!sub) {
            sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(config.publicKey)
            });
        }
        await this.syncToServer(sub);

        if (reg.showNotification) {
            reg.showNotification('Notifikasi Balasan Aktif', {
                body: 'Anda akan diberi tahu saat tim IT membalas pengaduan Anda.',
                icon: '{{ asset('images/logo-bpom.png') }}',
                badge: '{{ asset('images/logo-bpom.png') }}',
                tag: 'user-push-enabled',
                data: { url: window.location.href }
            });
        }

        this.notify('on');
        return 'on';
    },

    async disable() {
        try {
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            if (sub) {
                await this.post(this.unsubscribeUrl, { endpoint: sub.endpoint });
                await sub.unsubscribe();
            }
        } catch (e) {
            // Biarkan senyap; status akan dibaca ulang.
        }
        this.notify('off');
        return 'off';
    }
};
</script>
@endonce

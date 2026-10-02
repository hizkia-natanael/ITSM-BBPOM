/* BBPOM Semarang ITSM - Web Push Notification Service Worker */

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
    let payload = {
        title: 'Aduan Masuk Belum Ditangani!',
        body: 'Terdapat aduan TI baru yang belum ditangani oleh Admin 1 atau Admin 2.',
        icon: '/images/logo-bpom.png',
        badge: '/images/logo-bpom.png',
        tag: 'bbpom-unhandled-ticket',
        data: {
            url: '/admin'
        }
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            payload = Object.assign({}, payload, parsed);
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const options = {
        body: payload.body,
        icon: payload.icon || '/images/logo-bpom.png',
        badge: payload.badge || '/images/logo-bpom.png',
        tag: payload.tag || 'bbpom-unhandled-ticket',
        renotify: true,
        requireInteraction: true,
        vibrate: [200, 100, 200],
        data: payload.data || { url: '/admin' }
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url)
        ? event.notification.data.url
        : '/admin';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes('/admin') && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

self.addEventListener('push', function(event) {
    var data = event.data ? event.data.json() : {};
    var title = data.title || 'Forfatterskolen';
    var options = {
        body: data.body || '',
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        data: { url: data.url || '/' }
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    var url = event.notification.data.url || '/';
    event.waitUntil(clients.openWindow(url));
});

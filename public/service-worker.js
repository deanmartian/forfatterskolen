/*
 * KILL-SWITCH SERVICE WORKER
 * =========================
 *
 * Deployed 08.04.2026 to fix a nasty caching issue where users (first
 * reported by Bridgitt S. Lee) had an old Workbox-generated service
 * worker stuck in their browser that pre-cached /js/app.js and
 * /css/app.css from months ago — serving outdated assets on every
 * page load and making login impossible no matter how hard they
 * refreshed.
 *
 * What this SW does:
 *   1. Takes over ALL pages immediately (skipWaiting + clientsClaim)
 *   2. Deletes EVERY cache storage key
 *   3. Unregisters itself so the browser stops using any service worker
 *   4. Tells every open tab to reload
 *
 * After this runs once per user, their browser is clean. Next time we
 * deploy a proper Workbox SW (via npm run prod), they get a fresh start.
 *
 * Push notification subscriptions from the old SW will stop working
 * because the old SW no longer exists — users will re-subscribe next
 * time they enable notifications. This is an acceptable trade-off.
 */

self.addEventListener('install', function (event) {
    // Activate immediately without waiting for old SW to finish
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil((async function () {
        try {
            // 1. Claim all open pages so this SW controls them immediately
            if (self.clients && self.clients.claim) {
                await self.clients.claim();
            }

            // 2. Delete every cache storage key (precache, runtime, push, etc.)
            if (self.caches && self.caches.keys) {
                var keys = await self.caches.keys();
                await Promise.all(keys.map(function (key) {
                    return self.caches.delete(key);
                }));
            }

            // 3. Unregister this service worker — browser will stop using any SW
            if (self.registration && self.registration.unregister) {
                await self.registration.unregister();
            }

            // 4. Force-reload every tab so the user gets fresh HTML/JS/CSS
            //    from the network on their next request. This is the magic
            //    step that fixes users who were stuck.
            var clientList = await self.clients.matchAll({
                type: 'window',
                includeUncontrolled: true
            });
            for (var i = 0; i < clientList.length; i++) {
                try {
                    clientList[i].navigate(clientList[i].url);
                } catch (e) {
                    // Some browsers don't allow navigate on all clients — ignore
                }
            }
        } catch (err) {
            // Swallow errors — we want this to always succeed as much as possible
        }
    })());
});

// Explicitly do NOT handle fetch events. Let everything go to the network.
// If we did handle them, we'd just be re-implementing a cache layer, and
// the whole point of the kill switch is to stop caching.

// Explicitly do NOT handle push events either. Push subscriptions from the
// old SW are now orphaned — users must re-subscribe via the UI on their
// next visit.

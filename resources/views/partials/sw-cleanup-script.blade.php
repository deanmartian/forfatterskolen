{{--
    Inline service worker cleanup-script.

    Kjøres FØR alt annet i <head> og bruker localStorage-flagget
    sw_cleanup_v3 til å sikre at den bare kjører én gang per browser.

    Brukes på alle layouts og auth-views slik at stuck-brukere får
    kjørt opprydding på første sidebesøk uansett hvor de havner.
--}}
<script>
(function () {
    try {
        if (localStorage.getItem('sw_cleanup_v3') === '1') return;
    } catch (e) {}

    if (!('serviceWorker' in navigator)) {
        try { localStorage.setItem('sw_cleanup_v3', '1'); } catch (e) {}
        return;
    }

    navigator.serviceWorker.getRegistrations().then(function (regs) {
        var hadOld = regs.length > 0;
        var unregisters = regs.map(function (r) { return r.unregister(); });

        Promise.all(unregisters).then(function () {
            var clearCaches = (window.caches && caches.keys)
                ? caches.keys().then(function (keys) {
                    return Promise.all(keys.map(function (k) { return caches.delete(k); }));
                  })
                : Promise.resolve();

            clearCaches.then(function () {
                try { localStorage.setItem('sw_cleanup_v3', '1'); } catch (e) {}
                if (hadOld) {
                    window.location.reload();
                }
            });
        });
    }).catch(function () {
        try { localStorage.setItem('sw_cleanup_v3', '1'); } catch (e) {}
    });
})();
</script>

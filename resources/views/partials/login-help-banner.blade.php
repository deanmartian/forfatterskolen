{{--
    Login-hjelp-banner — synlig rød stripe øverst på alle sider.
    Lar brukere selv tømme cookies/cache/service worker med ett klikk
    hvis de har innloggingsproblemer.

    Kan skjules ved å klikke X, og forblir skjult i 24 timer via localStorage.

    Inkluderes fra frontend/layout.blade.php og editor/layout.blade.php.
--}}
<div id="loginHelpBanner" style="display:none; position:relative; background:#862736; color:#fff; padding:10px 56px 10px 20px; text-align:center; font-size:13px; font-family:-apple-system,sans-serif; z-index:9999;">
    <span style="margin-right:14px;">
        <i class="fa fa-info-circle"></i>
        Har du problemer med å logge inn eller komme gjennom checkout? Prøv å tømme cookies for denne siden.
    </span>
    <button type="button" id="loginHelpBannerFix"
            style="background:#fff; color:#862736; border:none; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap;">
        Tøm cookies og cache
    </button>
    <button type="button" id="loginHelpBannerClose" aria-label="Lukk"
            style="position:absolute; right:16px; top:50%; transform:translateY(-50%); background:transparent; color:#fff; border:none; font-size:18px; cursor:pointer; opacity:0.8; line-height:1;">
        &times;
    </button>
</div>

<script>
(function () {
    var banner = document.getElementById('loginHelpBanner');
    if (!banner) return;

    // Skjul banneret i 24 timer hvis brukeren har lukket det
    var dismissedAt = 0;
    try {
        dismissedAt = parseInt(localStorage.getItem('loginHelpBanner_dismissed') || '0', 10);
    } catch (e) {}

    var now = Date.now();
    if (dismissedAt && (now - dismissedAt) < 24 * 60 * 60 * 1000) {
        // Fortsatt innenfor 24-timers skjuling
        return;
    }

    // Vis banneret
    banner.style.display = 'block';

    // Lukke-knapp — skjul i 24 timer
    var closeBtn = document.getElementById('loginHelpBannerClose');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            banner.style.display = 'none';
            try { localStorage.setItem('loginHelpBanner_dismissed', String(Date.now())); } catch (e) {}
        });
    }

    // Fiks-knapp — tøm cookies, cache, service worker og reload
    var fixBtn = document.getElementById('loginHelpBannerFix');
    if (fixBtn) {
        fixBtn.addEventListener('click', function () {
            fixBtn.disabled = true;
            fixBtn.textContent = 'Rydder opp...';

            // 1. Slett alle cookies for dette domenet
            try {
                document.cookie.split(';').forEach(function (c) {
                    var eq = c.indexOf('=');
                    var name = (eq > -1 ? c.substr(0, eq) : c).trim();
                    if (!name) return;
                    // Slett på rot og alle paths
                    var hostname = window.location.hostname;
                    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
                    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=' + hostname;
                    // Også på parent-domenet (.forfatterskolen.no)
                    var parts = hostname.split('.');
                    if (parts.length > 2) {
                        var parent = '.' + parts.slice(-2).join('.');
                        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=' + parent;
                    }
                });
            } catch (e) {}

            // 2. Tøm localStorage og sessionStorage
            try { localStorage.clear(); } catch (e) {}
            try { sessionStorage.clear(); } catch (e) {}

            // 3. Unregistrer service workers og slett alle caches
            var swPromise = ('serviceWorker' in navigator)
                ? navigator.serviceWorker.getRegistrations().then(function (regs) {
                    return Promise.all(regs.map(function (r) { return r.unregister(); }));
                }).catch(function () {})
                : Promise.resolve();

            var cachePromise = (window.caches && caches.keys)
                ? caches.keys().then(function (keys) {
                    return Promise.all(keys.map(function (k) { return caches.delete(k); }));
                }).catch(function () {})
                : Promise.resolve();

            Promise.all([swPromise, cachePromise]).then(function () {
                // 4. Reload til login-siden med cache-busting param
                var sep = window.location.pathname.indexOf('?') > -1 ? '&' : '?';
                window.location.href = '/?fresh=' + Date.now();
            });
        });
    }
})();
</script>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
    <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
    @include('frontend.partials._meta')

    @yield('title')

    @yield('metas')

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />

    @include('frontend.partials.frontend-css')
    <link rel="stylesheet" href="{{asset('css/learner.css?v='.time())}}">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
              integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    @yield('styles')

    @include('frontend.partials._learner_head_scripts')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18021112843"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-18021112843');
    </script>

    <!-- Meta Pixel Code -->
    @if(config('services.tracking.enabled'))
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ config('services.meta_pixel.id') }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ config('services.meta_pixel.id') }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    @endif
</head>
<body>

    @if(Session::has('new_user_social'))
        <div class="alert alert-success" role="alert" id="fixed_to_bottom_alert">
            Thank you. The default password is 123. Please update your password
            <a href="{{ route('learner.profile') }}">here</a>.
        </div>
    @endif

    <?php
        $shopManuscriptAdvisory = \App\Http\FrontendHelpers::getShopManuscriptAdvisory();
        $from_date              = \Carbon\Carbon::parse($shopManuscriptAdvisory->from_date);
        $to_date                = \Carbon\Carbon::parse($shopManuscriptAdvisory->to_date);
        $isBetweenDate          = \Carbon\Carbon::today()->between($from_date, $to_date);
        $included_pages         = unserialize($shopManuscriptAdvisory->page_included);
    ?>
    {{-- check if advisory could be displayed today and current page is included --}}
    @if($isBetweenDate && in_array(Route::currentRouteName(), $included_pages))
        <div class="alert shop-manuscript-advisory" role="alert" id="fixed_to_bottom_alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Lukk"></button>
            {{ $shopManuscriptAdvisory->advisory }}
        </div>
    @endif

    @include('frontend.partials._learner_sidebar')

    <div id="main-container" class="enlarge">
        @include('frontend.partials._learner_topbar')

        <div id="main-content">
            @yield('content')
        </div>

        {{-- @include('frontend.partials.home-footer-new') --}}

        @if($errors->count())
        <?php
            $alert_type = session('alert_type');
            if(!Session::has('alert_type')) {
                $alert_type = 'danger';
            }
        ?>
            <div class="alert alert-{{ $alert_type }} global-alert-box" style="z-index: 9; min-width: 300px"
                 id="fixed_to_bottom_alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Lukk"></button>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div> <!-- end #main-container -->

@include('frontend.partials.scripts')
<script src="https://Forfatterskolen.cdn.vooplayer.com/assets/vooplayer.js" defer></script>
<script src="/js/lang.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cacheValue = Date.now().toString();
        const links = document.querySelectorAll('a[href]');

        links.forEach((link) => {
            const href = link.getAttribute('href');

            if (!href || href.startsWith('#')) {
                return;
            }

            const lowerHref = href.toLowerCase();
            if (
                lowerHref.startsWith('mailto:') ||
                lowerHref.startsWith('tel:') ||
                lowerHref.startsWith('javascript:')
            ) {
                return;
            }

            const hasDownloadAttr = link.hasAttribute('download');
            const hasDownloadClass = String(link.className || '').toLowerCase().includes('download');
            const isDownloadHref =
                lowerHref.includes('/download') ||
                lowerHref.includes('dropbox/download') ||
                lowerHref.includes('/storage/') ||
                /\.[a-z0-9]{2,5}([?#]|$)/i.test(lowerHref);

            if (!hasDownloadAttr && !hasDownloadClass && !isDownloadHref) {
                return;
            }

            const url = new URL(href, window.location.origin);
            url.searchParams.set('v', cacheValue);
            link.href = url.toString();
        });
    });
</script>
<script>

    var sidebar = $("#sidebar");
    var mainContainer = $("#main-container");

    checkWindowWidth();

    // Add an event listener for the window resize event
    window.addEventListener('resize', handleResize);

    // Toggle sidebar — attributt-selector matcher ALLE elementer med id="sidebarCollapse"
    // (jQuery #id matcher bare det første, men [id=...] matcher alle duplikater)
    $("[id='sidebarCollapse'], [data-sidebar-toggle]").click(function (e) {
        e.stopPropagation(); // Hindrer #main-content click fra å lukke med en gang
        sidebar.toggleClass("sidebar-visible");
        mainContainer.toggleClass("enlarge");
        $("body").toggleClass("sidebar-open");
    });

    // Lukk sidebar ved klikk utenfor — fanger klikk på #main-content OG body::after overlay
    $(document).on("click", function(e) {
        if (!sidebar.hasClass("sidebar-visible")) return;
        // Ikke lukk hvis klikket var på sidebar eller en toggle-knapp
        if ($(e.target).closest("#sidebar, [id='sidebarCollapse'], [data-sidebar-toggle]").length) return;
        sidebar.removeClass("sidebar-visible");
        mainContainer.removeClass("enlarge");
        $("body").removeClass("sidebar-open");
    });

    function handleResize() {
        // Code to execute when the window is resized
        checkWindowWidth();
    }

    function checkWindowWidth() {
        var windowWidth = window.innerWidth;

        if (windowWidth <= 1026) {
            sidebar.removeClass("sidebar-visible");
            mainContainer.removeClass("enlarge");
            $("body").removeClass("sidebar-open");
        } else {
            sidebar.addClass("sidebar-visible");
            mainContainer.addClass("enlarge");
        }
    }

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');

        if (document.activeElement &&
            $(document.activeElement).is('[type=submit]') &&
            $.contains(t, document.activeElement)) {
            submit_btn = $(document.activeElement);
        } else {
            submit_btn = submit_btn.first();
        }

        if (! submit_btn.length) {
            return;
        }

        const originalHtml = submit_btn.html();
        submit_btn.data('original-html', originalHtml);
        submit_btn.data('is-loading', true);

        const loadingText = submit_btn.data('loadingText') || 'Please wait...';
        submit_btn.html('<i class="fa fa-spinner fa-pulse"></i> ' + loadingText);
        submit_btn.attr('disabled', 'disabled');

        let timeoutId;

        function restoreButton() {
            if (! submit_btn.data('is-loading')) {
                return;
            }

            const savedHtml = submit_btn.data('original-html');
            if (typeof savedHtml !== 'undefined') {
                submit_btn.html(savedHtml);
            }

            submit_btn.removeAttr('disabled');
            submit_btn.removeData('is-loading');

            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        }

        function cleanupListeners() {
            window.removeEventListener('focus', onWindowFocus);
            document.removeEventListener('visibilitychange', onVisibilityChange);
        }

        function onWindowFocus() {
            restoreButton();
            cleanupListeners();
        }

        function onVisibilityChange() {
            if (document.visibilityState === 'visible') {
                restoreButton();
                cleanupListeners();
            }
        }

        window.addEventListener('focus', onWindowFocus);
        document.addEventListener('visibilitychange', onVisibilityChange);

        timeoutId = setTimeout(function () {
            if (submit_btn.data('is-loading')) {
                restoreButton();
                cleanupListeners();
            }
        }, 30000);
    }

    function disableSubmitOrigText(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.attr('disabled', 'disabled');
    }

</script>

<script defer>!function(){window;var e,t=document;e=function(){var e=t.createElement("script");
e.type="text/javascript",e.defer=!0,e.src="https://cdn.endorsal.io/widgets/widget.min.js";
var n=t.getElementsByTagName("script")[0];n.parentNode.insertBefore(e,n),
e.onload=function(){NDRSL.init("5de00781dd95d15fd33a275f")}},"interactive"===t.readyState||"complete"===t.readyState?e()
:t.addEventListener("DOMContentLoaded",e())}();</script>
<script>
    helpwiseSettings = {
        widget_id: '60b54b2873539',
        align:'right',
    }
</script>
<script src="https://cdn.helpwise.io/assets/js/livechat.js"></script>
</body>
</html>

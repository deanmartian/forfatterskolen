<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
    <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PBZBPBN2');</script>
    <!-- End Google Tag Manager -->

    @if(in_array(Route::currentRouteName(), ['front.free-webinar']))
    <!-- Event snippet for Webinar_pamelding conversion page In your html page, add the snippet and call
    gtag_report_conversion when someone clicks on the chosen link or button. -->
    <script>
        function gtag_report_conversion(url) {
            var callback = function () {
                if (typeof(url) != 'undefined') {
                    window.location = url;
                }
            };
            gtag('event', 'conversion', {
                'send_to': 'AW-754620576/3IacCOOq1sIDEKCx6ucC',
                'event_callback': callback
            });
            return false;
        }
    </script>
    @endif

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
            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
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
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
@yield('scripts')
<script>

    var sidebar = $("#sidebar");
    var mainContainer = $("#main-container");

    checkWindowWidth();

    // Add an event listener for the window resize event
    window.addEventListener('resize', handleResize);

    // Toggle sidebar on button click
    $("#sidebarCollapse").click(function () {
        sidebar.toggleClass("sidebar-visible");
        mainContainer.toggleClass("enlarge");
    });

    $("#main-content").click(function() {
        if (window.innerWidth <= 1026 && sidebar.hasClass("sidebar-visible")) {
            sidebar.removeClass("sidebar-visible");
            mainContainer.removeClass("enlarge");
        }
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
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PBZBPBN2"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
</body>
</html>

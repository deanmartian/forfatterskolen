<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        {{-- <link rel="alternate" href="{{ config('app.url') }}" hreflang="x-default" /> --}}
        <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
        <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
        <link rel="alternate" href="{{ url()->current() }}" hreflang="{{ app()->getLocale() }}" />
        <link rel="canonical" href="{{ url()->current() }}">
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PBZBPBN2');</script>
        <!-- End Google Tag Manager -->

        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @include('frontend.partials.frontend-css')
        {{-- <link rel="stylesheet" href="{{asset('css/self-publishing.css?v='.time())}}"> --}}
        <link rel="stylesheet" href="{{asset('css/learner.css?v='.time())}}">

        <!-- use meta title first before the title on the actual page added-->
        @yield('title')
        <meta name="keywords" content="forfatterskolen, forfatter, kurs, manusutvikling, manus, manuskript, kikt, sakprosa, serieroman, krim, roman">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743"/>
        @yield('metas')

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
              integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

        <style>
            #sidebar .navbar-brand {
                margin-bottom:35px;
                margin-right: 0;
            }
            .navbar-brand img {
                height: 70px;
                margin: 0 auto;
            }

            .modal.fade .modal-dialog {
                transform: translate(0,0);
            }

            .modal-open .modal {
                background-color: #66646463;
            }
        </style>
        @yield('styles')

        <script  async>
            window.Laravel = '{{ json_encode(['csrfToken' => csrf_token()]) }}';
        </script>

        <script type="text/javascript">
            window.GUMLET_CONFIG = {
                hosts: [{
                    current: "https://www.forfatterskolen.no/",
                    gumlet: "forfatterskolen.gumlet.com"
                }]
            };
        </script>
        <script async src="https://cdn.gumlet.com/gumlet.js/2.0/gumlet.min.js"></script>
    </head>
    <body>
        @include('frontend.partials._self_publishing_sidebar')

        <div id="main-container" class="enlarge">
            @include('frontend.partials._self_publishing_topbar')

            <div id="main-content">
                @yield('content')
            </div>
        </div>

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

        @include('frontend.partials.scripts')
        <script src="https://Forfatterskolen.cdn.vooplayer.com/assets/vooplayer.js" defer></script>
        <script src="/js/lang.js"></script>
        <script async>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if ('serviceWorker' in navigator ) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                        // Registration was successful
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        // registration failed :(
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }

            function disableSubmit(t) {
                let submit_btn = $(t).find('[type=submit]');
                submit_btn.text('');
                submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
                submit_btn.attr('disabled', 'disabled');
            }

            function disableSubmitOrigText(t) {
                let submit_btn = $(t).find('[type=submit]');
                submit_btn.attr('disabled', 'disabled');
            }
        </script>

        @yield('scripts')

        <script>
            checkWindowWidth();

            // Add an event listener for the window resize event
            window.addEventListener('resize', handleResize);

            // Toggle sidebar on button click
            $("#sidebarCollapse").click(function () {
                $("#sidebar").toggleClass("hidden-xs hidden");
                $("#main-container").toggleClass("enlarge");
            });

            $("#main-content").click(function() {
                checkWindowWidth();
            })

            function handleResize() {
                // Code to execute when the window is resized
                checkWindowWidth();
            }

            function checkWindowWidth() {
                var windowWidth = window.innerWidth;

                if (windowWidth <= 1026) {
                    $("#sidebar").addClass("hidden-xs hidden");
                    $("#main-container").removeClass("enlarge");
                } else {
                    $("#sidebar").removeClass("hidden-xs hidden");
                    $("#main-container").addClass("enlarge");
                }
            }
        </script>

        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PBZBPBN2"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
    </body>
</html>
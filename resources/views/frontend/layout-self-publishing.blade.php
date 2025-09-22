<!DOCTYPE html>
<html lang="en">
    <head>
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

        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @include('frontend.partials.frontend-css')
        <link rel="stylesheet" href="{{asset('css/self-publishing.css?v='.time())}}">

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

    <body class="main">

        <header>
            @include('frontend.partials._self_publishing_main_menu')
        </header>

        <main id="app-container" class="container-fluid">
            @yield('content')
        </main>

        <footer>
            @include('frontend.partials._self_publishing_footer')
        </footer>

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
        @yield('scripts')
       
        <script>
            $(".nav-item.dropdown").click(function() {
                $(this).toggleClass('show');
                $(this).find('.dropdown-menu').toggleClass('show');
            });
        </script>
        @if (!in_array(Route::currentRouteName(),['front.course.checkout', 'front.shop-manuscript.checkout']))
        <script defer>!function(){window;var e,t=document;e=function(){var e=t.createElement("script");e.type="text/javascript",e.defer=!0,e.src="https://cdn.endorsal.io/widgets/widget.min.js";var n=t.getElementsByTagName("script")[0];n.parentNode.insertBefore(e,n),e.onload=function(){NDRSL.init("5de00781dd95d15fd33a275f")}},"interactive"===t.readyState||"complete"===t.readyState?e():t.addEventListener("DOMContentLoaded",e())}();</script>
        <!-- support chat  -->
        <script>
            helpwiseSettings = {
                widget_id: '60b54b2873539',
                align:'right',
            }
        </script>
        <script src="https://cdn.helpwise.io/assets/js/livechat.js"></script>
        @endif

        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PBZBPBN2"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    </body>
</html>
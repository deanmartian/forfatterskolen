<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
        <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @include('frontend.partials.frontend-css')
        <link rel="stylesheet" href="{{asset('css/self-publishing.css?v='.filemtime(public_path('css/self-publishing.css')))}}">

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

        {{-- Gumlet fjernet — DNS non-existent --}}
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
                <a href="#" class="close" data-bs-dismiss="alert" aria-label="close" title="close">×</a>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('frontend.partials.scripts')
        {{-- vooplayer fjernet --}}
        <script src="/js/lang.js"></script>
        @yield('scripts')
       
        <script>
            $(".nav-item.dropdown").click(function() {
                $(this).toggleClass('show');
                $(this).find('.dropdown-menu').toggleClass('show');
            });
        </script>
        {{-- Endorsal + Helpwise fjernet — ikke i bruk --}}
    </body>
</html>
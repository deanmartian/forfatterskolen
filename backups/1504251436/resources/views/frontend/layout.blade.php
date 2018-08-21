<!DOCTYPE html>
<html lang="en">
    <head>
        @yield('title')
        @include('frontend.partials.frontend-css')
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
        @yield('styles')
    </head>
    <body>
        @include('frontend.partials.navbar')
        @yield('content')
        @include('frontend.partials.footer')
        @include('frontend.partials.scripts')
        @yield('scripts')
    </body>
</html>

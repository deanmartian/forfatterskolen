<!DOCTYPE html>
<html lang="en">
    <head>
        @yield('title')
        @include('backend.partials.backend-css')
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        @yield('styles')
    </head>
    <body>
        @include('backend.partials.navbar')
        @yield('content')
        @include('backend.partials.scripts')
        @yield('scripts')
    </body>
</html>

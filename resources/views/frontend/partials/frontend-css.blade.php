<link rel="stylesheet" href="{{asset('css/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{asset('css/vendor.min.css')}}">
<link rel="stylesheet" href="{{asset('css/ie-vendor.min.css')}}">
<?php
    $newDesignPages = ['front.shop-manuscript.index', 'front.publishing', 'front.blog']
?>
@if(in_array(Route::currentRouteName(), $newDesignPages))
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/front-style.css')}}">
    {{--<link rel="stylesheet" href="{{asset('css/front-style.min.css?v='.time())}}">--}}
@else
    <link rel="stylesheet" href="{{asset('css/frontend.min.css?v='.time())}}">
@endif

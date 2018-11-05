<link rel="stylesheet" href="{{asset('css/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{asset('css/vendor.min.css')}}">
<link rel="stylesheet" href="{{asset('css/ie-vendor.min.css')}}">
@if(Route::currentRouteName() == 'front.shop-manuscript.index')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/front-style.css')}}">
@else
    <link rel="stylesheet" href="{{asset('css/frontend.min.css?v='.time())}}">
@endif

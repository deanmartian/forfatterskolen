<script type="text/javascript" src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
<?php
$newDesignPages = ['front.shop-manuscript.index', 'front.publishing', 'front.blog', 'front.shop.thankyou', 'front.thank-you',
    'front.course.index', 'front.course.show', 'front.opt-in.thanks']
?>
@if(in_array(Route::currentRouteName(), $newDesignPages))
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
@else
    <script type="text/javascript" src="{{asset('js/vendor.js')}}"></script>
@endif
<script type="text/javascript" src="{{asset('js/frontend.min.js')}}"></script>
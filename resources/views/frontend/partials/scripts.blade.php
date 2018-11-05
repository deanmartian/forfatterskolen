<script type="text/javascript" src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
@if(Route::currentRouteName() == 'front.shop-manuscript.index')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
            crossorigin="anonymous"></script>
@else
    <script type="text/javascript" src="{{asset('js/vendor.js')}}"></script>
@endif
<script type="text/javascript" src="{{asset('js/frontend.min.js')}}"></script>
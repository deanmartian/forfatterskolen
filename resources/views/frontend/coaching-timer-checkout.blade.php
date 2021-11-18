@extends('frontend.layout')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    @if(Session::has('compute_manuscript'))
        <?php
        $data = Session::get('data');
        ?>
    @endif
    <div class="checkout-page" id="app-container">
        <div class="container">
            <coaching-time-checkout :price="{{ json_encode($data['price']) }}"
                                    :title="{{ json_encode(str_replace('_title_', $data['title'], trans('site.front.form.book-form-for'))) }}"
                                    :plan_id="{{ json_encode($data['plan_id']) }}"
                                    :user="{{ json_encode($user) }}"></coaching-time-checkout>
        </div> <!-- end container -->
    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
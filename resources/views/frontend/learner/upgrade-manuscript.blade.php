@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    {{ trans('site.learner.upgrades-text') }} {{$shopManuscriptTaken->shop_manuscript->title}}
@stop

@section('content')

    <div class="learner-container" id="app-container">
        <div class="container">
            <manuscript-upgrade :shop-manuscript-taken="{{ json_encode($shopManuscriptTaken) }}"
                                :shop-manuscript-upgrades="{{ json_encode($shopManuscriptUpgrades) }}"
                                :current-user="{{ $currentUser }}"></manuscript-upgrade>
        </div>
    </div> <!-- end learner-container -->

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
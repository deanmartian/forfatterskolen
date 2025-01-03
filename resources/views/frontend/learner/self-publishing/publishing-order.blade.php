@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Publishing Order &rsaquo; Forfatterskolen</title>
@stop


@section('content')

    <div class="learner-container" id="app-container">
        <div class="container">
            <publishing-order :shop-manuscript="{{ json_encode($shopManuscript) }}"
            :user="{{ Auth::user() }}" :project="{{ json_encode($standardProject) }}"></publishing-order>
        </div>
    </div> <!-- end learner-container -->

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
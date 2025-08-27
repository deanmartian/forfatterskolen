{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title', "Upgrade &rsaquo; Forfatterskolen")

@section('heading')
    {{ trans('site.front.buy') }} {{$assignment->title}}
@stop

@section('content')
    <div class="learner-container" id="app-container">
        <div class="container">
            <assignment-upgrade :assignment="{{ json_encode($assignment) }}"></assignment-upgrade>
        </div> <!-- end container -->
    </div> <!-- end learner-container -->
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
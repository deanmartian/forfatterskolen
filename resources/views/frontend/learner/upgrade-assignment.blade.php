{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('page_title', 'Oppgrader oppgave &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')

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
    <script type="text/javascript" src="{{ asset('js/app.js?v='.filemtime(public_path('js/app.js'))) }}"></script>
@stop
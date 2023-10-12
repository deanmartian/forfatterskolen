{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    {{ trans('site.learner.upgrades-text') }} {{$courseTaken->package->course->title}}
@stop

@section('content')
    <div class="learner-container" id="app-container">
        <div class="container">
            <course-upgrade :course-taken="{{ json_encode($courseTaken) }}"
                            :current-package="{{ json_encode($currentPackage) }}"
                            :current-user="{{ json_encode($currentUser) }}"></course-upgrade>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
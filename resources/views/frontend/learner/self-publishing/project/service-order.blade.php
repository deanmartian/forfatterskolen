@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Service Order &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
    <div class="container card">
        <div class="card-body" style="padding: 30px;">
            <a href="{{ route('learner.project.show', $projectId) }}" class="btn btn-secondary btn-sm" 
            style="margin-bottom: 15px">
                <i class="fa fa-angle-double-left"></i> Back
            </a>
            <service-calculator :active-service="{{ json_encode($service) }}"></service-calculator>
        </div>
    </div>
</div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
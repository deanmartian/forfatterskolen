@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Project &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container order-container">
    <div class="container card">
        <div class="card-body">
            checkout content here

            <a href="{{ route('learner.self-publishing.process-checkout') }}" class="btn btn-dark pull-right" style="margin-top: 20px">
                Process Payment
            </a>
        </div>
    </div>
</div>
@stop
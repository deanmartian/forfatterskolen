@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Bestill publisering &rsaquo; Selvpublisering &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')

@section('content')
<div class="learner-container order-container">
    <div class="container card">
        <div class="card-body">
            checkout content here

            <a href="{{ route('learner.self-publishing.process-checkout') }}" class="btn btn-dark float-end" style="margin-top: 20px">
                Process Payment
            </a>
        </div>
    </div>
</div>
@stop
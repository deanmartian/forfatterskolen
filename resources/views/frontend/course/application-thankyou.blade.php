@extends('frontend.layout')

@section('page_title', 'Takk for søknaden &rsaquo; Forfatterskolen')

@section('styles')
@include('frontend.partials.thank-you-styles')
@stop

@section('content')
<div class="ty-wrapper">
    <div class="ty-card">
        <div class="ty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>Takk for søknaden!</h1>
        <p>{{ trans('site.application-thankyou-description') }}</p>

        <a class="ty-btn" href="{{ route('learner.course') }}">
            <i class="fa fa-graduation-cap"></i> Gå til mine kurs
        </a>
    </div>
</div>
@stop

@extends('frontend.layout')

@section('page_title', 'Takk for gavekortet &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')
@section('meta_desc', 'Takk for kjøpet av gavekortet.')

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

        <h1>Takk for kjøpet!</h1>
        <p>Gavekortet er bestilt. Du finner det under fakturaer i portalen.</p>

        <a class="ty-btn" href="{{ route('learner.invoice') }}?tab=gift">
            <i class="fa fa-gift"></i> Se gavekort
        </a>
    </div>
</div>
@stop

@extends('backend.layout')

@section('page_title', 'Create Zoom Webinar')

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.zoom.webinars.partials.form')
        </div>
    </div>
@stop

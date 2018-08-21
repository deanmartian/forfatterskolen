@extends('backend.layout')

@section('title')
    <title>Create Zoom Webinar</title>
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.zoom.webinars.partials.form')
        </div>
    </div>
@stop

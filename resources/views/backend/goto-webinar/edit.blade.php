@extends('backend.layout')

@section('title')
    <title>Edit GoToWebinar &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.goto-webinar.partials.form')
            @include('backend.goto-webinar.partials.delete')
        </div>
    </div>
@stop

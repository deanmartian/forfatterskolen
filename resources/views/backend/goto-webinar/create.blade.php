@extends('backend.layout')

@section('page_title', 'Create New GoToWebinar &rsaquo; Forfatterskolen Admin')


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.goto-webinar.partials.form')
        </div>
    </div>
@stop

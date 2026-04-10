@extends('backend.layout')

@section('page_title', 'Edit GoToWebinar &rsaquo; Forfatterskolen Admin')


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.goto-webinar.partials.form')
            @include('backend.goto-webinar.partials.delete')
        </div>
    </div>
@stop

@extends('backend.layout')

@section('title')
    <title>Create New Testimonial &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.course.testimonials.partials.form')
        </div>
    </div>
@stop

@extends('backend.layout')

@section('page_title', 'Create New Testimonial &rsaquo; Forfatterskolen Admin')


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.course.video-testimonials.partials.form')
        </div>
    </div>
@stop

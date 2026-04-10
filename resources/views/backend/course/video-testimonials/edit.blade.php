@extends('backend.layout')

@section('page_title')Edit {{$testimonial['name']}} &rsaquo; Forfatterskolen Admin@endsection


@section('content')
<div class="container padding-top">
<div class="row">
@include('backend.course.video-testimonials.partials.form')
</div>
</div>
@stop


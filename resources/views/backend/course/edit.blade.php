@extends('backend.layout')

@section('page_title')Edit {{$course['title']}} &rsaquo; Forfatterskolen Admin@endsection


@section('content')
<div class="container padding-top">
<div class="row">
@include('backend.course.partials.form')
</div>
</div>
@stop


@extends('backend.layout')

@section('title')
<title>Edit {{$course['title']}} &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
<div class="container padding-top">
<div class="row">
@include('backend.course.partials.form')
</div>
</div>
@stop


@extends('backend.layout')

@section('title')
<title>Edit {{$writingGroup['name']}} &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
<div class="container padding-top">
<div class="row">
@include('backend.writing-group.partials.form')
</div>
</div>
@stop


@extends('backend.layout')

@section('title')
    <title>Edit {{ $poem['title'] }} &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.poem.partials.form')
        </div>
    </div>
@stop

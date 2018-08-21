@extends('backend.layout')

@section('title')
    <title>Edit {{ $publishingHouse['publishing'] }} &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.publishing.partials.form')
        </div>
    </div>
@stop

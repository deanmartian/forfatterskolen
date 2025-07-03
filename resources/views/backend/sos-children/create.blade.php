@extends('backend.layout')

@section('title')
    <title>Create New Document &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.sos-children.partials.form')
        </div>
    </div>

    @if ($document['id'])
        @include('backend.sos-children.partials.delete')
    @endif
@stop

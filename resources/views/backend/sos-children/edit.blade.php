@extends('backend.layout')

@section('page_title')Edit {{ $document['title'] }} &rsaquo; Forfatterskolen Admin@endsection

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.sos-children.partials.form')
        </div>
    </div>

    @include('backend.sos-children.partials.delete')
@stop

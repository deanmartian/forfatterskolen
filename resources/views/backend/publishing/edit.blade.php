@extends('backend.layout')

@section('page_title')Edit {{ $publishingHouse['publishing'] }} &rsaquo; Forfatterskolen Admin@endsection


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.publishing.partials.form')
        </div>
    </div>
@stop

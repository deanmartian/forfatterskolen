@extends('backend.layout')

@section('page_title')Edit {{ $poem['title'] }} &rsaquo; Forfatterskolen Admin@endsection


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.poem.partials.form')
        </div>
    </div>
@stop

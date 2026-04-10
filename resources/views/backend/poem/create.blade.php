@extends('backend.layout')

@section('page_title', 'Create Poem &rsaquo; Forfatterskolen Admin')

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.poem.partials.form')
        </div>
    </div>

    @include('backend.poem.partials.delete')
@stop

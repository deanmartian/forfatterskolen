@extends('backend.layout')

@section('title')
    <title>Create New Article &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.solution.article.partials.form')
        </div>
    </div>
@stop

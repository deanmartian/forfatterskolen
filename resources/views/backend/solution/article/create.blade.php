@extends('backend.layout')

@section('page_title', 'Create New Article &rsaquo; Forfatterskolen Admin')


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.solution.article.partials.form')
        </div>
    </div>
@stop

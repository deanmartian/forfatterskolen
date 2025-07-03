@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Create New Blog &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.blog.partials.form')
        </div>
    </div>

    @if ($blog['id'])
        @include('backend.blog.partials.delete')
    @endif
@stop

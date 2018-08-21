@extends('backend.layout')

@section('title')
    <title>Create New Blog &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.blog.partials.form')
        </div>
    </div>

    @include('backend.blog.partials.delete')
@stop

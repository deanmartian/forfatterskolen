@extends('backend.layout')

@section('title')
    <title>Create Opt-in &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.opt-in.partials.form')
        </div>
    </div>

    @include('backend.opt-in.partials.delete')
@stop

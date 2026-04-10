@extends('backend.layout')

@section('page_title', 'Create Opt-in &rsaquo; Forfatterskolen Admin')

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.opt-in.partials.form')
        </div>
    </div>

    @include('backend.opt-in.partials.delete')
@stop

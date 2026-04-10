@extends('backend.layout')

@section('page_title', 'Create New Publishing House &rsaquo; Forfatterskolen Admin')


@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.publishing.partials.form')
        </div>
    </div>
@stop

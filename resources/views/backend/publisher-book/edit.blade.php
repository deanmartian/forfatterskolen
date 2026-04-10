@extends('backend.layout')

@section('page_title', 'Edit ' . $book['title'] . ' &rsaquo; Forfatterskolen Admin')

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.publisher-book.partials.form')
        </div>
    </div>

    @include('backend.publisher-book.partials.delete')
@stop

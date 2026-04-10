@extends('backend.layout')

@section('page_title', 'Create Publisher Book &rsaquo; Forfatterskolen Admin')

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.publisher-book.partials.form')
        </div>
    </div>

    @if ($book['id'])
        @include('backend.publisher-book.partials.delete')
    @endif
@stop

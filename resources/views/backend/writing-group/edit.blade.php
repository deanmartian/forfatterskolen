@extends('backend.layout')

@section('page_title')Edit {{$writingGroup['name']}} &rsaquo; Forfatterskolen Admin@endsection


@section('content')
<div class="container padding-top">
<div class="row">
@include('backend.writing-group.partials.form')
</div>
</div>
@stop


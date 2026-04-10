@extends('backend.layout')

@section('page_title')Rediger {{$editor['name']}} &rsaquo; Forfatterskolen Admin@endsection


@section('content')
<div class="container padding-top">
<div class="row">
@include('backend.editor.partials.form')
</div>
</div>
@stop


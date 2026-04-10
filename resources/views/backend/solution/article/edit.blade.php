@extends('backend.layout')

@section('page_title')Edit {{$article['title']}} &rsaquo; Forfatterskolen Admin@endsection


@section('content')
<div class="container padding-top">
<div class="row">
@include('backend.solution.article.partials.form')
</div>
</div>
@stop


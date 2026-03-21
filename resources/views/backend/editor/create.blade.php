@extends('backend.layout')

@section('title')
<title>Opprett ny redaktør &rsaquo; Forfatterskolen Admin</title>
@stop


@section('content')
<div class="container padding-top">
	<div class="row">
		@include('backend.editor.partials.form')
	</div>
</div>
@stop

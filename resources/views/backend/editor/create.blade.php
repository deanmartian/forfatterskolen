@extends('backend.layout')

@section('page_title', 'Opprett ny redaktør &rsaquo; Forfatterskolen Admin')


@section('content')
<div class="container padding-top">
	<div class="row">
		@include('backend.editor.partials.form')
	</div>
</div>
@stop

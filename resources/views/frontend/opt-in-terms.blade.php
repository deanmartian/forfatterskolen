@extends('frontend.layout')

@section('page_title', 'Vilk&aring;r for gratis materiale &rsaquo; Forfatterskolen')
@section('meta_desc', 'Les vilkårene for gratis materiale og skrivetips fra Forfatterskolen. Personvern og betingelser.')

@section('content')
	<div class="container terms-page">
		<div class="col-12 py-5">
			{!! App\Settings::optInTerms() !!}
		</div>
	</div>
@stop
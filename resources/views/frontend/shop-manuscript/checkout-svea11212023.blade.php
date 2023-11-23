@extends('frontend.layout')

@section('title')
<title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')

	<div class="checkout-page" data-bg="https://www.forfatterskolen.no/images-new/checkout-bg.png" id="app-container">
		<div class="container">
			<shop-manuscript-checkout :user="{{ json_encode($user) }}" :shop-manuscript="{{ json_encode($shopManuscript) }}"
									  :assignment-types="{{ json_encode($assignmentTypes) }}">
			</shop-manuscript-checkout>
		</div>
		<h1 class="hidden">{{ $shopManuscript->title }}</h1>
	</div>

@stop

@section('scripts')
	<script>
        import 'bootstrap/dist/css/bootstrap.css'
        import 'bootstrap-vue/dist/bootstrap-vue.css'
	</script>
	<script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
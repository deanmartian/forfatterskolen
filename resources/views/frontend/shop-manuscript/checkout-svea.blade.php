@extends('frontend.layout')

@section('title')
<title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')

	<div class="global-checkout-page" id="app-container">
		<div class="header" data-bg="https://www.forfatterskolen.no/images-new/checkout-top.png">
		</div>
		<div class="body">
			<div class="container">
				<shop-manuscript-checkout :user="{{ json_encode($user) }}" :shop-manuscript="{{ json_encode($shopManuscript) }}"
										  :assignment-types="{{ json_encode($assignmentTypes) }}"
										  :user-has-paid-course="{{ json_encode($userHasPaidCourse) }}">
				</shop-manuscript-checkout>
			</div>
		</div>
		<h1 class="hidden">{{ $shopManuscript->title }}</h1>
	</div>

@stop

@section('scripts')
	<script>
        /* import 'bootstrap/dist/css/bootstrap.css'
        import 'bootstrap-vue/dist/bootstrap-vue.css' */
	</script>
	<script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
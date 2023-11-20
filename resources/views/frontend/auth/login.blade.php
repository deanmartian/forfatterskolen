@extends('frontend.layout')

@section('title')
<title>
@if(!Request::input('t'))
Login 
@elseif(Request::input('t') == 'register')
Register
@elseif(Request::input('t') == 'passwordreset')
Password Reset
@elseif(Request::input('t') == 'password-change')
Password Change
@endif
&rsaquo; Forfatterskolen
</title>
@stop

@section('styles')
<style>
	.nav-tabs {
		margin-bottom: 30px;
	}
	.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
		color: #555;
		cursor: default;
		background-color: #fff;
		border: 1px solid #ddd;
		border-bottom-color: transparent;
	}
</style>
@stop

@section('content')
<div class="login-wrapper">
	<div class="container">
		<div class="row first-row align-items-center">
			<div class="col-lg-5 image-container">
				<img src="{{ asset('images-new/login/left-image.png') }}" alt="left image">
			</div>
			<div class="col-lg-7">
				<ul class="nav global-nav-tabs">
					<li class="nav-item">
						<a data-toggle="tab" href="#login" class="nav-link @if(!Request::input('t')) active @endif" role="tab">
							<span>{{ trans('site.front.form.login') }}</span>
						</a>
					</li>
					<li class="nav-item">
						<a data-toggle="tab" href="#register" class="nav-link @if(Request::input('t') == 'register') active @endif" role="tab">
							<span>{{ trans('site.front.login.register') }}</span>
						</a>
					</li>
					<li class="nav-item">
						<a data-toggle="tab" href="#passwordreset" class="nav-link @if(Request::input('t') == 'passwordreset') active @endif" role="tab">
							<span>{{ trans('site.front.login.password-reset') }}</span>
						</a>
					</li>
					<li class="nav-item">
						<a data-toggle="tab" href="#password-change" class="nav-link @if(Request::input('t') == 'password-change') active @endif" role="tab">
							<span>{{ trans('site.front.login.change-password') }}</span>
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div id="login" class="tab-pane fade @if(!Request::input('t')) in active @endif" role="tabpanel">
						@include('frontend.auth.partials._login')
					</div>
					<div id="register" class="tab-pane fade @if(Request::input('t') == 'register') in active @endif" role="tabpanel">
						@include('frontend.auth.partials._register')
					</div>
					<div id="passwordreset" class="tab-pane fade @if(Request::input('t') == 'passwordreset') in active @endif" role="tabpanel">
						@include('frontend.auth.partials._password-reset')
					</div>
					<div id="password-change" class="tab-pane fade @if(Request::input('t') == 'password-change') in active @endif" role="tabpanel">
						@include('frontend.auth.partials._password-change')
					</div>
				</div>
			</div>
		</div> <!-- end row -->
	</div>
</div>
@stop
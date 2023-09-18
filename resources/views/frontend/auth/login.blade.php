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
<div class="login-container" data-bg="https://www.forfatterskolen.no/images-new/login/login-bg.jpg">
	<div class="container">
		<div class="row first-row">
			<div class="col-md-6 left-container" data-bg="https://www.forfatterskolen.no/images-new/login/left-bg.jpg">
				<ul class="nav flex-column signup-tab" role="tablist">
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
				</ul> <!-- end signup-tab -->
			</div> <!-- end left-container -->
			<div class="col-md-6 right-container">
				<div class="d-table h-100 w-100 text-center">
					<div class="d-table-cell align-middle">
						<div class="tab-content">
							<div id="login" class="tab-pane fade @if(!Request::input('t')) in active @endif" role="tabpanel">
								<ul class="nav nav-tabs margin-top">
									<li @if( Request::input('tab') == 'main' || Request::input('tab') == '') class="active" @endif>
										<a href="?tab=main">Min Side</a>
									</li>
									<li @if( Request::input('tab') == 'self-publishing' ) class="active" @endif>
										<a href="?tab=self-publishing">Selvpubliseringsportal</a>
									</li>
								</ul>

								@if( Request::input('tab') == 'main' || Request::input('tab') == '')
									<form method="post" action="{{route('frontend.login.store')}}" onsubmit="disableSubmit(this)">
										{{csrf_field()}}
										<h1>{{ trans('site.front.form.login') }}</h1>

										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa at-icon"></i></span>
											</div>
											<input type="email" name="email" class="form-control no-border-left"
												placeholder="{{ trans('site.front.form.email') }}" required value="{{old('email')}}">
										</div>

										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa lock-icon"></i></span>
											</div>
											<input type="password" name="password" placeholder="{{ trans('site.front.form.password') }}"
												class="form-control no-border-left" required>
										</div>

										<button type="submit" class="btn site-btn-global">{{ trans('site.front.form.login') }}</button>

										<div class="clearfix"></div>

										<div class="social-btn-container">
											<a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
												{{ ucwords(trans('site.front.form.login-with-facebook')) }}
											</a>

											<a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
												{{ ucwords(trans('site.front.form.login-with-google')) }}
											</a>

											<a href="{{ route('auth.login.vipps') }}" class="loginBtn btn mt-2">
												<img src="{{ asset('images-new/vipps-login.png') }}" height="38px"
													alt="vipps-login-button">
											</a>
										</div>
									</form>
								@endif

								@if( Request::input('tab') == 'self-publishing' )
									<form method="post" action="{{route('frontend.login.self-publishing-store')}}" 
									onsubmit="disableSubmit(this)">
										{{csrf_field()}}
										<h1>{{ trans('site.front.form.login') }}</h1>

										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa at-icon"></i></span>
											</div>
											<input type="email" name="email" class="form-control no-border-left"
												placeholder="{{ trans('site.front.form.email') }}" required value="{{old('email')}}">
										</div>

										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa lock-icon"></i></span>
											</div>
											<input type="password" name="password" placeholder="{{ trans('site.front.form.password') }}"
												class="form-control no-border-left" required>
										</div>

										<button type="submit" class="btn site-btn-global">{{ trans('site.front.form.login') }}</button>
									</form>
								@endif

								<div class="clearfix"></div>

								@if (Session::has('passwordreset_success'))
									<div class="alert alert-success no-bottom-margin  d-flex mt-3">
										{{Session::get('passwordreset_success')}}
									</div>
								@endif

								{{--@if ( $errors->any() )
									<div class="alert alert-danger no-bottom-margin d-flex mt-3">
										<ul>
											@foreach($errors->all() as $error)
												<li>{{$error}}</li>
											@endforeach
										</ul>
									</div>
								@endif--}}

							</div> <!-- end login pane -->
							<div id="register" class="tab-pane fade @if(Request::input('t') == 'register') in active @endif" role="tabpanel">
								<form method="post" method="post" action="{{route('frontend.register.store')}}" onsubmit="disableSubmit(this)">
									{{csrf_field()}}
									@if (Request::has('r'))
										<input type="hidden" name="redirect" value="{{ Request::get('r') }}">
									@endif
									<div class="h1">{{ trans('site.front.login.register') }}</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa at-icon"></i></span>
										</div>
										<input type="email" name="register_email" placeholder="{{ trans('site.front.form.email') }}"
											   class="form-control no-border-left" required value="{{old('register_email')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa user-icon"></i></span>
										</div>
										<input type="text" placeholder="{{ trans('site.front.form.first-name') }}" name="register_first_name"
											   class="form-control no-border-left" required value="{{old('register_first_name')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa user-icon"></i></span>
										</div>
										<input type="text" name="register_last_name" placeholder="{{ trans('site.front.form.last-name') }}"
											   class="form-control no-border-left" required value="{{old('register_last_name')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa lock-icon"></i></span>
										</div>
										<input type="password" name="register_password" placeholder="{{ trans('site.front.form.password') }}"
											   class="form-control no-border-left" required>
									</div>

									{!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
									{!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

									<button type="submit" class="btn site-btn-global mt-3">{{ trans('site.front.register') }}</button>
									<div class="clearfix"></div>

									<div class="social-btn-container">
										<a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
											{{ ucwords(trans('site.front.form.login-with-facebook')) }}
										</a>

										<a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
											{{ ucwords(trans('site.front.form.login-with-google')) }}
										</a>
									</div>
								</form>

								<div class="clearfix"></div>

								@if (Session::has('passwordreset_success'))
									<div class="alert alert-success no-bottom-margin  d-flex mt-3">
										{{Session::get('passwordreset_success')}}
									</div>
								@endif

								{{--@if ( $errors->any() )
									<div class="alert alert-danger no-bottom-margin d-flex mt-3">
										<ul>
											@foreach($errors->all() as $error)
												<li>{{$error}}</li>
											@endforeach
										</ul>
									</div>
								@endif--}}

							</div> <!-- end register pane -->
							<div id="passwordreset" class="tab-pane fade @if(Request::input('t') == 'passwordreset') in active @endif" role="tabpanel">
								<form method="post" action="{{route('frontend.passwordreset.store')}}" onsubmit="disableSubmit(this)">
									{{csrf_field()}}
									<div class="h1">{{ trans('site.front.login.password-reset-title') }}</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa at-icon"></i></span>
										</div>
										<input type="email" name="reset_email" placeholder="{{ trans('site.front.form.email') }}" class="form-control no-border-left" required value="{{old('reset_email')}}">
									</div>
									<button type="submit" class="btn site-btn-global">{{ trans('site.front.login.password-reset') }}</button>
									<div class="clearfix"></div>
								</form>

								<div class="clearfix"></div>

								@if (Session::has('passwordreset_success'))
									<div class="alert alert-success no-bottom-margin  d-flex mt-3">
										{{Session::get('passwordreset_success')}}
									</div>
								@endif

								{{--@if ( $errors->any() )
									<div class="alert alert-danger no-bottom-margin d-flex mt-3">
										<ul>
											@foreach($errors->all() as $error)
												<li>{{$error}}</li>
											@endforeach
										</ul>
									</div>
								@endif--}}

							</div> <!-- end passwordreset pane -->

							<div id="password-change" class="tab-pane fade @if(Request::input('t') == 'password-change') in active @endif" role="tabpanel">
								<form method="post" action="{{route('frontend.password-change')}}" onsubmit="disableSubmit(this)">
									{{csrf_field()}}
									<div class="h1">{{ trans('site.front.login.change-password') }}</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa at-icon"></i></span>
										</div>
										<input type="email" name="email" placeholder="{{ trans('site.front.form.email') }}"
											   class="form-control no-border-left" required value="{{old('email')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa lock-icon"></i></span>
										</div>
										<input type="password" name="current_password"
											   placeholder="{{ trans('site.front.login.enter-your-current-password') }}"
											   class="form-control no-border-left" required>
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa lock-icon"></i></span>
										</div>
										<input type="password" name="password"
											   placeholder="{{ trans('site.front.login.enter-your-new-password') }}"
											   class="form-control no-border-left" required>
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa lock-icon"></i></span>
										</div>
										<input type="password" name="password_confirmation"
											   placeholder="{{ trans('site.confirm-password') }}"
											   class="form-control no-border-left" required>
									</div>

									<button type="submit" class="btn site-btn-global">
										{{ trans('site.front.login.change-password') }}
									</button>
								</form>

								<div class="clearfix"></div>

								@if (Session::has('password_change_success'))
									<div class="alert alert-success no-bottom-margin  d-flex mt-3">
										{{Session::get('password_change_success')}}
									</div>
								@endif
							</div>

						</div> <!-- end tab-content -->
					</div> <!-- end d-table-cell -->
				</div> <!-- end d-table -->
			</div> <!-- end right-container -->
		</div> <!-- end row -->
	</div>
</div>
@stop
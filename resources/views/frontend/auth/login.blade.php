@extends('frontend.layout')

@section('title')
<title>
@if(!Request::input('t'))
Login 
@elseif(Request::input('t') == 'register')
Register
@elseif(Request::input('t') == 'passwordreset')
Password Reset
@endif
&rsaquo; Forfatterskolen
</title>
@stop

@section('content')
<div class="login-container">
	<div class="container">
		<div class="row first-row">
			<div class="col-md-6 left-container">
				<ul class="nav flex-column signup-tab" role="tablist">
					<li class="nav-item">
						<a data-toggle="tab" href="#login" class="nav-link @if(!Request::input('t')) active @endif" role="tab">
							<span>Login</span>
						</a>
					</li>
					<li class="nav-item">
						<a data-toggle="tab" href="#register" class="nav-link @if(Request::input('t') == 'register') active @endif" role="tab">
							<span>Registrer</span>
						</a>
					</li>
					<li class="nav-item">
						<a data-toggle="tab" href="#passwordreset" class="nav-link @if(Request::input('t') == 'passwordreset') active @endif" role="tab">
							<span>Tilbakestill passordet ditt</span>
						</a>
					</li>
				</ul> <!-- end signup-tab -->
			</div> <!-- end left-container -->
			<div class="col-md-6 right-container">
				<div class="d-table h-100 w-100 text-center">
					<div class="d-table-cell align-middle">
						<div class="tab-content">
							<div id="login" class="tab-pane fade @if(!Request::input('t')) in active @endif" role="tabpanel">
								<form method="post" action="{{route('frontend.login.store')}}" onsubmit="disableSubmit(this)">
									{{csrf_field()}}
									<h1>Login</h1>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa at-icon"></i></span>
										</div>
										<input type="email" name="email" class="form-control no-border-left"
											   placeholder="Epost" required value="{{old('email')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa lock-icon"></i></span>
										</div>
										<input type="password" name="password" placeholder="Passord"
											   class="form-control no-border-left" required>
									</div>

									<button type="submit" class="btn site-btn-global">Login</button>

									<div class="clearfix"></div>

									<div class="social-btn-container">
										<a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
											Logg Inn Med Facebook
										</a>

										<a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
											Logg Inn Med Google
										</a>
									</div>
								</form>
							</div> <!-- end login pane -->
							<div id="register" class="tab-pane fade @if(Request::input('t') == 'register') in active @endif" role="tabpanel">
								<form method="post" method="post" action="{{route('frontend.register.store')}}" onsubmit="disableSubmit(this)">
									{{csrf_field()}}
									<h1>Register</h1>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa at-icon"></i></span>
										</div>
										<input type="email" name="register_email" placeholder="Epost"
											   class="form-control no-border-left" required value="{{old('register_email')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa user-icon"></i></span>
										</div>
										<input type="text" placeholder="Fornavn" name="register_first_name"
											   class="form-control no-border-left" required value="{{old('register_first_name')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa user-icon"></i></span>
										</div>
										<input type="text" name="register_last_name" placeholder="Etternavn"
											   class="form-control no-border-left" required value="{{old('register_last_name')}}">
									</div>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa lock-icon"></i></span>
										</div>
										<input type="password" name="register_password" placeholder="Passord"
											   class="form-control no-border-left" required>
									</div>

									<button type="submit" class="btn site-btn-global">Registrer deg</button>
									<div class="clearfix"></div>

									<div class="social-btn-container">
										<a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
											Logg Inn Med Facebook
										</a>

										<a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
											Logg Inn Med Google
										</a>
									</div>
								</form>
							</div> <!-- end register pane -->
							<div id="passwordreset" class="tab-pane fade @if(Request::input('t') == 'passwordreset') in active @endif" role="tabpanel">
								<form method="post" action="{{route('frontend.passwordreset.store')}}" onsubmit="disableSubmit(this)">
									{{csrf_field()}}
									<h1>Tilbakestill passordet</h1>

									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa at-icon"></i></span>
										</div>
										<input type="email" name="reset_email" placeholder="E-post" class="form-control no-border-left" required value="{{old('reset_email')}}">
									</div>
									<button type="submit" class="btn site-btn-global">Tilbakestill passord ditt</button>
									<div class="clearfix"></div>
								</form>
							</div> <!-- end passwordreset pane -->
						</div> <!-- end tab-content -->

						@if (Session::has('passwordreset_success'))
							<div class="alert alert-success no-bottom-margin">
								{{Session::get('passwordreset_success')}}
							</div>
						@endif

						@if ( $errors->any() )
							<div class="alert alert-danger no-bottom-margin">
								<ul>
									@foreach($errors->all() as $error)
										<li>{{$error}}</li>
									@endforeach
								</ul>
							</div>
						@endif

					</div> <!-- end d-table-cell -->
				</div> <!-- end d-table -->
			</div> <!-- end right-container -->
		</div> <!-- end row -->
	</div>
</div>
@stop
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
<div class="container login-container">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<div class="theme-tabs authentication-tabs">
				
				<ul class="nav nav-tabs">
				  <li @if(!Request::input('t')) class="active" @endif ><a data-toggle="tab" href="#login" id="tab-login"><span>Login</span></a></li>
				  <li @if(Request::input('t') == 'register') class="active" @endif><a data-toggle="tab" href="#register" id="tab-register"><span>Registrer deg</span></a></li>
				  <li @if(Request::input('t') == 'passwordreset') class="active" @endif><a data-toggle="tab" href="#passwordreset" id="tab-passwordreset"><span>Tilbakestill passordet ditt</span></a></li>
				</ul>

				<div class="tab-content">

				  <div id="login" class="tab-pane fade @if(!Request::input('t')) in active @endif">
					<form method="post" action="{{route('frontend.login.store')}}">
						{{csrf_field()}}
						<div class="form-group">
							<div class="input-group">
							  	<span class="input-group-addon" id="sizing-addon1"><i class="fa fa-at"></i></span>
								<input type="email" name="email" class="form-control no-border-left" placeholder="Epost" required value="{{old('email')}}">
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
							  	<span class="input-group-addon" id="sizing-addon1"><i class="fa fa-lock"></i></span>
								<input type="password" name="password" placeholder="Passord" class="form-control no-border-left" required>
							</div>
						</div>
						<button type="submit" class="btn btn-primary pull-right">Login</button>

                        <div class="clearfix"></div>

                        <a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn pull-right">
                            Logg inn med Google
                        </a>

                        <div class="clearfix"></div>

                        <a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn pull-right">
                            Logg inn med Facebook
                        </a>

					</form>
				  </div>


				  <div id="register" class="tab-pane fade @if(Request::input('t') == 'register') in active @endif">
				  	<form method="post" method="post" action="{{route('frontend.register.store')}}">
						{{csrf_field()}}
						<div class="form-group">
							<div class="input-group">
							  	<span class="input-group-addon" id="sizing-addon1"><i class="fa fa-at"></i></span>
								<input type="email" name="register_email" placeholder="Epost" class="form-control no-border-left" required value="{{old('register_email')}}">
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
							  	<span class="input-group-addon" id="sizing-addon1"><i class="fa fa-user"></i></span>
								<input type="text" placeholder="Fornavn" name="register_first_name" class="form-control no-border-left" required value="{{old('register_first_name')}}">
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
							  	<span class="input-group-addon" id="sizing-addon1"><i class="fa fa-user"></i></span>
								<input type="text" name="register_last_name" placeholder="Etternavn" class="form-control no-border-left" required value="{{old('register_last_name')}}">
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
							  	<span class="input-group-addon" id="sizing-addon1"><i class="fa fa-lock"></i></span>
								<input type="password" name="register_password" placeholder="Passord" class="form-control no-border-left" required>
							</div>
						</div>
						<button type="submit" class="btn btn-primary pull-right">Registrer deg</button>
						<div class="clearfix"></div>

						<a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn pull-right">
							Logg inn med Google
						</a>

						<div class="clearfix"></div>

						<a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn pull-right">
							Logg inn med Facebook
						</a>
					</form>
				  </div>



				  <div id="passwordreset" class="tab-pane fade @if(Request::input('t') == 'passwordreset') in active @endif">
					<form method="post" action="{{route('frontend.passwordreset.store')}}">
						{{csrf_field()}}
						<div class="form-group">
							<div class="input-group">
							  	<span class="input-group-addon" id="sizing-addon1"><i class="fa fa-at"></i></span>
								<input type="email" name="reset_email" placeholder="E-post" class="form-control no-border-left" required value="{{old('reset_email')}}">
							</div>
						</div>
						<button type="submit" class="btn btn-primary pull-right">Tilbakestill passord ditt</button>
						<div class="clearfix"></div>
					</form>
				  </div>
				  {{--<span class="margin-top" style="display: inline-block;">
				  Logg deg inn på det gamle systemet (gjelder for de som har registrert seg før 1.1.15). <br />
				  <a class="btn btn-sm btn-theme" style="margin-top: 4px" href="http://old.forfatterskolen.no">Klikk her</a>
				  </span>--}}
				</div>
				@if (Session::has('passwordreset_success'))
                <br />
                <div class="alert alert-success no-bottom-margin">
                    {{Session::get('passwordreset_success')}}
                </div>
				@endif

				@if ( $errors->any() )
                <br />
                <div class="alert alert-danger no-bottom-margin">
                    <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                    </ul>
                </div>
                @endif
			</div>
		</div>
	</div>

</div>
@stop
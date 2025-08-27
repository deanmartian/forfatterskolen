@extends('frontend.layout')

@php
    if(!Request::input('t')) {
        $pageTitle = 'Login';
    } elseif(Request::input('t') == 'register') {
        $pageTitle = 'Register';
    } elseif(Request::input('t') == 'passwordreset') {
        $pageTitle = 'Password Reset';
    }
@endphp
@section('title', $pageTitle . ' &rsaquo; Forfatterskolen')

@section('content')
<div class="container login-container">
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h4>Passord Tilbakestilling</h4>
				</div>
				<div class="panel-body">
					<form method="POST" action="{{route('frontend.passwordreset.update', $passwordReset->token)}}">
						{{csrf_field()}}
						<div class="form-group">
							<label>Nytt Passord</label>
							<input type="password" name="password" class="form-control" required>
						</div>
						<div class="form-group">
							<label>Bekreft Passordet</label>
							<input type="password" name="password_confirmation" class="form-control" required>
						</div>
						<button type="submit" class="btn btn-primary pull-right">Oppdater Passordet</button>
						<div class="clearfix"></div>
					</form>
				</div>
			</div>
			@if($errors->any())
			<div class="alert alert-danger">
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
@stop
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
		<div class="col-sm-4 col-sm-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h4>Password Reset</h4>
				</div>
				<div class="panel-body">
					<form method="POST" action="{{route('frontend.passwordreset.update', $passwordReset->token)}}">
						{{csrf_field()}}
						<div class="form-group">
							<label>New Password</label>
							<input type="password" name="password" class="form-control" required>
						</div>
						<div class="form-group">
							<label>Confirm Password</label>
							<input type="password" name="password_confirmation" class="form-control" required>
						</div>
						<button type="submit" class="btn btn-primary pull-right">Update Password</button>
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
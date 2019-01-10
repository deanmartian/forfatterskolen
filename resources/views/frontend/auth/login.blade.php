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
		<div class="row">
			<div class="col-md-6 left-container">
				<ul class="nav flex-column signup-tab" role="tablist">
					<li class="nav-item">
						<a data-toggle="tab" href="#login" class="nav-link active" role="tab">
							<span>Login</span> <!-- check if webinar-pakke -->
						</a>
					</li>
					<li class="nav-item">
						<a data-toggle="tab" href="#register" class="nav-link" role="tab">
							<span>Registrer</span> <!-- check if webinar-pakke -->
						</a>
					</li>
					<li class="nav-item">
						<a data-toggle="tab" href="#passwordreset" class="nav-link" role="tab">
							<span>Tilbakestill passordet ditt</span> <!-- check if webinar-pakke -->
						</a>
					</li>
				</ul>
			</div>
			<div class="col-md-6 right-container">
				<div class="d-table h-100 w-100 text-center">
					<div class="d-table-cell align-middle">
						<div class="tab-content">
							<div id="login" class="tab-pane fade in active" role="tabpanel">
								login
							</div>
							<div id="register" class="tab-pane fade" role="tabpanel">
								register
							</div>
							<div id="passwordreset" class="tab-pane fade" role="tabpanel">
								reset
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
@extends('frontend.layout')

@section('title')
<title>
	Login &rsaquo; Forfatterskolen
</title>
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
				</ul> <!-- end signup-tab -->
			</div> <!-- end left-container -->
			<div class="col-md-6 right-container">
				<div class="d-table h-100 w-100 text-center">
					<div class="d-table-cell align-middle">
						<div class="tab-content">
							<div id="login" class="tab-pane fade @if(!Request::input('t')) in active @endif" role="tabpanel">
								<form method="post" action="{{route('frontend.login.self-publishing-store')}}" onsubmit="disableSubmit(this)">
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

								<div class="clearfix"></div>

								@if (Session::has('passwordreset_success'))
									<div class="alert alert-success no-bottom-margin  d-flex mt-3">
										{{Session::get('passwordreset_success')}}
									</div>
								@endif

							</div> <!-- end login pane -->
						</div> <!-- end tab-content -->
					</div> <!-- end d-table-cell -->
				</div> <!-- end d-table -->
			</div> <!-- end right-container -->
		</div> <!-- end row -->
	</div>
</div>
@stop
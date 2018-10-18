@extends('frontend.layout')

@section('title')
	<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
	<div class="container">
		<div class="row">

			<div class="col-sm-12">
				{!! nl2br($optIn->description) !!}
			</div>

			<div class="col-sm-6 col-sm-offset-3">

				@if(Session::has('opt-in-message'))
					<div class="alert alert-success" role="alert">
						Takk for at du skrev deg på, du vil snart få en epost i innboksen din.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				@endif

				<form class="margin-bottom" method="POST" action="">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Fornavn</label>
						<input type="text" class="form-control" name="name" required value="{{ old('name') }}">
					</div>
					<div class="form-group">
						<label>E-post</label>
						<input type="email" class="form-control" name="email" required value="{{ old('email') }}">
					</div>
					<div class="form-group">
						<input type="checkbox" name="terms" required>
						<label>Jeg aksepterer <a href="{{ route('front.opt-in-terms') }}" target="_blank">vilkårene</a></label>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-theme">Send inn</button>
					</div>
				</form>
				<br />

				@if ( $errors->any() )
					<div class="alert alert-danger bottom-margin">
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

@section('scripts')
	<script>
		$(".btn-theme").click(function(e){
		    e.preventDefault();
		   $(this).attr('disabled', true).text('Sende...');
		   $("form").submit();
		});
	</script>
@stop
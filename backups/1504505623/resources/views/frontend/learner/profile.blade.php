@extends('frontend.layout')

@section('title')
<title>Profile &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<form method="POST" action="{{route('learner.profile.update')}}" enctype="multipart/form-data">
				{{csrf_field()}}
				<div class="row">
					<div class="col-sm-12 col-md-6">
						<div class="panel panel-default">
							<div class="panel-body">
								<h4>Profil</h4>
								<br />
								<div class="user-image image-file margin-bottom">
									<div class="image-preview" style="background-image: url('{{Auth::user()->profile_image}}')" data-default="{{Auth::user()->profile_image}}" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
									<input type="file" accept="image/*" name="image">
								</div>
								<div class="form-group">
									<label>Epost</label>
									<input type="email" class="form-control" disabled readonly value="{{Auth::user()->email}}">
								</div>
								<div class="form-group">
									<label>Fornavn</label>
									<input type="text" class="form-control" autocomplete='off' name="first_name" value="{{Auth::user()->first_name}}" required>
								</div>
								<div class="form-group">
									<label>Etternavn</label>
									<input type="text" class="form-control" autocomplete='off' name="last_name" value="{{Auth::user()->last_name}}" required>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-body">
								<h4>Sikkerhet</h4>
								<br />
								<div class="form-group">
									<label>Nytt passord</label>
									<input type="password" class="form-control" autocomplete='off' name="new_password">
								</div>
								<div class="form-group">
									<label>Gammelt passord</label>
									<input type="password" class="form-control" autocomplete='off' name="old_password">
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12 col-md-6">
						<div class="panel panel-default">
							<div class="panel-body">
								<h4>Adresse</h4>
								<br />
								<div class="form-group">
									<label>Gate</label>
									<input type="text" class="form-control" autocomplete='off' name="street" value="{{Auth::user()->address->street}}">
								</div>
								<div class="form-group">
									<label>Postnummer</label>
									<input type="text" class="form-control" autocomplete='off' name="zip" value="{{Auth::user()->address->zip}}">
								</div>
								<div class="form-group">
									<label>Sted</label>
									<input type="text" class="form-control" autocomplete='off' name="city" value="{{Auth::user()->address->city}}">
								</div>
								<div class="form-group">
									<label>Telefon</label>
									<input type="tel" class="form-control" autocomplete='off' name="phone" value="{{Auth::user()->address->phone}}">
								</div>
							</div>
						</div>
						@if ( $errors->any() )
		                <div class="alert alert-danger no-bottom-margin">
		                    <ul>
		                    @foreach($errors->all() as $error)
		                    <li>{{$error}}</li>
		                    @endforeach
		                    </ul>
		                </div>
		                @endif
		                @if(session()->has('profile_success'))
					    <div class="alert alert-success">
					        {{ session()->get('profile_success') }}
					    </div>
						@endif
					</div>
				</div>

				<button type="submit" class="btn btn-primary">Oppdater profilen</button>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

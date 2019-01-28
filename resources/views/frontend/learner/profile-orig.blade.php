@extends('frontend.layout')

@section('title')
<title>Profile &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
	<link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
	<style>
		.list-group {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-orient: vertical;
			-webkit-box-direction: normal;
			-ms-flex-direction: column;
			flex-direction: column;
			padding-left: 0;
			margin-bottom: 0;
		}
	</style>
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
									<input type="email" class="form-control" disabled readonly value="{{Auth::user()->email}}" id="profile_email">
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

						@if(Auth::user()->diplomas->count())
							<div class="panel panel-default">
								<div class="panel-body">
									<h3 class="mt-0">Kursbevis</h3>

									@foreach(Auth::user()->diplomas()->orderBy('created_at', 'DESC')->get()->chunk('3') as $diploma_chunk)
										@foreach($diploma_chunk as $diploma)
											<div class="col-sm-4">
												<div style="border: 1px solid #ccc" class="text-center">
													<span>{{ $diploma->course->title }}</span>

													<a href="#previewDiplomaModal" data-toggle="modal"
													   data-diploma="{{asset($diploma->diploma)}}"
													   class="previewDiplomaBtn darken">
														<img src="{{ asset('images/diploma.jpg') }}"
															 style="height: 140px; width: 100%">
														<span class="message">Preview</span>
													</a>

													<a href="{{ route('learner.download-diploma', $diploma->id) }}">Last ned</a>
												</div>
											</div>
										@endforeach
									@endforeach
								</div>
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

		<div class="col-sm-12 margin-top">
			<div class="row">
				<div class="col-sm-12 col-md-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<h4>Epost Adresser</h4>
							<hr>
							<div class="form-group email-container">
								<small class="text-muted d-block">
									Om du skal bruke flere epost adresser i systemet, eller skal endre epost adresse,
									så er det «Hoved» epost adressen som skal brukes med å logge inn med og epost vil bli kun sendt til «hoved» adressen din.
								</small>
								<ul class="list-group mt-2" id="email-list">
								</ul>
								<div class="form-group mt-1 mb-1">
									<label class="lead ml-2 mb-1 mt-2">Legg til ny epost adresse</label>
									<div class="input-group-global mb-0">
										<input type="text" class="form-control" name="email" placeholder="Din nye epost adresse" aria-label="Recipient's email address" aria-describedby="basic-addon2" autocomplete="off" onkeyup="methods.sendConfirmation(event)">
										<div class="input-group-append">
											<button class="btn btn-info email-btn" type="button" onclick="methods.sendConfirmation()"><i class="fa fa-plus-circle"></i></button>
										</div>
									</div>
								</div>
								<small class="text-muted d-block">{{ "Du vil motta en epost når du har lagt til ny epost adresse. Denne må du godkjenne før du kan bruke denne nye adressen. Kun godkjente epost adresser kan være «hoved» adresse" }}</small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

	<div id="previewDiplomaModal" class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Preview</h4>
				</div>
				<div class="modal-body">
					<iframe src="" frameborder="0" width="100%" height="550">
					</iframe>
				</div>
			</div>
		</div>
	</div>

@stop

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
	<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
	<script src="{{ asset('js/profile.js') }}"></script>
	<script>
		$(".previewDiplomaBtn").click(function(){
		   let diploma = $(this).data('diploma');
		   let modal = $("#previewDiplomaModal");
            modal.find('iframe').attr('src', diploma);
		});

        $('.darken').hover(
            function(){
                $(this).find('.message').fadeIn(1000);
            },
            function(){
                $(this).find('.message').fadeOut(1000);
            }
        );
	</script>
@stop

{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Profile &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
	<link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')

	<div class="learner-container learner-profile">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<div class="card">
						<div class="card-body p-5">
							<div class="user-image image-file text-center">
								<form method="POST" action="{{route('learner.profile.update-photo')}}" enctype="multipart/form-data"
									  id="photo-form">
									{{csrf_field()}}
									<div class="circle">
										<div class="image-preview" data-bg="{{ Auth::user()->profile_image }}"
											 data-default="{{Auth::user()->profile_image}}"
											 title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
										<input type="file" accept="image/*" name="image">
									</div>
								</form>
							</div>
							<div class="text-center">
								<h1 class="font-barlow-regular mt-4">
									{{Auth::user()->full_name}}
								</h1>
								<span class="note-color font-16">
									{{Auth::user()->email}}
								</span>
							</div>

							<h3 class="font-weight-normal font-barlow-regular mt-4">
								<span class="note-color d-block font-15">
									{{ trans('site.learner.address') }}
								</span>
								{{ Auth::user()->address->street.', '.Auth::user()->address->zip.' '.Auth::user()->address->city }}
							</h3>

							<h3 class="font-weight-normal font-barlow-regular mt-3">
								<span class="note-color d-block font-15">
									{{ trans('site.learner.telephone') }}
								</span>
								{{ Auth::user()->address->phone }}
							</h3>

							<h3 class="font-weight-normal font-barlow-regular mt-3">
								<span class="note-color d-block font-15">
									{{ trans('site.learner.facebook') }}
								</span>
								{{ Auth::user()->social->facebook ?: 'None' }}
							</h3>

							<h3 class="font-weight-normal font-barlow-regular mt-3">
								<span class="note-color d-block font-15">
									{{ trans('site.learner.instagram') }}
								</span>
								{{ Auth::user()->social->instagram ?: 'None' }}
							</h3>
						</div> <!-- end card-body -->
					</div> <!-- end card -->

					<div class="card my-5 site-link-container">
						<div class="card-body p-5">
							<a href="{{ route('learner.invoice') }}" class="invoice-link">{{ trans('site.learner.my-invoice') }}</a>
							<a href="{{ route('learner.assignment') }}" class="assignment-link">{{ trans('site.learner.assignment') }}</a>
							<a href="{{ route('learner.upgrade') }}" class="upgrade-link">{{ trans('site.learner.upgrading-text') }}</a>
						</div>
					</div>
				</div> <!-- end col-md-4 -->

				<div class="col-md-8">
					<div class="card">
						<div class="card-body p-0">
							<div class="theme-tabs">
								<ul class="nav nav-tabs pl-5" role="tablist">
									<li class="nav-item">
										<a data-toggle="tab" href="#profile-panel" class="nav-link active" role="tab">
											<span>{{ trans('site.learner.profile-text') }}</span>
										</a>
									</li>
									<li class="nav-item">
										<a data-toggle="tab" href="#email-panel" class="nav-link" role="tab">
											<span>{{ trans('site.learner.email-addresses-text') }}</span>
										</a>
									</li>
									<li class="nav-item">
										<a data-toggle="tab" href="#diploma-panel" class="nav-link" role="tab">
											<span>{{ trans('site.learner.diploma-text') }}</span>
										</a>
									</li>
								</ul> <!-- end nav-tabs-->

								<div class="tab-content p-0">
									<div id="profile-panel" class="tab-pane fade in active" role="tabpanel">
                                                                                <form id="profileForm" method="POST" action="{{route('learner.profile.update')}}" enctype="multipart/form-data"
                                                                                onsubmit="disableSubmit(this)">
											{{csrf_field()}}
											<section>
												<div class="col-md-10 text-center">

													<div class="form-group row">
														<label class="col-sm-3 col-form-label"></label>
														<div class="col-sm-9">
															<h1 class="text-left font-barlow-regular">{{ trans('site.learner.profile-text') }}</h1>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.front.form.email') }}</label>
														<div class="col-sm-9">
															<input type="email" class="form-control" disabled readonly
																   value="{{Auth::user()->email}}" id="profile_email">
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.front.form.first-name') }}</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" autocomplete='off'
																   name="first_name" value="{{Auth::user()->first_name}}" required>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.front.form.last-name') }}</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" autocomplete='off'
																   name="last_name" value="{{Auth::user()->last_name}}" required>
														</div>
													</div>
												</div>
											</section> <!-- profile section-->

											<section>
												<div class="col-md-10 text-center">
													<div class="form-group row">
														<label class="col-sm-3 col-form-label"></label>
														<div class="col-sm-9">
															<h1 class="text-left font-barlow-regular">{{ trans('site.learner.address') }}</h1>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.front.form.street') }}</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" autocomplete='off'
																   name="street" value="{{Auth::user()->address->street}}">
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.front.form.zip') }}</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" autocomplete='off'
																   name="zip" value="{{Auth::user()->address->zip}}">
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.learner.place-text') }}</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" autocomplete='off'
																   name="city" value="{{Auth::user()->address->city}}">
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.learner.telephone') }}</label>
														<div class="col-sm-9">
															<input type="tel" class="form-control" autocomplete='off'
																   name="phone" value="{{Auth::user()->address->phone}}">
														</div>
													</div>
												</div>
											</section> <!-- end address section -->

											<section>
												<div class="col-md-10 text-center">
													<div class="form-group row">
														<label class="col-sm-3 col-form-label"></label>
														<div class="col-sm-9">
															<h1 class="text-left font-barlow-regular">{{ trans('site.learner.social-text') }}</h1>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.learner.facebook') }}</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" autocomplete='off'
																   name="facebook" value="{{Auth::user()->social->facebook}}">
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.learner.instagram') }}</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" autocomplete='off'
																   name="instagram" value="{{Auth::user()->social->instagram}}">
														</div>
													</div>
												</div>
											</section> <!-- end social section -->

											<section>
												<div class="col-md-10 text-center">
													<div class="form-group row">
														<label class="col-sm-3 col-form-label"></label>
														<div class="col-sm-9">
															<h1 class="text-left font-barlow-regular">{{ trans('site.learner.safety-text') }}</h1>
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.learner.update-password.title') }}</label>
														<div class="col-sm-9">
															<input type="password" class="form-control" autocomplete='off'
																   name="new_password">
														</div>
													</div>

													<div class="form-group row">
														<label class="col-sm-3 col-form-label">{{ trans('site.learner.old-password-text') }}</label>
														<div class="col-sm-9">
															<input type="password" class="form-control" autocomplete='off'
																   name="old_password">
														</div>
													</div>
												</div>
											</section> <!-- end password section-->

											<section>
												<div class="col-md-10 text-center">
													<div class="form-group row mb-0">
														<div class="col-sm-9 text-left col-md-offset-3">
															<button type="submit" class="btn site-btn-global">
																{{ trans('site.learner.update-profile-text') }}
															</button>
															<a href="{{ route('learner.profile') }}" class="btn light-button">
																{{ trans('site.front.cancel') }}
															</a>
														</div>
													</div>
												</div>
											</section>
										</form>

										@if ( $errors->any() )
											<div class="alert alert-danger mx-4">
												<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
												<ul>
													@foreach($errors->all() as $error)
														<li>{{$error}}</li>
													@endforeach
												</ul>
											</div>
										@endif
										@if(session()->has('profile_success'))
											<div class="alert alert-success mx-4">
												<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
												{{ session()->get('profile_success') }}
											</div>
										@endif

									</div> <!-- end profile panel -->

									<div id="email-panel" class="tab-pane fade" role="tabpanel">
										<section class="d-block email-container">
											<h1 class="font-barlow-regular">
												{{ trans('site.learner.email-addresses-text') }}
											</h1>

											<p class="note-color mt-4">
												{{ trans('site.learner.email-addresses-note') }}
											</p>

											<ul class="list-group mt-5" id="email-list">
											</ul>

											<h2 class="font-barlow-regular mt-5">
												{{ trans('site.learner.add-new-email-address') }}
											</h2>

											<div class="form-group mb-0">
												<div class="input-group mt-5">
													<input type="text" class="form-control" name="email"
														   placeholder="Din nye epost adresse" aria-label="Recipient's email address"
														   aria-describedby="basic-addon2" autocomplete="off" onkeyup="methods.sendConfirmation(event)">
													<div class="input-group-append">
														<button class="btn site-btn-global rounded-0 email-btn" type="button"
																onclick="methods.sendConfirmation()">
															<i class="plus"></i>
														</button>
													</div>
												</div>
											</div>

											<p class="note-color mt-5">
												{{ trans('site.learner.add-new-email-address-note') }}
											</p>

										</section>
									</div> <!-- end email panel-->

									<div id="diploma-panel" class="tab-pane fade" role="tabpanel">
										<section class="d-block">
											<h1 class="font-barlow-regular">
												{{ trans('site.learner.diploma-text') }}
											</h1>

											<div class="row">
												@foreach(Auth::user()->diplomas()->orderBy('created_at', 'DESC')->get() as $diploma)
													<div class="col-lg-4 col-md-6">
														<div class="card card-global">
															<div class="card-body text-center">
																<img data-src="https://www.forfatterskolen.no/images-new/diploma.png" alt="">
																<h3 class="font-weight-normal">
																	{{ $diploma->course->title }}
																</h3>

																<a href="{{ route('learner.download-diploma', $diploma->id) }}"
																class="btn site-btn-global">{{ trans('site.learner.download-text') }}</a>
															</div>
														</div>
													</div>
												@endforeach

												@foreach($certificates as $certificate)
														<div class="col-lg-4 col-md-6">
															<div class="card card-global">
																<div class="card-body text-center">
																	<img data-src="https://www.forfatterskolen.no/images-new/diploma.png" alt="">
																	<h3 class="font-weight-normal">
																		{{ $certificate->course_title }}
																	</h3>

																	<a href="{{ route('learner.download-course-certificate', $certificate->id) }}"
																	   class="btn site-btn-global">{{ trans('site.learner.download-text') }}</a>
																</div>
															</div>
														</div>
												@endforeach
											</div>
										</section>
									</div>
								</div> <!-- end tab-content -->
							</div> <!-- end theme-tabs -->
						</div> <!-- end card-body -->
					</div> <!-- end card -->
				</div> <!-- end col-md-8 -->
			</div> <!-- end row -->
		</div> <!-- end container -->
	</div> <!-- end learner-container -->

        <div id="previewDiplomaModal" class="modal fade" role="dialog" data-backdrop="static">
                <div class="modal-dialog">
                        <div class="modal-content">
                                <div class="modal-header">
                                        <h3 class="modal-title">{{ trans('site.learner.preview-text') }}</h3>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                        <iframe src="" frameborder="0" width="100%" height="550">
                                        </iframe>
                                </div>
                        </div>
                </div>
        </div>

		<!-- Hidden trigger -->
		<button id="hiddenTrigger" type="button" data-toggle="modal" data-target="#unsavedAddressModal" style="display:none;"></button>

        <div id="unsavedAddressModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
							<h3 class="modal-title">Save Changes</h3>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
							<p>Do you want to save the new information?</p>
					</div>
					<div class="modal-footer">
							<button type="button" class="btn btn-primary save-changes">Yes</button>
							<button type="button" class="btn btn-secondary discard-changes" data-dismiss="modal">No</button>
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

                (function(){
                    var form = document.getElementById('profileForm');
                    if(!form) return;
                    var inputs = form.querySelectorAll("input[name='street'], input[name='zip'], input[name='city'], input[name='phone']");
                    var original = {};
                    inputs.forEach(function(input){ original[input.name] = input.value; });
                    var isDirty = false;
                    function checkDirty(){
                        isDirty = Array.prototype.some.call(inputs, function(input){
                            return input.value !== original[input.name];
                        });
                    }
                    inputs.forEach(function(input){ input.addEventListener('input', checkDirty); });
                    var targetHref = null;
                    window.addEventListener('beforeunload', function(e){
                        if(isDirty){
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                    document.querySelectorAll('a').forEach(function(a){
                        a.addEventListener('click', function(e){
                            if(isDirty){
                                e.preventDefault();
                                targetHref = this.href;
								document.getElementById('hiddenTrigger').click();
                                //$('#unsavedAddressModal').modal('show');	
                            }
                        });
                    });
                    $(window).on('keydown', function(e){
                        if(isDirty && (e.which === 116 || (e.which === 82 && e.ctrlKey))){
                            e.preventDefault();
                            targetHref = window.location.href;
							document.getElementById('hiddenTrigger').click();
                            //$('#unsavedAddressModal').modal('show');
                        }
                    });
                    $('#unsavedAddressModal .save-changes').on('click', function(){
						isDirty = false;
						form.submit();
					});
                    $('#unsavedAddressModal .discard-changes').on('click', function(){
                        isDirty = false;
                        $('#unsavedAddressModal').modal('hide');
                        if(targetHref){
                            window.location.href = targetHref;
                        }else{
                            window.location.reload();
                        }
                    });
                })();

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

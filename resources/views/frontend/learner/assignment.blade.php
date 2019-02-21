@extends('frontend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Assignments &rsaquo; Forfatterskolen</title>
@stop

@section('content')

	<div class="learner-container learner-assignment">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1 class="font-barlow-regular">
						Oppgaver
					</h1>
				</div>

				<div class="clearfix"></div>
				@foreach($assignments as $assignment)
					<div class="col-md-6 mt-5">
						<div class="card card-global">
                            <?php $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first(); ?>
                            <?php $extension = $manuscript ? explode('.', basename($manuscript->filename)) : ''; ?>

							<div class="card-header p-4">
								<div class="row">
									<div class="col-md-9">
										<h2><i class="contract-sign"></i> {{ $assignment->title }}</h2>
									</div>
									<div class="col-md-3">
										@if (!$manuscript)
											@if($assignment->for_editor)
												<button class="btn site-btn-global site-btn-global-sm w-100 submitEditorManuscriptBtn" data-toggle="modal"
														data-target="#submitEditorManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														data-show-group-question="{{ $assignment->show_join_group_question }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@else
												<button class="btn site-btn-global site-btn-global-sm w-100 submitManuscriptBtn" data-toggle="modal"
														data-target="#submitManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														data-show-group-question="{{ $assignment->show_join_group_question }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@endif
										@endif
									</div>
								</div> <!-- end row -->
							</div> <!-- end card-header -->
							<div class="card-body p-4">
								<p>
									{{ $assignment->description }}
								</p>

								<span class="font-barlow-regular">Frist:</span>
								<span>{{ \App\Http\FrontendHelpers::formatDateTimeNor2($assignment->submission_date) }}</span>
								@if( $manuscript )
									<div class="mt-3">
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">
												{{ basename($manuscript->filename) }}
											</a>
										@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">
												{{ basename($manuscript->filename) }}
											</a>
										@endif

										@if (!$manuscript->locked)
											<div class="pull-right">
												<button type="button" class="btn btn-sm btn-info editManuscriptBtn"
														data-toggle="modal" data-target="#editManuscriptModal"
														data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}">
													<i class="fa fa-pencil"></i>
												</button>
												<button type="button" class="btn btn-sm btn-danger deleteManuscriptBtn"
														data-toggle="modal" data-target="#deleteManuscriptModal"
														data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}">
													<i class="fa fa-trash"></i>
												</button>
											</div>
										@endif
									</div>
								@endif
							</div> <!-- end card-body -->
							<div class="card-footer p-4">
								<span class="font-barlow-regular">Kurs:</span>
								<span>{{ $assignment->course->title }}</span>
							</div> <!-- end card-body-->
						</div> <!-- end card -->
					</div> <!-- end col-md-6 -->
				@endforeach
			</div> <!-- end assignment section -->

			<div class="row">
				<div class="col-md-12 mt-5">
					<div class="row">
						<div class="col-md-6">
							<h1 class="font-barlow-regular">Grupper</h1>
                            <?php $assignmentGroups = App\AssignmentGroupLearner::where('user_id', Auth::user()->id)->get(); ?>
							@if( $assignmentGroups->count() > 0 )
								@foreach( $assignmentGroups as $group )
									<div class="card mt-5">
										<div class="card-header p-4">
											<h2>
												<i class="contract-sign"></i>
												<a href="{{ route('learner.assignment.group.show', $group->group->id) }}"
												class="h2-font">
													{{ $group->group->title }}
												</a>
											</h2>
										</div>
										<div class="card-body p-4">
											<span class="d-block">Oppgave: {{ $group->group->assignment->title }}</span>
											<span>Innleverings dato: {{ $group->group->submission_date }}</span>
										</div>
									</div>
								@endforeach
							@endif
						</div> <!-- end group section -->

						<div class="col-md-6 feedback-section">
							<h1 class="font-barlow-regular">Tilbakemelding fra redaktør</h1>

							<div class="card mt-5">
								<div class="card-header p-4">
									<h2>
										<i class="contract-sign"></i>
										Redaktor
									</h2>
								</div>
								<div class="card-body p-4">
                                    <?php
                                    $noGroupWithFeedback = \App\AssignmentFeedbackNoGroup::where('learner_id', Auth::user()->id)
										->orderBy('created_at', 'desc')
                                        ->get();
                                    ?>
									@if($noGroupWithFeedback->count() > 0)
										@foreach( $noGroupWithFeedback as $feedback )
											@if( $feedback->is_active && (!$feedback->availability ||  date('Y-m-d') >= $feedback->availability) )
												<div class="mb-4">
													<?php
													$files = explode(',',$feedback->filename);
													$filesDisplay = '';

													foreach ($files as $file) {
														$extension = explode('.', basename($file));

														if (end($extension) == 'pdf' || end($extension) == 'odt') {
															$filesDisplay .= '<a href="/js/ViewerJS/#../..'.trim($file).'">'.basename($file).'</a>, ';
														} else {
															$filesDisplay .= '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file).'">'.basename($file).'</a>, ';
														}
													}

													echo trim($filesDisplay, ', ');
													?>

													@if( $feedback->is_admin ) - Admin @endif

													<a href="{{route('learner.assignment.no-group-feedback.download', $feedback->id)}}"
													   class="pull-right btn site-btn-global site-btn-global-sm" style="width: 20%">
														Last ned
													</a>
												</div>
											@endif
										@endforeach
									@endif
								</div>
							</div>
						</div> <!-- end feedback section -->

					</div> <!-- end row -->
				</div> <!-- end col-md-12 -->
			</div> <!-- end group and feedback section-->

			<div class="divider-center-text">
				PAST ASSIGNMENTS
			</div>

			<div class="row past-assignment grid">
				@foreach($expiredAssignments as $assignment)
                    <?php $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first(); ?>
                    <?php $extension = $manuscript ? explode('.', basename($manuscript->filename)) : ''; ?>
					<div class="col-md-6 mb-5 grid-item">
						<div class="card">
							<div class="card-header py-4">
								<div class="row">
									<div class="col-md-9">
										<h2><i class="contract-sign"></i> {{ $assignment->title }}</h2>
									</div>
									<div class="col-md-3">
										@if (!$manuscript)
											@if($assignment->for_editor)
												<button class="btn site-btn-global site-btn-global-sm w-100 submitEditorManuscriptBtn" data-toggle="modal"
														data-target="#submitEditorManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														data-show-group-question="{{ $assignment->show_join_group_question }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@else
												<button class="btn site-btn-global site-btn-global-sm w-100 submitManuscriptBtn" data-toggle="modal"
														data-target="#submitManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														data-show-group-question="{{ $assignment->show_join_group_question }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@endif
										@endif
									</div> <!-- end column -->
								</div> <!-- end row-->
							</div> <!-- end card-header -->
							<div class="card-body">
								<p>
									{{ $assignment->description }}
								</p>

								<span class="font-barlow-regular">Frist:</span>
								<span>{{ \App\Http\FrontendHelpers::formatDateTimeNor2($assignment->submission_date) }}</span>
								@if( $manuscript )
									<div class="mt-3">
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">
												{{ basename($manuscript->filename) }}
											</a>
										@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">
												{{ basename($manuscript->filename) }}
											</a>
										@endif

										@if (!$manuscript->locked)
											<div class="pull-right">
												<button type="button" class="btn btn-sm btn-info editManuscriptBtn"
														data-toggle="modal" data-target="#editManuscriptModal"
														data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}">
													<i class="fa fa-pencil"></i>
												</button>
												<button type="button" class="btn btn-sm btn-danger deleteManuscriptBtn"
														data-toggle="modal" data-target="#deleteManuscriptModal"
														data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}">
													<i class="fa fa-trash"></i>
												</button>
											</div>
										@endif
									</div>
								@endif
							</div> <!-- end card-body -->
							<div class="card-footer p-4">
								<span class="font-barlow-regular">Kurs:</span>
								<span>{{ $assignment->course->title }}</span>
							</div> <!-- end card-body-->
						</div> <!-- end card -->
					</div> <!-- end grid-item -->
				@endforeach
			</div> <!-- end past-assignment section -->

		</div> <!-- end container -->
	</div> <!-- end learner-container -->

<div id="submitSuccessModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-body text-center">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
			  <p>
				  Din oppgave har blitt levert!
			  </p>
		  </div>
		</div>
	</div>
</div>

<div id="errorMaxword" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
				<p>
					Antall ord er for mange, maks {{ Session::get('editorMaxWord') }} ord. Rediger teksten og send inn på nytt.
				</p>
			</div>
		</div>
	</div>
</div>

<div id="submitEditorManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Last opp manus</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data"
				onsubmit="disableSubmit(this);">
					{{ csrf_field() }}
					<div class="form-group">
						<label>
							* Godkjente fil formater er DOC, DOCX.
						</label>
						<input type="file" class="form-control" required name="filename" accept="application/msword,
						application/vnd.openxmlformats-officedocument.wordprocessingml.document">
					</div>

					<div class="form-group">
						<label>
							Sjanger
						</label>
						<select class="form-control" name="type" required>
							<option value="" disabled="disabled" selected>Select Type</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label class="d-block">
							Hvor i manuset
						</label>
						@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
							<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
						@endforeach
					</div>

					<div class="join-question-container hide">
						<div class="form-group">
							<label>Ønsker du å gi og få tilbakemeldinger fra andre elever?</label> <br>
							<input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
						</div>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Upload</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="submitManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <h3 class="modal-title">Last opp manus</h3>
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
		      	{{ csrf_field() }}
				<div class="form-group">
					<label>
						* Godkjente fil formater er DOC, DOCX, PDF og ODT.
					</label>
					<input type="file" class="form-control margin-top" required name="filename" accept="application/msword,
					application/vnd.openxmlformats-officedocument.wordprocessingml.document,
					application/vnd.oasis.opendocument.text">
				</div>

				<div class="form-group">
					<label>
						Sjanger
					</label>
					<select class="form-control" name="type" required>
						<option value="" disabled="disabled" selected>Select Type</option>
						@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
							<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label class="d-block">Hvor i manuset</label>
					@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
						<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
					@endforeach
				</div>

				<div class="join-question-container hide">
					<div class="form-group">
						<label>Ønsker du å gi og få tilbakemeldinger fra andre elever?</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
					</div>
				</div>

		      	<button type="submit" class="btn btn-primary pull-right">Upload</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Replace manuscript</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Manuscript</label>
						<input type="file" class="form-control" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						* Godkjente fil formater er DOC, DOCX, PDF og ODT.
					</div>

					<button type="submit" class="btn btn-primary pull-right">Submit</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Delete manuscript</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p>
					Are you sure to delete this manuscript?
					Warning: This cannot be undone.
				</p>
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

@if(Session::has('manuscript_test_error'))
	<div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body text-center">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
					{!! Session::get('manuscript_test_error') !!}
				</div>
			</div>
		</div>
	</div>
@endif
@stop

@section('scripts')
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>

    // call the function once fully loaded
    $(window).on('load', function() {
        $('.grid').masonry({
            // options
            itemSelector : '.grid-item'
        });
    });

	@if (Session::has('success'))
	$('#submitSuccessModal').modal('show');
	@endif

	@if (Session::has('errorMaxWord'))
		$('#errorMaxword').modal('show');
    @endif

	@if(Session::has('manuscript_test_error'))
    	$('#manuscriptTestErrorModal').modal('show');
	@endif

	$('.submitManuscriptBtn').click(function(){
		let form = $('#submitManuscriptModal form');
        let action = $(this).data('action');
        let show_group_question = $(this).data('show-group-question');
		form.attr('action', action);

		if (show_group_question) {
		    form.find('.join-question-container').removeClass('hide');
		} else {
            form.find('.join-question-container').addClass('hide');
		}
	});

    $('.submitEditorManuscriptBtn').click(function(){
        let form = $('#submitEditorManuscriptModal form');
        let action = $(this).data('action');
        let show_group_question = $(this).data('show-group-question');
        form.attr('action', action);

        if (show_group_question) {
            form.find('.join-question-container').removeClass('hide');
        } else {
            form.find('.join-question-container').addClass('hide');
        }
    });

    $('.editManuscriptBtn').click(function(){
        let form = $('#editManuscriptModal form');
        let action = $(this).data('action');
        form.attr('action', action);
    });

    $('.deleteManuscriptBtn').click(function(){
        let form = $('#deleteManuscriptModal form');
        let action = $(this).data('action');
        form.attr('action', action)
    });
</script>
@stop


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
						{{ trans('site.learner.assignment') }}
					</h1>
				</div>

				<div class="clearfix"></div>
				@foreach($assignments as $assignment)
					<div class="col-md-6 mt-5">
						<div class="card card-global">
                            <?php $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first(); ?>
                            <?php
								$extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
								$submission_date_formatted = $assignment->submission_date;
								if (!\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
									$coursesTaken = Auth::user()->coursesTaken()->get()->toArray();
                                    $allowed_packages = $assignment->allowed_package ?
                                        json_decode($assignment->allowed_package) : [];

									$courseStarted = '';
									foreach ($coursesTaken as $course) {
										if (in_array($course['package_id'], $allowed_packages)) {
											$courseStarted =  $course['started_at'];
										}
									}

									$submission_date_formatted = \Carbon\Carbon::parse($courseStarted)
										->addDays($assignment->submission_date);
								}
                            ?>

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
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($submission_date_formatted))) disabled @endif>
													{{ trans('site.learner.upload-script') }}
												</button>
											@else
												<button class="btn site-btn-global site-btn-global-sm w-100 submitManuscriptBtn" data-toggle="modal"
														data-target="#submitManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														data-show-group-question="{{ $assignment->show_join_group_question }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($submission_date_formatted))) disabled @endif>
													{{ trans('site.learner.upload-script') }}
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

								<span class="font-barlow-regular">{{ trans('site.learner.deadline') }}:</span>
								<span>{{ \App\Http\FrontendHelpers::formatDateTimeNor($submission_date_formatted) }}</span>
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
													<i class="fa fa-pen"></i>
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
							@if($assignment->course)
								<div class="card-footer p-4">
									<span class="font-barlow-regular">{{ trans('site.front.course-text') }}:</span>
									<span>{{ $assignment->course->title }}</span>
								</div> <!-- end card-footer -->
							@endif
						</div> <!-- end card -->
					</div> <!-- end col-md-6 -->
				@endforeach
			</div> <!-- end assignment section -->

			<div class="row">
				<div class="col-md-12 mt-5">
					<div class="row">
						<div class="col-md-6">
							<h1 class="font-barlow-regular">
								{{ trans('site.learner.groups') }}
							</h1>
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
											<span class="d-block">{{ trans('site.front.course-text') }}:
												{{ $group->group->assignment->course->title }}
											</span>
											<span class="d-block">{{ trans('site.learner.assignment-single') }}:
												{{ $group->group->assignment->title }}
											</span>
											<?php
                                            	/*$submission_date = strtr(trans('site.learner.submission-date-value'), [
													   '_date_' => \Carbon\Carbon::parse($group->group->submission_date)->format('d M Y'),
														'_time_' => \Carbon\Carbon::parse($group->group->submission_date)->format('H:i')
													]);*/
                                            	$submission_date = \App\Http\FrontendHelpers::formatDateTimeNor($group->group->submission_date);
											?>
											<span>{{ trans('site.learner.submission-date') }}:
												{{ $submission_date }}
											</span>
										</div>
									</div>
								@endforeach
							@endif
						</div> <!-- end group section -->

						<div class="col-md-6 feedback-section">
							<h1 class="font-barlow-regular">
								{{ trans('site.learner.feedback-from-editor') }}
							</h1>

							<div class="card mt-5">
								<div class="card-header p-4">
									<h2>
										<i class="contract-sign"></i>
										{{ trans('site.learner.editor-text') }}
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
											@if( $feedback->is_active && (!$feedback->availability ||  date('Y-m-d') >= $feedback->availability) && $feedback->manuscript->status)
												<div class="mb-4">
													<?php
													$files = explode(',',$feedback->filename);
													$title = $feedback->manuscript->assignment->course
                                                        ? $feedback->manuscript->assignment->course->title
														: $feedback->manuscript->assignment->title;
													$titleLabel = $feedback->manuscript->assignment->course
														? trans('site.front.course-text')
														: trans('site.learner.assignment');

													$filesDisplay = $titleLabel . ': '. $title . '<br/> ';
													echo $filesDisplay
													?>
													{!! $feedback->file_link !!}

													@if( $feedback->is_admin ) - {{ trans('site.learner.admin-text') }} @endif

													<a href="{{route('learner.assignment.no-group-feedback.download', $feedback->id)}}"
													   class="pull-right btn site-btn-global site-btn-global-sm" style="width: 20%">
														{{ trans('site.learner.download-text') }}
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
				{{ strtoupper(trans('site.learner.past-assignments')) }}
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
										<?php
										$submission_date_formatted = $assignment->submission_date;
										if (!\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
										    $coursesTaken = Auth::user()->coursesTaken()->get()->toArray();
										    $allowed_packages = $assignment->allowed_package ?
												json_decode($assignment->allowed_package) : [];

                                            $courseStarted = '';
										    foreach ($coursesTaken as $course) {
                                                if (in_array($course['package_id'], $allowed_packages)) {
                                                    $courseStarted =  $course['started_at'];
                                                }
											}

                                            $submission_date_formatted = \Carbon\Carbon::parse($courseStarted)
												->addDays($assignment->submission_date);
										}
										?>
										@if (!$manuscript)
											@if($assignment->for_editor)
												<button class="btn site-btn-global site-btn-global-sm w-100 submitEditorManuscriptBtn" data-toggle="modal"
														data-target="#submitEditorManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														data-show-group-question="{{ $assignment->show_join_group_question }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($submission_date_formatted))) disabled @endif>
													{{ trans('site.learner.upload-script') }}
												</button>
											@else
												<button class="btn site-btn-global site-btn-global-sm w-100 submitManuscriptBtn" data-toggle="modal"
														data-target="#submitManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														data-show-group-question="{{ $assignment->show_join_group_question }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($submission_date_formatted))) disabled @endif>
													{{ trans('site.learner.upload-script') }}
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
								<span>{{--{{ \App\Http\FrontendHelpers::formatDateTimeNor2($assignment->submission_date) }}--}}</span>
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
							@if($assignment->course)
								<div class="card-footer p-4">
									<span class="font-barlow-regular">{{ trans('site.front.course-text') }}:</span>
									<span>{{ $assignment->course->title }}</span>
								</div> <!-- end card-footer -->
							@endif
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
				  {{ trans('site.learner.submit-success-text') }}
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
					{{ strtr(trans('site.learner.error-max-word-text'),
                    ['_word_count_' => Session::get('editorMaxWord')]) }}
				</p>
			</div>
		</div>
	</div>
</div>

<div id="submitEditorManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">
					{{ trans('site.learner.upload-script') }}
				</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data"
				onsubmit="disableSubmit(this);">
					{{ csrf_field() }}
					<div class="form-group">
						<label>
							* {{ trans('site.learner.manuscript.doc-format-text') }}
						</label>
						<input type="file" class="form-control" required name="filename" accept="application/msword,
						application/vnd.openxmlformats-officedocument.wordprocessingml.document">
					</div>

					<div class="form-group">
						<label>
							{{ trans('site.front.genre') }}
						</label>
						<select class="form-control" name="type" required>
							<option value="" disabled="disabled" selected>
								{{ trans('site.front.select-genre') }}
							</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label class="d-block">
							{{ trans('site.learner.manuscript.where-in-manuscript') }}
						</label>
						@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
							<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
						@endforeach
					</div>

					<div class="join-question-container hide">
						<div class="form-group">
							<label>{{ trans('site.learner.join-group-question') }}?</label> <br>
							<input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
						</div>
					</div>

					<button type="submit" class="btn btn-primary pull-right">
						{{ trans('site.front.upload') }}
					</button>
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
		    <h3 class="modal-title">
				{{ trans('site.learner.upload-script') }}
			</h3>
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
		      	{{ csrf_field() }}
				<div class="form-group">
					<label>
						* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
					</label>
					<input type="file" class="form-control margin-top" required name="filename" accept="application/msword,
					application/vnd.openxmlformats-officedocument.wordprocessingml.document,
					application/vnd.oasis.opendocument.text,application/pdf">
				</div>

				<div class="form-group">
					<label>
						{{ trans('site.front.genre') }}
					</label>
					<select class="form-control" name="type" required>
						<option value="" disabled="disabled" selected>
							{{ trans('site.front.select-genre') }}
						</option>
						@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
							<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label class="d-block">
						{{ trans('site.learner.manuscript.where-in-manuscript') }}
					</label>
					@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
						<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
					@endforeach
				</div>

				<div class="join-question-container hide">
					<div class="form-group">
						<label>{{ trans('site.learner.join-group-question') }}?</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
					</div>
				</div>

		      	<button type="submit" class="btn btn-primary pull-right">
					{{ trans('site.front.upload') }}
				</button>
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
				<h3 class="modal-title">
					{{ trans('site.learner.manuscript.replace-manuscript') }}
				</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>
							{{ trans('site.learner.manuscript-text') }}
						</label>
						<input type="file" class="form-control" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
					</div>

					<button type="submit" class="btn btn-primary pull-right">
						{{ trans('site.front.submit') }}
					</button>
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
				<h3 class="modal-title">
					{{ trans('site.learner.delete-manuscript.title') }}
				</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p>
					{{ trans('site.learner.delete-manuscript.question') }}
				</p>
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger pull-right margin-top">
						{{ trans('site.learner.delete') }}
					</button>
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


{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $group->title }} &rsaquo; Assignments &rsaquo; Forfatterskolen</title>
@stop

@section('content')

<div class="learner-container learner-assignment-show">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1 class="font-barlow-regular w-100">{{ $group->title }}</h1>
				<h3 class="w-100 font-weight-normal font-barlow-regular mb-0">Oppgave: {{ $group->assignment->title }}</h3>
				@if ($group->submission_date)
					<h3 class="w-100 font-weight-normal font-barlow-regular mb-0">
						{{ trans('site.learner.deadline') }} {{ ucwords(strtr(trans('site.learner.submission-date-value'), [
						   '_date_' => \Carbon\Carbon::parse($group->submission_date)->format('d.m.Y'),
							'_time_' => \Carbon\Carbon::parse($group->submission_date)->format('H:i')
						])) }}
					</h3>
				@endif
			</div> <!-- end col-sm-12 -->
		</div> <!-- end row -->

		<div class="row">
            <?php $i = 1; ?>
			@foreach( $groupLearnerList as $learner )
				<div class="col-md-4 mt-5">
					<div class="card card-global">
						<div class="card-header">
							<h2 class="font-barlow-regular">
								@if( $learner->user->id == Auth::user()->id )
									{{ trans('site.learner.you-text') }}
								@else
									{{ trans('site.learner.learner-text') }} {{ $learner->user->id }} {{--old value is $i--}}
								@endif
							</h2>
						</div>
						<div class="card-body">
                            <?php $manuscript = $group->assignment->manuscripts->where('user_id', $learner->user_id)->first(); ?>
                            <?php $extension = explode('.', basename($manuscript->filename)); ?>
							<p>
								@if( $manuscript->filename )
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
										<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
									@elseif( end($extension) == 'docx' || end($extension) == 'doc')
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
									@endif

									@if( $learner->user->id !== Auth::user()->id )
										<a href="{{route('learner.assignment.manuscript.download', $manuscript->id)}}"
										   class="pull-right btn site-btn-global site-btn-global-sm w-25">
											{{ trans('site.learner.download-text') }}
										</a>
									@endif
									<br>
									@if ($manuscript->type)
										<span class="font-barlow-semi-bold">
											{{ \App\Http\FrontendHelpers::assignmentType($manuscript->type) }}
										</span>
									@endif

									@if ($manuscript->manu_type)
										- <span class="font-barlow-italic">
												{{ \App\Http\FrontendHelpers::manuscriptType($manuscript->manu_type) }}
											</span>
									@endif

								@else
									<em>{{ trans('site.learner.no-manuscript-uploaded-text') }}</em>
								@endif
							</p>
						</div>
						<div class="card-footer p-0">
							@if( $learner->user->id == Auth::user()->id )
								@if( $manuscript->filename )
									<button type="button" class="btn site-btn-global w-100 rounded-0 disabled">
										{{ trans('site.learner.script-is-uploaded-text') }}
									</button>
								@endif
							@else
                                <?php $feedback = App\AssignmentFeedback::where('assignment_group_learner_id',
									$learner->id)->where('user_id', Auth::user()->id)->first(); ?>
								@if( $feedback )
									<button type="button" class="btn site-btn-global w-100 rounded-0 disabled">
										@if( $feedback->is_active )
											{{ trans('site.learner.feeback-provided') }}
										@else
											{{ trans('site.learner.delivered-text') }}
										@endif
									</button>
									@if( !$feedback->is_active && !$feedback->locked)
										<button type="button" class="btn btn-danger deleteManuscriptBtn pull-right w-50 rounded-0 font-16"
												data-toggle="modal" data-target="#deleteManuscriptModal"
												data-action="{{ route('learner.assignment.group.delete_feedback', $feedback->id) }}">
											<i class="fa fa-trash"></i>
										</button>
										<button type="button" class="btn btn-info editManuscriptBtn pull-right w-50 rounded-0 font-16"
												data-toggle="modal" data-target="#editManuscriptModal"
												data-action="{{ route('learner.assignment.group.replace_feedback', $feedback->id) }}">
											<i class="fa fa-pencil"></i>
										</button>
									@endif

								@else
									{{-- if is included when used is $group->learners --}}
									{{--@if(in_array($learner->id, $could_send_feedback_to))--}}
										<button type="button" class="btn site-btn-global w-100 rounded-0 submitFeedbackBtn"
												data-toggle="modal" data-target="#submitFeedbackModal"
												data-name="Learner {{ $i }}"
												data-action="{{ route('learner.assignment.group.submit_feedback',
												['group_id' => $group->id, 'id' => $learner->id]) }}">
											{{ trans('site.learner.give-feedback') }}
										</button>
									{{--@endif--}}
								@endif
							@endif
						</div> <!-- end card-footer -->
					</div> <!-- end card -->
				</div> <!-- end col-md-4 -->
				<?php $i++; ?>
			@endforeach
		</div> <!-- end row -->

        <?php
        	$groupLearner = $group->learners->where('user_id', Auth::user()->id)->first();
        	$feedbacks = App\AssignmentFeedback::where('assignment_group_learner_id', $groupLearner->id)->orderBy('created_at', 'desc')->get();
        ?>

		@if( $feedbacks->count() > 0 && $assignmentManuscript->status)
			<div class="row mt-5">
				<div class="col-md-6">
					<div class="card">
						<div class="card-header py-4">
							<div class="row">
								<div class="col-lg-9 col-md-8">
									<h2 class="font-barlow-regular">
										{{ trans('site.learner.feedback-text') }}
									</h2>
								</div>
								<div class="col-lg-3 col-md-4">
									@if ($group->allow_feedback_download)
										<a href="{{ route('learner.assignment.group.feedback.download-all', $group->id) }}"
										   class="btn site-btn-global site-btn-global-sm w-100">
											{{ trans('site.learner.download-all') }}
										</a>
									@endif
								</div>
							</div>
						</div> <!-- end card-header -->
						<div class="card-body">
							@foreach( $feedbacks as $feedback )
								@if( $feedback->is_active && (!$feedback->availability ||  date('Y-m-d') >= $feedback->availability)
								 && (($manuscript->editor_id === $feedback->user_id && $manuscript->status) || $manuscript->editor_id !== $feedback->user_id))
									<p>
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

										@if( $feedback->is_admin ) - {{ trans('site.learner.admin-text') }} @endif

										<a href="{{route('learner.assignment.feedback.download', $feedback->id)}}"
										   class="pull-right btn site-btn-global site-btn-global-sm w-25">
											{{ trans('site.learner.download-text') }}
										</a>
									</p>
								@endif
							@endforeach
						</div> <!-- end card-body -->
					</div> <!-- end card-->
				</div> <!-- end col-md-6 -->
			</div> <!-- end row-->
		@endif

	</div> <!-- end container -->
</div> <!-- end learner-container -->


<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			  <h3 class="modal-title">{{ trans('site.learner.submit-feedback-to') }} <em></em></h3>
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
			  <form method="POST" action=""  enctype="multipart/form-data">
		      	{{ csrf_field() }}
				  <div class="form-group">
					  <label>* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</label>
					  <input type="file" class="form-control margin-top" required multiple name="filename[]"
							 accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							 application/pdf, application/vnd.oasis.opendocument.text">
				  </div>

		      	<button type="submit" class="btn btn-primary pull-right">{{ trans('site.front.submit') }}</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.replace-feedback') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</label>
						<input type="file" class="form-control margin-top" required name="filename"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							   application/pdf, application/vnd.oasis.opendocument.text">
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.front.submit') }}</button>
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
				<h3 class="modal-title">{{ trans('site.learner.delete-feedback') }}</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p>
					{{ trans('site.learner.delete-feedback-question') }}
				</p>
				<form method="POST" action="">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger pull-right">{{ trans('site.learner.delete') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
<script>
	$('.submitFeedbackBtn').click(function(){
		var modal = $('#submitFeedbackModal');
		var name = $(this).data('name');
		var action = $(this).data('action');
		modal.find('em').text(name);
		modal.find('form').attr('action', action);
	});

    $('.editManuscriptBtn').click(function(){
        var form = $('#editManuscriptModal form');
        var action = $(this).data('action');
        form.attr('action', action);
    });

    $('.deleteManuscriptBtn').click(function(){
        var form = $('#deleteManuscriptModal form');
        var action = $(this).data('action');
        form.attr('action', action)
    });
</script>
@stop


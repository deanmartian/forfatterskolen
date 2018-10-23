@extends('frontend.layout')

@section('title')
<title>Assignments &rsaquo; Forfatterskolen</title>
@stop


@section('styles')
	<style>
		.no-padding-left {
			padding-left: 0;
		}
	</style>
@stop

@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Oppgaver</h3>
			<div class="row">
				@foreach(array_chunk($assignments, 3) as $assignment_chunk)
					<div class="col-sm-12">
						@foreach($assignment_chunk as $assignment)
							<div class="col-md-4 no-padding-left">
								<div class="panel panel-default">
									<div class="panel-body">
										<h4 class="no-margin-top no-margin-bottom">{{ $assignment->title }}</h4>
										{{ $assignment->description }} <br>
										<b>Frist:</b> <i>{{ \App\Http\FrontendHelpers::formatDateTimeNor2($assignment->submission_date) }}</i>
                                        <?php $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first(); ?>
                                        <?php $extension = $manuscript ? explode('.', basename($manuscript->filename)) : ''; ?>
										<div class="margin-top margin-bottom">
											Kurs: {{ $assignment->course->title }} <br />
											@if( $manuscript )Ord:  {{ $manuscript->words }} <br />@endif
										</div>
										@if( $manuscript )
											@if( end($extension) == 'pdf' || end($extension) == 'odt' )
												<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
											@elseif( end($extension) == 'docx' )
												<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
											@endif

											@if (!$manuscript->locked)
												<div class="pull-right">
													<button type="button" class="btn btn-sm btn-info editManuscriptBtn" data-toggle="modal" data-target="#editManuscriptModal" data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}"><i class="fa fa-pencil"></i></button>
													<button type="button" class="btn btn-sm btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}"><i class="fa fa-trash"></i></button>
												</div>
											@endif
										@else
											@if($assignment->for_editor)
												<button class="btn btn-primary submitEditorManuscriptBtn" data-toggle="modal"
														data-target="#submitEditorManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@else
												<button class="btn btn-primary submitManuscriptBtn" data-toggle="modal"
														data-target="#submitManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@endif
										@endif
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
			<?php $assignmentGroups = App\AssignmentGroupLearner::where('user_id', Auth::user()->id)->get(); ?>
			@if( $assignmentGroups->count() > 0 )
				<br />

					<hr>

				<h3 class="margin-top">Grupper</h3>
				<div class="row">
					@foreach( $assignmentGroups as $group )
					<div class="col-sm-12 col-md-4">
						<div class="panel panel-default">
							<div class="panel-body">
								<h4 class="no-margin-top margin-bottom"><a href="{{ route('learner.assignment.group.show', $group->group->id) }}">{{ $group->group->title }}</a></h4>
								Oppgave: {{ $group->group->assignment->title }} <br>
								Innleverings dato: {{ $group->group->submission_date }}
							</div>
						</div>
					</div>
					@endforeach
				</div>
			@endif

			<?php
				$noGroupWithFeedback = \App\AssignmentFeedbackNoGroup::where('learner_id', Auth::user()->id)
                    ->orderBy('created_at', 'desc')
					->get();
			?>
			@if($noGroupWithFeedback->count() > 0)
				<hr>
				<h3 class="margin-top">Tilbakemelding fra redaktør</h3>
				<div class="row">
					<div class="col-sm-4">
						<div class="panel panel-default">
							<div class="panel-body">
								@foreach( $noGroupWithFeedback as $feedback )
									@if( $feedback->is_active && (!$feedback->availability ||  date('Y-m-d') >= $feedback->availability) )
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

										<a href="{{route('learner.assignment.no-group-feedback.download', $feedback->id)}}" class="pull-right btn btn-primary btn-xs">Last ned</a>
										<div class="clearfix" style="margin-top: 5px"></div>
									@endif
								@endforeach
							</div>
						</div>
					</div>
				</div>
			@endif

				<hr>

			<div class="row">
				@foreach(array_chunk($expiredAssignments, 3) as $assignment_chunk)
					<div class="col-sm-12">
						@foreach($assignment_chunk as $assignment)
							<div class="col-md-4 no-padding-left">
								<div class="panel panel-default">
									<div class="panel-body">
										<h4 class="no-margin-top no-margin-bottom">{{ $assignment->title }}</h4>
										{{ $assignment->description }} <br>
										<b>Frist:</b> <i>{{ \App\Http\FrontendHelpers::formatDateTimeNor2($assignment->submission_date) }}</i>
                                        <?php $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first(); ?>
                                        <?php $extension = $manuscript ? explode('.', basename($manuscript->filename)) : ''; ?>
										<div class="margin-top margin-bottom">
											Kurs: {{ $assignment->course->title }} <br />
											@if( $manuscript )Ord:  {{ $manuscript->words }} <br />@endif
										</div>
										@if( $manuscript )
											@if( end($extension) == 'pdf' || end($extension) == 'odt' )
												<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
											@elseif( end($extension) == 'docx' )
												<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
											@endif

											@if (!$manuscript->locked)
												<div class="pull-right">
													<button type="button" class="btn btn-sm btn-info editManuscriptBtn" data-toggle="modal" data-target="#editManuscriptModal" data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}"><i class="fa fa-pencil"></i></button>
													<button type="button" class="btn btn-sm btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}"><i class="fa fa-trash"></i></button>
												</div>
											@endif
										@else
											@if($assignment->for_editor)
												<button class="btn btn-primary submitEditorManuscriptBtn" data-toggle="modal"
														data-target="#submitEditorManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@else
												<button class="btn btn-primary submitManuscriptBtn" data-toggle="modal"
														data-target="#submitManuscriptModal"
														data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
														@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif>
													Last opp manus
												</button>
											@endif
										@endif
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
		</div>
	</div>
	<div class="clearfix"></div>

</div>

<div id="submitSuccessModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-body text-center">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
		  	Din oppgave har blitt levert!
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
				Antall ord er for mange, maks {{ Session::get('editorMaxWord') }} ord. Rediger teksten og send inn på nytt.
			</div>
		</div>
	</div>
</div>

<div id="submitEditorManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Last opp manus</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data"
				onsubmit="disableSubmit(this);">
					{{ csrf_field() }}
					* Godkjente fil formater er DOCX.
					<input type="file" class="form-control margin-top" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">

					<div class="form-group margin-top">
						Sjanger
						<select class="form-control" name="type" required>
							<option value="" disabled="disabled" selected>Select Type</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						Hvor i manuset <br>
						@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
							<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
						@endforeach
					</div>
					<button type="submit" class="btn btn-primary pull-right margin-top">Upload</button>
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
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Last opp manus</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" enctype="multipart/form-data">
		      	{{ csrf_field() }}
      			* Godkjente fil formater er DOCX, PDF og ODT.
      			<input type="file" class="form-control margin-top" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">

				<div class="form-group margin-top">
					Sjanger
					<select class="form-control" name="type" required>
						<option value="" disabled="disabled" selected>Select Type</option>
						@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
							<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					Hvor i manuset <br>
					@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
						<input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label>{{ $manu['option'] }}</label> <br>
					@endforeach
				</div>
		      	<button type="submit" class="btn btn-primary pull-right margin-top">Upload</button>
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
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Replace manuscript</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Manuscript</label>
						<input type="file" class="form-control" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						* Godkjente fil formater er DOCX, PDF og ODT.
					</div>

					<button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
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
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete manuscript</h4>
			</div>
			<div class="modal-body">
				Are you sure to delete this manuscript?
				Warning: This cannot be undone.
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
	@if (Session::has('success'))
	$('#submitSuccessModal').modal('show');
	@endif

	@if (Session::has('errorMaxWord'))
		$('#errorMaxword').modal('show');
    @endif

$('.submitManuscriptBtn').click(function(){
		var form = $('#submitManuscriptModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

    $('.submitEditorManuscriptBtn').click(function(){
        var form = $('#submitEditorManuscriptModal form');
        var action = $(this).data('action');
        form.attr('action', action);
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


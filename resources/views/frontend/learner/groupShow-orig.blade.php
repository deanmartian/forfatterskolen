@extends('frontend.layout')

@section('title')
<title>{{ $group->title }} &rsaquo; Assignments &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top no-margin-bottom">{{ $group->title }}</h3>
			Oppgave: {{ $group->assignment->title }} <br>
			@if ($group->submission_date)
			Frist {{ \Carbon\Carbon::parse($group->submission_date)->format('d.m.Y') }}
			klokken {{ \Carbon\Carbon::parse($group->submission_date)->format('H:i') }}
			@endif
			<div class="row"> 
				<?php $i = 1; ?>
				@foreach( $group->learners as $learner )
				<div class="col-sm-4">
					<div class="panel panel-default margin-top">
						<div class="panel-body">
							<h4>
								@if( $learner->user->id == Auth::user()->id )
								Deg
								@else
								Elev {{ $learner->user->id }} {{--old value is $i--}}
								@endif
							</h4>
							<p class="margin-top no-margin-bottom">

								<?php $manuscript = $group->assignment->manuscripts->where('user_id', $learner->user_id)->first(); ?>
								<?php $extension = explode('.', basename($manuscript->filename)); ?>
								@if( $manuscript->filename )
									@if( end($extension) == 'pdf' || end($extension) == 'odt' )
									<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
									@elseif( end($extension) == 'docx' || end($extension) == 'doc')
									<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
									@endif

										@if( $learner->user->id !== Auth::user()->id )
										<a href="{{route('learner.assignment.manuscript.download', $manuscript->id)}}" class="pull-right btn btn-primary btn-xs">Last ned</a>
										@endif
										<br>
									@if ($manuscript->type)
										<b>{{ \App\Http\FrontendHelpers::assignmentType($manuscript->type) }}</b>
									@endif

									@if ($manuscript->manu_type)
										- <i>{{ \App\Http\FrontendHelpers::manuscriptType($manuscript->manu_type) }}</i>
									@endif

								@else
									<em>No manuscript uploaded</em>
								@endif

								<br />
								@if( $learner->user->id == Auth::user()->id )
									@if( $manuscript->filename )
										<button type="button" class="btn btn-primary btn-sm margin-top disabled">Manuscript uploaded</button>
									@endif
								@else
								<?php $feedback = App\AssignmentFeedback::where('assignment_group_learner_id', $learner->id)->where('user_id', Auth::user()->id)->first(); ?>
								@if( $feedback )
								<button type="button" class="btn btn-warning btn-sm margin-top disabled">
									@if( $feedback->is_active ) Tilbakemelding levert
									@else levert
									@endif
								</button>

									@if( !$feedback->is_active && !$feedback->locked)
										<button type="button" class="btn btn-sm btn-danger deleteManuscriptBtn pull-right margin-top" data-toggle="modal" data-target="#deleteManuscriptModal" data-action="{{ route('learner.assignment.group.delete_feedback', $feedback->id) }}"><i class="fa fa-trash"></i></button>
										<button type="button" class="btn btn-sm btn-info editManuscriptBtn pull-right margin-top margin-right-5" data-toggle="modal" data-target="#editManuscriptModal" data-action="{{ route('learner.assignment.group.replace_feedback', $feedback->id) }}"><i class="fa fa-pencil"></i></button>
									@endif

								@else
								<button type="button" class="btn btn-warning btn-sm margin-top submitFeedbackBtn" data-toggle="modal" data-target="#submitFeedbackModal" data-name="Learner {{ $i }}" data-action="{{ route('learner.assignment.group.submit_feedback', ['group_id' => $group->id, 'id' => $learner->id]) }}">Gi tilbakemelding</button>
								@endif
								@endif
							</p>
						</div>
					</div>
				</div>
				<?php $i++; ?>
				@endforeach
			</div>


			<?php 
			$groupLearner = $group->learners->where('user_id', Auth::user()->id)->first();
			$feedbacks = App\AssignmentFeedback::where('assignment_group_learner_id', $groupLearner->id)->orderBy('created_at', 'desc')->get(); 
			?>
			@if( $feedbacks->count() > 0 )
			<div class="row margin-top"> 
				<div class="col-sm-4">
					<h4>
						Tilbakemelding
						@if ($group->allow_feedback_download)
						<a href="{{ route('learner.assignment.group.feedback.download-all', $group->id) }}" class="btn btn-primary btn-xs pull-right">Last ned alle</a>
						@endif
					</h4>{{--feedbacks--}}
					<div class="panel panel-default">
						<div class="panel-body">
							@foreach( $feedbacks as $feedback )
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

										<a href="{{route('learner.assignment.feedback.download', $feedback->id)}}" class="pull-right btn btn-primary btn-xs">Last ned</a>
										<div class="clearfix" style="margin-top: 5px"></div>
								@endif
							@endforeach
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
	<div class="clearfix"></div>

</div>



<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Submit feedback to <em></em></h4>
		  </div>
		  <div class="modal-body">
			  <form method="POST" action=""  enctype="multipart/form-data">
		      	{{ csrf_field() }}
      			* Godkjente fil formater er DOCX, PDF og ODT.
      			<input type="file" class="form-control margin-top" required multiple name="filename[]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">

		      	<button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
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
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Replace feedback</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					{{ csrf_field() }}
					* Godkjente fil formater er DOCX, PDF og ODT.
					<input type="file" class="form-control margin-top" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">

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
				<h4 class="modal-title">Delete feedback</h4>
			</div>
			<div class="modal-body">
				Are you sure to delete this feedback?
				Warning: This cannot be undone.
				<form method="POST" action="">
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


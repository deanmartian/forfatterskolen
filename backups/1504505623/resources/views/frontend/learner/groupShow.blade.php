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
			Assignment: {{ $group->assignment->title }}
			<div class="row"> 
				<?php $i = 1; ?>
				@foreach( $group->learners as $learner )
				<div class="col-sm-4">
					<div class="panel panel-default margin-top">
						<div class="panel-body">
							<h4>
								@if( $learner->user->id == Auth::user()->id )
								You
								@else
								Learner {{ $i }}
								@endif
							</h4>
							<p class="margin-top no-margin-bottom">

								<?php $manuscript = $group->assignment->manuscripts->where('user_id', $learner->user_id)->first(); ?>
								@if( $manuscript->filename )
									<a href="/storage/assignment-manuscripts/{{ basename($manuscript->filename) }}">{{ basename($manuscript->filename) }}</a>
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
								<button type="button" class="btn btn-warning btn-sm margin-top disabled">Feedback submitted</button>
								@else
								<button type="button" class="btn btn-warning btn-sm margin-top submitFeedbackBtn" data-toggle="modal" data-target="#submitFeedbackModal" data-name="Learner {{ $i }}" data-action="{{ route('learner.assignment.group.submit_feedback', ['group_id' => $group->id, 'id' => $learner->id]) }}">Submit feedback</button>
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
			$feedbacks = App\AssignmentFeedback::where('assignment_group_learner_id', $groupLearner->id)->get(); 
			?>
			@if( $feedbacks->count() > 0 )
			<div class="row margin-top"> 
				<div class="col-sm-4">
					<h4>Feedbacks</h4>
					<div class="panel panel-default">
						<div class="panel-body">
							@foreach( $feedbacks as $feedback )
							<a href=""> {{ basename($feedback->filename) }} </a> @if( $feedback->is_admin ) - Admin @endif <br />
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
      			* Accepted file formats are DOCX, PDF, ODT.
      			<input type="file" class="form-control margin-top" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">

		      	<button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
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
</script>
@stop


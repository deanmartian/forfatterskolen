@extends('frontend.layout')

@section('title')
<title>Assignments &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<h3 class="no-margin-top">Assignments</h3>
			<div class="row">
				@foreach( $assignments as $assignment )
				<div class="col-sm-12 col-md-4">
					<div class="panel panel-default">
						<div class="panel-body">
							<h4 class="no-margin-top no-margin-bottom">{{ $assignment->title }}</h4>
							{{ $assignment->description }}
							<div class="margin-top margin-bottom">
							Kurs: {{ $assignment->course->title }}
							</div>
							<?php $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first(); ?>
							@if( $manuscript )
							<a href="{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
							@else
							<button class="btn btn-primary submitManuscriptBtn" data-toggle="modal" data-target="#submitManuscriptModal" data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}">Submit manuscript</button>
							@endif
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<?php $assignmentGroups = App\AssignmentGroupLearner::where('user_id', Auth::user()->id)->get(); ?>
			@if( $assignmentGroups->count() > 0 )
			<br />
			<h3 class="margin-top">Assignment groups</h3>
			<div class="row">
				@foreach( $assignmentGroups as $group )
				<div class="col-sm-12 col-md-4">
					<div class="panel panel-default">
						<div class="panel-body">
							<h4 class="no-margin-top margin-bottom"><a href="{{ route('learner.assignment.group.show', $group->group->id) }}">{{ $group->group->title }}</a></h4>
							Assignment: {{ $group->group->assignment->title }}
						</div>
					</div>
				</div>
				@endforeach
			</div>
			@endif
		</div>
	</div>
	<div class="clearfix"></div>

</div>

<div id="submitManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Submit manuscript</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" enctype="multipart/form-data">
		      	{{ csrf_field() }}
      			* Accepted file formats are DOCX, PDF, ODT.
      			<input type="file" class="form-control margin-top" required name="filename" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">

		      	<button type="submit" class="btn btn-primary pull-right margin-top">Upload</button>
		      	<div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
	$('.submitManuscriptBtn').click(function(){
		var form = $('#submitManuscriptModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});
</script>
@stop


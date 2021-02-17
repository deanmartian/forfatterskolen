@extends('backend.layout')

@section('title')
<title>Manuscripts &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3> 
	<?php $extension = explode('.', basename($manuscript->filename)); ?>
	@if( end($extension) == 'pdf' )
	<i class="fa fa-file-pdf-o"></i> 
	@elseif( end($extension) == 'docx' )
	<i class="fa fa-file-word-o"></i> 
	@elseif( end($extension) == 'odt' )
	<i class="fa fa-file-text-o"></i> 
	@endif
	Manuscript <em>{{ basename($manuscript->filename) }}</em> for course <em>{{ $manuscript->courseTaken->package->course->title }}</em></h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="Search manuscript..">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="col-md-12">
	<div class="margin-top">
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12 col-md-7">
							@if( end($extension) == 'pdf' || end($extension) == 'odt' )
							<iframe src="/js/ViewerJS/#../..{{ $manuscript->filename }}" style="width: 100%; border: 0; height: 600px"></iframe>
							@elseif( end($extension) == 'docx' )
							<iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}" style="width: 100%; border: 0; height: 600px"></iframe>
							@endif
						</div>
						<div class="col-sm-12 col-md-5">
							<div class="pull-right">
								<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editManuscriptModal"><i class="fa fa-pencil"></i></button>
								<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteManuscriptModal"><i class="fa fa-trash"></i></button>
                @if( $manuscript->expected_finish )
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#sendEmailModal"><i class="fa fa-envelope"></i></button>
                @endif
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateDocumentModal">Update document</button>
							</div>
				  		@if( $manuscript->status == 'Finished' )
							<span class="label label-success">Finished</span>
							@elseif( $manuscript->status == 'Started' )
							<span class="label label-primary">Started</span>
							@elseif( $manuscript->status == 'Not started' )
							<span class="label label-warning">Not started</span>
							@endif
							<br />
							Filename: {{ basename($manuscript->filename) }}<br />
							Words: {{ $manuscript->word_count }}<br />
							Uploaded by: <a href="{{route('admin.learner.show', $manuscript->user->id)}}">{{ $manuscript->user->fullname }}</a><br />
							Date uploaded: {{ $manuscript->created_at }}<br />
							Course: <a href="{{route('admin.course.show', $manuscript->courseTaken->package->course->id)}}">{{ $manuscript->courseTaken->package->course->title }}</a><br />
							Admin: 
              @if( $manuscript->admin )
              {{ $manuscript->admin->full_name }}
              @else
              <em>Not set</em>
              @endif<br />
              
              Expected finish:
              @if( $manuscript->expected_finish )
              {{ date_format(date_create($manuscript->expected_finish), 'M d, Y') }}
              @else
              <em>Not set</em>
              @endif<br />

							<strong>Grade: @if($manuscript->grade)
							{{$manuscript->grade}}
							@else
							<em>Not set</em>
							@endif
							</strong>
							<br />
							<br />
							<h4>Feedbacks
							@if( $manuscript->feedbacks->count() == 0 )
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addFeedbackModal">+ Add feedback</button>@endif</h4>
							<div class="row margin-top">
								@foreach($manuscript->feedbacks as $feedback)
								<div class="col-sm-12">
									<div class="panel panel-default">
										<div class="panel-body">
											<button type="button" class="btn btn-xs btn-danger btn-delete-feedback pull-right" data-action="{{ route('admin.feedback.destroy', $feedback->id) }}" data-toggle="modal" data-target="#deleteFeedbackModal"><i class="fa fa-trash"></i></button>
											<strong>Files:</strong> 
												@foreach( $feedback->filename as $filename )<br />
												<a href="{{ $filename }}" target="_blank">{{ basename($filename) }}</a>
												@endforeach
											<br />
											<strong>Notes:</strong> {{ $feedback->notes }} <br />
											<strong>Uploaded on:</strong> {{ $feedback->created_at }} <br />
										</div>
									</div>
								</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@if( $manuscript->expected_finish )
<!-- Send email Modal -->
<div id="sendEmailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Send email <?php /*$manuscript->user->fullname*/ ?></h4>
        <?php /*$manuscript->user->email */?>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.manuscript.email', $manuscript->id) }}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control" required value="Forventet dato for tilbakemelding">
          </div>
          <div class="form-group">
            <label>Message</label>
              <?php
                  if ($emailTemplate) {
                      $replace_string = \Carbon\Carbon::parse($emailTemplate->expected_finish)->format('d.m.Y');
                      $replace_content = str_replace('_date_',$replace_string, $emailTemplate->email_content);
                        $replace_content .= "\nExpected Finish: ".$manuscript->expected_finish;
                  }
              ?>
            <textarea name="message" class="form-control editor" required rows="8">{{ $emailTemplate ? $replace_content : '' }}</textarea>
          </div>
            <input type="hidden" name="from_email" value="{{ $emailTemplate ? $emailTemplate->from_email : 'post@forfatterskolen.no' }}">
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endif




<!-- Update document Modal -->
<div id="updateDocumentModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update document</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="form-group">
            <input type="file" name="manuscript" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text" required>
          </div>
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
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
        <h4 class="modal-title">Edit manuscript</h4>
      </div>
      <div class="modal-body">

        <form method="POST" action="{{ route('admin.manuscript.update', $manuscript->id) }}">
          {{csrf_field()}}
          {{method_field('PUT')}}
          <div class="form-group">
            <label>Editor</label>
            <select class="form-control select2" name="feedback_user_id" required>
              @foreach( App\User::whereIn('role', array(1,3))->orderBy('id', 'desc')->get()  as $admin)
              <option value="{{ $admin->id }}">{{ $admin->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Grade</label>
            <input type="number" step=".1" class="form-control" name="grade" value="{{ $manuscript->grade }}">
          </div>
          <div class="form-group">
            <label>Expected finish</label>
            <input type="date" class="form-control" name="expected_finish" value="{{ $manuscript->expected_finish }}">
          </div>
          <button type="submit" class="btn btn-primary pull-right">Update</button>
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
      	Are you sure to delete this manuscript?<br />
      	Warning: This cannot be undone. 
      	<form method="POST" action="{{ route('admin.manuscript.destroy', $manuscript->id) }}" class="margin-top">
      		{{csrf_field()}}
      		{{method_field('DELETE')}}
  			<button type="submit" class="btn btn-danger pull-right">Delete manuscript</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="deleteFeedbackModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete feedback</h4>
      </div>
      <div class="modal-body">
      	Are you sure to delete this feedback?
      	<form method="POST" action="" class="margin-top">
      		{{csrf_field()}}
  			<button type="submit" class="btn btn-danger pull-right">Delete feedback</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

@if( $manuscript->feedbacks->count() == 0 )
<div id="addFeedbackModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Feedback</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.feedback.store', $manuscript->id) }}" enctype="multipart/form-data">
      		{{csrf_field()}}
      		<div class="form-group">
      			<label>Files</label>
				<input type="file" class="form-control" name="files[]" multiple accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text" required>
      		</div>
      		<div class="form-group">
      			<label>Notes</label>
				<textarea class="form-control" name="notes" rows="6"></textarea>
      		</div>
      		Adding a feedback will complete this manuscript.
  			<button type="submit" class="btn btn-primary pull-right">Add feedback</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>
@endif
@stop

@section('scripts')
    <script src="https://cdn.tinymce.com/4/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector:'.editor',
            height : "300",
            menubar: false,
            toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
        });
    </script>
@stop
@extends('backend.layout')

@section('title')
<title>Shop Manuscript &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3> 
	<?php $extension = explode('.', basename($shopManuscriptTaken->file)); ?>
	@if( end($extension) == 'pdf' )
	<i class="fa fa-file-pdf-o"></i> 
	@elseif( end($extension) == 'docx' )
	<i class="fa fa-file-word-o"></i> 
	@elseif( end($extension) == 'odt' )
	<i class="fa fa-file-text-o"></i> 
	@endif
	{{ $shopManuscriptTaken->shop_manuscript->title }} <em>{{ basename($shopManuscriptTaken->file) }}</em></h3>
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
							<iframe src="/js/ViewerJS/#../..{{ $shopManuscriptTaken->file }}" style="width: 100%; border: 0; height: 600px"></iframe>
							@elseif( end($extension) == 'docx' )
							<iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$shopManuscriptTaken->file}}" style="width: 100%; border: 0; height: 600px"></iframe>
							@endif
						</div>
						<div class="col-sm-12 col-md-5">
							<div class="pull-right">
							<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#assignEditorModal">Assign editor</button>
							<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateDocumentModal">Update document</button>
							</div>
				  			@if( $shopManuscriptTaken->status == 'Finished' )
							<span class="label label-success">Finished</span>
							@elseif( $shopManuscriptTaken->status == 'Started' )
							<span class="label label-primary">Started</span>
							@elseif( $shopManuscriptTaken->status == 'Not started' )
							<span class="label label-warning">Not started</span>
							@endif
							<h3 class="no-margin-top">{{ $shopManuscriptTaken->shop_manuscript->title }}</h3>
							Learner: <a href="{{ route('admin.learner.show', $shopManuscriptTaken->user_id) }}">{{ $shopManuscriptTaken->user->full_name }}</a><br />
							Filename: {{ basename($shopManuscriptTaken->file) }}<br />
							Words: {{ $shopManuscriptTaken->words }}<br />
							Date uploaded: {{ $shopManuscriptTaken->created_at }}<br />
							Admin: 
							@if( $shopManuscriptTaken->admin )
							{{ $shopManuscriptTaken->admin->full_name }}
							@else
							<em>Not set</em>
							@endif<br />
							<br />

							<h4>Feedbacks
							@if( $shopManuscriptTaken->feedbacks->count() == 0 )
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addFeedbackModal">+ Add feedback</button>
							@endif</h4>
							<div class="row margin-top">
								@foreach($shopManuscriptTaken->feedbacks as $feedback)
								<div class="col-sm-12">
									<div class="panel panel-default">
										<div class="panel-body">
											<button type="button" class="btn btn-xs btn-danger btn-delete-feedback pull-right" data-action="{{ route('admin.shop-manuscript-taken-feedback.delete', $feedback->id) }}" data-toggle="modal" data-target="#deleteFeedbackModal"><i class="fa fa-trash"></i></button>
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


							<br />
							<h4>Comments</h4>
							<form method="POST" class="margin-top" action="{{ route('shop_manuscript_taken_comment', ['id' => $learner->id, 'shop_manuscript_taken_id' => $shopManuscriptTaken->id]) }}">
								{{ csrf_field() }}
								<input type="text" placeholder="Comment" name="comment" class="form-control" required>
								<div class="text-right margin-top">
									<button class="btn btn-info btn-sm" type="submit">Add Comment</button>
								</div>
							</form>
							<hr />
							<div class="margin-top">
							@foreach( $shopManuscriptTaken->comments as $comment )
							@if( $comment->user_id == Auth::user()->id )
							<div class="text-right">
								<div class="comment owner">
									<div>{{ $comment->comment }}</div>
									<div><small><em>You</em></small></div>
									<small>{{ $comment->created_at }}</small>
								</div>
							</div>
							@else
							<div>
								<div class="comment">
									<div>{{ $comment->comment }}</div>
									<div><small><em>{{ $comment->user->full_name }}</em></small></div>
									<small>{{ $comment->created_at }}</small>
								</div>
							</div>
							@endif
							@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="assignEditorModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Assign editor</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.shop-manuscript-taken.assign_editor', $shopManuscriptTaken->id) }}">
      		{{csrf_field()}}
      		<div class="form-group">
      			<label>Editor</label>
      			<select class="form-control select2" name="feedback_user_id" required>
      				@foreach( App\User::where('role', 1)->orderBy('id', 'desc')->get()  as $admin)
      				<option value="{{ $admin->id }}">{{ $admin->full_name }}</option>
      				@endforeach
      			</select>
      		</div>
  			<button type="submit" class="btn btn-primary pull-right">Assign</button>
  			<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


@if( $shopManuscriptTaken->feedbacks->count() == 0 )
<div id="addFeedbackModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Feedback</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.shop-manuscript-taken-feedback.store', $shopManuscriptTaken->id) }}" enctype="multipart/form-data">
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

<!-- Update document Modal -->
<div id="updateDocumentModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update document</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('shop_manuscript_taken.update_document', $shopManuscriptTaken->id) }}" enctype="multipart/form-data">
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
@stop

@section('scripts')
<script>
$(document).ready(function(){
  $('.btn-delete-feedback').click(function(){
        var action = $(this).data('action');

        var form = $('#deleteFeedbackModal');
        form.find('form').attr('action', action);
    });
});
</script>
@stop
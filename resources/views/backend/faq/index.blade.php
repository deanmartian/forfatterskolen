@extends('backend.layout')

@section('title')
<title>Assignments &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-question-circle"></i> FAQs</h3>
</div>

<br />
<div class="col-sm-8 col-sm-offset-2">
	<button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addFaqModal">Add FAQ</button>
	<a href="{{ route('admin.competition.index') }}" class="btn btn-primary margin-bottom">Konkurranser</a>
	<a href="{{ route('admin.writing-group.index') }}" class="btn btn-primary margin-bottom">Skrivegrupper</a>
	@foreach( $faqs as $faq )
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="pull-right">
				<button class="btn btn-xs btn-primary editFaqBtn" data-fields="{{ json_encode($faq) }}" data-action="{{ route('admin.faq.update', $faq->id) }}" data-toggle="modal" data-target="#editFaqModal"><i class="fa fa-pencil"></i></button>
				<button class="btn btn-xs btn-danger deleteFaqBtn" data-action="{{ route('admin.faq.destroy', $faq->id) }}" data-toggle="modal" data-target="#deleteFaqModal"><i class="fa fa-trash"></i></button>
			</div>
			<h4>{{ $faq->title }}</h4>
			<p style="margin-bottom: 0; margin-top: 7px">{!! nl2br($faq->description) !!}</p>
		</div>
	</div>
	@endforeach
</div>


<div id="addFaqModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Add FAQ</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.faq.store') }}">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>Title</label>
		      	<input type="text" class="form-control" name="title" placeholder="Title" required>
		      </div>
		      <div class="form-group">
		      	<label>Description</label>
		      	<textarea class="form-control" name="description" placeholder="Title" required rows="8"></textarea>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editFaqModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Edit FAQ</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>Title</label>
		      	<input type="text" class="form-control" name="title" placeholder="Title" required>
		      </div>
		      <div class="form-group">
		      	<label>Description</label>
		      	<textarea class="form-control" name="description" placeholder="Title" required rows="8"></textarea>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="deleteFaqModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Delete FAQ</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.faq.store') }}">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }}
		      Are you sure to delete this FAQ?
		      <br />
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
	$('.deleteFaqBtn').click(function(){
		var form = $('#deleteFaqModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

	$('.editFaqBtn').click(function(){
		var form = $('#editFaqModal form');
		var fields = $(this).data('fields');
		var action = $(this).data('action');
		form.attr('action', action);
		form.find('input[name=title]').val(fields.title);
		form.find('textarea[name=description]').val(fields.description);
	});
</script>
@stop
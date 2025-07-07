@extends('backend.layout')

@section('title')
<title>Faq &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-question-circle"></i> {{ trans('site.faqs') }}</h3>
</div>

<br />
<div class="col-sm-8 col-sm-offset-2">
	<button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addFaqModal">{{ trans('site.add-faq') }}</button>
	<a href="{{ route('admin.competition.index') }}" class="btn btn-primary margin-bottom">{{ trans_choice('site.competitions', 2) }}</a>
	<a href="{{ route('admin.writing-group.index') }}" class="btn btn-primary margin-bottom">{{ trans_choice('site.writing-groups', 2) }}</a>
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
		    <h4 class="modal-title">{{ trans('site.add-faq') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.faq.store') }}" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>{{ trans('site.title') }}</label>
		      	<input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}" required>
		      </div>
		      <div class="form-group">
		      	<label>{{ trans('site.description') }}</label>
		      	<textarea class="form-control tinymce" name="description" placeholder="{{ trans('site.description') }}" rows="8"></textarea>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
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
		    <h4 class="modal-title">{{ trans('site.edit-faq') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>{{ trans('site.title') }}</label>
		      	<input type="text" class="form-control" name="title" placeholder="{{ trans('site.title') }}" required>
		      </div>
		      <div class="form-group">
		      	<label>{{ trans('site.description') }}</label>
		      	<textarea class="form-control tinymce" name="description" placeholder="{{ trans('site.description') }}"
						  id="editFaqEditor" rows="8"></textarea>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
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
		    <h4 class="modal-title">{{ trans('site.delete-faq') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.faq.store') }}" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }}
				{{ trans('site.delete-faq-question') }}
		      <br />
		      <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
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
        // set content to the active editor
        tinymce.get('editFaqEditor').setContent(fields.description);
    });
</script>
@stop
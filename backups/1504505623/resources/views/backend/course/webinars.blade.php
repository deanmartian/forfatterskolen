@extends('backend.layout')

@section('title')
<title>Webinars &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addWebinarModal">Add Webinar</button> 
				</div>

				@foreach( $course->webinars as $webinar )
				<div class="col-sm-12 col-md-4">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="webinar-image" style="background-image:url('{{ $webinar->image }}')"></div>
							<div class="pull-right">
								<a class="btn btn-xs btn-info editWebinarBtn" 
								data-toggle="modal" 
								data-target="#editWebinarModal" 
								data-action="{{ route('admin.webinar.update', $webinar->id) }}" 
								data-title="{{ $webinar->title }}"
								data-description="{{ $webinar->description }}"
								data-start_date="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($webinar->start_date)) }}"
								data-image="{{ $webinar->image }}"
								data-link="{{ $webinar->link }}"
								>
								<i class="fa fa-pencil"></i></a>

								<a class="btn btn-xs btn-danger deleteWebinarBtn" 
								data-toggle="modal" 
								data-target="#deleteWebinarModal"
								data-action="{{ route('admin.webinar.destroy', $webinar->id) }}" 
								data-title="{{ $webinar->title }}"
								><i class="fa fa-trash"></i></a>
							</div>
							<strong>{{ $webinar->title }}</strong>
							<br />
							{!! nl2br($webinar->description) !!}
							<br />
							<p style="line-height: 1.8em; margin-top: 7px;">
								<i class="fa fa-link"></i>&nbsp;&nbsp;{{ $webinar->link }} <br />
								<i class="fa fa-calendar-o"></i>&nbsp;&nbsp;{{ $webinar->start_date }} <br />
								<!-- <i class="fa fa-users"></i>&nbsp;&nbsp;Attendees (20) -->
							</p>
							
							<hr />
							<div >
								<button class="btn btn-xs btn-primary margin-bottom addPresenterBtn pull-right" 
								data-toggle="modal" 
								data-target="#addPresenterModal"
								data-title="{{ $webinar->title }}"
								data-action="{{ route('admin.webinar.webinar-presenter.store', ['webinar_id' => $webinar->id]) }}">
								Add presenter</button>
								<strong style="font-size: 15px">Presenters</strong> <br />
								<div class="clearfix"></div> 

								@foreach( $webinar->webinar_presenters as $webinar_presenter )
								<div>
									<div class="pull-right">
										<a class="btn btn-xs btn-info editPresenterBtn" 
										data-toggle="modal" 
										data-target="#editPresenterModal"
										data-first_name="{{ $webinar_presenter->first_name }}"
										data-last_name="{{ $webinar_presenter->last_name }}"
										data-email="{{ $webinar_presenter->email }}"
										data-action="{{ route('admin.webinar.webinar-presenter.update', ['webinar_id' =>$webinar->id, 'id' => $webinar_presenter->id]) }}"
										>
										<i class="fa fa-pencil"></i></a>

										<a class="btn btn-xs btn-danger deletePresenterBtn" 
										data-toggle="modal" 
										data-target="#deletePresenterModal"
										data-first_name="{{ $webinar_presenter->first_name }}"
										data-last_name="{{ $webinar_presenter->last_name }}"
										data-action="{{ route('admin.webinar.webinar-presenter.destroy', ['webinar_id' =>$webinar->id, 'id' => $webinar_presenter->id]) }}">
										<i class="fa fa-trash"></i></a>
									</div>
									<div class="webinar-presenter">
										<div class="presenter-thumb" style="background-image: url('{{ $webinar_presenter->image  }}')"></div>
										{{ $webinar_presenter->first_name }} {{ $webinar_presenter->last_name }} <br />
										{{ $webinar_presenter->email }}
									</div>
								</div>
								<br />
								@endforeach
							</div>
						</div>
					</div>
				</div>
				@endforeach

			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<!-- Delete Presenter Modal -->
<div id="deletePresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete Presenter <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <p>Are you sure to delet this presenter?</p>
          <div class="text-right">
          	<button type="submit" class="btn btn-danger">Delete presenter</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Edit Presenter Modal -->
<div id="editPresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="no-margin">Edit Presenter <em></em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
	      	<div class="form-group">
	      		<div class="text-center">
			        <div class="user-thumb-image image-file margin-bottom">
			          <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
			          <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
			        </div>
		        </div>
	      	</div>
	      	<div class="form-group">
	      		<label>First Name</label>
	      		<input type="text" name="first_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Last Name</label>
	      		<input type="text" name="last_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Email</label>
	      		<input type="email" name="email" required class="form-control"> 
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">Add Presenter</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>

<!-- Add Presenter Modal -->
<div id="addPresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
      	<h4 class="no-margin">Add Presenter to <em></em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="">
      		{{ csrf_field() }}
	      	<div class="form-group">
	      		<div class="text-center">
			        <div class="user-thumb-image image-file margin-bottom">
			          <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
			          <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
			        </div>
		        </div>
	      	</div>
	      	<div class="form-group">
	      		<label>First Name</label>
	      		<input type="text" name="first_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Last Name</label>
	      		<input type="text" name="last_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Email</label>
	      		<input type="email" name="email" required class="form-control"> 
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">Add Presenter</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>


<!-- Add Webinar Modal -->
<div id="addWebinarModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Webinar</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.webinar.store') }}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="hidden" name="course_id" value="{{ $course->id }}">
          <div class="form-group">
          	<label>Title</label>
          	<input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>Description</label>
          	<textarea class="form-control" name="description" required rows="6"></textarea>
          </div>
          <div class="form-group">
          	<label>Start date</label>
          	<input type="datetime-local" name="start_date" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>URL</label>
          	<input type="url" name="link" class="form-control" required>
          </div>

          <div class="form-group">
            <label id="course-image">Image</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
            </div>
          </div>
          <div class="text-right">
          	<button type="submit" class="btn btn-primary">Add webinar</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Edit Webinar Modal -->
<div id="editWebinarModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Webinar <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="form-group">
          	<label>Title</label>
          	<input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>Description</label>
          	<textarea class="form-control" name="description" required rows="6"></textarea>
          </div>
          <div class="form-group">
          	<label>Start date</label>
          	<input type="datetime-local" name="start_date" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>URL</label>
          	<input type="url" name="link" class="form-control" required>
          </div>

          <div class="form-group">
            <label id="course-image">Image</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
            </div>
          </div>
          <div class="text-right">
          	<button type="submit" class="btn btn-primary">Update webinar</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Delete Webinar Modal -->
<div id="deleteWebinarModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete Webinar <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <p>Are you sure to delet this webinar?</p>
          <div class="text-right">
          	<button type="submit" class="btn btn-danger">Delete webinar</button>
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

		$('.deletePresenterBtn').click(function(){
			var form = $('#deletePresenterModal form');
			var action = $(this).data('action');
			var first_name = $(this).data('first_name');
			var last_name = $(this).data('last_name');

			$('#deletePresenterModal em').text(first_name + ' ' + last_name);
			form.attr('action', action);
		});

		$('.editPresenterBtn').click(function(){
			var modal = $('#editPresenterModal');
			var image = $(this).data('image');
			var first_name = $(this).data('first_name');
			var last_name = $(this).data('last_name');
			var email = $(this).data('email');
			var action = $(this).data('action');
			modal.find('form').attr('action', action);
			modal.find('.image-preview').css('background-image', 'url('+image+')');
			modal.find('em').text(first_name + ' ' + last_name);
			modal.find('input[name=first_name]').val(first_name);
			modal.find('input[name=last_name]').val(last_name);
			modal.find('input[name=email]').val(email);
		});

		$('.addPresenterBtn').click(function(){
			var modal = $('#addPresenterModal');
			var title = $(this).data('title');
			var action = $(this).data('action');
			modal.find('em').text(title);
			modal.find('form').attr('action', action);
		});


		$('.editWebinarBtn').click(function(){
			var form = $('#editWebinarModal form');
			var action = $(this).data('action');
			var title = $(this).data('title');
			var description = $(this).data('description');
			var start_date = $(this).data('start_date');
			var image = $(this).data('image');
			var link = $(this).data('link');

			$('#editWebinarModal em').text(title);
			form.attr('action', action);
			form.find('input[name=title]').val(title);
			form.find('textarea[name=description]').val(description);
			form.find('input[name=start_date]').val(start_date);
			form.find('input[name=link]').val(link);
			form.find('.image-preview').css('background-image', 'url('+image+')');
		});


		$('.deleteWebinarBtn').click(function(){
			var form = $('#deleteWebinarModal form');
			var action = $(this).data('action');
			var title = $(this).data('title');

			$('#deleteWebinarModal em').text(title);
			form.attr('action', action);
		});
	});
</script>
@stop
@extends('backend.layout')

@section('title')
<title>Free Courses &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> Free Courses</h3>
	<div class="clearfix"></div>
</div>

<div class="margin-top">
	<div class="col-md-12 margin-bottom">
		<button class="btn btn-success" data-toggle="modal" data-target="#addFreeCourseModal">Add free course</button>
	</div>
	@foreach( $freeCourses as $freeCourse )
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="pull-right">
					<button type="button" data-toggle="modal" data-target="#editFreeCourseModal" class="btn btn-info btn-xs editFreeCourseBtn" data-action="{{ route('admin.free-course.update', $freeCourse->id) }}" data-title="{{ $freeCourse->title }}" data-description="{{ $freeCourse->description }}" data-url="{{ $freeCourse->url }}" data-image="{{ $freeCourse->course_image }}"><i class="fa fa-pencil"></i></button>
					<button type="button" data-target="#deleteFreeCourseModal" data-toggle="modal" class="btn btn-danger btn-xs deleteFreeCourseBtn" data-action="{{ route('admin.free-course.destroy', $freeCourse->id) }}" data-title="{{ $freeCourse->title }}"><i class="fa fa-trash"></i></button>
				</div>
				<h4 class="margin-bottom">{{ $freeCourse->title }}</h4>
				<div class="margin-top">
					{!! nl2br($freeCourse->description) !!}
					<br />
					<br />
					URL: <a href="{{ $freeCourse->link }}" target="_blank">{{ $freeCourse->url }}</a>
					@if( $freeCourse->course_image )
					<br />
					<img src="{{ $freeCourse->course_image }}" height="150px" class="margin-top">
					@endif
				</div>
			</div>
		</div>
	</div>
	@endforeach

	<div class="clearfix"></div>
</div>


<!-- Add Free Course Modal -->
<div id="addFreeCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Free Course</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.free-course.store') }}" enctype="multipart/form-data">
          {{csrf_field()}}
          <div class="form-group">
          	<label>Title</label>
          	<input type="text" name="title" class="form-control" required>
          </div>
          <div class="form-group">
          	<label>Description</label>
          	<textarea class="form-control" name="description" required rows="6"></textarea>
          </div>
          <div class="form-group">
          	<label>URL</label>
          	<input type="text" name="url" class="form-control" required>
          </div>

          <div class="form-group">
            <label id="course-image">Image</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="course_image" accept="image/jpg, image/jpeg, image/png">
            </div>
          </div>
          <div class="text-right">
          	<button type="submit" class="btn btn-primary">Add free course</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>



<!-- Edit Free Course Modal -->
<div id="editFreeCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Free Course</h4>
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
          	<label>URL</label>
          	<input type="text" name="url" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label id="course-image">Image</label>
            <div class="course-form-image image-file margin-bottom">
              <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
              <input type="file" accept="image/*" name="course_image" accept="image/jpg, image/jpeg, image/png">
            </div>
          </div>
          <div class="text-right">
          	<button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Delete Free Course Modal -->
<div id="deleteFreeCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete Course</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <p>Are you sure to delete the free course <strong></strong>?</p>
          <div class="text-right">
          	<button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@stop

@section('scripts')
<script>
	$('.editFreeCourseBtn').click(function(){
		var form = $('#editFreeCourseModal form');
		var title = $(this).data('title');
		var description = $(this).data('description');
		var url = $(this).data('url');
		var image = $(this).data('image');
		var action = $(this).data('action');

		form.attr('action', action);
		form.find('input[name=title]').val(title);
		form.find('textarea[name=description]').val(description);
		form.find('input[name=url]').val(url);
		form.find('.image-preview').css('background-image', 'url('+image+')');
	});

	$('.deleteFreeCourseBtn').click(function(){
		var form = $('#deleteFreeCourseModal form');
		var action = $(this).data('action');
		var title = $(this).data('title');

		form.attr('action', action);
		form.find('strong').text(title);
	});
</script>
@stop
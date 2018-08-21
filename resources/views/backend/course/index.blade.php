@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Courses &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> All Courses</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" name="search" placeholder="Search course..">
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
	<a class="btn btn-success margin-top" href="{{route('admin.course.create')}}">Add Course</a>
	<a class="btn btn-primary margin-top" href="{{route('admin.course-testimonial.index')}}">Testimonials</a>
	<a class="btn btn-primary margin-top" href="{{route('admin.survey.index')}}">Surveys</a>
	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>ID</th>
			        <th>Title</th>
			        <th>Type</th>
			        <th>Learners</th>
			        <th>Lessons</th>
			        <th>Manuscripts</th>
					<th>Display Order</th>
					<th>For Sale</th>
					<th>Status</th>
			        <th>Date Created</th>
					<th>Discounts</th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($courses as $course)
		    	<tr>
					<td>{{$course->id}}</td>
					<td><a href="{{route('admin.course.show', $course->id)}}">{{$course->title}}</a></td>
					<td>{{$course->type}}</td>
					<td>{{count($course->learners->get())}}</td>
					<td>{{count($course->lessons)}}</td>
					<td>{{count($course->manuscripts)}}</td>
					<td>{{$course->display_order}}</td>
					<td>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="for-sale-toggle" data-off="No"
							   data-id="{{$course->id}}" data-size="mini" @if($course->for_sale) {{ 'checked' }} @endif>
					</td>
					<td>
						<input type="checkbox" data-toggle="toggle" data-on="Active"
							   class="status-toggle" data-off="Inactive"
							   data-id="{{$course->id}}" data-size="mini" @if($course->status) {{ 'checked' }} @endif>
					</td>
					<td>{{$course->created_at}}</td>
					<td><a href="{{ route('admin.course-discount.index', $course->id) }}">View</a></td>
		      	</tr>
		      	@endforeach
		    </tbody>
		</table>
	</div>
	
	<div class="pull-right">
		{{$courses->render()}}
	</div>
	<div class="clearfix"></div>
</div>

@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

	<script>
		$(function(){
		   $(".status-toggle").change(function(){
		       var course_id = $(this).attr('data-id');
		       var is_checked = $(this).prop('checked');
		       var check_val = is_checked ? 1 : 0;
               $.ajax({
                   type:'POST',
                   url:'/course-status',
                   headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                   data: { "course_id" : course_id, 'status' : check_val },
                   success: function(data){
                   }
               });
           });

		   $(".for-sale-toggle").change(function(){
               var course_id = $(this).attr('data-id');
               var is_checked = $(this).prop('checked');
               var check_val = is_checked ? 1 : 0;
               $.ajax({
                   type:'POST',
                   url:'/course-for-sale',
                   headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                   data: { "course_id" : course_id, 'for_sale' : check_val },
                   success: function(data){
                   }
               });
		   });
		});
	</script>
@stop
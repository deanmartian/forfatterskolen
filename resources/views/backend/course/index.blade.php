@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Courses &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> {{ trans('site.all-courses') }}</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" name="search" placeholder="{{ trans('site.search-course') }}..">
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
	<a class="btn btn-success margin-top" href="{{route('admin.course.create')}}">{{ trans('site.add-course') }}</a>
	<a class="btn btn-primary margin-top" href="{{route('admin.course-testimonial.index')}}">{{ trans_choice('site.testimonials',2) }}</a>
	<a class="btn btn-primary margin-top" href="{{route('admin.survey.index')}}">{{ trans('site.surveys') }}</a>
	<a class="btn btn-primary margin-top" href="{{route('admin.shareable-course.index')}}">Shareable Course</a>
	<a class="btn btn-primary margin-top" href="{{route('admin.course.all-upcoming-webinars')}}">Webinars</a>
	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>{{ trans('site.id') }}</th>
			        <th>{{ trans('site.title') }}</th>
			        <th>{{ trans('site.type') }}</th>
			        <th>{{ trans_choice('site.learners', 2) }}</th>
			        <th>{{ trans_choice('site.lessons', 2) }}</th>
			        <th>{{ trans_choice('site.manuscripts', 2) }}</th>
					<th>{{ trans('site.display-order') }}</th>
					<th>{{ trans('site.for-sale') }}</th>
					<th>{{ trans('site.status') }}</th>
			        <th>{{ trans('site.date-created') }}</th>
					<th>Free</th>
					<th>{{ trans_choice('site.discounts', 2) }}</th>
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
					<td>{{$course->manuscripts->count()}}</td>
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
					<td>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="is-free-toggle" data-off="No"
							   data-id="{{$course->id}}" data-size="mini" @if($course->is_free) {{ 'checked' }} @endif>
					</td>
					<td><a href="{{ route('admin.course-discount.index', $course->id) }}">{{ trans('site.view') }}</a></td>
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

		   $(".is-free-toggle").change(function(){
               let course_id = $(this).attr('data-id');
               let is_checked = $(this).prop('checked');
               let check_val = is_checked ? 1 : 0;
               $.ajax({
                   type:'POST',
                   url:'/course-is-free',
                   headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                   data: { "course_id" : course_id, 'is_free' : check_val },
                   success: function(data){
                   }
               });
		   });
		});
	</script>
@stop
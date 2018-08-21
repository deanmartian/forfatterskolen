@extends('backend.layout')

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
			        <th>Date Created</th>
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
					<td>{{$course->created_at}}</td>
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
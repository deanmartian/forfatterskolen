@extends('backend.layout')

@section('title')
<title>Assignments &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-users"></i> All Assignments</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="Search assignment..">
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
	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>ID</th>
			        <th>Title</th>
			        <th>Course</th>
			        <th>Date Created</th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($assignments as $assignment)
		    	<tr>
					<td>{{$assignment->id}}</td>
					<td>{{$assignment->title}}</td>
					<td>{{$assignment->course->title}}</td>
					<td>{{$assignment->created_at}}</td>
				</tr>
		      	@endforeach
		    </tbody>
		</table>
	</div>
	
	<div class="pull-right">
		{{$assignments->render()}}
	</div>
	<div class="clearfix"></div>
</div>
@stop
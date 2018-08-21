@extends('backend.layout')

@section('title')
<title>Learners &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-users"></i> All Learners</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="GET">
				<div class="input-group">
				  	<input type="text" class="form-control" name="search" value="{{Request::input('search')}}" placeholder="Search learner..">
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
			        <th>First Name</th>
			        <th>Last Name</th>
			        <th>Email</th>
			        <th>Courses</th>
			        <th>Date Joined</th>
			        <th></th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($learners as $learner)
		    	<tr>
					<td><a href="{{route('admin.learner.show', $learner->id)}}">{{$learner->id}}</a></td>
					<td>{{$learner->first_name}}</td>
					<td>{{$learner->last_name}}</td>
					<td>{{$learner->email}}</td>
					<td>{{count($learner->coursesTaken)}}</td>
					<td>{{$learner->created_at}}</td>
					<td><a href="{{route('admin.learner.show', $learner->id)}}" class="btn btn-xs btn-primary pull-right">View Learner</a></td>
		      	</tr>
		      	@endforeach
		    </tbody>
		</table>
	</div>
	
	<div class="pull-right">
		{{$learners->render()}}
	</div>
	<div class="clearfix"></div>
</div>
@stop
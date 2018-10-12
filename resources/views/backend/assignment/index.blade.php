@extends('backend.layout')

@section('title')
<title>Assignments &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file"></i> {{ ucwords(trans('site.all-assignments')) }}</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="{{ trans('site.search-assignment') }}..">
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
			        <th>{{ trans('site.id') }}</th>
			        <th>{{ trans('site.title') }}</th>
			        <th>{{ trans_choice('site.courses', 1) }}</th>
			        <th>{{ trans_choice('site.groups', 2) }}</th>
			        <th>{{ trans('site.date-created') }}</th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($assignments as $assignment)
		    	<tr>
					<td>{{$assignment->id}}</td>
					<td><a href="{{ route('admin.assignment.show', ['course_id' => $assignment->course->id, 'id' => $assignment->id]) }}">{{$assignment->title}}</a></td>
					<td>{{$assignment->course->title}}</td>
					<td>{{$assignment->groups->count()}}</td>
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
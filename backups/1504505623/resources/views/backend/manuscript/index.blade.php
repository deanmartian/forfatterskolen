@extends('backend.layout')

@section('title')
<title>Manuscripts &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> All Manuscripts</h3>
</div>

<div class="col-md-12">
	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>ID</th>
			        <th>Manuscript</th>
			        <th>Words count</th>
			        <th>Course</th>
			        <th>Grade</th>
			        <th>Feedbacks</th>
			        <th>Uploaded by</th>
			        <th>Date Uploaded</th>
			        <th>Status</th>
			        <th>Assigned admin</th>
		      	</tr>
		    </thead>

		    <tbody>
		    	@foreach($manuscripts as $manuscript)
		    	<tr>
					<td>{{$manuscript->id}}</td>
					<td>
						<?php $extension = explode('.', basename($manuscript->filename)); ?>
						@if( end($extension) == 'pdf' )
						<i class="fa fa-file-pdf-o"></i> 
						@elseif( end($extension) == 'docx' )
						<i class="fa fa-file-word-o"></i> 
						@elseif( end($extension) == 'odt' )
						<i class="fa fa-file-text-o"></i> 
						@endif
						<a href="{{ route('admin.manuscript.show', $manuscript->id) }}">{{ basename($manuscript->filename) }}</a>
					</td>
					<td>{{$manuscript->word_count}}</td>
					<td><a href="{{route('admin.course.show', $manuscript->courseTaken->package->course->id)}}">{{$manuscript->courseTaken->package->course->title}}</a></td>
					<td>
						@if($manuscript->grade)
						{{$manuscript->grade}}
						@else
						<em>Not set</em>
						@endif
					</td>
					<td>{{count($manuscript->feedbacks)}}</td>
					<td><a href="{{route('admin.learner.show', $manuscript->user->id)}}">{{$manuscript->user->fullname}}</a></td>
					<td>{{$manuscript->created_at}}</td>
					<td>
			  			@if( $manuscript->status == 'Finished' )
						<span class="label label-success">Finished</span>
						@elseif( $manuscript->status == 'Started' )
						<span class="label label-primary">Started</span>
						@elseif( $manuscript->status == 'Not started' )
						<span class="label label-warning">Not started</span>
						@endif
					</td>
					<td>
						@if( $manuscript->admin )
						{{ $manuscript->admin->full_name }}
						@else
						<em>Not set</em>
						@endif
					</td>
				</tr>
		      	@endforeach
		    </tbody>
		</table>
	</div>
	
	<div class="pull-right">
		{{$manuscripts->render()}}
	</div>
	<div class="clearfix"></div>
</div>
@stop
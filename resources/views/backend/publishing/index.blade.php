@extends('backend.layout')

@section('title')
<title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file"></i> All Publishers House</h3>
	<div class="navbar-form navbar-right">
		<div class="form-group">
			<form role="search" method="get" action="">
				<div class="input-group">
					<input type="text" class="form-control" name="search" placeholder="Search publisher house..">
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
	<a class="btn btn-success margin-top" href="{{ route('admin.publishing.create') }}">Create Publisher House</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.calendar-note.index') }}">Calendar Notes</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.solution.index') }}">Solutions</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.sos-children.index') }}">SOS Children</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.blog.index') }}">Blog</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.publisher-book.index') }}">Publisher Books</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.opt-in.index') }}">Opt-in</a>

	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>Publishing</th>
			        <th>Post Address</th>
			        <th>Phone</th>
			        <th>Genre</th>
					<th>Email</th>
		      	</tr>
		    </thead>

		    <tbody>
			@foreach($publishingHouses as $publishingHouse)
				<tr>
					<td>
						<a href="{{ route('admin.publishing.edit', $publishingHouse->id) }}">{{ $publishingHouse->publishing }}</a>
					</td>
					<td>{{ $publishingHouse->mail_address }}</td>
					<td>{{ $publishingHouse->phone }}</td>
					<td>{{ $publishingHouse->genre ? \App\Http\FrontendHelpers::formatAssignmentType($publishingHouse->genre) : ''}}</td>
					<td>{{ $publishingHouse->email }}</td>
				</tr>
			@endforeach
		    </tbody>
		</table>
	</div>
	
	<div class="pull-right">
		{{ $publishingHouses->render() }}
	</div>
	<div class="clearfix"></div>
</div>
@stop
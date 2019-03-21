@extends('backend.layout')

@section('title')
<title>Learners &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<style>
		.form-inline {
			display: inline;
			margin-left: 10px;
		}

		.center-area {
			position: absolute;
			left: 30%;
		}
	</style>
@stop

@section('content')
<div class="page-toolbar" style="position: relative;">
	<h3><i class="fa fa-users"></i> {{ trans('site.all-learners') }}</h3>
	<form method="GET" class="center-area centered">
		<div class="form-check form-inline">
			<input class="form-check-input" type="checkbox" name="free-course" value="1"
				   @if (Request::has('free-course')) checked @endif>
			<label class="form-check-label" for="free-course">Free Course</label>
		</div>

		<div class="form-check form-inline">
			<input class="form-check-input" type="checkbox" name="workshop" value="1"
				   @if (Request::has('workshop')) checked @endif>
			<label class="form-check-label" for="workshop">Workshop</label>
		</div>

		<div class="form-check form-inline">
			<input class="form-check-input" type="checkbox" name="shop-manuscript" value="1"
				   @if (Request::has('shop-manuscript')) checked @endif>
			<label class="form-check-label" for="workshop">Shop-manuscript</label>
		</div>

		<div class="form-check form-inline">
			<input class="form-check-input" type="checkbox" name="course" value="1"
				   @if (Request::has('course')) checked @endif>
			<label class="form-check-label" for="workshop">Courses</label>
		</div>

		<button class="btn btn-default form-inline" type="submit">Filter</button>
	</form>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="GET">
				<div class="input-group">
				  	<input type="text" class="form-control" name="search" value="{{Request::input('search')}}" placeholder="{{ trans('site.search-learner') }}..">
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
			        <th>{{ trans('site.first-name') }}</th>
			        <th>{{ trans('site.last-name') }}</th>
			        <th>{{ trans_choice('site.emails', 1) }}</th>
					<th>Free Courses</th>
					<th>{{ trans_choice('site.workshops',1) }}</th>
					<th>{{ trans_choice('site.shop-manuscripts', 1) }}</th>
			        <th>{{ trans_choice('site.courses', 2) }}</th>
			        <th>{{ trans('site.date-joined') }}</th>
					<th>{{ trans('site.admin') }}</th>
					<th>{{ trans('site.auto-renew') }}</th>
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
					<td>{{ $learner->freeCourses->count() }}</td>
					<td>{{($learner->workshopsTaken->count())}}</td>
					<td>{{($learner->shopManuscriptsTaken->count())}}</td>
					<td>{{count($learner->coursesTaken)}}</td>
					<td>{{$learner->created_at}}</td>
					<td>{{ $learner->is_admin ? 'Yes' : 'No' }}</td>
					<td>{{ $learner->auto_renew_courses ? 'Yes' : 'No' }}</td>
					<td><a href="{{route('admin.learner.show', $learner->id)}}" class="btn btn-xs btn-primary pull-right">{{ trans('site.view-learner') }}</a></td>
		      	</tr>
		      	@endforeach
		    </tbody>
		</table>
	</div>
	
	<div class="pull-right">
		{{$learners->appends(Request::all())->render()}}
	</div>
	<div class="clearfix"></div>
</div>
@stop
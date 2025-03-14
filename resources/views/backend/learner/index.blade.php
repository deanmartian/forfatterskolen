@extends('backend.layout')

@section('title')
<title>Learners &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.form-inline {
			display: inline;
			margin-left: 10px;
		}

		.center-area {
			position: absolute;
			left: 18%;/*30%*/
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

		{{-- <div class="form-check form-inline">
			<input class="form-check-input" type="checkbox" name="workshop" value="1"
				   @if (Request::has('workshop')) checked @endif>
			<label class="form-check-label" for="workshop">Workshop</label>
		</div> --}}

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
				<input type="text" class="form-control" name="sid" value="{{Request::input('sid')}}" placeholder="Search ID..">
				<input type="text" class="form-control" name="sfname" value="{{Request::input('sfname')}}" placeholder="Search First Name..">
				<input type="text" class="form-control" name="slname" value="{{Request::input('slname')}}" placeholder="Search Last Name..">
				<div class="input-group">
					<input type="text" class="form-control" name="semail" value="{{Request::input('semail')}}" placeholder="Search Email..">
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
        <a href=" {{ route('admin.learner.list_notes') }}" class="btn btn-success margin-top">
            {{ trans_choice('site.notes', 2) }}
        </a>

		<button type="button" class="btn btn-success addLearnerBtn margin-top" data-toggle="modal"
				data-target="#addLearnerModal">
			Add Learner
		</button>

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
					<th>Self Publishing</th>
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
					<td>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="is-publishing-learner-toggle" data-off="No" data-id="{{ $learner->id }}"
							   name="is_self_publishing_learner" data-size="mini" @if($learner->is_self_publishing_learner) {{ 'checked' }} @endif>
					</td>
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

<div id="addLearnerModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
					Add Learner
				</h4>
			</div>

			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.register') }}" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<div class="form-group">
						<label>{{ trans('site.front.form.email') }}</label>
						<input type="email" name="email"
							   class="form-control no-border-left" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.front.form.first-name') }}</label>
						<input type="text" name="first_name"
							   class="form-control no-border-left" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.front.form.last-name') }}</label>
						<input type="text" name="last_name"
							   class="form-control no-border-left" required>
					</div>

					<div class="form-group">
						<label>{{ trans('site.front.form.password') }}</label>
						<input type="text" name="password"
							   class="form-control no-border-left" required>
						<button class="btn btn-success btn-sm generatePassword margin-top" type="button">
							Generate
						</button>
					</div>

					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script>
		$(".generatePassword").click(function() {
			$.ajax({
				type:'GET',
				url:'/learner/generate-password',
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				data: {},
				success: function(data){
					$("input[name=password]").val(data);
				}
			});
		});

        $(".is-publishing-learner-toggle").change(function(){
            let learner_id = $(this).attr('data-id');
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;

            $.ajax({
                type:'POST',
                url:'/learner/' + learner_id + '/update-is-publishing-learner',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { 'is_self_publishing_learner' : check_val },
                success: function(data){
                }
            });
        });
	</script>
@stop
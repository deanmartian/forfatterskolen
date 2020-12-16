@extends('backend.layout')

@section('title')
<title>Manuscripts &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12 col-md-12">
			<div class="table-responsive">
				<table class="table table-side-bordered table-white">
					<thead>
						<tr>
					        <th>{{ trans_choice('site.manuscripts', 1) }}</th>
					        <th>{{ trans('site.words-count') }}</th>
					        <th>{{ trans_choice('site.learners', 1) }}</th>
					        <th>{{ trans('site.grade') }}</th>
					        <th>{{ trans('site.date-uploaded') }}</th>
						</tr>
					</thead>
					<tbody>
						@if($course->manuscripts->count() > 0)
						@foreach( $course->manuscripts->paginate(25) as $manuscript )
						<tr>
				    		<td><a href="{{ route('admin.manuscript.show', $manuscript->id) }}">{{ basename($manuscript->filename) }}</a></td>
							<td>{{ $manuscript->word_count }}</td>
							<td><a href="{{route('admin.learner.show', $manuscript->user->id)}}">{{$manuscript->user->full_name}}</a></td>
							<td>
								@if($manuscript->grade)
								{{$manuscript->grade}}
								@else
								<em>Not set</em>
								@endif
							</td>
							<td>{{ $manuscript->created_at }}</td>
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>

			@if($course->manuscripts->count() > 0)
			<div class="pull-right">{!! $course->manuscripts->paginate(25)->appends(Request::all())->render() !!}</div>
			<div class="clearfix"></div>
			@endif
		</div>
	</div>
	<div class="clearfix"></div>
</div>




@stop
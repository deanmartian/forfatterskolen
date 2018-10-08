@extends('backend.layout')

@section('title')
<title>{{$course->title}} &rsaquo; Course &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')

<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<!-- Details -->
		<div class="col-sm-12 col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="pull-right">
						<a class="btn btn-sm btn-success" href="{{route('admin.course.edit', $course->id)}}"><i class="fa fa-pencil"></i> {{ trans('site.edit') }}</a>
						<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#cloneModal"><i class="fa fa-copy"></i> {{ trans('site.clone') }}</button>
					</div>

					<h4>{{ trans('site.course-details') }}</h4>
				</div>
				<div class="panel-body panel-course-details">
					<div class="col-sm-12 col-md-5">
						<div class="course-image" style="background-image: url('{{$course->course_image}}')"></div>
					</div>
					<div class="col-sm-12 col-md-7">
						<h3>{{ $course->title }}</h3>
						<p>{!! nl2br($course->description) !!}</p>
						<p>
							<i class="fa fa-bookmark-o"></i> {{ trans('site.course-type') }}: {{$course->type}}
							@if( $course->start_date )
							<br />
							<i class="fa fa-calendar-o"></i> {{ trans('site.start-date') }}: {{ $course->start_date }}
							@endif
							@if( $course->end_date )
							<br />
							<i class="fa fa-calendar-o"></i> {{ trans('site.end-date') }}: {{ $course->end_date }}
							@endif
						</p>
						<br /><br />
						<h4>{{ trans('site.course-plan') }}</h4>
						@if ($course->lesson_kursplan()->get()->count())
							{!! $course->lesson_kursplan()->get()[0]->content !!}
						@else
							{!! nl2br($course->course_plan) !!}
						@endif
					</div>
				</div>
			</div>
		</div>
		
		<div>
			<!-- Lessons -->
			<div class="col-sm-12 col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<a class="pull-right btn btn-xs btn-default" href="{{route('admin.course.show', $course->id)}}?section=lessons">{{ trans('site.view-more') }}</a>
						<h4>{{ trans_choice('site.lessons', 2) }}</h4>
					</div>
					<div class="table-responsive">
						<table class="table">
						    <thead>
						      <tr>
						        <th>{{ trans('site.title') }}</th>
						        <th>{{ trans('site.availability') }}</th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach($course->lessons as $lesson)
							    <tr>
							    	<td>{{$lesson->title}}</td>
							        <td>
							        @if(AdminHelpers::isDate($lesson['delay']))
									{{date_format(date_create($lesson->delay), 'M d, Y')}}
									@else
									{{$lesson->delay}} days delay
									@endif
							        </td>
							    </tr>
							    @endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>

			
			<!-- Packages -->
			<div class="col-sm-12 col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<a class="pull-right btn btn-xs btn-default" href="{{route('admin.course.show', $course->id)}}?section=packages">{{ trans('site.view-more') }}</a>
						<h4>{{ trans_choice('site.packages', 2) }}</h4>
					</div>
					<div class="table-responsive">
						<table class="table">
						    <thead>
						      <tr>
						        <th>{{ trans('site.variation') }}</th>
						        <th>{{ trans('site.price') }}</th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach($course->packages as $package)
							    <tr>
							    	<td>{{$package->variation}}</td>
							        <td>{{AdminHelpers::currencyFormat($package->price)}}</td>
							    </tr>
							    @endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>


			<div class="clearfix"></div>
		</div>

		<div>
			<!-- Manuscripts -->
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<a class="pull-right btn btn-xs btn-default" href="{{route('admin.course.show', $course->id)}}?section=manuscripts">{{ trans('site.view-more') }}</a>
						<h4>{{ trans('site.recent-manuscripts-uploaded') }}</h4>
					</div>
					<div class="table-responsive">
						<table class="table">
						    <thead>
						      <tr>
						        <th>{{ trans_choice('site.manuscripts', 2) }}</th>
						        <th>{{ trans_choice('site.words', 2) }}</th>
						        <th>{{ trans_choice('site.learners', 1) }}</th>
						        <th>{{ trans('site.date-uploaded') }}</th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $course->manuscripts->take(5) as $manuscript )
						    	<tr>
						    		<td><a href="{{ route('admin.manuscript.show', $manuscript->id) }}">{{ basename($manuscript->filename) }}</a></td>
									<td>{{ $manuscript->word_count }}</td>
									<td><a href="{{route('admin.learner.show', $manuscript->user->id)}}">{{$manuscript->user->full_name}}</a></td>
									<td>{{ $manuscript->created_at }}</td>
						    	</tr>
						    	@endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>
			

			<!-- Email -->
			<div class="col-sm-7">
				<div class="panel panel-default">
					<div class="panel-heading">
						<button class="pull-right btn btn-xs btn-primary" data-toggle="modal" data-target="#editEmailModal">{{ trans('site.edit') }}</button>
						<h4>{{ trans('site.email') }}</h4>
					</div>
					<div class="panel-body">
					{!! nl2br($course->email) !!}
					</div>
				</div>
			</div>
			

			<!-- Similar Courses -->
			<div class="col-sm-5">
				<div class="panel panel-default">
					<div class="panel-heading">
						<button class="pull-right btn btn-xs btn-primary" data-toggle="modal" data-target="#addSimilarCourseModal">+ {{ trans('site.add') }}</button>
						<h4>{{ trans('site.similar-courses') }}</h4>
					</div>
					<div class="table-responsive">
						<table class="table">
						    <thead>
						      <tr>
						        <th>{{ trans_choice('site.courses', 1) }}</th>
						        <th></th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $course->similar_courses as $similar_course )
						    	<tr>
						    		<td><a href="{{ route('admin.course.show', $similar_course->similar_course_id) }}">{{ $similar_course->similar_course->title }}</a></td>
						    		<td class="text-right">
						    			<button class="btn btn-xs btn-danger removeSimilarCourse" data-action="{{ route('admin.course.remove_similar_course', $similar_course->id) }}" data-title="{{ $similar_course->similar_course->title }}" data-toggle="modal" data-target="#removeSimilarCourseModal"><i class="fa fa-trash"></i></button>
						    		</td>
						    	</tr>
						    	@endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>
			
			
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div id="editEmailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Email</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.course.update.email', $course->id) }}">
      		{{ csrf_field() }}
      		<textarea class="form-control" name="email" rows="6">{{ $course->email }}</textarea>
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">Save</button>
      		</div>
      	</form>
      </div>
    </div>
  </div>
</div>


<div id="addSimilarCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Similar Course</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.course.add_similar_course', $course->id) }}">
      		{{ csrf_field() }}
      		<?php $similar_courses_ids = $course->similar_courses->pluck('similar_course_id')->toArray(); ?>
      		<?php $all_courses = App\Course::where('id', '<>', $course->id)->whereNotIn('id', $similar_courses_ids)->orderBy('created_at', 'desc')->get(); ?>
      		<select name="similar_course_id" class="form-control" required>
      			<option value="" disabled selected>- Select course</option>
      			@foreach( $all_courses as $all_course )
      			<option value="{{ $all_course->id }}">{{ $all_course->title }}</option>
      			@endforeach
      		</select>
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">Add</button>
      		</div>
      	</form>
      </div>
    </div>
  </div>
</div>


<div id="removeSimilarCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Remove Similar Course</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="">
      		{{ csrf_field() }}
      		Are you sure to remove the similar course <strong></strong>?
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-danger">Remove</button>
      		</div>
      	</form>
      </div>
    </div>
  </div>
</div>

<div id="cloneModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('clone-course') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.course.clone', $course->id) }}">
      		{{ csrf_field() }}
			{{ trans('clone-course-question') }}
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">{{ trans('clone') }}</button>
      		</div>
      	</form>
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts')
<script>
	$('.removeSimilarCourse').click(function(){
		var action = $(this).data('action');
		var title = $(this).data('title');
		var removeSimilarCourseModal = $('#removeSimilarCourseModal');
		removeSimilarCourseModal.find('form').attr('action', action);
		removeSimilarCourseModal.find('strong').text(title);
	});
</script>
@stop
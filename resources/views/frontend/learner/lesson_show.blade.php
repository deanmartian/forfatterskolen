@extends('frontend.layout')

@section('title')
<title> {{$lesson->title}} &rsaquo; {{$lesson->course->title}} &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="container margin-top lesson-body">
	<?php  
	$previousLesson = $course->lessons->where('order', '<', $lesson->order)->first();
	$nextLesson = $course->lessons->where('order', '>', $lesson->order)->first();
	?>
	@if($previousLesson)
		@if(FrontendHelpers::isLessonAvailable($courseTaken->started_at, $previousLesson->delay, $previousLesson->period) ||
		FrontendHelpers::hasLessonAccess($courseTaken, $previousLesson))
		<a class="btn btn-sm btn-primary margin-bottom" href="{{route('learner.course.lesson', ['course_id' => $courseTaken->package->course->id, 'id' => $previousLesson->id])}}"><i class="fa fa-angle-left"></i>&nbsp;&nbsp;{{$previousLesson->title}}</a>
		@else
		<button type="button" class="btn btn-sm btn-default disabled"><i class="fa fa-angle-left"></i>&nbsp;&nbsp;{{$previousLesson->title}}</button>
		@endif
	@endif

	@if($nextLesson)
		@if(FrontendHelpers::isLessonAvailable($courseTaken->started_at, $nextLesson->delay, $nextLesson->period) ||
		FrontendHelpers::hasLessonAccess($courseTaken, $nextLesson))
		<a class="btn btn-sm btn-primary pull-right" href="{{route('learner.course.lesson', ['course_id' => $courseTaken->package->course->id, 'id' => $nextLesson->id])}}">{{$nextLesson->title}}&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a>
		@else
		<button type="button" class="btn btn-sm btn-default disabled pull-right">{{$nextLesson->title}}&nbsp;&nbsp;<i class="fa fa-angle-right"></i></button>
		@endif
		<div class="clearfix"></div>
	@endif
	
	<div class="text-center">
		<br />
		<br />
		<h2 class="margin-top no-margin-bottom">{{$lesson->title}}</h2>
		<div class="no-margin-top margin-bottom"><a href="{{route('learner.course.show', $courseTaken->id)}}">{{$lesson->course->title}}</a></div>
	</div>
	<div class="margin-top">&nbsp;</div>

		<div class="row">
			<div class="col-sm-10">
				<div class="margin-top lesson-body">
					<!-- check if for old structure or new -->
					@if ($lesson->id <= 169)
						{!! html_entity_decode($lesson->content) !!}
					@else
						@foreach($lesson->lessonContent as $content)
							<h1>{{ $content->title }}</h1>
							{!! html_entity_decode($content->lesson_content) !!}
						@endforeach
					@endif
				</div>
			</div>
			<div class="col-sm-2">
				<div class="margin-top lesson-body">
					@if ($lesson->documents->count())
						<b>Dokumenter og skjemaer</b>
						<ul style="padding-left: 15px">
							@foreach($lesson->documents as $document)
								<li><a href="{{ route('learner.lesson.download-lesson-document', $document->id) }}">{{ $document->name }}</a></li>
							@endforeach
						</ul>
					@endif
				</div>
			</div>
		</div>
</div>
@stop
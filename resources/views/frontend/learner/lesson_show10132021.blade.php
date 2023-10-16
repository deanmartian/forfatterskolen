{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title> {{$lesson->title}} &rsaquo; {{$lesson->course->title}} &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		.scroll-top {
			width: 50px;
			height: 50px;
			position: fixed;
			bottom: 50px;
			right: 70px;
			display: none;
			z-index: 9999;
		}
		.scroll-top i {
			display: inline-block;
			color: #fff;
		}

        .wistia_responsive_padding {
            padding: 0 !important;
        }

        .wistia_responsive_wrapper {
            position: inherit !important;
        }
	</style>
@stop


@section('content')
	<div class="learner-container">
		<div class="container margin-top lesson-body">
            <?php
            $previousLesson = $course->lessons->where('order', '<', $lesson->order)->last();
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

			@if ($course->id != 17)
				<div class="row">
					<div class="col-sm-12">
						<a class="btn btn-info download-btn" href="{{ route('learner.course.download-lesson', ['course_id' => $course->id, 'id' => $lesson->id]) }}"
						   onclick="disableButton(this)">{{ trans('site.learner.download-pdf-of-lesson') }}</a>
					</div>
				</div>
			@endif

			<div class="text-center">
				<h1 class="margin-top mt-4">{{$lesson->title}}</h1>
				<div><a href="{{route('learner.course.show', $courseTaken->id)}}">{{$lesson->course->title}}</a></div>
			</div>
			<div class="margin-top">&nbsp;</div>

			<div class="row">
				<div class="col-md-10">
					<div class="margin-top lesson-body ss-overflow-x-scroll">

						<!-- display search on this lesson only -->
						@if ($lesson->id == 191)
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<form class="" method="get" action="">
											<div class="input-group-global">
												<input type="text" name="search_replay" class="form-control" placeholder="{{ trans('site.learner.search-webinar-replay') }}" aria-label="Enter here..." aria-describedby="basic-addon2"
													   value="{{ Request::get('search_replay') }}">
												<div class="input-group-append">
													<button class="btn btn-outline-success border-color-grey" type="submit"><i class="fa fa-search"></i> {{ trans('site.learner.search-text') }}</button>
													<a class="btn btn-outline-info border-color-grey" type="reset"
													   href="{{ route('learner.course.lesson', ['course_id' => $lesson->course_id, 'id' => $lesson->id]) }}"><i class="fa fa-redo"></i> {{ trans('site.learner.reset-text') }}</a>
												</div>
											</div>
										</form> <!-- end searchBoxForm -->
									</div> <!-- end #simpleSearchbox -->
								</div>
							</div>
						@endif

					<!-- check if webinar-pakke -->
						@if ($course->id == 17)
						<!-- check if for old structure or new -->
							@if ($lesson->id <= 169)
								{!! html_entity_decode($lesson->content) !!}
							@else
								@foreach($lesson_content as $content)
									<h1>{{ $content->title }}</h1>
									{!! html_entity_decode($content->lesson_content) !!}
								@endforeach
							@endif
						@else
						<!-- if course is not webinar pakke then use old structure -->
							{!! html_entity_decode($lesson->content) !!}
						@endif
					</div>
				</div>
				<div class="col-md-2">
					<div class="margin-top lesson-body">
						@if ($lesson->documents->count())
							<b>{{ trans('site.learner.documents-and-forms-text') }}</b>
							<ul style="padding-left: 15px">
								@foreach($lesson->documents as $document)
									<li style="word-break: break-all;"><a href="{{ route('learner.lesson.download-lesson-document', $document->id) }}">{{ $document->name }}</a></li>
								@endforeach
							</ul>
						@endif
					</div>
				</div>
			</div>

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

				<button class="btn btn-primary scroll-top" data-scroll="up" type="button">
					<i class="fa fa-chevron-up"></i>
				</button>
		</div>
	</div>
@stop

@section('scripts')
	<script src="https://fast.wistia.com/embed/medias/68ni4qzcad.jsonp" async></script>
	<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
	<script>
        $(document).ready(function () {
            $(window).scroll(function () {
                if ($(this).scrollTop() > 250) {
                    $('.scroll-top').fadeIn();
                } else {
                    $('.scroll-top').fadeOut();
                }
            });

            $('.scroll-top').click(function () {
                $("html, body").animate({
                    scrollTop: 0
                }, 300);
                return false;
            });

        });

        function disableButton(t) {
            let btn = $(t);
            btn.text('');
            btn.append('<i class="fa fa-spinner fa-pulse"></i> Vennligst vent...');
            btn.attr('disabled', 'disabled');
		}

		setInterval(function(){
		    checkCookie();
		}, 1000);

        // check cookie to re-enable download button
        function checkCookie() {
            let cookie = getCookie("_lesson_dl");
            if (cookie !== "") {
                document.cookie = "_lesson_dl=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                $(".download-btn").text('')
                    .append('Last ned utskriftsvennlig pdf av leksjonen')
                	.removeAttr('disabled');
            }
        }

        function getCookie(cname) {
            let name = cname + "=";
            let ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
	</script>
@stop
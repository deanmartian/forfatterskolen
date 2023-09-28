@extends('editor.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('css/editor.css')}}">
	<style>
		.panel {
			overflow-x: auto;
		}
        image_container, .image_container_edit {
			display: none;
			height: 300px;
			margin-bottom: 10px;
		}

		.webinar-img img{
			width: 100%;
			height: 170px;
			margin-bottom: 12px;
		}

		.webinar-list-container {
			padding-right: 0;
			padding-left: 0;
		}
        .course-title, .content a{
            color: #862736;
        }
	</style>
@stop

@section('content')
<div class="col-sm-12 dashboard-left">
    <div class="row">
        <div class="col-sm-12 webinar-list-container">
            @foreach($webinars as $webinar)
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="webinar-img">
                                <img src="{{ $webinar->image ? $webinar->image : asset('images/no_image.png') }}">
                            </div>
                            <div class="content">
                                <i class="fa fa-book" aria-hidden="true"></i>&nbsp;{{ trans('site.front.course-text') }}: <span class="course-title">{{ $webinar->course_title }}</span><br>
                                <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; 
                                {{ str_replace(['_date_', '_time_'],
                                    [\Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
                                    \Carbon\Carbon::parse($webinar->start_date)->format('H:i')],
                                    trans('site.front.our-course.show.start-date')) }} <br>
                                <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ trans('site.presenter-url') }}&nbsp;<a href="{{ $webinar->presenter_url }}">{{ $webinar->presenter_url }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@stop


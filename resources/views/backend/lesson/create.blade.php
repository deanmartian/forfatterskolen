@extends('backend.layout')

@section('title')
<title>Create Lesson &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('content_tools/content-tools.min.css')}}">
<link rel="stylesheet" href="{{asset('fileuploader/src/jquery.fileuploader.css')}}">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop


@section('content')

<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> {{ trans('site.create-new-lesson-for') }} {{$course->title}}</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="Search Course, Webinar, Manuscript, etc">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	  	<div class="form-group">
	  		<a href="{{url('course')}}" class="btn btn-primary">{{ trans('site.view-all-courses') }}</a>
	  	</div>
	</div>
	<div class="clearfix"></div>
</div>


<div class="margin-top">
	@include('backend.lesson.partials.form')
</div>

@stop


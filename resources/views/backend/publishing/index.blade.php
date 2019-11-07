@extends('backend.layout')

@section('title')
<title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file"></i> {{ trans('site.all-publishers-house') }}</h3>
	<div class="navbar-form navbar-right">
		<div class="form-group">
			<form role="search" method="get" action="">
				<div class="input-group">
					<input type="text" class="form-control" name="search" placeholder="{{ trans('site.search-publisher-house') }}..">
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
	<a class="btn btn-success margin-top" href="{{ route('admin.publishing.create') }}">{{ trans('site.create-publisher-house') }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.calendar-note.index') }}">{{ trans('site.calendar-notes') }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.solution.index') }}">{{ trans_choice('site.solutions', 2) }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.sos-children.index') }}">{{ trans('site.sos-children') }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.blog.index') }}">{{ trans('site.blog') }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.publisher-book.index') }}">{{ trans('site.publisher-books') }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.opt-in.index') }}">{{ trans('site.opt-in') }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.poem.index') }}">Poem</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.page_meta.index') }}">Page Meta</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.editor.index') }}">Editors</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.cron-log.index') }}">CRON Logs</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.goto-webinar.index') }}">GoTo Webinar Email Notifications</a>
	<a class="btn btn-success margin-top" href="{{ action('\Barryvdh\TranslationManager\Controller@getView') }}/site">Translations</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.testimonial.index') }}">Testimonials</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.file.index') }}">Files</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.personal-trainer.index') }}">Personal Trainer Applicants</a>

	<div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>{{ trans('site.publishing') }}</th>
			        <th>{{ trans('site.post-address') }}</th>
			        <th>{{ trans('site.phone') }}</th>
			        <th>{{ trans('site.genre') }}</th>
					<th>{{ trans_choice('site.emails', 1) }}</th>
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
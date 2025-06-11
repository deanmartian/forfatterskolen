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
	<a class="btn btn-success margin-top" href="{{ action([\Barryvdh\TranslationManager\Controller::class, 'getView']) }}/site">Translations</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.testimonial.index') }}">Testimonials</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.file.index') }}">Files</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.personal-trainer.index') }}">Personal Trainer Applicants</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.single-competition.index') }}">Competition</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.email-template.index') }}">{{ trans('site.email-template') }}</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.sales.index') }}">Sales</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.email-history.index') }}">Email History</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.replay.index') }}">Replay</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.checkout-log.index') }}">Checkout Logs</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.upcoming.index') }}">Upcoming Sections</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.news.index') }}">News</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.genre.index') }}">Genre</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.self-publishing.index') }}">Self Publishing</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.svea.orders') }}">Svea Orders</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.marketing-plan.index') }}">Marketing Plan</a>
	@if(Auth::user()->isSuperUser())
		<a class="btn btn-success margin-top" href="{{ route('admin.invoice.index') }}">Invoices</a>
	@endif
	{{-- <a class="btn btn-success margin-top" href="{{ route('admin.book-for-sale.index') }}">
		Books For Sale
	</a> --}}
	<a class="btn btn-success margin-top" href="{{ route('admin.application') }}">
		Application
	</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.queue-jobs') }}">
		Queue Jobs
	</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.tinymce.images') }}">
		Tinymce Images
	</a>

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
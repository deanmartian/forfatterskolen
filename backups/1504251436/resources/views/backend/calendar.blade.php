@extends('backend.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('bootstrap-calendar/css/calendar.min.css')}}">
@stop

@section('content')
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<div class="pull-right form-inline calendar-nav">
				<div class="btn-group">
					<button class="btn btn-primary" data-calendar-nav="prev"><< Prev</button>
					<button class="btn btn-success" data-calendar-nav="today">Today</button>
					<button class="btn btn-primary" data-calendar-nav="next">Next >></button>
				</div>
				<div class="btn-group">
					<button class="btn btn-warning" data-calendar-view="year">Year</button>
					<button class="btn btn-warning active" data-calendar-view="month">Month</button>
					<button class="btn btn-warning" data-calendar-view="week">Week</button>
					<button class="btn btn-warning" data-calendar-view="day">Day</button>
				</div>
			</div>
			<ul class="calendar-guide">
				<li><span class="guide guide-blue"></span>&nbsp;&nbsp;Manuscripts</li>
				<li><span class="guide guide-green"></span>&nbsp;&nbsp;Webinars</li>
				<li><span class="guide guide-purple"></span>&nbsp;&nbsp;Webinars</li>
				<li><span class="guide guide-red"></span>&nbsp;&nbsp;Webinars</li>
				<li><span class="guide guide-orange"></span>&nbsp;&nbsp;Webinars</li>
			</ul>
			<div class="clearfix"></div>
			<div id="calendar"></div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script type="text/javascript" src="{{asset('js/underscore-min.js')}}"></script>
<script type="text/javascript" src="{{asset('bootstrap-calendar/js/calendar.js')}}"></script>
<script type="text/javascript">
	var calendar = $("#calendar").calendar(
		{
			tmpl_path: "{{asset('bootstrap-calendar/tmpls')}}/",
			events_source: function () { return [
				@foreach($events as $event)
				{!! json_encode($event) !!},
				@endforeach
			]; }
		});			

	$('.btn-group button[data-calendar-nav]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.navigate($this.data('calendar-nav'));
		});
	});

	$('.btn-group button[data-calendar-view]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.view($this.data('calendar-view'));
		});
	});

</script>
@stop
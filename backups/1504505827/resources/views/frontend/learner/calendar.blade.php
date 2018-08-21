@extends('frontend.layout')

@section('title')
<title>Calendar &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('bootstrap-calendar/css/calendar.min.css')}}">
@stop

@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">
			<ul class="calendar-guide">
				<li><span class="guide guide-blue"></span>&nbsp;&nbsp;Manus</li>
				<li><span class="guide guide-green"></span>&nbsp;&nbsp;Webinars</li>
				<li><span class="guide guide-purple"></span>&nbsp;&nbsp;Webinars</li>
				<li><span class="guide guide-red"></span>&nbsp;&nbsp;Webinars</li>
				<li><span class="guide guide-orange"></span>&nbsp;&nbsp;Webinars</li>
			</ul>
			<div class="pull-right form-inline">
				<div class="btn-group">
					<button class="btn btn-primary" data-calendar-nav="prev"><< Forrige</button>
					<button class="btn btn-success" data-calendar-nav="today">Idag</button>
					<button class="btn btn-primary" data-calendar-nav="next">Neste >></button>
				</div>
				<div class="btn-group">
					<button class="btn btn-warning" data-calendar-view="year">År</button>
					<button class="btn btn-warning active" data-calendar-view="month">Måned</button>
					<button class="btn btn-warning" data-calendar-view="week">Uke</button>
					<button class="btn btn-warning" data-calendar-view="day">Dag</button>
				</div>
			</div>
			<div class="clearfix"></div>
			<div id="calendar"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop


@section('scripts')
<script type="text/javascript" src="{{asset('js/underscore-min.js')}}"></script>
<script type="text/javascript" src="{{asset('bootstrap-calendar/js/language/no-NO.js')}}"></script>
<script type="text/javascript" src="{{asset('bootstrap-calendar/js/calendar.js')}}"></script>
<script type="text/javascript">
	var calendar = $("#calendar").calendar(
		{
			language: 'no-NO',
			tmpl_path: "{{asset('bootstrap-calendar/tmpls')}}/",
			events_source: [
				@foreach($events as $event)
				{!! json_encode($event) !!},
				@endforeach
			],
		}
	);			

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

@extends('backend.layout')

@section('title')
<title>Lessons &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12 col-md-12">
			<form class="pull-right" method="POST" action="{{ route('admin.lesson.save_order') }}">
				{{ csrf_field() }}
				<button type="submit" class="btn btn-success" id="save_order" disabled>{{ trans('site.save-order') }}</button>
				<input type="hidden" name="lesson_order" id="lesson_order">
				<input type="hidden" name="page" value="{{ isset($_REQUEST['page']) ? $_REQUEST['page'] : 1 }}">
			</form>
			<a class="btn btn-primary margin-bottom" href="{{route('admin.lesson.create', $course->id)}}">+ {{ trans('site.add-lesson') }}</a>
			<div class="table-responsive">
				<table class="table table-side-bordered table-white" id="lessonsTable" style="table-layout: fixed">
					<thead>
						<tr>
							<th>{{ trans('site.title') }}</th>
							<th>{{ trans('site.availability') }}</th>
							<th>{{ trans('site.created-at') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($course->lessons()->paginate(25) as $lesson)
						<tr style="cursor: move; background-color: #fff;" id="{{ $lesson->id }}">
							<td>
								<div style="display: flex;">
								<i class="fa fa-reorder" style="margin-right: 10px; font-size: 18px"></i>
								<a href="{{route('admin.lesson.edit', ['course_id' => $course->id, 'lesson' => $lesson->id ])}}">{{$lesson->title}}</a>
								</div>
							</td>
							<td>
							@if(AdminHelpers::isDate($lesson['delay']))
							{{date_format(date_create($lesson->delay), 'M d, Y')}}
							@else
							{{$lesson->delay}} days delay
							@endif
							</td>
							<td>{{$lesson->created_at}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="pull-right">{!! $course->lessons()->paginate(25)->appends(Request::all())->render() !!}</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

@stop

@section('scripts')
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script>
var fixHelper = function(e, ui) {  
  ui.children().each(function() {  
    $(this).outerWidth($(this).outerWidth());  
  });  
  return ui;  
};
$( function() {
	$( "#lessonsTable" ).sortable({
		helper: fixHelper,
		items: "tbody tr",
		axis: "y",
	  	containment: "parent",
	  	update: function (event, ui) {
	        var data = $(this).sortable('toArray');
	       $('#lesson_order').val(data);
	       $('#save_order').prop('disabled', false);
	    }
	});
} );
</script>
@stop
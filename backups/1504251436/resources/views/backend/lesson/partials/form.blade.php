<div class="container">
	<a class="btn btn-info margin-bottom" href="{{route('admin.course.show', $course->id)}}?section=lessons"><i class="fa fa-arrow-left"></i> Back to lessons</a>
	@if(Request::is('course/*/lesson/create'))
	<form action="{{route('admin.lesson.store', $course->id)}}" method="post">
	@else
	@include('backend.lesson.partials.delete')
	<form action="{{route('admin.lesson.update', ['course_id' => $lesson['id'], 'id' => $course->id])}}" method="post" id="lessonForm">
	{{method_field('PUT')}}
	@endif
		{{csrf_field()}}
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="title">Lesson Title</label>
							<input type="text" class="form-control" name="title" id="title" required value="{{$lesson['title']}}">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Delay type</label>
							<select class="form-control" id="lesson-delay-toggle">
								<option value="days">Days</option>
								<option value="date" @if(AdminHelpers::isDate($lesson['delay'])) selected @endif>Date</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<label>Delay</label>
						<div class="input-group">
							@if(AdminHelpers::isDate($lesson['delay']))
						  	<input type="date" class="form-control" name="delay" id="lesson-delay" min="0" required value="{{$lesson['delay']}}">
							@else
						  	<input type="number" class="form-control" name="delay" id="lesson-delay" min="0" required value="{{$lesson['delay']}}">
						  	@endif
						  	<span class="input-group-addon lesson-delay-text" id="basic-addon2">
						  	@if(AdminHelpers::isDate($lesson['delay']))
						  	date
						  	@else
						  	days
						  	@endif
						  	</span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6 col-sm-offset-6 text-right">
						@if(Request::is('course/*/lesson/create'))
						<button type="submit" class="btn btn-info">Create Lesson</button>
						@else
						<button type="submit" class="btn btn-info">Update Lesson</button>
						<button type="button" data-toggle="modal" data-target="#deleteLessonModal" class="btn btn-danger">Delete Lesson</button>
						@endif
						<textarea id="description-ct" class="hidden" name="content">{{$lesson['content']}}</textarea>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>



<div class="content-tools-container">
	<div class="container">
		<div data-editable data-name="main_content">
		    {!! $lesson['content'] !!}
		</div>
	</div>
</div>








@section('scripts')
<script src="{{asset('content_tools/content-tools.js')}}"></script>
<script>
jQuery(document).ready(function(){
	$('#lessonForm').on('submit', function(e){
		if( $('#description-ct').val().length  <= 0 ) {
			alert('Content must not be empty.');
			e.preventDefault();
			return false;
		}
	});
});
window.addEventListener('load', function() {

    var editor = ContentTools.EditorApp.get();
	editor.init('*[data-editable]', 'data-name');

	editor.addEventListener('saved', function (ev) {
	    var regions;

	    regions = ev.detail().regions;
	    if (Object.keys(regions).length == 0) {
	        return;
	    }

	    $('#description-ct').val(regions.main_content);
	});
});
</script>
@stop
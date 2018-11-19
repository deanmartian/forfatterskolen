@section('styles')
<link rel="stylesheet" href="{{asset('simplemde/simplemde.min.css')}}">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop
@if(Request::is('course/*/edit'))
@include('backend.course.partials.delete')
<form method="POST" action="{{route('admin.course.update', $course['id'])}}" enctype="multipart/form-data">
{{ method_field('PUT') }}
@else
<form method="POST" action="{{route('admin.course.store')}}" enctype="multipart/form-data">
@endif
	{{csrf_field()}}
	<div class="col-sm-12">
		@if(Request::is('course/*/edit'))
		<h3>Edit <em>{{$course['title']}}</em></h3>
		@else
		<h3>{{ trans('site.add-new-course') }}</h3>
		@endif
	</div>
	<div class="col-sm-12 col-md-8">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label>{{ trans('site.course-title') }}</label>
					<input type="text" class="form-control" name="title" value="{{$course['title']}}" required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.description') }}</label>
					<textarea name="description" rows="12" id="description-ct" class="form-control ckeditor">{{ $course['description'] }}</textarea>
				</div>
				<div class="form-group">
					<label>{{ trans('site.course-plan') }}</label>
					<textarea name="course_plan" rows="10" class="form-control ckeditor">{{ $course['course_plan'] }}</textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label id="course-image">{{ trans('site.course-image') }}</label>
					<div class="course-form-image image-file margin-bottom">
						<div class="image-preview" style="background-image: url('{{$course['course_image']}}')" data-default="{{Auth::user()->profile_image}}" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
						<input type="file" accept="image/*" name="course_image" accept="image/jpg, image/jpeg, image/png">
					</div>
				</div>
				<div class="form-group">
					<label>{{ trans('site.course-type') }}</label>
					<select class="form-control" name="type" required>
						<option value="Single" @if($course['type'] == "Single") selected @endif>Single Course</option>
						<option value="Group" @if($course['type'] == "Group") selected @endif>Group Course</option>
					</select>
				</div>
				<div class="form-group">
					<label>{{ trans('site.start-date') }}</label>
					<input type="date" class="form-control" name="start_date" @if( $course['start_date'] ) value="{{ date_format(date_create($course['start_date']), 'Y-m-d') }}" @endif>
				</div>
				<div class="form-group">
					<label>{{ trans('site.end-date') }}</label>
					<input type="date" class="form-control" name="end_date" @if( $course['end_date'] ) value="{{ date_format(date_create($course['end_date']), 'Y-m-d') }}" @endif>
				</div>
				<div class="form-group">
					<label>{{ trans('site.display-order') }}</label>
					<input type="number" class="form-control" name="display_order" @if( $course['display_order'] ) value="{{ $course['display_order'] }}" @endif>
				</div>
				<div class="form-group">
					<label>Instructor</label>
					<input type="text" class="form-control" name="instructor" @if( $course['instructor'] ) value="{{ $course['instructor'] }}" @endif>
				</div>

				<div class="form-group">
					<label>Free</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" name="is_free"
						   class="for-sale-toggle" data-off="No"
						   @if($course['is_free']) {{ 'checked' }} @endif>
				</div>

				@if(Request::is('course/*/edit'))
				<button type="submit" class="btn btn-primary">{{ trans('site.update-course') }}</button>
				<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteCourseModal">{{ trans('site.delete-course') }}</button>
				@else
				<button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.create-course') }}</button>
				@endif
			</div>
		</div>
		@if ( $errors->any() )
        <div class="alert alert-danger no-bottom-margin">
            <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
            </ul>
        </div>
        @endif
	</div>
</form>

	@section('scripts')
		<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
		<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
		<script>
            // tinymce
            var editor_config = {
                path_absolute: "{{ URL::to('/') }}",
                height: '15em',
                selector: '.ckeditor',
                plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern'],
                toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
                'alignjustify  | removeformat',
                toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
                relative_urls: false,
                file_browser_callback : function(field_name, url, type, win) {
                    var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                    var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                    var cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
                    if (type == 'image') {
                        cmsURL = cmsURL + '&type=Images';
                    } else {
                        cmsURL = cmsURL + '&type=Files';
                    }

                    tinyMCE.activeEditor.windowManager.open({
                        file : cmsURL,
                        title : 'Filemanager',
                        width : x * 0.8,
                        height : y * 0.8,
                        resizable : 'yes',
                        close_previous : 'no'
                    });
                }
            };
            tinymce.init(editor_config);
		</script>
	@stop
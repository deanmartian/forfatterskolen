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
					<textarea name="description" rows="12" id="description-ct" class="form-control tinymce">{{ $course['description'] }}</textarea>
				</div>
				<div class="form-group">
					<label>{{ trans('site.course-plan') }}</label>
					<textarea name="course_plan" rows="10" class="form-control tinymce">{{ $course['course_plan'] }}</textarea>
				</div>
				<div class="form-group">
					<label>Course Plan Data</label>
					<textarea name="course_plan_data" rows="10" class="form-control tinymce">{{ $course['course_plan_data'] }}</textarea>
				</div>
				<div class="form-group">
					<label>Meta Title</label>
					<input type="text" name="meta_title" class="form-control" minlength="40" maxlength="70"
						   value="{{ $course['meta_title'] }}" required>
				</div>
				<div class="form-group">
					<label>Meta Image</label>
					<input type="file" name="meta_image" accept="image/jpg, image/jpeg, image/png">
				</div>
				<div class="form-group">
					<label>Meta Description</label>
					<textarea class="form-control" name="meta_description" rows="6" maxlength="160"
							  minlength="70"
							  onkeyup="countChar(this)" required>{{ $course['meta_description'] }}</textarea>
					<div class="charNum">160 characters left</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label id="course-image">{{ trans('site.course-image') }}</label>
					<div class="course-form-image image-file">
						<div class="image-preview" style="background-image: url('{{$course['course_image']}}')" data-default="{{Auth::user()->profile_image}}" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
						<input type="file" accept="image/*" name="course_image" accept="image/jpg, image/jpeg, image/png">
					</div>

					<p class="text-center">
						<small class="text-muted">1140*600</small>
					</p>

				</div>
				<div class="form-group">
					<label>Photographer</label>
					<input type="text" class="form-control" name="photographer" @if( $course['photographer'] ) value="{{ $course['photographer'] }}" @endif>
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
					<label>Active Campaign List ID</label>
					<input type="number" class="form-control" name="auto_list_id" @if( $course['auto_list_id'] ) value="{{ $course['auto_list_id'] }}" @endif>
					{{-- <input type="text" class="form-control" name="auto_list_id" @if( $course['auto_list_id'] ) value="{{ $course['auto_list_id'] }}" @endif> --}}
				</div>

				<div class="form-group">
					<label>Free</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" name="is_free"
						   class="for-sale-toggle" data-off="No"
						   @if($course['is_free']) {{ 'checked' }} @endif>
				</div>

				<div class="form-group">
					<label>Free for days</label>
					<input type="number" class="form-control" name="free_for_days" 
					@if( $course['free_for_days'] ) value="{{ $course['free_for_days'] }}" @endif>
				</div>

				<div class="form-group">
					<label>Søknader</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" name="pay_later_with_application"
						   data-off="No"
						   @if($course['pay_later_with_application']) {{ 'checked' }} @endif>
				</div>

				<div class="form-group">
					<label>Hide Price</label> <br>
					<input type="checkbox" data-toggle="toggle" data-on="Yes" name="hide_price"
						   class="for-sale-toggle" data-off="No"
					@if($course['hide_price']) {{ 'checked' }} @endif>
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
		<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
		<script>
            function countChar(val) {
                let len = val.value.length;
                if (len >= 160) {
                    val.value = val.value.substring(0, 160);
                    $('.charNum').text(0 + " character left");
                } else {
                    let charText = "characters left";
                    if (160 - len === 1) {
                        charText = "character left";
                    }
                    $('.charNum').text(160 - len + " "+charText);
                }
            }
		</script>
	@stop
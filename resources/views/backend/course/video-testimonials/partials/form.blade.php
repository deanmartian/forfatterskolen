<form method="POST" action="{{Request::is('course/video/testimonial/*/edit')
? route('admin.course-video-testimonial.update', $testimonial['id'])
: route('admin.course-video-testimonial.store')}}" enctype="multipart/form-data">
	@if(Request::is('course/video/testimonial/*/edit'))
		{{ method_field('PUT') }}
	@endif
	{{csrf_field()}}

	<div class="col-sm-12">
		@if(Request::is('course/video/testimonial/*/edit'))
			<h3>{{ trans('site.edit') }} <em>{{$testimonial['name']}}</em>
				<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#cloneTestimonialModal"
						type="button">
					<i class="fa fa-copy"></i>
					{{ trans('site.clone') }}
				</button>
			</h3>
		@else
			<h3>{{ trans('site.add-new-testimonial') }}</h3>
		@endif
	</div>

	<div class="col-sm-12 col-md-8">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label>{{ trans('site.name') }}</label>
					<input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $testimonial['name'] }}" required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.testimony') }}</label>
					<textarea name="testimony" rows="12" id="description-ct" class="form-control">{{ old('testimony') ? old('testimony') : $testimonial['testimony'] }}</textarea>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-12 col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label id="course-image">{{ trans('site.video') }}</label>
					<div class="editor-form-video video-file margin-bottom">
						<video width="330" controls >
							<source src="{{ URL::asset($testimonial['user_image']) }}" id="video_here">
							Your browser does not support HTML5 video.
						</video>
						<input type="file" accept="video/*" name="user_image" class="form-control">
					</div>
				</div>

				<div class="form-group">
					<label>{{ trans_choice('site.courses', 1) }}</label>
					<select class="form-control" name="course_id" required>
						<option value="" disabled="disabled" selected>Select Course</option>
						@foreach(\App\Course::all() as $course)
							<option value="{{ $course->id }}" @if ($testimonial['course_id'] == $course->id) selected @endif> {{ $course->title }}</option>
						@endforeach
					</select>
				</div>

				@if(Request::is('course/video/testimonial/*/edit'))
					<button type="submit" class="btn btn-primary">{{ trans('site.update-testimonial') }}</button>
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteEditorModal">{{ trans('site.delete-testimonial') }}</button>
				@else
					<button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.create-testimonial') }}</button>
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

@if(Request::is('course/video/testimonial/*/edit'))
	@include('backend.course.video-testimonials.partials.delete')
	@include('backend.course.testimonials.partials.clone')
@endif
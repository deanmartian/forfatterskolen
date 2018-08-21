@section('styles')
	<style>
		#group-name-container {
			padding-left: 0;
		}

		#contact-id-container {
			padding-right: 0;
		}
	</style>
@stop
<form method="POST" action="{{Request::is('writing-group/*/edit')
? route('admin.writing-group.update', $writingGroup['id'])
: route('admin.writing-group.store')}}" enctype="multipart/form-data">

	@if(Request::is('writing-group/*/edit'))
		{{ method_field('PUT') }}
	@endif
	{{csrf_field()}}

	<div class="col-sm-12">
		@if(Request::is('writing-group/*/edit'))
			<h3>Edit <em>{{$writingGroup['name']}}</em></h3>
		@else
			<h3>Add New Group</h3>
		@endif
	</div>

		<div class="col-sm-12 col-md-8">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="form-group col-md-6" id="group-name-container">
						<label>Group Name</label>
						<input type="text" class="form-control" name="name"
							   value="{{ old('publishing') ? old('name') : $writingGroup['name'] }}" required>
					</div>
					<div class="form-group col-md-6" id="contact-id-container">
						<label for="inputPassword4">Contact Person</label>
						<select class="form-control select2" name="contact_id" required>
							<option value="" disabled selected>- Search learner -</option>
							@foreach( $learners as $learner )
								<option value="{{ $learner->id }}"
										@if($writingGroup['contact_id'] == $learner->id) selected @endif>
									{{ $learner->full_name }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label>About the group</label>
						<textarea name="description" id="" cols="30" rows="10" class="form-control"
								  required>{{ old('description') ? old('description') : $writingGroup['description'] }}</textarea>
					</div>
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

		<div class="col-sm-12 col-md-4">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="form-group">
						<label id="course-image">Photo</label>
						<div class="course-form-image image-file margin-bottom">
							<div class="image-preview" style="background-image: url('{{$writingGroup['group_photo']}}')" data-default="" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
							<input type="file" accept="image/*" name="group_photo" accept="image/jpg, image/jpeg, image/png">
						</div>
					</div>

					<div class="form-group">
						<label>Next Meeting</label>
						<textarea name="next_meeting" id="" cols="20" rows="8" class="form-control"
						>{{ old('next_meeting') ? old('next_meeting') : $writingGroup['next_meeting'] }}</textarea>
					</div>

					@if(Request::is('writing-group/*/edit'))
						<button type="submit" class="btn btn-primary">Update Group</button>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteWritingGroupModal">Delete Group</button>
					@else
						<button type="submit" class="btn btn-primary btn-block btn-lg">Create Group</button>
					@endif
				</div>
			</div>
		</div>
</form>

@if(Request::is('writing-group/*/edit'))
	@include('backend.writing-group.partials.delete')
@endif
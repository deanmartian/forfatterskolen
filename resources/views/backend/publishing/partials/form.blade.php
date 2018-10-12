<form method="POST" action="{{Request::is('publishing/*/edit')
? route('admin.publishing.update', $publishingHouse['id'])
: route('admin.publishing.store')}}">
	@if(Request::is('publishing/*/edit'))
		{{ method_field('PUT') }}
	@endif
	{{csrf_field()}}

	<div class="col-sm-12">
		@if(Request::is('publishing/*/edit'))
			<h3>Edit <em>{{$publishingHouse['publishing']}}</em></h3>
		@else
			<h3>{{ trans('site.add-new-publishers-house') }}</h3>
		@endif
	</div>

	<div class="col-sm-12 col-md-8">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group">
					<label>{{ trans('site.publisher-house') }}</label>
					<input type="text" class="form-control" name="publishing" value="{{ old('publishing') ? old('publishing') : $publishingHouse['publishing'] }}" required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.link-to-homepage') }}</label>
					<input type="text" class="form-control" name="home_link" value="{{ old('home_link') ? old('home_link') : $publishingHouse['home_link'] }}">
				</div>
				<div class="form-group">
					<label>{{ trans('site.mail-address') }}</label>
					<input type="text" class="form-control" name="mail_address" value="{{ old('mail_address') ? old('mail_address') : $publishingHouse['mail_address'] }}" required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.phone-number') }}</label>
					<input type="text" class="form-control" name="phone" value="{{ old('phone') ? old('phone') : $publishingHouse['phone'] }}" required>
				</div>
				<div class="form-group">
					<label>{{ trans('site.genre') }}</label>
					<select class="form-control" name="genre[]" required multiple>
                        <?php $currentGenre = old('genre') ? old('genre') : array_filter(explode(', ',$publishingHouse['genre']));?>
						<option value="" disabled="disabled" @if(empty($currentGenre)) selected @endif>Velg sjanger</option>
						@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								@if(is_array($currentGenre) && in_array($type['id'], $currentGenre))
								<option value="{{ $type['id'] }}" selected> {{ $type['option'] }} </option>
								@else
									<option value="{{ $type['id'] }}" > {{ $type['option'] }} </option>
								@endif
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label>{{ trans('site.link-to-send-manuscript') }}</label>
					<input type="text" class="form-control" name="send_manuscript_link" value="{{ old('send_manuscript_link') ? old('send_manuscript_link') : $publishingHouse['send_manuscript_link'] }}" >
				</div>
				<div class="form-group">
					<label>{{ trans('site.email-address') }}</label>
					<input type="email" class="form-control" name="email" value="{{ old('email') ? old('email') : $publishingHouse['email'] }}" required>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-12 col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">


				@if(Request::is('publishing/*/edit'))
					<button type="submit" class="btn btn-primary">{{ trans('site.update-publishing') }}</button>
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deletePublishingModal">{{ trans('site.delete-publishing') }}</button>
				@else
					<button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.create-publishing-house') }}</button>
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

@if(Request::is('publishing/*/edit'))
	@include('backend.publishing.partials.delete')
@endif
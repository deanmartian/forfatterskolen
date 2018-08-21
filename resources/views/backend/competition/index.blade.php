@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Competitions &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> All Competitions</h3>
	<div class="clearfix"></div>
</div>

<div class="col-md-12">
	<button class="btn btn-primary margin-top margin-bottom" data-toggle="modal" data-target="#addCompetitionModal">Add Competition</button>

	@foreach($competitions->chunk(3) as $competition_chunk)
		<div class="col-sm-12">
			@foreach($competition_chunk as $competition)
				<div class="col-sm-4">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="webinar-image" style="background-image:url('{{ $competition->image }}')"></div>
							<div class="pull-right">
								<a class="btn btn-xs btn-info editCompetitionBtn"
								   data-toggle="modal"
								   data-target="#editCompetitionModal"
								   data-action="{{ route('admin.competition.update', $competition->id) }}"
								   data-title="{{ $competition->title }}"
								   data-genre="{{ $competition->genre }}"
								   data-description="{{ $competition->description }}"
								   data-start_date="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($competition->start_date)) }}"
								   data-image="{{ $competition->image }}"
								   data-link="{{ $competition->link }}"
								>
									<i class="fa fa-pencil"></i></a>

								<a class="btn btn-xs btn-danger deleteCompetitionBtn"
								   data-toggle="modal"
								   data-target="#deleteCompetitionModal"
								   data-action="{{ route('admin.competition.destroy', $competition->id) }}"
								   data-title="{{ $competition->title }}"
								><i class="fa fa-trash"></i></a>
							</div>
							<strong>{{ $competition->title }}</strong>
							<br />
							Genre: <i>{{ $competition->genre ? \App\Http\FrontendHelpers::assignmentType($competition->genre) : '' }}</i>
							<br>
							{!! nl2br($competition->description) !!}
							<br>
							<p style="line-height: 1.8em; margin-top: 7px;">
								<i class="fa fa-link"></i>&nbsp;&nbsp;{{ strlen($competition->link) > 55 ? substr($competition->link,0,55)."..." : $competition->link }} <br />
								<i class="fa fa-calendar-o"></i>&nbsp;&nbsp;{{ $competition->start_date }} <br />
							<!-- <i class="fa fa-users"></i>&nbsp;&nbsp;Attendees (20) -->
							</p>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	@endforeach

	<div class="clearfix"></div>
</div>

{{--Add Competition Modal--}}
<div id="addCompetitionModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Competition</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.competition.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Title</label>
						<input type="text" name="title" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Genre</label>
						<select name="genre" id="" class="form-control" required>
							<option value="" disabled="disabled" selected>Velg Sjanger</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Description</label>
						<textarea class="form-control" name="description" required rows="6"></textarea>
					</div>
					<div class="form-group">
						<label>Start date</label>
						<input type="datetime-local" name="start_date" class="form-control" required>
					</div>
					<div class="form-group">
						<label>URL</label>
						<input type="url" name="link" class="form-control" required>
					</div>

					<div class="form-group">
						<label id="course-image">Image</label>
						<div class="course-form-image image-file margin-bottom">
							<div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
							<input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
						</div>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">Add Competition</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

	{{--Edit Competition Modal--}}
<div id="editCompetitionModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit <em></em></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<div class="form-group">
						<label>Title</label>
						<input type="text" name="title" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Genre</label>
						<select name="genre" id="" class="form-control" required>
							<option value="" disabled="disabled" selected>Velg Sjanger</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Description</label>
						<textarea class="form-control" name="description" required rows="6"></textarea>
					</div>
					<div class="form-group">
						<label>Start date</label>
						<input type="datetime-local" name="start_date" class="form-control" required>
					</div>
					<div class="form-group">
						<label>URL</label>
						<input type="url" name="link" class="form-control" required>
					</div>

					<div class="form-group">
						<label id="course-image">Image</label>
						<div class="course-form-image image-file margin-bottom">
							<div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
							<input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
						</div>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">Update competition</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<!-- Delete Competition Modal -->
<div id="deleteCompetitionModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Competition <em></em></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}
					<p>Are you sure to delete this competition?</p>
					<div class="text-right">
						<button type="submit" class="btn btn-danger">Delete competition</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>
@stop

@section('scripts')
	<script>
		$(function(){
            $('.editCompetitionBtn').click(function(){
                var modal = $('#editCompetitionModal');
                var form = modal.find('form');
                var action = $(this).data('action');
                var title = $(this).data('title');
                var genre = $(this).data('genre');
                var description = $(this).data('description');
                var start_date = $(this).data('start_date');
                var image = $(this).data('image');
                var link = $(this).data('link');

                modal.find('em').text(title);
                form.attr('action', action);
                form.find('input[name=title]').val(title);
                if (genre) {
                    form.find('select[name=genre]').val(genre);
                }
                form.find('textarea[name=description]').val(description);
                form.find('input[name=start_date]').val(start_date);
                form.find('input[name=link]').val(link);
                form.find('.image-preview').css('background-image', 'url('+image+')');
            });

            $('.deleteCompetitionBtn').click(function(){
                var modal = $('#deleteCompetitionModal');
                var form = modal.find('form');
                var action = $(this).data('action');
                var title = $(this).data('title');

                modal.find('em').text(title);
                form.attr('action', action);
            });
		});
	</script>
@stop
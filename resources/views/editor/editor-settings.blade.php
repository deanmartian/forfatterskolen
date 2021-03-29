@extends('editor.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('css/editor.css')}}">
	<style>
		.panel {
			overflow-x: auto;
		}
	</style>
@stop

@section('content')
<div class="col-sm-12 dashboard-left">
	<div class="row">
		<div class="col-sm-12">
            <ul class="nav nav-tabs margin-top">
                <li @if( Request::input('tab') == 'howManyManuscriptEditorCanTake' || Request::input('tab') == '') class="active" @endif><a href="?tab=howManyManuscriptEditorCanTake">{{ trans('site.admin-menu.how-many-manuscript-you-can-take') }}</a></li>
                <li @if( Request::input('tab') == 'genrePreference' ) class="active" @endif><a href="?tab=genrePreference">{{ trans('site.genre-preference') }}</a></li>
                <li @if( Request::input('tab') == 'assignmentManuscriptYouCanTake' ) class="active" @endif><a href="?tab=assignmentManuscriptYouCanTake">{{ trans('site.how-many-manuscript-assignments-editor-can-take') }}</a></li>
            </ul>
			
            @if( Request::input('tab') == 'howManyManuscriptEditorCanTake' || Request::input('tab') == '')

				<div class="panel panel-default">
					<br>
					<div class="panel-heading">
						<h4 class="dib">
							{{ trans('site.admin-menu.how-many-manuscript-you-can-take') }}
						</h4>
						<button class="btn btn-xs btn-primary addBtn pull-right"
								data-toggle="modal"
								data-target="#addModal"
								data-action="{{ route('editor.manuscript-you-can-take-save') }}">{{ trans('site.add-new') }}</button>
					</div>
					<table class="table">
						<thead>
						<tr>
							<th>{{ trans('site.start-date') }}</th>
							<th>{{ trans('site.end-date') }}</th>
							<th>{{ trans('site.how-many-manuscript') }}</th>
							<th>{{ trans('site.how-many-hours') }}</th>
							<th>{{ trans_choice('site.notes', 1) }}</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($manuscriptEditorCanTake as $data)
							<tr>
								<td>{{ $data->date_from }}</td>
								<td>{{ $data->date_to }}</td>
								<td>{{ $data->how_many_script }}</td>
								<td>{{ $data->how_many_hours }}</td>
								<td>{{ $data->note }}</td>
								<td>
									<button class="btn btn-xs btn-success addBtn"
											data-toggle="modal"
											data-target="#addModal"
											data-edit="1"
											data-id="{{ $data->id }}"
											data-date_from="{{ $data->date_from }}"
											data-date_to="{{ $data->date_to }}"
											data-how_many_script="{{ $data->how_many_script }}"
											data-how_many_hours="{{ $data->how_many_hours }}"
											data-note="{{ $data->note }}"
											data-action="{{ route('editor.manuscript-you-can-take-save') }}">
											<i class="fa fa-pencil-square-o" aria-hidden="true"> {{ trans('site.edit') }}</i>
									</button>
									<button class="btn btn-xs btn-danger deleteBtn"
											data-toggle="modal"
											data-target="#deleteModal"
											data-action="{{ route('editor.manuscript-you-can-take.delete', $data->id) }}">
											<i class="fa fa-trash" aria-hidden="true"> {{ trans('site.delete') }}</i>
									</button>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			@elseif( Request::input('tab') == 'assignmentManuscriptYouCanTake' )

			<div class="panel panel-default">
				<br>
				<div class="panel-heading">
					<h4 class="dib">
						{{ trans('site.how-many-manuscript-assignments-editor-can-take') }}
					</h4>

				</div>
				<table class="table">
					<thead>
					<tr>
						<th>{{ trans_choice('site.courses', 1) }}</th>
						<th>{{ trans_choice('site.assignments', 1) }}</th>
						<th>{{ trans('site.learner.submission-date') }}</th>
						<th>{{ trans('site.deadline') }}</th>
						<th style="width: 200px;">{{ trans('site.how-many-you-can-take') }}</th>
					</tr>
					</thead>
					<tbody>
						@foreach($assignmentsBeforeEditorDeadline as $key)
						<tr>
							<td>{{ $key->course->title }}</td>
							<td>{{ $key->title }}</td>
							<td>{{ $key->submission_date }}</td>
							<td>{{ $key->editor_expected_finish }}</td>
							<td>
								<?php 
									$data = \App\AssignmentManuscriptEditorCanTake::where('assignment_manuscript_id', $key->id)->where('editor_id', Auth::user()->id)->first(); 
									if($data){
										echo $data->how_many_you_can_take;
										echo '<button style="margin-right: 16px;" type="button" class="pull-right btn btn-sm btn-xs btn-info editHowManyYouCanTakeBtn" 
													data-toggle="modal"
													data-action="'.route('editor.saveAssignmentManuscriptEditorCanTake', ['id' => $data->id, 'assignment_manu_id' => $key->id]).'" 
													data-target="#editHowManyYouCanTake"
													data-edit = "1"
													data-value ="'.$data->how_many_you_can_take.'">
													<i class="fa fa-pencil"></i></button>';
									}else{
										echo '<button style="margin-right: 16px;" type="button" class="pull-right btn btn-sm btn-xs btn-info editHowManyYouCanTakeBtn" 
													data-toggle="modal"
													data-action="'.route('editor.saveAssignmentManuscriptEditorCanTake', ['id' => 0, 'assignment_manu_id' => $key->id]).'" 
													data-target="#editHowManyYouCanTake">
													<i class="fa fa-pencil"></i></button>';
									}
								?>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

            @else
				<div class="panel panel-default">
					<br>
					<div class="panel-heading">
						<h4 class="dib">
						{{ trans('site.genre-preference') }}
						</h4>
					</div>
					<div class="col-sm-6">
						<table class="table">
							<thead>
								<tr>
									<th style="width: 300px;"></th>
									<th>
										<button class="btn btn-xs btn-primary addBtn pull-right"
										data-toggle="modal"
										data-target="#addGenrePreferences">{{ trans('site.add-new') }}
										</button>
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach($genrePrefrences as $key)
									<tr>
										<td>{{ $key->genre->name }}</td>
										<td>
											<a class="deleteGenrePreferencesBtn"data-toggle="modal"
												data-target="#deleteGenrePreferences"
												data-action="{{ route('editor.delete-genre-preferences', $key->id) }}"
												style="color: #e63030; font-size: 2rem;">
												<i class="fa fa-times" aria-hidden="true"></i>
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

            @endif
		</div>
	</div>
</div>

<!-- add modal  -->
<div id="addModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form id="addForm" method="POST" action="" enctype="multipart/form-data">
					{{csrf_field()}}
					<input type="hidden" class="form-control" name="id">
					<div class="form-group">
						<label>{{ trans('site.start-date') }}</label>
						<input required name="date_from" type="date" class="form-control"></input>
					</div>
					<div class="form-group">
                        <label>{{ trans('site.end-date') }}</label>
                        <input required name="date_to" type="date" class="form-control">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.how-many-manuscript') }}</label>
                        <input required name="how_many_script" type="number" value=0 class="form-control">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.how-many-hours') }}</label>
                        <input required name="how_many_hours" step="0.01" type="number" value=0 class="form-control">
                    </div>
					<div class="form-group">
                        <label>{{ trans_choice('site.notes', 1) }}</label>
                        <textarea name="note" rows="3" class="form-control"></textarea>
                    </div>
					<button type="submit" class="btn btn-primary pull-right">{{ trans_choice('site.save', 1) }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="deleteModal" class="modal fade " role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.delete-question') }}</h4>
			</div>
			<div class="modal-body">
				<form id="deleteForm" method="POST" action="" enctype="multipart/form-data">
					{{csrf_field()}}
					<input type="hidden" class="form-control" name="id">
					<div style="text-align: center;" class="decision">
						<button style="padding: 10px 65px;" type="submit" class="btn btn-lg btn-danger">{{ trans('site.front.yes') }}</button>
						<button style="padding: 10px 65px; margin-right: 4px;" type="button" data-dismiss="modal" class="btn btn-lg btn-default">{{ trans('site.front.no') }}</button>
					</div>
					
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="addGenrePreferences" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.add-new') }}</h4>
			</div>
			<div class="modal-body">
				<form id="addForm" method="POST" action="{{ route('editor.save-genre-prefences', 0) }}" enctype="multipart/form-data">
					{{csrf_field()}}
					
					<select class="form-control select2" name="genre_id" required>
						<option value="" selected disabled>
							-- Select Genre --
						</option>
						@foreach($genreIHaveNotSelected as $key)
							<option value="{{ $key->id }}">
								{{ $key->name }}
							</option>
						@endforeach
					</select>
					<br><br>
					<button type="submit" class="btn btn-primary pull-right">{{ trans_choice('site.save', 1) }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="deleteGenrePreferences" class="modal fade " role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.delete-question') }}</h4>
			</div>
			<div class="modal-body">
				<form id="deleteForm" method="POST" action="" enctype="multipart/form-data">
					{{csrf_field()}}
					<input type="hidden" class="form-control" name="id">
					<div style="text-align: center;" class="decision">
						<button style="padding: 10px 65px;" type="submit" class="btn btn-lg btn-danger">{{ trans('site.front.yes') }}</button>
						<button style="padding: 10px 65px; margin-right: 4px;" type="button" data-dismiss="modal" class="btn btn-lg btn-default">{{ trans('site.front.no') }}</button>
					</div>
					
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editHowManyYouCanTake" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.how-many-you-can-take') }}</h4>
			</div>
			<div class="modal-body">
				<form id="addForm" method="POST" action="" enctype="multipart/form-data">
					{{csrf_field()}}
					<div class="form-group">
                        <label>{{ trans('site.how-many-you-can-take') }}</label>
                        <input name="how_many_you_can_take" type="text" class="form-control">
                    </div>
					<button type="submit" class="btn btn-primary pull-right">{{ trans_choice('site.save', 1) }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script>
		$(".addBtn").click(function(){

			var modal = $('#addModal');
			var action = $(this).data('action');
			var is_edit = $(this).data('edit');
			modal.find('form').attr('action', action);

			$('#addForm').trigger('reset');
			modal.find('#feedbackFileAppend').html('');
			modal.find('.modal-title').text("Add New");
			modal.find('[name=id]').val('')

			if(is_edit){

				let id = $(this).data('id');
				let dateFrom = $(this).data('date_from');
				let dateTo= $(this).data('date_to');
				let howMayScript= $(this).data('how_many_script');
				let howMayHours= $(this).data('how_many_hours');
				let note = $(this).data('note');

				modal.find('[name=id]').val(id)
				modal.find('[name=date_from]').val(dateFrom)
				modal.find('[name=date_to]').val(dateTo)
				modal.find('[name=how_many_script]').val(howMayScript)
				modal.find('[name=how_many_hours]').val(howMayHours)
				modal.find('[name=note]').val(note)

			}
			
		});

		$(".editHowManyYouCanTakeBtn").click(function(){
			var is_edit = $(this).data('edit');
			var action = $(this).data('action');
			var value = $(this).data('value'); //$(this).data('action');
			var modal = $('#editHowManyYouCanTake');
			modal.find('form').attr('action', action);
			
			if (is_edit){
				modal.find('[name=how_many_you_can_take]').val(value);
			}else{
				modal.find('[name=how_many_you_can_take]').val('');
			}

		});

		$(".deleteBtn").click(function(){
			var modal = $('#deleteModal');
			var action = $(this).data('action');
			modal.find('form').attr('action', action);
		});

		$(".deleteGenrePreferencesBtn").click(function(){
			var modal = $('#deleteGenrePreferences');
			var action = $(this).data('action');
			modal.find('form').attr('action', action);
		});
		
	</script>
@stop

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
			<div class="panel panel-default">
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
						<input name="date_from" type="date" class="form-control"></input>
					</div>
					<div class="form-group">
                        <label>{{ trans('site.end-date') }}</label>
                        <input name="date_to" type="date" class="form-control">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.how-many-manuscript') }}</label>
                        <input name="how_many_script" step="0.01" type="number" class="form-control">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.how-many-hours') }}</label>
                        <input name="how_many_hours" step="0.01" type="number" class="form-control">
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

		$(".deleteBtn").click(function(){

			var modal = $('#deleteModal');
			var action = $(this).data('action');
			modal.find('form').attr('action', action);

		});
	</script>
@stop

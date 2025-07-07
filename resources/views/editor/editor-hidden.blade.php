@extends('backend.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.panel {
			overflow-x: auto;
		}
	</style>
@stop

@section('content')
	<div class="row" style="margin: 14px;">
        <div class="col">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>{{ trans('site.editor-hidden-at') }}</h4></div>
                <button class="btn btn-warning btn-xs hideEditorBtn pull-right" 
                        style="margin-right: 15px; margin-bottom: 5px;"
                        data-toggle="modal" 
                        data-target="#hideEditorModal"
                        data-action="{{ route('admin.hide-show-editor', ['editor_id' => $editor->id, 'hide' => 1]) }}"
                >
                + {{ trans('site.hide-editor') }}
                </button>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans('site.start-date') }}</th>
                                <th>{{ trans('site.end-date') }}</th>
                                <th>{{ trans_choice('site.notes', 2) }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hiddenEditor as $key)
                                <tr>
                                    <th>{{ $key->hide_date_from }}</th>
                                    <th>
                                        @if($key->hide_date_to)
                                            {{ $key->hide_date_to }}
                                        @else
                                            {{ trans('site.until-turned-back-unhidden') }}
                                        @endif
                                    </th>
                                    <th>{{ $key->notes }}</th>
                                    <th>
                                        <a href="{{ route('admin.delete-editor-hidden', $key->id) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="panel panel-default">
            <div class="panel-heading"><h4>{{ trans('site.all-assigned-assignments') }}</h4></div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans_choice('site.courses', 1) }}</th>
                                <th>{{ trans_choice('site.assignments', 1) }}</th>
                                <th>{{ trans('site.learner.submission-date') }}</th>
                                <th>{{ trans('site.deadline') }}</th>
                                <th style="width: 200px;">{{ trans('site.how-many-you-can-take') }}</th>
                                <th>{{ trans('site.assigned-assignment-count') }}</th>
                                <th>{{ trans('site.finished') }}</th>
                                <th>{{ trans('site.pending') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignment as $key)
                                <tr>
                                    <td>{{$key->course->title}}</td>
                                    <td>{{$key->title}}</td>
                                    <td>{{$key->submission_date}}</td>
                                    <td>{{$key->editor_expected_finish}}</td>
                                    <td>{{$key->assignmentManuscriptEditorCanTake->where('editor_id', $editor->id)->count()}}</td>
                                    <td>{{$key->manuscripts->count()}}</td>
                                    <td>{{$key->manuscripts->where('has_feedback', 1)->count()}}</td>
                                    <td>{{$key->manuscripts->where('has_feedback', 0)->count()}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="hideEditorModal" class="modal fade" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ trans('site.hide-editor') }} <em></em></h4>
                    </div>
                    <div class="modal-body">
                        <form id="hideEditorForm" method="POST" action=""  enctype="multipart/form-data" 
                            onsubmit="disableSubmit(this)">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>{{ trans('site.start-date') }}</label>
                                <input required type="date" class="form-control" step="0.01" name="start_date">
                            </div>
                            <input class="form-check-input" type="checkbox" id="hideUntilTurnedBackUnhidden" name="hideUntilTurnedBackUnhidden">
                            <label class="form-check-label" for="hideUntilTurnedBackUnhidden">
                                <strong>{{ trans('site.until-turned-back-unhidden') }}</strong>
                            </label>
                            <br><br>
                            <div class="form-group">
                                <div class="hide-end-date">
                                    <label>{{ trans('site.end-date') }}</label>
                                    <input type="date" class="form-control" step="0.01" name="end_date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ trans_choice('site.notes', 2) }}</label>
                                <textarea name="notes" maxlength="1000" cols="39" rows="5"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning pull-right margin-top">{{ trans('site.hide-editor') }}</button>
                            <div class="clearfix"></div>
                        </form>
                </div>
                </div>
            </div>
        </div>
	</div>
@stop

@section('scripts')
<script>
	$('.hideEditorBtn').click(function(){
        
        let action = $(this).data('action');
        let modal = $('#hideEditorModal');
        let edit = $(this).data('edit');
        let dateFrom = $(this).data('date_from');
        let dateTo = $(this).data('date_to');
        let notes = $(this).data('notes');

        modal.find('form').attr('action', action);
        modal.find('#hideUntilTurnedBackUnhidden').prop("checked", false);
    })
    $('#hideUntilTurnedBackUnhidden').click(function(){
        if($(this).is(':checked')){
            $('.hide-end-date').css('display','none');
        }else{
            $('.hide-end-date').css('display','block');
        }
    });
</script>
@stop
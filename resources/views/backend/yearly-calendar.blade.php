@extends('backend.layout')

@section('title')
    <title>Yearly Calendar &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    #editors-note-panel table td {
        border: 1px solid #eeeeee;
        padding: 5px 15px;
    }

    #editors-note-panel tr:nth-child(even) {
        background-color: #f2f2f2; /* Alternate background color for even rows */
    }
</style>
@stop

@section('content')
<div class="col-md-12">
    <ul class="nav nav-tabs margin-top">
        <li @if( Request::input('tab') == 'howManyManuscriptEditorCanTake' || Request::input('tab') == '') class="active" @endif><a href="?tab=howManyManuscriptEditorCanTake">How Many Manuscript You Can Take</a></li>
        {{-- <li @if( Request::input('tab') == 'yearlyCalendar' ) class="active" @endif><a href="?tab=yearlyCalendar">Yearly Calendar</a></li> --}}
        <li @if( Request::input('tab') == 'howManyAssignmentsEditorCanTake' ) class="active" @endif><a href="?tab=howManyAssignmentsEditorCanTake">{{ trans('site.how-many-manuscript-assignments-editor-can-take') }}</a></li>
        <li @if( Request::input('tab') == 'editorsAvailability' ) class="active" @endif><a href="?tab=editorsAvailability">{{ trans('site.editors-availability') }}</a></li>
        <li @if( Request::input('tab') == 'unfinished-manuscript' ) class="active" @endif><a href="?tab=unfinished-manuscript">{{ trans('site.unfinished-manuscript') }}</a></li>
        <li @if( Request::input('tab') == 'editors-note' ) class="active" @endif><a href="?tab=editors-note">Redaktørinnstruks</a></li>
    </ul>
    <div class="col-sm-12 dashboard-left">
        @if( Request::input('tab') == 'yearlyCalendar')

            <div class="container">
                <h3><i class="fa fa-file-text-o"></i> Yearly Calendar</h3>
                <div class="clearfix"></div>
                <div style="max-width:1000px;width:100%;"><div style="position: relative;padding-bottom: 117%;padding-top: 35px;height: 0;overflow: hidden;"><iframe src="https://create.plandisc.com/wheel/embed/pB6HbNe" scrolling="no" frameborder="0" style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe></div></div><a style="font-size:10px;" href="https://create.plandisc.com/pB6HbNe">Problemer med å se Plandiscen? Trykk her</a>
            </div>
        @elseif( Request::input('tab') == 'howManyAssignmentsEditorCanTake')

            <div class="table-users table-responsive">
            
                    <table class="table margin-top">
                        <thead>
                        <tr>
                            <th>{{ trans_choice('site.editors', 1) }}</th>
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
                            @foreach($assignmentManuscriptEditorCanTake as $i)
                                <tr>
                                    <td>{{ $i->editor->full_name }}</td>
                                    <td>{{ $i->assignment->course->title }}</td>
                                    <td>{{ $i->assignment->title }}</td>
                                    <td>{{ $i->assignment->submission_date }}</td>
                                    <td>{{ $i->assignment->editor_expected_finish }}</td>
                                    <td>
                                    {{ $i->how_many_you_can_take }} &nbsp;
                                   <a style="margin-right: 130px; color: green;" class="pull-right editHowManyYouCanTake"
                                        data-toggle="modal" 
                                        data-target="#editHowManyYouCanTake"
                                        data-how_many_manuscript = "{{ $i->how_many_you_can_take }}"
                                        data-action="{{ route('admin.setHowManyManuscriptYouCanTake', $i->id) }}">
                                   <i class="fa fa-pencil" aria-hidden="true"></i>
                                   </a>
                                    </td>
                                    <td>{{ $i->AssignedCount }}</td>
                                    <td>{{ $i->FinishedCount }}</td>
                                    <td>{{ $i->PendingCount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>

        @elseif( Request::input('tab') == 'howManyManuscriptEditorCanTake' || Request::input('tab') == '')
            
            <div class="table-users table-responsive">
        
                @foreach($editor as $key)
                    <?php $hm = null; ?>
                    @if($key->HowManyManuscriptYouCanTakeActive->count() > 0)
                        <table class="table margin-top">
                            <thead>
                            <tr>
                                <th style="width: 260px;">{{ $key->FullName }}</th>
                                <th style="width: 100px;">{{ trans('site.start-date') }}</th>
                                <th style="width: 100px;">{{ trans('site.end-date') }}</th>
                                <th>{{ trans('site.how-many-manuscript') }}</th>
                                <th>{{ trans('site.how-many-hours') }}</th>
                                <th style="width: 779px;">{{ trans_choice('site.notes', 1) }}</th>
                            </tr>
                            </thead>

                            <tbody>
                                <?php $hm = \App\ManuscriptEditorCanTake::where('editor_id', $key->id)->orderBy('date_from', 'DESC')->paginate(10, ["*"], $key->id); ?>
                                @foreach($hm as $i)
                                    <tr>
                                        <td></td>
                                        <td>{{ $i->date_from }}</td>
                                        <td>{{ $i->date_to }}</td>
                                        <td>{{ $i->how_many_script }}</td>
                                        <td>{{ $i->how_many_hours }}</td>
                                        <td>{{ $i->note }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="margin-top: -13px;" class="pull-right">
                            @if($hm)
                                {{$hm->render()}}
                            @endif
                        </div>
                    @endif

                @endforeach

            </div>
            
        @elseif(Request::input('tab') == 'editorsAvailability')
            
            <div class="table-users table-responsive">
                <table class="table margin-top">
                    <thead>
                    <tr>
                        <th>{{ trans_choice('editors', 1) }}</th>
                        <th>{{ trans('site.hide-editor') }}</th>
                        <th>{{ trans('site.preview-editor-assignments-and-editor-hidden-dates') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                        @foreach($editor as $key)
                        <tr>
                            <td>{{ $key->FullName }}</td>
                            <td>
                                <button class="btn btn-warning btn-xs hideEditorBtn" 
                                        data-toggle="modal" 
                                        data-target="#hideEditorModal"
                                        data-action="{{ route('admin.hide-show-editor', ['editor_id' => $key->id, 'hide' => 1]) }}"
                                >
                                + {{ trans('site.hide-editor') }}
                                </button>
                            </td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.show-editor-hidden', $key->id) }}"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;{{ trans('site.preview-details') }}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @elseif(Request::input('tab') == 'unfinished-manuscript')
        <!-- assignment -->
            <div class="panel">
                <div class="panel-body">
                    <h3>{{ trans_choice('site.assignments', 1) }}</h3>

                    <div class="table-users table-responsive margin-top">
                        <table class="table dt-table">
                            <thead>
                                <tr>
                                    <th>{{ trans_choice('site.assignments', 1) }}</th>
                                    <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                                    <th>{{ trans_choice('site.learners', 1) }}</th>
                                    <th>Expected Finish</th>
                                    <th>Expected Editor Finish</th>
                                    <th>{{ trans_choice('site.editors', 1) }}</th>
                                    <th>{{ trans('site.type') }}</th>
                                    <th>{{ trans('site.where') }}</th>
                                    <th>{{ trans_choice('site.words', 2) }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unfinishedAssignments as $unfinishedAssignment)
                                    <tr>
                                        <td>
                                            @if ($unfinishedAssignment->assignment->course_id)
                                                <a href="{{ route('admin.assignment.show',
                                                [
                                                    'course_id' => $unfinishedAssignment->assignment->course_id ,
                                                    'assignment' => $unfinishedAssignment->assignment_id
                                                ]) }}">
                                                    {{$unfinishedAssignment->assignment->title}}
                                                </a>
                                            @else
                                                <a href="{{ route('admin.learner.assignment',
                                                [
                                                    'user_id' => $unfinishedAssignment->user_id ,
                                                    'id' => $unfinishedAssignment->assignment_id
                                                ]) }}">
                                                    {{$unfinishedAssignment->assignment->title}}
                                                </a>
                                            @endif
                                            
                                        </td>
                                        <td>
                                            {!! $unfinishedAssignment->file_link !!}
                                        </td>
                                        <td>
                                            <a href="{{route('admin.learner.show', $unfinishedAssignment->user->id)}}">{{ $unfinishedAssignment->user->full_name }}</a>
                                        </td>
                                        <td>
                                            {{ $unfinishedAssignment->expected_finish }}
                                        </td>
                                        <td>
                                            {{ $unfinishedAssignment->editor_expected_finish }}
                                        </td>
                                        <td>
                                            @if( $unfinishedAssignment->editor )
                                                {{ $unfinishedAssignment->editor->full_name }}
											@else
												<em>Not set</em>
											@endif
                                        </td>
                                        <td>
                                            {{ $unfinishedAssignment->assignment_type }}
                                        </td>
                                        <td>
                                            {{ $unfinishedAssignment->where_in_script }}
                                        </td>
                                        <td>
                                            {{ $unfinishedAssignment->words }}
                                        </td>
                                        <td>
                                            <button class="btn btn-success btn-xs finishAssignmentManuscriptBtn" data-toggle="modal" 
                                            data-target="#finishAssignmentManuscriptModal" 
                                            data-action="{{ route('admin.assignment-manuscript.mark-finished', $unfinishedAssignment->id) }}">
                                                Mark as finished
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <!-- shop manu -->
            <div class="panel">
                <div class="panel-body">
                    <h3>{{ trans_choice('site.shop-manuscripts', 1) }}</h3>

                    <div class="table-users table-responsive margin-top">
                        <table class="table dt-table">
                            <thead>
                                <tr>
                                    <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                                    <th>{{ trans_choice('site.learners', 1) }}</th>
                                    <th>{{ trans_choice('site.editors', 1) }}</th>
                                    <th>{{ trans('site.genre') }}</th>
                                    <th>{{ trans_choice('site.words', 2) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unfinishedShopManuscripts as $unfinishedShopManuscript)
                                    <tr>
                                        <td>
                                            <a href="{{ route('shop_manuscript_taken',
                                            ['id' => $unfinishedShopManuscript->user->id,
                                            'shop_manuscript_taken_id' => $unfinishedShopManuscript->id]) }}">
                                                {{$unfinishedShopManuscript->shop_manuscript->title}}
                                            </a>
                                        </td>
                                        <td>
                                        <a href="{{ route('admin.learner.show', $unfinishedShopManuscript->user->id) }}">
											{{ $unfinishedShopManuscript->user->full_name }}
										</a>
                                        </td>
                                        <td>
                                            @if( $unfinishedShopManuscript->admin )
                                                {{ $unfinishedShopManuscript->admin->full_name }}
											@else
												<em>Not set</em>
											@endif
                                        </td>
                                        <td>
                                            @if($unfinishedShopManuscript->genre > 0)
												{{ \App\Http\FrontendHelpers::assignmentType($unfinishedShopManuscript->genre) }}
											@endif
                                        </td>
                                        <td>
                                            {{ $unfinishedShopManuscript->words }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif(Request::input('tab') == 'editors-note')
            <div class="panel" style="padding: 30px 50px;" id="editors-note-panel">
                <div class="panel-heading">
                    <button type="button" class="btn btn-primary btn-xs pull-right" 
                    data-toggle="modal" data-target="#editEditorsNoteModal">
                        <i class="fa fa-pencil"></i>
                    </button>
                </div>
                <div class="panel-body">
                    {!! App\Settings::editorsNote() !!}
                </div>
            </div>
        @endif
    </div>
</div>

<div id="showModal" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		  <div class="modal-body">
            <table class="table margin-top">
                    <thead>
                        <tr>
                            <th style="min-width: 84px;">{{ trans('site.start-date') }}</th>
                            <th style="min-width: 84px;">{{ trans('site.end-date') }}</th>
                            <th>{{ trans('site.how-many-manuscript') }}</th>
                            <th>{{ trans('site.how-many-hours') }}</th>
                            <th>{{ trans_choice('site.notes', 1) }}</th>
                        </tr>
                    </thead>
                    <tbody class="content">
                    </tbody>
                </table>
		  </div>
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
                <form id="hideEditorForm" method="POST" action=""  enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>{{ trans('site.start-date') }}</label>
                        <input required type="date" class="form-control" step="0.01" name="start_date">
                    </div>
                    <input class="form-check-input" type="checkbox" value="" id="hideUntilTurnedBackUnhidden" name="hideUntilTurnedBackUnhidden">
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

<div id="editHowManyYouCanTake" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		    <div class="modal-body">
                <form method="POST" action=""  enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>{{ trans('site.how-many-you-can-take') }}</label>
                        <input required type="number" class="form-control" name="howManyManuscriptYouCanTake">
                    </div>
                    <button type="submit" class="btn btn-warning pull-right margin-top">{{ trans('site.save') }}</button>
                    <div class="clearfix"></div>
                </form>
		  </div>
		</div>
	</div>
</div>

<div id="editEditorsNoteModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
                    Redaktørinnstruks
                </h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.settings.update.editors-note') }}" 
                onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<textarea class="form-control tinymce" name="editors_note">{{ App\Settings::editorsNote() }}</textarea>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="finishAssignmentManuscriptModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.finish-assignment') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					{{ trans('site.finish-assignment-question') }}
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
    <script>

    $('.showBtn').click(function(){
		var data = $(this).data('data');
        var count = $(this).data('count');
		var modal = $('#showModal');
        modal.find('.content').html('');
        for(var i=0; i < count; i++){
            modal.find('.content').append('<tr><td>'+data[i]['date_from']+'</td><td>'+data[i]['date_to']+'</td><td>'+data[i]['how_many_script']+'</td><td>'+data[i]['how_many_hours']+'</td><td>'+data[i]['note']+'</td></tr>');
        }
	});

    $('#hideUntilTurnedBackUnhidden').click(function(){
        if($(this).is(':checked')){
            $('.hide-end-date').css('display','none');
        }else{
            $('.hide-end-date').css('display','block');
        }
    });

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

    $('.editHowManyYouCanTake').click(function(){
        let action = $(this).data('action');
        let modal = $('#editHowManyYouCanTake');
        let hMMYCT = $(this).data('how_many_manuscript');

        modal.find('form').attr('action', action);
        modal.find('[name=howManyManuscriptYouCanTake]').val(hMMYCT);
    })

    // use this for datatable click button to work not only on first page
    $(document).on("click", ".finishAssignmentManuscriptBtn", function () {
        let modal = $('#finishAssignmentManuscriptModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
    });

    </script>
@stop

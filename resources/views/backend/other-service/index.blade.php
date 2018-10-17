@extends('backend.layout')

@section('title')
    <title>Other Services &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> {{ trans('site.other-services') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="margin-top">
        <ul class="nav nav-tabs margin-top">
            <li @if( Request::input('tab') == 'coaching-timer' || Request::input('tab') == '') class="active" @endif><a href="?tab=coaching-timer">{{ trans('site.coaching-timer') }}</a></li>
            <li @if( Request::input('tab') == 'correction' ) class="active" @endif><a href="?tab=correction">{{ trans('site.correction') }}</a></li>
            <li @if( Request::input('tab') == 'copy-editing' ) class="active" @endif><a href="?tab=copy-editing">{{ trans('site.copy-editing') }}</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade in active">
                @if( Request::input('tab') == 'correction' )
                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <div class="table-users table-responsive">
                                <table class="table no-margin-bottom">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.manus', 2) }}</th>
                                        <th>{{ trans_choice('site.learners', 1) }}</th>
                                        <th>{{ trans_choice('site.editors', 1) }}</th>
                                        <th>{{ trans('site.date-ordered') }}</th>
                                        <th>{{ trans('site.expected-finish') }}</th>
                                        <th>{{ trans('site.status') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($corrections as $correction)
                                        <?php $extension = explode('.', basename($correction->file)); ?>
                                        <tr>
                                            <td>
                                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                    <a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
                                                @elseif( end($extension) == 'docx' )
                                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.learner.show', $correction->user->id) }}">
                                                    {{ $correction->user->full_name }}
                                                </a>
                                            </td>
                                            <td>
                                                @if ($correction->editor_id)
                                                    {{ $correction->editor->full_name }}
                                                @else
                                                    <button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $correction->id, 'type' => 2]) }}">{{ trans('site.assign-editor') }}</button>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
                                            </td>
                                            <td>
                                                @if ($correction->expected_finish)
                                                    {{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
                                                    <br>
                                                @endif

                                                @if ($correction->status !== 2)
                                                    <a href="#setOtherServiceFinishDateModal" data-toggle="modal"
                                                       class="setOtherServiceFinishDateBtn"
                                                       data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $correction->id, 'type' => 2]) }}"
                                                       data-finish="{{ $correction->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($correction->expected_finish)) : '' }}">
                                                        {{ trans('site.set-date') }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if( $correction->status == 2 )
                                                    <span class="label label-success">Finished</span>
                                                @elseif( $correction->status == 1 )
                                                    <span class="label label-primary">Started</span>
                                                @elseif( $correction->status == 0 )
                                                    <span class="label label-warning">Not started</span>
                                                @endif
                                            </td>
                                            <td>
                                                <?php
                                                $btnColor = $correction->status == 1 ? 'primary' : 'warning';
                                                ?>

                                                @if ($correction->status !== 2)
                                                    <button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
                                                            data-toggle="modal" data-target="#updateOtherServiceStatusModal"
                                                            data-service="2"
                                                            data-action="{{ route('admin.other-service.update-status', ['id' => $correction->id, 'type' => 2]) }}"><i class="fa fa-check"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pull-right margin-top">
                            {{ $corrections->render() }}
                        </div>
                    </div>
                @elseif( Request::input('tab') == 'copy-editing' )
                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <div class="table-users table-responsive">
                                <table class="table no-margin-bottom">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.manus', 2) }}</th>
                                        <th>{{ trans_choice('site.learners', 1) }}</th>
                                        <th>{{ trans_choice('site.editors', 1) }}</th>
                                        <th>{{ trans('site.date-ordered') }}</th>
                                        <th>{{ trans('site.expected-finish') }}</th>
                                        <th>{{ trans('site.status') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($copyEditing as $editing)
                                            <?php $extension = explode('.', basename($editing->file)); ?>
                                            <tr>
                                                <td>
                                                    @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                        <a href="/js/ViewerJS/#../../{{ $editing->file }}">{{ basename($editing->file) }}</a>
                                                    @elseif( end($extension) == 'docx' )
                                                        <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$editing->file}}">{{ basename($editing->file) }}</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.learner.show', $editing->user->id) }}">
                                                        {{ $editing->user->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($editing->editor_id)
                                                        {{ $editing->editor->full_name }}
                                                    @else
                                                        <button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $editing->id, 'type' => 1]) }}">{{ trans('site.assign-editor') }}</button>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ \App\Http\FrontendHelpers::formatDate($editing->created_at) }}
                                                </td>
                                                <td>
                                                    @if ($editing->expected_finish)
                                                        {{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($editing->expected_finish) }}
                                                        <br>
                                                    @endif

                                                    @if ($editing->status !== 2)
                                                        <a href="#setOtherServiceFinishDateModal" data-toggle="modal"
                                                           class="setOtherServiceFinishDateBtn"
                                                           data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $editing->id, 'type' => 1]) }}"
                                                           data-finish="{{ $editing->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($editing->expected_finish)) : '' }}">
                                                            {{ trans('site.set-date') }}
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if( $editing->status == 2 )
                                                        <span class="label label-success">Finished</span>
                                                    @elseif( $editing->status == 1 )
                                                        <span class="label label-primary">Started</span>
                                                    @elseif( $editing->status == 0 )
                                                        <span class="label label-warning">Not started</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <?php
                                                    $btnColor = $editing->status == 1 ? 'primary' : 'warning';
                                                    ?>

                                                    @if ($editing->status !== 2)
                                                        <button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
                                                                data-toggle="modal" data-target="#updateOtherServiceStatusModal"
                                                                data-service="1"
                                                                data-action="{{ route('admin.other-service.update-status', ['id' => $editing->id, 'type' => 1]) }}"><i class="fa fa-check"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pull-right margin-top">
                            {{ $copyEditing->render() }}
                        </div>
                    </div>
                @else
                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <div class="table-users table-responsive">
                                <table class="table no-margin-bottom">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.manus', 2) }}</th>
                                        <th>{{ trans_choice('site.learners', 1) }}</th>
                                        <th>{{ trans('site.length') }}</th>
                                        <th>{{ trans('site.learner-suggestion') }}</th>
                                        <th>{{ trans('site.admin-suggestion') }}</th>
                                        <th>{{ trans('site.approved-date') }}</th>
                                        <th>{{ trans('site.assigned-to') }}</th>
                                        <th>{{ trans('site.replay') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($coachingTimers as $coachingTimer)
                                        <?php $extension = explode('.', basename($coachingTimer->file)); ?>
                                        <tr>
                                            <td>
                                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                    <a href="/js/ViewerJS/#../../{{ $coachingTimer->file }}">{{ basename($coachingTimer->file) }}</a>
                                                @elseif( end($extension) == 'docx' )
                                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$coachingTimer->file}}">{{ basename($coachingTimer->file) }}</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.learner.show', $coachingTimer->user->id) }}">
                                                    {{ $coachingTimer->user->full_name }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
                                            </td>
                                            <td>
                                                <?php
                                                    $suggested_dates = json_decode($coachingTimer->suggested_date);
                                                ?>
                                                @if($suggested_dates)
                                                    @for($i =0; $i <= 2; $i++)
                                                        <div style="margin-top: 5px">
                                                        {{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($suggested_dates[$i]) }}
                                                            @if (!$coachingTimer->approved_date && !$coachingTimer->is_suggested_by_admin)
                                                                <button class="btn btn-success btn-xs approveDateBtn"
                                                                data-toggle="modal" data-target="#approveDateModal"
                                                                        data-date="{{ $suggested_dates[$i] }}"
                                                                data-action="{{ route('admin.other-service.coaching-timer.approve_date', $coachingTimer->id) }}">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endfor
                                                @endif
                                            </td>
                                            <td>
                                                <?php
                                                $suggested_dates_admin = json_decode($coachingTimer->suggested_date_admin);
                                                ?>
                                                @if($suggested_dates_admin)
                                                    @for($i =0; $i <= 2; $i++)
                                                        <div style="margin-top: 5px">
                                                            {{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($suggested_dates_admin[$i]) }}
                                                        </div>
                                                    @endfor
                                                @endif
                                                @if (!$coachingTimer->approved_date)
                                                    <a href="#suggestDateModal" data-toggle="modal"
                                                       class="suggestDateBtn"
                                                       data-action="{{ route('admin.other-service.coaching-timer.suggestDate', $coachingTimer->id) }}">{{ trans('site.suggest-different-dates') }}</a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $coachingTimer->approved_date ?
                                                \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                                 : ''}}
                                            </td>
                                            <td>
                                                @if ($coachingTimer->editor_id)
                                                    {{ $coachingTimer->editor->full_name }}
                                                @else
                                                    <button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $coachingTimer->id, 'type' => 3]) }}">{{ trans('site.assign-editor') }}</button>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($coachingTimer->replay_link)
                                                    <a href="{{ $coachingTimer->replay_link }}" target="_blank">
                                                        {{ trans('site.view-replay') }}
                                                    </a> <br>
                                                @endif
                                                <button class="btn btn-xs btn-primary setReplayBtn" data-toggle="modal"
                                                        data-target="#setReplayModal" data-action="{{ route('admin.other-service.coaching-timer.set_replay', $coachingTimer->id) }}">{{ trans('site.set-replay') }}</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pull-right margin-top">
                            {{ $coachingTimers->render() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approve Coaching Timer Date Modal -->
    <div id="approveDateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.approve-date') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{csrf_field()}}
                        {{ trans('site.approve-date-question') }}
                        <input type="hidden" name="approved_date">
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-success">{{ trans('site.approve') }}</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <!-- Suggest Date Modal -->
    <div id="suggestDateModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.suggest-session-dates') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="suggestDateForm"
                          onsubmit="disableSubmit(this)">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label>{{ trans('site.date') }}</label>
                            <input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.date') }}</label>
                            <input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.date') }}</label>
                            <input type="datetime-local" class="form-control" name="suggested_date_admin[]" required>
                        </div>

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <div id="assignEditorModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.assign-editor') }}</label>
                            <select name="editor_id" class="form-control select2" required>
                                <option value="" disabled="" selected>-- Select Editor --</option>
                                @foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
                                    <option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="setReplayModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.set-replay') }}</label>
                            <input type="url" name="replay_link" class="form-control" required>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="updateOtherServiceStatusModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{!! str_replace('_SERVICE_','<span></span>',trans('site.update-service-status')) !!}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <p>
                            {{ trans('site.update-service-status-question') }}
                        </p>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="setOtherServiceFinishDateModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span></span> {{ trans('site.expected-finish') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.expected-finish-date') }}</label>
                            <input type="datetime-local" name="expected_finish" class="form-control" required>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".approveDateBtn").click(function(){
            let action = $(this).data('action');
            let approved_date = $(this).data('date');
            let form = $("#approveDateModal").find('form');

            form.attr('action', action);
            form.find('[name=approved_date]').val(approved_date);
        });

        $(".suggestDateBtn").click(function(){
            let action = $(this).data('action');
            let form = $("#suggestDateModal").find('form');

            form.attr('action', action);
        });

        $('.assignEditorBtn').click(function(){
            let action = $(this).data('action');
            let editor = $(this).data('editor');
            let modal = $('#assignEditorModal');
            modal.find('select').val(editor);
            modal.find('form').attr('action', action);
        });

        $(".setReplayBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#setReplayModal');
            modal.find('form').attr('action', action);
        });

        $(".updateOtherServiceStatusBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#updateOtherServiceStatusModal');
            let service = $(this).data('service');
            let title = 'Korrektur';

            if (service === 1) {
                title = 'Språkvask';
            }
            modal.find('form').attr('action', action);
            modal.find('.modal-title').find('span').text(title);
        });

        $(".setOtherServiceFinishDateBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#setOtherServiceFinishDateModal');
            let finish = $(this).data('finish');

            modal.find('form').attr('action', action);
            modal.find('form').find('[name=expected_finish]').val(finish);
        });

        function disableSubmit(t) {
            let submit_btn = $(t).find('[type=submit]');
            submit_btn.text('');
            submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            submit_btn.attr('disabled', 'disabled');
        }
    </script>
@stop
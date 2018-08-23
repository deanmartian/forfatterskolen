@extends('backend.layout')

@section('title')
    <title>Other Services &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Other Services</h3>
        <div class="clearfix"></div>
    </div>

    <div class="margin-top">
        <ul class="nav nav-tabs margin-top">
            <li @if( Request::input('tab') == 'coaching-timer' || Request::input('tab') == '') class="active" @endif><a href="?tab=coaching-timer">Coaching Timer</a></li>
            <li @if( Request::input('tab') == 'correction' ) class="active" @endif><a href="?tab=correction">Correction</a></li>
            <li @if( Request::input('tab') == 'copy-editing' ) class="active" @endif><a href="?tab=copy-editing">Copy Editing</a></li>
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
                                        <th>Manus</th>
                                        <th>Learner</th>
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
                                        <th>Manus</th>
                                        <th>Learner</th>
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
                                        <th>Manus</th>
                                        <th>Learner</th>
                                        <th>Suggested Date</th>
                                        <th>Approved Date</th>
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
                                                        @if (!$coachingTimer->approved_date && !$coachingTimer->is_suggested_by_admin)
                                                            <a href="#suggestDateModal" data-toggle="modal"
                                                               class="suggestDateBtn"
                                                            data-action="{{ route('admin.other-service.coaching-timer.suggestDate', $coachingTimer->id) }}">Suggest Different Dates</a>
                                                        @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $coachingTimer->approved_date ?
                                                \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                                 : ''}}
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
                    <h4 class="modal-title">Approve Date</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{csrf_field()}}
                        Are you sure you want to approve this date?
                        <input type="hidden" name="approved_date">
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-success">Approve</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <!-- Suggest Date Modal -->
    <div id="suggestDateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Suggest Session Dates</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label>Date</label>
                            <input type="datetime-local" class="form-control" name="suggested_date[]" required>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="datetime-local" class="form-control" name="suggested_date[]" required>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="datetime-local" class="form-control" name="suggested_date[]" required>
                        </div>

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
    </script>
@stop
<form method="POST" action="{{Request::is('zoom/webinar/*/edit')
? route('admin.zoom.webinar.update',$webinar['id'])
: route('admin.zoom.webinar.store',$user_id)}}">

    @if(Request::is('zoom/webinar/*/edit'))
        {{ method_field('PUT') }}
    @endif
    {{csrf_field()}}

        <div class="col-sm-12">
            @if(Request::is('zoom/webinar/*/edit'))
                <h3>Edit <em>{{$webinar['topic']}}</em></h3>
            @else
                <h3>Add Webinar</h3>
            @endif
        </div>

        <div class="col-sm-12 col-md-8">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label>Topic</label>
                        <input type="text" class="form-control" name="topic"
                               value="{{ $webinar['topic'] }}" required>
                    </div>
                    <div class="form-group">
                        <?php
                            $start_time = $webinar['start_time'];
                            if ($start_time){
                                $start_time = strftime('%Y-%m-%dT%H:%M:%S', strtotime(
                                    \App\Http\AdminHelpers::convertTZtoDateTime($start_time,$webinar['timezone'])));
                            }

                        ?>
                        <label>Start Time</label>
                        <input type="datetime-local" class="form-control" name="start_time"
                               value="{{ $start_time }}" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="agenda" id="" cols="30" rows="10" class="form-control">{{ $webinar['agenda'] }}</textarea>
                    </div>

                    @if(Request::is('zoom/webinar/*/edit'))
                        <div class="form-group">
                            <label>Invite Panelists</label>
                            <a href="#" class="pull-right" data-toggle="modal" data-target="#panelistsModal">Add Panelist</a>

                            <ul class="no-bullet" id="panelist-list">
                                @foreach($panelists as $panelist)
                                    <li>
                                        {{ $panelist->name.' ('.$panelist->email.')' }}
                                        <a href="#" class="pull-right deletePanelistBtn" data-toggle="modal"
                                           data-target="#deletePanelistModal"
                                        data-panelist="{{ $panelist->name }}"
                                        data-action="{{ route('admin.zoom.webinar.panelist.delete',
                                        ['webinar_id' => $webinar['id'], 'panelist_id' => $panelist->id]) }}">Delete</a>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                        <hr>

                        <div class="form-group">
                            <label>Manage Attendees</label>
                            @if ($webinar['settings']->approval_type != 2)
                                <a href="#" class="pull-right" data-target="#registrantsModal" data-toggle="modal">View</a>
                            @endif

                            <ul class="no-bullet">
                                <li>Registrants: {{ $total_records }}</li>
                                <li>
                                    @if ($webinar['settings']->approval_type == 1)
                                        Approved: {{ $approvedRegistrants->total_records }}
                                    @else
                                        {{ \App\Http\AdminHelpers::zoomWebinarApprovalType($webinar['settings']->approval_type) }}
                                    @endif
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>Settings</h4>
                    <div class="form-group" style="margin-top: 10px">
                        <label>
                            <input type="checkbox" name="host_video"
                                   @if (isset($webinar['settings']) && $webinar['settings']->host_video) checked @endif>
                            Start video when host join webinar
                        </label>
                        <label>
                            <input type="checkbox" name="panelists_video"
                                   @if (isset($webinar['settings']) && $webinar['settings']->panelists_video) checked @endif>
                            Start video when panelists join webinar
                        </label>
                        <label>
                            <input type="checkbox" name="hd_video"
                                   @if (isset($webinar['settings']) && $webinar['settings']->hd_video) checked @endif>
                            Default to HD Video
                        </label>
                        <label>
                            <input type="checkbox" name="show_share_button"
                                   @if (isset($webinar['settings']) && $webinar['settings']->show_share_button) checked @endif>
                            Show social share buttons on registration page.
                        </label>
                        <label>
                            <input type="checkbox" name="allow_multiple_devices"
                                   @if (isset($webinar['settings']) && $webinar['settings']->allow_multiple_devices) checked @endif>
                            Allow attendees to join from multiple devices.
                        </label>
                        <label>
                            <input type="checkbox" name="close_registration"
                            @if (isset($webinar['settings']) && $webinar['settings']->close_registration) checked @endif>
                            Close registration after event date.
                        </label>
                    </div>

                    <?php
                        $selectedApproval = isset($webinar['settings']) && is_numeric($webinar['settings']->approval_type) ?
                            $webinar['settings']->approval_type : 2;
                        $selectedAudio = isset($webinar['settings']) && $webinar['settings']->audio ?
                            $webinar['settings']->audio :'both';
                    ?>

                    <div class="form-group">
                        <label> Approval Type </label>
                        <select name="approval_type" class="form-control">
                            @foreach(\App\Http\AdminHelpers::zoomWebinarApprovalType() as $approvalType)
                                <option value="{{ $approvalType['id'] }}"
                                @if($selectedApproval == $approvalType['id']) selected @endif>{{ $approvalType['option'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label> Meeting audio options </label>
                        <select name="audio" class="form-control">
                            @foreach(\App\Http\AdminHelpers::zoomWebinarAudioOptions() as $audioOption)
                                <option value="{{ $audioOption['id'] }}"
                                        @if($selectedAudio == $audioOption['id']) selected @endif>{{ $audioOption['option'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(Request::is('zoom/webinar/*/edit'))
                        <button type="submit" class="btn btn-primary">Update Webinar</button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteWebinarModal">Delete Webinar</button>
                    @else
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Create Webinar</button>
                    @endif
                </div>
            </div>
        </div>

</form>

@if(Request::is('zoom/webinar/*/edit'))
    @include('backend.zoom.webinars.partials.delete')
@endif

<div id="panelistsModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Panelist</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.zoom.webinar.panelist.store', $webinar['id']) }}">
                    {{csrf_field()}}

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <button type="submit" class="btn btn-success pull-right">Add Panelist</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

    </div>
</div>

<div id="deletePanelistModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete <em></em></h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    {{csrf_field()}}
                    {{ method_field('DELETE') }}
                    <p>
                        Are you sure to delete this panelist?
                    </p>
                    <button type="submit" class="btn btn-danger pull-right">Delete Panelist</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

    </div>
</div>

<div id="registrantsModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Registrants for '{{ $webinar['topic'] }}'</h4>
            </div>
            <div class="modal-body">
                <div class="attendeesDialogContent">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#pendingApprovalTab" aria-controls="pendingApprovalTab" role="tab" data-toggle="tab">
                                    Pending Approval ({{ $pendingRegistrants->total_records }})
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#approvedTab" aria-controls="approvedTab" role="tab" data-toggle="tab">
                                    Approved ({{ $approvedRegistrants->total_records }})
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#deniedTab" aria-controls="deniedTab" role="tab" data-toggle="tab">
                                    Denied/Blocked ({{ $deniedRegistrants->total_records }})
                                </a>
                            </li>
                        </ul> <!-- end of tablist -->

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="pendingApprovalTab">
                                <table class="table participants-list-table">
                                    <thead>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkall" aria-label="Select All">
                                        </td>
                                        <th scope="col" style="border-bottom: 0px;">
                                            Registrants
                                        </th>
                                        <th scope="col" style="width:30%;word-break: break-all;border-bottom: 0px;">
                                            Email Address
                                        </th>
                                        <th scope="col" style="border-bottom: 0px;">
                                            Registration Date
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingRegistrants->registrants as $pendingRegistrant)
                                            <tr>
                                                <td scope="row">
                                                    <input type="checkbox" class="selectbox" data-email="{{ $pendingRegistrant->email }}">
                                                    <input type="hidden" name="participant_id" value="{{ $pendingRegistrant->id }}">
                                                </td>
                                                <td>
                                                    <a href="javascript:;" class="showdetail">
                                                        {{ $pendingRegistrant->first_name.' '.$pendingRegistrant->last_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $pendingRegistrant->email }}
                                                </td>
                                                <td>
                                                    <?php
                                                        // can't display the time to be similar on zoom API
                                                        // the time is displayed to the set timezone accordingly
                                                        echo date('Y-m-d H:i A', strtotime($pendingRegistrant->create_time));
                                                    ?>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                            </div> <!-- end of pendingApprovalTab tab -->

                            <div role="tabpanel" class="tab-pane" id="approvedTab">
                                <table class="table participants-list-table">
                                    <thead>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkall" aria-label="Select All">
                                        </td>
                                        <th scope="col" style="border-bottom: 0px;">
                                            Registrants
                                        </th>
                                        <th scope="col" style="width:30%;word-break: break-all;border-bottom: 0px;">
                                            Email Address
                                        </th>
                                        <th scope="col" style="border-bottom: 0px;">
                                            Registration Date
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($approvedRegistrants->registrants as $approvedRegistrant)
                                        <tr>
                                            <td scope="row">
                                                <input type="checkbox" class="selectbox" data-email="{{ $approvedRegistrant->email }}">
                                                <input type="hidden" name="participant_id" value="{{ $approvedRegistrant->id }}">
                                            </td>
                                            <td>
                                                <a href="javascript:;" class="showdetail">
                                                    {{ $approvedRegistrant->first_name.' '.$approvedRegistrant->last_name }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $approvedRegistrant->email }}
                                            </td>
                                            <td>
                                                <?php
                                                // can't display the time to be similar on zoom API
                                                // the time is displayed to the set timezone accordingly
                                                echo date('Y-m-d H:i A', strtotime($approvedRegistrant->create_time));
                                                ?>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end of approvedTab tab -->

                            <div role="tabpanel" class="tab-pane" id="deniedTab">
                                <table class="table participants-list-table">
                                    <thead>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkall" aria-label="Select All">
                                        </td>
                                        <th scope="col" style="border-bottom: 0px;">
                                            Registrants
                                        </th>
                                        <th scope="col" style="width:30%;word-break: break-all;border-bottom: 0px;">
                                            Email Address
                                        </th>
                                        <th scope="col" style="border-bottom: 0px;">
                                            Registration Date
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($deniedRegistrants->registrants as $deniedRegistrant)
                                        <tr>
                                            <td scope="row">
                                                <input type="checkbox" class="selectbox" data-email="{{ $deniedRegistrant->email }}">
                                                <input type="hidden" name="participant_id" value="{{ $deniedRegistrant->id }}">
                                            </td>
                                            <td>
                                                <a href="javascript:;" class="showdetail">
                                                    {{ $deniedRegistrant->first_name.' '.$deniedRegistrant->last_name }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $deniedRegistrant->email }}
                                            </td>
                                            <td>
                                                <?php
                                                // can't display the time to be similar on zoom API
                                                // the time is displayed to the set timezone accordingly
                                                echo date('Y-m-d H:i A', strtotime($deniedRegistrant->create_time));
                                                ?>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end of deniedTab tab -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@section('scripts')
    <script>
        $(".deletePanelistBtn").click(function(){
            var modal = $("#deletePanelistModal"),
                action = $(this).data('action'),
                panelist = $(this).data('panelist');

            modal.find('form').attr('action', action);
            modal.find('.modal-title').find('em').text(panelist);
        });
    </script>
@stop
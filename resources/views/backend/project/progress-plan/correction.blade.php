@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default mr-2">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-file-text-o"></i> {{ $stepTitle }}</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="table-responsive">
            <div class="table-users table-responsive">
                <div class="table-users table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ trans_choice('site.manus', 2) }}</th>
                            <th>{{ trans_choice('site.editors', 1) }}</th>
                            <th>{{ trans('site.date-ordered') }}</th>
                            <th>{{ trans('site.expected-finish') }}</th>
                            <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                            <th>{{ trans('site.status') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($project->corrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
                                <tr>
                                    <td>
                                        @if (strpos($correction->file, 'project-'))
                                            <a href="{{ url('/dropbox/download/' . trim($correction->file)) }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>&nbsp;
                                            <a href="{{ url('/dropbox/shared-link/' . trim($correction->file)) }}" target="_blank">
                                                {{ basename($correction->file) }}
                                            </a>
                                        @else
                                            @if ($correction->file)
                                                <a href="{{ route($downloadOtherService, ['id' => $correction->id, 'type' => 2]) }}" download>
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                    <a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
                                                @elseif( end($extension) == 'docx' )
                                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if ($correction->editor_id)
                                            {{ $correction->editor->full_name }} <br>

                                            <button class="btn btn-xs btn-primary assignEditorBtn" data-toggle="modal"
                                                    data-target="#assignEditorModal"
                                                    data-editor="{{ json_encode($correction->editor) }}"
                                                    data-action="{{ route($assignEditorRoute, ['id' => $correction->id, 'type' => 2]) }}">
                                                {{ trans('site.assign-editor') }}
                                            </button>
                                        @else
                                            <button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal"
                                                    data-target="#assignEditorModal"
                                                    data-action="{{ route($assignEditorRoute, ['id' => $correction->id, 'type' => 2]) }}">
                                                Assign Editor
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
                                    </td>
                                    <td>
                                        @if ($correction->expected_finish)
                                            {{ $correction->expected_finish_formatted }}
                                            <br>
                                        @endif

                                        @if ($correction->status !== 2)
                                            <a href="#setOtherServiceFinishDateModal" data-toggle="modal"
                                            class="setOtherServiceFinishDateBtn"
                                            data-action="{{ route($updateExpectedFinishRoute,
                                            ['id' => $correction->id, 'type' => 2]) }}"
                                            data-finish="{{ $correction->expected_finish ?
                                            strftime('%Y-%m-%d', strtotime($correction->expected_finish)) : '' }}">
                                                Set Date
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- show only if no feedback is given yet for this copyEditing -->
                                        @if (!$correction->feedback)
                                            <a href="#addOtherServiceFeedbackModal" data-toggle="modal" style="color:#dc3545"
                                            class="addOtherServiceFeedbackBtn" data-service="2"
                                            data-action="{{ route($otherServiceFeedbackRoute,
                                                        ['id' => $correction->id, 'type' => 2]) }}"
                                            data-email-template="{{ json_encode($correctionFeedbackTemplate) }}">+ {{ trans('site.add-feedback') }}</a>
                                        @else
                                        <?php //$files = explode(',',$correction->feedback->manuscript); ?>
                                            {{-- @foreach($files as $file)
                                                <a href="{{ route('dropbox.download_file', trim($file)) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a> &nbsp;
                                            @endforeach --}}
                                        <a href="{{ route($otherServiceDownloadFeedbackRoute, [$correction->feedback->id, 2]) }}"
                                            class="btn btn-success btn-xs">
                                                Download Feedback
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

                                            <input type="checkbox" data-toggle="toggle" data-on="Locked"
                                                class="lock-toggle" data-off="Unlocked"
                                                data-type="correction" onchange="lockToggle(this)"
                                                data-id="{{$correction->id}}" data-size="mini" @if($correction->is_locked)
                                                {{ 'checked' }}
                                                    @endif>

                                        @if ($correction->status !== 2)
                                            <button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
                                                    data-toggle="modal" data-target="#updateOtherServiceStatusModal"
                                                    data-service="2"
                                                    data-action="{{ route($updateStatusRoute, ['id' => $correction->id, 'type' => 2]) }}"><i class="fa fa-check"></i></button>
                                        @endif

                                            <button class="btn btn-danger btn-xs deleteOtherServiceBtn" type="button"
                                                    data-toggle="modal" data-target="#deleteOtherServiceModal"
                                                    data-action="{{ route($otherServiceDeleteRoute, ['id' => $correction->id, 'type' => 2]) }}">
                                                <i class="fa fa-trash"></i>
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

    <div id="assignEditorModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Assign editor</label>
                            <select name="editor_id" class="form-control select2" required>
                                <option value="" disabled="" selected>-- Select Editor --</option>
                                @foreach( AdminHelpers::editorList() as $editor )
                                    <option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Save</button>
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
                    <h4 class="modal-title"><span></span> Expected Finish</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Expected finish date</label>
                            <input type="date" name="expected_finish" class="form-control" required>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Submit</button>
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
                    <h4 class="modal-title"><span></span> Expected Finish</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Expected finish date</label>
                            <input type="date" name="expected_finish" class="form-control" required>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="addOtherServiceFeedbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><span></span> {{ trans('site.add-feedback') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" class="form-control" name="manuscript[]" multiple
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.subject') }}</label>
                            <input type="text" class="form-control" name="subject" value=""
                                   required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.from') }}</label>
                            <input type="text" class="form-control" name="from_email"
                                   value="" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.message') }}</label>
                            <textarea class="form-control tinymce" name="message" rows="6"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
                        <div class="clearfix"></div>
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
                    <h4 class="modal-title">Update <span></span> Status</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <p>
                            Are you sure to update the status of this record?
                        </p>
                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteOtherServiceModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        {{ trans('site.delete') }}
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action=""
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <p>
                            {{ trans('site.delete-item-question') }}
                        </p>
                        <button class="btn btn-danger pull-right" type="submit">
                            {{ trans('site.delete') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    $('.assignEditorBtn').click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let modal = $('#assignEditorModal');
        modal.find('select').val(editor);
        modal.find('form').attr('action', action);

        if (editor) {
            modal.find('form').find('select[name=editor_id]').val(editor.id).trigger('change');
        }
    });

    $(".setOtherServiceFinishDateBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#setOtherServiceFinishDateModal');
        let finish = $(this).data('finish');

        modal.find('form').attr('action', action);
        modal.find('form').find('[name=expected_finish]').val(finish);
    });

    $(".addOtherServiceFeedbackBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#addOtherServiceFeedbackModal');
        let service = $(this).data('service');
        let emailTemplate = $(this).data('email-template');
        let title = 'Korrektur';

        if (service === 1) {
            title = 'Språkvask';
        }
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);
        modal.find('.modal-body').find('[name=subject]').val(emailTemplate.subject);
        modal.find('.modal-body').find('[name=from_email]').val(emailTemplate.from_email);
        tinyMCE.activeEditor.setContent(emailTemplate.email_content);
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

    $(".deleteOtherServiceBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteOtherServiceModal');
        modal.find('form').attr('action', action);
    });

    function lockToggle(self) {
        let id = $(self).attr('data-id');
        let type = $(self).attr('data-type');
        let is_checked = $(self).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/other-service/' + id + '/lock-status/' + type,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { 'is_locked' : check_val },
            success: function(data){
                console.log(data);
            }
        });
    }
</script>
@endsection
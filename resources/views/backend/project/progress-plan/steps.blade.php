@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-file-text-o"></i> Progress Plans</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="table-responsive">
            <div class="table-users table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Step</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Expected Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $steps as $step )
                            <tr>
                                <td>
                                    {{ $step['step_number'] }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.project.progress-plan-step',[$project->id, $step['step_number']]) }}">
                                        {{ $step['title'] }}
                                    </a>
                                </td>
                                <td>
                                    {{ $step['status_text'] }}
                                </td>
                                <td>
                                    {{ $step['expected_date'] }}
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-primary progressPlanBtn" data-toggle="modal" 
                                    data-target="#progressPlanModal" data-title="Update {{ $step['title'] }}"
                                    data-record="{{ json_encode($step) }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="progressPlanModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.project.progress-plan.save') }}"
                          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                          @csrf
                          <input type="hidden" name="step_number">
                          <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="not_planned">Not Planned</option>
                                <option value="not_started">Not Started</option>
                                <option value="started">Started</option>
                                <option value="finished">Finished</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Expected Finish</label>
                            <input type="date" class="form-control" name="expected_date">
                        </div>

                        <button type="submit" class="btn btn-success pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>
    
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(".progressPlanBtn").click(function() {
            const modal = $("#progressPlanModal");
            const title = $(this).data('title');
            const action = $(this).data('action');
            const record = $(this).data('record');

            modal.find('.modal-title').text(title);
            modal.find('form').attr('action', action);
            modal.find('[name=step_number]').val(record.step_number);
            modal.find('[name=status]').val(record.status);
            modal.find('[name=expected_date]').val(record.expected_date);
        });
    </script>
@endsection
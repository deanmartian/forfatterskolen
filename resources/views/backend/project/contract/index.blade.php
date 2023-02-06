@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-handshake-o"></i> Contract</h3>
        <a href="{{ $backRoute }}" class="btn btn-default" style="margin-right: 10px">
            << {{ trans('site.back') }}
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="panel panel-default" style="border-top: 0">
            <div class="panel-body">
                <a class="btn btn-success margin-bottom" href="{{ route($createContractRoute, $project->id) }}">
                    Create Contract
                </a>

                <button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#uploadContractModal">
                    Upload Contract
                </button>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Receiver Name</th>
                        <th>End Date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($contracts as $contract)
                        <tr>
                            <td>
                                <a href="{{ $contract->signature ? route($contractShowRoute, [$project->id, $contract->id]) :
                                            route($contractEditRoute, [$project->id, $contract->id]) }}">
                                    {{ $contract->title }}
                                </a>
                            </td>
                            <td>
                                {{ $contract->receiver_name }}
                            </td>
                            <td>{{ $contract->end_date }}</td>
                            <td>
                                @if ($contract->signature)
                                    <label class="label label-success">Signed</label>
                                @else
                                    <label class="label label-warning">Unsigned</label>
                                @endif

                                {{--@if (Auth::user()->isSuperUser())
                                    <input type="checkbox" data-toggle="toggle" data-on="Show" data-off="Hide" data-size="mini" name="status"
                                           class="status-toggle" data-id="{{ $contract->id }}"
                                           @if ($contract->status === 1) checked @endif>
                                @endif--}}

                                @if ($contract->signature)
                                        @if($contract->is_file)
                                            <a href="{{ $contract->signed_file }}" class="button btn btn-info btn-xs" download>Download PDF</a>
                                        @else
                                            <a href="{{ route('admin.contract.download-pdf', $contract->id) }}" class="button btn btn-info btn-xs">Download PDF</a>
                                        @endif
                                @else
                                    @if($contract->is_file)
                                        <button class="btn btn-primary btn-xs uploadSignedContractBtn" data-toggle="modal"
                                                data-target="#uploadSignedContractModal"
                                                data-action="{{ route($signedUploadRoute, [$contract->project_id, $contract->id]) }}">
                                            Upload Signed Contract
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="pull-right">
                    {{ $contracts->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="uploadContractModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Upload Contract
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($uploadContractRoute, $project->id) }}"
                          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Sent file</label>
                            <input type="file" class="form-control" name="sent_file" accept="application/pdf">
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

    <div id="uploadSignedContractModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Upload Signed Contract
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action=""
                          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Signed file</label>
                            <input type="file" class="form-control" name="signed_file" accept="application/pdf">
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
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $(".uploadSignedContractBtn").click(function() {
            let modal = $("#uploadSignedContractModal");
            let action = $(this).data('action');

            modal.find("form").attr('action', action);
        });
    </script>
@stop
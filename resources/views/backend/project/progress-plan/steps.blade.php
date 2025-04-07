@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Progress Plans</h3>
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
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
                                    {{ $step['status'] }}
                                </td>
                                <td>
                                    {{ $step['expected_date'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
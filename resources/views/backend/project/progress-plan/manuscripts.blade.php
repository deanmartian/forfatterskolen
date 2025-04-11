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
        <h3><i class="fa fa-file-text-o"></i> {{ $stepTitle }}</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="table-responsive">
            <div class="table-users table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <td>File</td>
                            <td>Upload Date</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $manuscripts as $manuscript )
                            <tr>
                                <td>
                                    {!! $manuscript->dropbox_file_link_with_download !!}</td>
                                <td>
                                    {{ FrontendHelpers::formatDate($manuscript->created_at) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
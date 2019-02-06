@extends('backend.layout')

@section('title')
    <title>CRON Logs &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> CRON Logs</h3>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Activity</th>
                        <th>Log Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Http\AdminHelpers::getCronLogs() as $log)
                        <tr>
                            <td>{{ $log['id'] }}</td>
                            <td>{{ $log['activity'] }}</td>
                            <td>{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($log['created_at']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{ \App\Http\AdminHelpers::getCronLogs()->render() }}
        </div>
    </div>
@stop
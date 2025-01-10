@extends('backend.layout')

@section('title')
<title>Queue Jobs &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file"></i> Queue Jobs</h3>

    <div class="pull-right">
        <h3>Queue Worker Status:</h3>
        @if ($isQueueRunning)
            <h3 class="text-success" style="font-weight: bold">
                Running
            </h3>
        @else
            <h3 class="text-danger" style="font-weight: bold">
                Stopped
            </h3>
        @endif
    </div>
	<div class="clearfix"></div>
</div>

<div class="col-md-12">
    <ul class="nav nav-tabs margin-top parent-nav">
        <li class="{{ Request::input('tab') == 'failed-jobs' || !Request::has('tab') ? 'active' : '' }}">
            <a href="?tab=failed-jobs">Failed Jobs</a>
        </li>
        <li class="{{ Request::input('tab') == 'jobs' ? 'active' : '' }}">
            <a href="?tab=jobs">Jobs</a>
        </li>
    </ul>

    <div class="tab-content">
		<div class="tab-pane fade in active">
            @if( Request::input('tab') != 'jobs' )
                <div class="table-users table-responsive">
                    @if(!$failedJobs->isEmpty())
                        <!-- Retry All Button -->
                        <form action="{{ route('admin.queue-jobs.failed-jobs.retry-all') }}" method="POST" class="margin-top"
                            onsubmit="disableSubmit(this)">
                            @csrf
                            <button type="submit" class="btn btn-success pull-right">Retry All</button>
                            <div class="clearfix"></div>
                        </form>
                    @endif
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Connection</th>
                                <th>Queue</th>
                                <th>Payload</th>
                                <th>Failed At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($failedJobs as $job)
                                <tr>
                                    <td>{{ $job->id }}</td>
                                    <td>{{ $job->connection }}</td>
                                    <td>{{ $job->queue }}</td>
                                    <td><pre>{{ $job->decoded_payload }}</pre></td>
                                    <td>{{ $job->failed_at }}</td>
                                    <td>
                                        <!-- Retry Button -->
                                        <form action="{{ route('admin.queue-jobs.failed-jobs.retry', $job->id) }}" method="POST" 
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">Retry</button>
                                        </form>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.queue-jobs.failed-jobs.destroy', $job->id) }}" method="POST" 
                                            style="display:inline;" 
                                            onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pull-right">{{$failedJobs->render()}}</div>
                </div>
            @else
                <div class="table-users table-responsive">
                    @if(!$jobs->isEmpty())
                        <form action="{{ route('admin.queue-jobs.jobs.run') }}" method="POST" class="margin-top"
                                onsubmit="disableSubmit(this)">
                            @csrf
                            <button type="submit" class="btn btn-success pull-right">Run Jobs</button>
                            <div class="clearfix"></div>
                        </form>
                    @endif
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Queue</th>
                                <th>Payload</th>
                                <th>Attempts</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ $job->queue }}</td>
                                <td>
                                    <pre>{{ $job->decoded_payload }}</pre>
                                </td>
                                <td>{{ $job->attempts }}</td>
                                <td>{{ $job->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="pull-right">{{ $jobs->appends(Request::all())->render() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('backend.layout')

@section('title')
<title>Helpwise Logg &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-envelope"></i> Helpwise Webhook Logg</h3>
    <div class="clearfix"></div>
</div>

<div class="col-md-12">
    <a class="btn btn-default margin-bottom" href="{{ route('admin.publishing.index') }}">
        <i class="fa fa-arrow-left"></i> Tilbake
    </a>

    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Siste hendelser</strong>
            <span class="badge">{{ $logs->total() }}</span>
        </div>
        <div class="panel-body">
            @if($logs->isEmpty())
                <p class="text-muted">Ingen webhook-hendelser registrert ennå.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Dato</th>
                                <th>Avsender</th>
                                <th>E-post</th>
                                <th>Type</th>
                                <th>Skal svare</th>
                                <th>Confidence</th>
                                <th>Utkast-status</th>
                                <th>Feil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ $log->sender_name ?? '-' }}</td>
                                    <td>{{ $log->sender_email ?? '-' }}</td>
                                    <td>{{ $log->event_type ?? '-' }}</td>
                                    <td>
                                        @if($log->should_reply)
                                            <span class="label label-success">Ja</span>
                                        @else
                                            <span class="label label-default">Nei</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->confidence !== null)
                                            <span class="label {{ $log->confidence >= 0.7 ? 'label-success' : ($log->confidence >= 0.4 ? 'label-warning' : 'label-danger') }}">
                                                {{ number_format($log->confidence * 100, 0) }}%
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($log->draft_status) {
                                                'created' => 'label-success',
                                                'skipped' => 'label-info',
                                                'failed' => 'label-danger',
                                                'saved_locally' => 'label-warning',
                                                'processing' => 'label-default',
                                                default => 'label-default',
                                            };
                                        @endphp
                                        <span class="label {{ $statusClass }}">{{ $log->draft_status }}</span>
                                    </td>
                                    <td title="{{ $log->error_message }}">
                                        {{ $log->error_message ? Str::limit($log->error_message, 40) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@stop

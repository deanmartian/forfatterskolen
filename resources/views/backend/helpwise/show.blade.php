@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-comments"></i> Samtale: {{ $conversation->subject ?? $conversation->helpwise_id }}</h3>
    <a href="{{ route('admin.helpwise.index') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    <div class="row">
        {{-- Conversation Info --}}
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Samtaleinformasjon</strong></div>
                <div class="panel-body">
                    <table class="table table-condensed" style="margin-bottom: 0;">
                        <tr><td><strong>Kunde</strong></td><td>{{ $conversation->customer_name ?? '-' }}</td></tr>
                        <tr><td><strong>E-post</strong></td><td>{{ $conversation->customer_email ?? '-' }}</td></tr>
                        <tr><td><strong>Inbox</strong></td><td><span class="label label-info">{{ $conversation->inbox ?? '-' }}</span></td></tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td><span class="label label-{{ $conversation->status === 'open' ? 'warning' : 'success' }}">{{ ucfirst($conversation->status) }}</span></td>
                        </tr>
                        <tr><td><strong>Tildelt</strong></td><td>{{ $conversation->assigned_to ?? '-' }}</td></tr>
                        <tr><td><strong>Tags</strong></td><td>
                            @if($conversation->tags)
                                @foreach($conversation->tags as $tag)
                                    <span class="label label-default">{{ $tag }}</span>
                                @endforeach
                            @else - @endif
                        </td></tr>
                        <tr><td><strong>Opprettet</strong></td><td>{{ $conversation->helpwise_created_at?->format('d.m.Y H:i') ?? $conversation->created_at->format('d.m.Y H:i') }}</td></tr>
                        @if($conversation->helpwise_closed_at)
                            <tr><td><strong>Lukket</strong></td><td>{{ $conversation->helpwise_closed_at->format('d.m.Y H:i') }}</td></tr>
                        @endif
                        <tr><td><strong>Helpwise ID</strong></td><td><code>{{ $conversation->helpwise_id }}</code></td></tr>
                    </table>

                    @if($conversation->user)
                        <hr>
                        <p><strong><i class="fa fa-user"></i> Koblet elev:</strong></p>
                        <a href="{{ route('admin.helpwise.student', $conversation->user_id) }}" class="btn btn-sm btn-info">
                            {{ $conversation->user->first_name }} {{ $conversation->user->last_name }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Messages Thread --}}
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-comments-o"></i> Meldinger ({{ $conversation->messages->count() }})</strong></div>
                <div class="panel-body" style="max-height: 600px; overflow-y: auto;">
                    @forelse($conversation->messages as $msg)
                        <div style="margin-bottom: 15px; padding: 12px; border-radius: 6px; background: {{ $msg->direction === 'outbound' ? '#e8f4fd' : '#f5f5f5' }}; border-left: 3px solid {{ $msg->direction === 'outbound' ? '#3498db' : '#95a5a6' }};">
                            <div style="margin-bottom: 5px;">
                                <strong>
                                    @if($msg->direction === 'outbound')
                                        <i class="fa fa-reply text-primary"></i>
                                    @else
                                        <i class="fa fa-envelope-o"></i>
                                    @endif
                                    {{ $msg->from_name ?? $msg->from_email ?? ($msg->direction === 'outbound' ? 'Agent' : 'Kunde') }}
                                </strong>
                                <span class="pull-right text-muted">
                                    <small>{{ $msg->message_at?->format('d.m.Y H:i') ?? $msg->created_at->format('d.m.Y H:i') }}</small>
                                    @if($msg->channel)
                                        <span class="label label-default" style="margin-left: 5px;">{{ $msg->channel }}</span>
                                    @endif
                                </span>
                            </div>
                            @if($msg->subject)
                                <p style="margin-bottom: 5px;"><em>{{ $msg->subject }}</em></p>
                            @endif
                            <div style="word-wrap: break-word;">
                                {!! nl2br(e($msg->body_plain ?? strip_tags($msg->body ?? ''))) !!}
                            </div>
                            @if($msg->attachments)
                                <div style="margin-top: 8px;">
                                    <small><i class="fa fa-paperclip"></i> {{ count($msg->attachments) }} vedlegg</small>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted text-center">Ingen meldinger registrert ennå.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-user"></i> Elevprofil - {{ $profile['user']['name'] }}</h3>
    <a href="{{ route('admin.helpwise.index') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    {{-- Student Info --}}
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading"><strong>Elevinformasjon</strong></div>
                <div class="panel-body">
                    <h4>{{ $profile['user']['name'] }}</h4>
                    <p><i class="fa fa-envelope"></i> {{ $profile['user']['email'] }}</p>
                    <p><i class="fa fa-key"></i> ID: {{ $profile['user']['id'] }}</p>
                    <hr>
                    <table class="table table-condensed" style="margin-bottom: 0;">
                        <tr><td>Totalt samtaler</td><td class="text-right"><strong>{{ $profile['summary']['total_conversations'] }}</strong></td></tr>
                        <tr><td>Åpne samtaler</td><td class="text-right"><strong>{{ $profile['summary']['open_conversations'] }}</strong></td></tr>
                        <tr><td>Totalt meldinger</td><td class="text-right"><strong>{{ $profile['summary']['total_messages'] }}</strong></td></tr>
                        <tr><td>Første kontakt</td><td class="text-right">{{ $profile['summary']['first_contact'] ? \Carbon\Carbon::parse($profile['summary']['first_contact'])->format('d.m.Y') : '-' }}</td></tr>
                        <tr><td>Siste kontakt</td><td class="text-right">{{ $profile['summary']['last_contact'] ? \Carbon\Carbon::parse($profile['summary']['last_contact'])->format('d.m.Y') : '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Per Inbox Breakdown --}}
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><strong><i class="fa fa-inbox"></i> Aktivitet per inbox</strong></div>
                <div class="panel-body">
                    @if(count($profile['by_inbox']) > 0)
                        <table class="table table-striped">
                            <thead><tr><th>Inbox</th><th>Samtaler</th><th>Meldinger</th><th>Åpne</th><th>Siste aktivitet</th></tr></thead>
                            <tbody>
                                @foreach($profile['by_inbox'] as $inbox)
                                    <tr>
                                        <td><span class="label label-info">{{ $inbox['inbox'] }}</span></td>
                                        <td>{{ $inbox['conversations'] }}</td>
                                        <td>{{ $inbox['messages'] }}</td>
                                        <td>{{ $inbox['open_conversations'] }}</td>
                                        <td>{{ $inbox['last_activity'] ? \Carbon\Carbon::parse($inbox['last_activity'])->format('d.m.Y H:i') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Ingen samtaler registrert for denne eleven.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- All Conversations --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong><i class="fa fa-comments"></i> Alle samtaler</strong></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead><tr><th>Emne</th><th>Inbox</th><th>Status</th><th>Tildelt</th><th>Meldinger</th><th>Opprettet</th><th></th></tr></thead>
                <tbody>
                    @foreach($profile['conversations'] as $conv)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($conv->subject, 50) ?? '-' }}</td>
                            <td><span class="label label-info">{{ $conv->inbox ?? '-' }}</span></td>
                            <td><span class="label label-{{ $conv->status === 'open' ? 'warning' : 'success' }}">{{ ucfirst($conv->status) }}</span></td>
                            <td>{{ $conv->assigned_to ?? '-' }}</td>
                            <td>{{ $conv->messages->count() }}</td>
                            <td>{{ $conv->helpwise_created_at?->format('d.m.Y') ?? $conv->created_at->format('d.m.Y') }}</td>
                            <td><a href="{{ route('admin.helpwise.show', $conv->id) }}" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

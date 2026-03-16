@extends('backend.layout')

@section('title')
<title>Meldinger &rsaquo; Forfatterskolen Adminportal</title>
@stop

@section('page-title', 'Meldinger')

@section('content')
<div class="ed-section">
    <div class="ed-section__header">
        <h2 class="ed-section__title">Innboks</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($conversations->isEmpty())
        <div class="panel panel-default">
            <div class="panel-body text-center" style="padding: 40px;">
                <i class="fa fa-envelope-o" style="font-size: 48px; color: var(--ink-soft); margin-bottom: 15px; display: block;"></i>
                <p style="color: var(--ink-soft); margin: 0;">Ingen meldinger ennå.</p>
            </div>
        </div>
    @else
        <div class="panel panel-default">
            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Emne</th>
                            <th>Deltakere</th>
                            <th>Siste melding</th>
                            <th style="width: 140px;">Dato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conversations as $conversation)
                            @php
                                $unread = $conversation->unreadCountFor(Auth::user());
                                $latest = $conversation->latestMessage->first();
                                $otherParticipants = $conversation->participants->where('id', '!=', Auth::id());
                            @endphp
                            <tr style="cursor: pointer;{{ $unread > 0 ? ' font-weight: 600; background: #fdf9f4;' : '' }}" onclick="window.location='{{ route('admin.messages.show', $conversation->id) }}'">
                                <td>
                                    {{ $conversation->subject }}
                                    @if($unread > 0)
                                        <span class="label label-danger" style="margin-left: 5px;">{{ $unread }}</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($otherParticipants as $p)
                                        <span class="label label-default">{{ $p->full_name }}</span>
                                    @endforeach
                                </td>
                                <td style="color: var(--ink-soft); font-size: 13px;">
                                    @if($latest)
                                        {{ \Illuminate\Support\Str::limit(strip_tags($latest->body), 60) }}
                                    @endif
                                </td>
                                <td style="color: var(--ink-soft); font-size: 13px;">
                                    @if($latest)
                                        {{ $latest->created_at->format('d.m.Y H:i') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@stop

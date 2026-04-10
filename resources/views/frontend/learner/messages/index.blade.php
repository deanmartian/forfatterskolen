@extends('frontend.layouts.course-portal')

@section('page_title', 'Meldinger &rsaquo; Forfatterskolen')

@section('heading') Meldinger @stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            @include('frontend.partials.learner-search-new')
        </div>

        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 style="margin: 0;">Innboks</h4>
                    <a href="{{ route('learner.messages.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Ny samtale
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($conversations->isEmpty())
                    <div class="card global-card">
                        <div class="card-body text-center" style="padding: 40px;">
                            <i class="fa fa-envelope" style="font-size: 48px; color: #999; margin-bottom: 15px; display: block;"></i>
                            <p style="color: #999; margin: 0;">Ingen meldinger ennå. Start en ny samtale med din redaktor!</p>
                        </div>
                    </div>
                @else
                    <div class="card global-card">
                        <div class="card-body py-0">
                            <table class="table table-global">
                                <thead>
                                    <tr>
                                        <th>Emne</th>
                                        <th>Fra</th>
                                        <th>Siste melding</th>
                                        <th>Dato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($conversations as $conversation)
                                        @php
                                            $unread = $conversation->unreadCountFor(Auth::user());
                                            $latest = $conversation->latestMessage->first();
                                            $otherParticipants = $conversation->participants->where('id', '!=', Auth::id());
                                        @endphp
                                        <tr style="cursor: pointer;{{ $unread > 0 ? ' font-weight: 600; background: #fdf9f4;' : '' }}" onclick="window.location='{{ route('learner.messages.show', $conversation->id) }}'">
                                            <td>
                                                {{ $conversation->subject }}
                                                @if($unread > 0)
                                                    <span class="badge badge-danger" style="margin-left: 5px;">{{ $unread }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($otherParticipants as $p)
                                                    {{ $p->full_name }}@if(!$loop->last), @endif
                                                @endforeach
                                            </td>
                                            <td style="color: #999; font-size: 13px;">
                                                @if($latest)
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($latest->body), 50) }}
                                                @endif
                                            </td>
                                            <td style="color: #999; font-size: 13px; white-space: nowrap;">
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
        </div>
    </div>
</div>
@stop

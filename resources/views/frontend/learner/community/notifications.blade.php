@extends('frontend.layouts.course-portal')

@section('page_title', 'Varsler › Skrivefellesskap › Forfatterskolen')
@section('robots')<meta name="robots" content="noindex, follow">@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/community.css?v=' . time()) }}">
@stop

@section('content')
<div class="learner-container community-wrapper">
    <div class="container">
        @include('frontend.learner.community._nav')
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin: 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="d-flex notification-header-flex" style="justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 class="community-title">Varsler</h1>
            </div>
            @if($notifications->where('read', false)->count() > 0)
                <form action="{{ route('learner.community.markAllNotificationsRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="community-btn-outline" style="font-size: 12px;">
                        <i class="fa fa-check-circle-o"></i> Marker alle som lest
                    </button>
                </form>
            @endif
        </div>

        @forelse($notifications as $notification)
            @php
                $fromProfile = $notification->fromUser->profile ?? null;
                $fromName = $fromProfile ? ucwords($fromProfile->name) : 'System';
                $fromInitials = collect(explode(' ', $fromName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                $fColors = ['#2563eb', '#0d7a5f', '#7c3aed', '#b45309', '#862736'];
                $fColor = $fColors[crc32($fromName) % count($fColors)];
            @endphp
            <div class="community-card mb-2 {{ !$notification->read ? 'notification-unread' : '' }}">
                <div class="card-body">
                    <div class="d-flex" style="gap: 12px; align-items: center;">
                        <div class="avatar-circle avatar-sm" style="background: {{ $fColor }};">{{ $fromInitials }}</div>
                        <div style="flex: 1;">
                            <p class="notification-text">{{ $notification->content }}</p>
                            <span class="notification-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                        </div>
                        @if(!$notification->read)
                            <form action="{{ route('learner.community.markNotificationRead', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action" title="Marker som lest">
                                    <i class="fa fa-check"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="community-card">
                <div class="card-body text-center py-5">
                    <i class="fa fa-bell-o" style="font-size: 48px; color: var(--border);"></i>
                    <p style="color: var(--text-muted); margin-top: 12px;">Ingen varsler ennå</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@stop

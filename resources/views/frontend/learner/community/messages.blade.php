@extends('frontend.layouts.course-portal')

@section('page_title', 'Meldinger › Skrivefellesskap › Forfatterskolen')

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
        <h1 class="community-title">Meldinger</h1>

        <div class="row">
            {{-- Conversation list --}}
            <div class="col-md-4">
                <div class="community-card mb-3">
                    <div class="card-body p-0">
                        <div style="padding: 12px;">
                            <button class="community-btn-primary" style="width: 100%;" onclick="document.getElementById('new-message-form').style.display = document.getElementById('new-message-form').style.display === 'none' ? 'block' : 'none'">
                                <i class="fa fa-pencil"></i> Ny melding
                            </button>
                        </div>

                        <div id="new-message-form" style="display: none; padding: 0 12px 12px;">
                            <form action="{{ route('learner.community.sendMessage') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <select name="recipient_id" class="form-control" required style="border: 1px solid var(--border); border-radius: 8px; font-size: 13px;">
                                        <option value="">Velg mottaker</option>
                                        @foreach($members as $member)
                                            @if($member->user_id !== Auth::id())
                                                <option value="{{ $member->user_id }}">{{ ucwords($member->name) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea name="content" class="form-control community-textarea" rows="2" placeholder="Skriv melding..." required></textarea>
                                </div>
                                <button type="submit" class="community-btn-primary" style="font-size: 12px; padding: 6px 14px;">Send</button>
                            </form>
                        </div>

                        @forelse($conversations as $conv)
                            @php
                                $cPartner = $conv['partner'];
                                $cProfile = $cPartner->profile ?? null;
                                $cName = $cProfile ? ucwords($cProfile->name) : ucwords($cPartner->first_name . ' ' . $cPartner->last_name);
                                $cInitials = collect(explode(' ', $cName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
                                $isActive = $activeChat == $cPartner->id;
                                $cColors = ['#2563eb', '#0d7a5f', '#7c3aed', '#b45309', '#862736'];
                                $cColor = $cColors[crc32($cName) % count($cColors)];
                            @endphp
                            <a href="{{ route('learner.community.conversation', $cPartner->id) }}" class="conversation-item {{ $isActive ? 'active' : '' }}">
                                <div class="avatar-circle avatar-sm" style="background: {{ $cColor }};">{{ $cInitials }}</div>
                                <div style="flex: 1; min-width: 0;">
                                    <strong class="conversation-name">{{ $cName }}</strong>
                                    <p class="conversation-preview">{{ Str::limit($conv['last_message']->content ?? '', 40) }}</p>
                                </div>
                                @if($conv['unread_count'] > 0)
                                    <span style="background: var(--brand); color: #fff; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 10px;">{{ $conv['unread_count'] }}</span>
                                @endif
                            </a>
                        @empty
                            <div class="text-center py-4">
                                <p style="font-size: 13px; color: var(--text-muted);">Ingen samtaler ennå</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Chat area --}}
            <div class="col-md-8">
                @if($activeChat && $chatPartner)
                    @php
                        $partnerProfile = $chatPartner->profile ?? null;
                        $partnerName = $partnerProfile ? ucwords($partnerProfile->name) : ucwords($chatPartner->first_name . ' ' . $chatPartner->last_name);
                    @endphp
                    <div class="community-card">
                        <div class="card-body">
                            <div class="chat-header">
                                <strong>{{ $partnerName }}</strong>
                            </div>

                            <div class="chat-messages">
                                @foreach($chatMessages as $msg)
                                    @php $isMine = $msg->sender_id === Auth::id(); @endphp
                                    <div class="chat-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                                        <p>{{ $msg->content }}</p>
                                        <span class="chat-time">{{ \Carbon\Carbon::parse($msg->created_at)->format('d.m H:i') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <form action="{{ route('learner.community.sendMessage') }}" method="POST" class="chat-input-form">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $chatPartner->id }}">
                                <input type="text" name="content" class="form-control" placeholder="Skriv en melding..." required autofocus>
                                <button type="submit" class="community-btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="community-card">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-envelope-o" style="font-size: 48px; color: var(--border);"></i>
                            <p style="color: var(--text-muted); margin-top: 12px;">Velg en samtale eller start en ny melding</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

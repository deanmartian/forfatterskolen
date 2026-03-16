@extends('backend.layout')

@section('title')
<title>Fellesskap &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    {{-- Tab navigation --}}
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li class="active"><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
    </ul>

    {{-- Stats --}}
    <div class="row">
        <div class="col-sm-3">
            <div class="panel panel-default text-center">
                <div class="panel-body">
                    <h3 style="margin: 0;">{{ $stats['members'] }}</h3>
                    <small class="text-muted">Totalt medlemmer</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default text-center">
                <div class="panel-body">
                    <h3 style="margin: 0;">{{ $stats['posts'] }}</h3>
                    <small class="text-muted">Innlegg</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default text-center">
                <div class="panel-body">
                    <h3 style="margin: 0;">{{ $stats['discussions'] }}</h3>
                    <small class="text-muted">Diskusjoner</small>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="panel panel-default text-center">
                <div class="panel-body">
                    <h3 style="margin: 0;">{{ $stats['courseGroups'] }}</h3>
                    <small class="text-muted">Kursgrupper</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent posts --}}
    <div class="panel panel-default">
        <div class="panel-heading"><h4 style="margin: 0;">Siste innlegg</h4></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Forfatter</th>
                    <th>Innhold</th>
                    <th>Dato</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPosts as $post)
                    @php
                        $profile = $post->user->profile ?? null;
                        $name = $profile ? ucwords($profile->name) : ($post->user->fullName ?? 'Ukjent');
                    @endphp
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ Str::limit($post->content, 100) }}</td>
                        <td>{{ $post->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">Ingen innlegg ennå.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

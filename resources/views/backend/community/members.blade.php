@extends('backend.layout')

@section('title')
<title>Medlemmer &rsaquo; Fellesskap &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li class="active"><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
    </ul>

    {{-- Search & filter --}}
    <form method="GET" action="{{ route('admin.community.members') }}" class="form-inline" style="margin-bottom: 15px;">
        <div class="form-group" style="margin-right: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Søk etter navn..." value="{{ request('search') }}">
        </div>
        <div class="form-group" style="margin-right: 10px;">
            <select name="badge" class="form-control">
                <option value="">Alle roller</option>
                <option value="aktiv_elev" {{ request('badge') === 'aktiv_elev' ? 'selected' : '' }}>Aktiv elev</option>
                <option value="tidligere_elev" {{ request('badge') === 'tidligere_elev' ? 'selected' : '' }}>Tidligere elev</option>
                <option value="mentor" {{ request('badge') === 'mentor' ? 'selected' : '' }}>Mentor</option>
                <option value="moderator" {{ request('badge') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                <option value="admin" {{ request('badge') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Filtrer</button>
        @if(request('search') || request('badge'))
            <a href="{{ route('admin.community.members') }}" class="btn btn-link">Nullstill</a>
        @endif
    </form>

    <div class="panel panel-default">
        <table class="table">
            <thead>
                <tr>
                    <th>Navn</th>
                    <th>Forfatternavn</th>
                    <th>Badge</th>
                    <th>Suspendert</th>
                    <th style="width: 250px;">Handlinger</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>{{ ucwords($member->name) }}</td>
                        <td>{{ $member->author_name ?: '—' }}</td>
                        <td>
                            <span class="label label-default">{{ $member->badge ?: 'ingen' }}</span>
                        </td>
                        <td>
                            @if($member->is_suspended)
                                <span class="label label-danger">Ja</span>
                            @else
                                <span class="label label-success">Nei</span>
                            @endif
                        </td>
                        <td>
                            {{-- Edit badge --}}
                            <form action="{{ route('admin.community.members.badge', $member->id) }}" method="POST" style="display: inline-flex; gap: 4px; align-items: center;">
                                @csrf
                                <select name="badge" class="form-control input-sm" style="width: auto;">
                                    <option value="aktiv_elev" {{ $member->badge === 'aktiv_elev' ? 'selected' : '' }}>Aktiv elev</option>
                                    <option value="tidligere_elev" {{ $member->badge === 'tidligere_elev' ? 'selected' : '' }}>Tidligere elev</option>
                                    <option value="mentor" {{ $member->badge === 'mentor' ? 'selected' : '' }}>Mentor</option>
                                    <option value="moderator" {{ $member->badge === 'moderator' ? 'selected' : '' }}>Moderator</option>
                                    <option value="admin" {{ $member->badge === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-xs btn-primary" title="Lagre badge"><i class="fa fa-check"></i></button>
                            </form>

                            {{-- Suspend toggle --}}
                            <form action="{{ route('admin.community.members.toggle-suspend', $member->id) }}" method="POST" style="display: inline; margin-left: 6px;">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $member->is_suspended ? 'btn-success' : 'btn-warning' }}" title="{{ $member->is_suspended ? 'Gjenopprett' : 'Suspender' }}">
                                    <i class="fa {{ $member->is_suspended ? 'fa-check' : 'fa-ban' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Ingen medlemmer funnet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {!! $members->appends(request()->query())->render() !!}
</div>
@stop

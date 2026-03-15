@extends('backend.layout')

@section('title')
<title>Diskusjoner &rsaquo; Fellesskap &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li class="active"><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
    </ul>

    <div class="panel panel-default">
        <table class="table">
            <thead>
                <tr>
                    <th>Tittel</th>
                    <th>Kategori</th>
                    <th>Forfatter</th>
                    <th>Svar</th>
                    <th>Dato</th>
                    <th>Festet</th>
                    <th style="width: 120px;">Handlinger</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discussions as $discussion)
                    @php
                        $profile = $discussion->user->profile ?? null;
                        $name = $profile ? ucwords($profile->name) : ($discussion->user->fullName ?? 'Ukjent');
                    @endphp
                    <tr @if($discussion->pinned) style="background: #fff8e1;" @endif>
                        <td><strong>{{ $discussion->title }}</strong></td>
                        <td><span class="label label-info">{{ $discussion->category }}</span></td>
                        <td>{{ $name }}</td>
                        <td>{{ $discussion->replies_count }}</td>
                        <td>{{ $discussion->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($discussion->pinned)
                                <span class="label label-warning"><i class="fa fa-thumb-tack"></i> Ja</span>
                            @else
                                <span class="text-muted">Nei</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.community.discussions.toggle-pin', $discussion->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $discussion->pinned ? 'btn-default' : 'btn-warning' }}" title="{{ $discussion->pinned ? 'Løsne' : 'Fest' }}">
                                    <i class="fa fa-thumb-tack"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.community.discussions.destroy', $discussion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Er du sikker på at du vil slette denne diskusjonen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" title="Slett">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Ingen diskusjoner ennå.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {!! $discussions->render() !!}
</div>
@stop

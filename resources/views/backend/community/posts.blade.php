@extends('backend.layout')

@section('title')
<title>Innlegg &rsaquo; Fellesskap &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li class="active"><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
    </ul>

    <div class="panel panel-default">
        <table class="table">
            <thead>
                <tr>
                    <th>Forfatter</th>
                    <th>Innhold</th>
                    <th>Kommentarer</th>
                    <th>Dato</th>
                    <th>Festet</th>
                    <th style="width: 120px;">Handlinger</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    @php
                        $profile = $post->user->profile ?? null;
                        $name = $profile ? ucwords($profile->name) : ($post->user->fullName ?? 'Ukjent');
                    @endphp
                    <tr @if($post->pinned) style="background: #fff8e1;" @endif>
                        <td>{{ $name }}</td>
                        <td>{{ Str::limit($post->content, 80) }}</td>
                        <td>{{ $post->comments->count() }}</td>
                        <td>{{ $post->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($post->pinned)
                                <span class="label label-warning"><i class="fa fa-thumb-tack"></i> Ja</span>
                            @else
                                <span class="text-muted">Nei</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.community.posts.toggle-pin', $post->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $post->pinned ? 'btn-default' : 'btn-warning' }}" title="{{ $post->pinned ? 'Løsne' : 'Fest' }}">
                                    <i class="fa fa-thumb-tack"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.community.posts.destroy', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Er du sikker på at du vil slette dette innlegget?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" title="Slett">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Ingen innlegg ennå.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {!! $posts->render() !!}
</div>
@stop

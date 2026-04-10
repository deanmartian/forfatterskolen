@extends('backend.layout')

@section('page_title', 'Kursgrupper &rsaquo; Fellesskap &rsaquo; Forfatterskolen Admin')

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li class="active"><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
    </ul>

    {{-- Create form --}}
    <button class="btn btn-primary" style="margin-bottom: 15px;" onclick="document.getElementById('create-group-form').style.display = document.getElementById('create-group-form').style.display === 'none' ? 'block' : 'none'">
        <i class="fa fa-plus"></i> Ny kursgruppe
    </button>

    <div id="create-group-form" class="panel panel-default" style="display: none;">
        <div class="panel-heading"><h4 style="margin: 0;">Opprett ny kursgruppe</h4></div>
        <div class="panel-body">
            <form action="{{ route('admin.community.course-groups.store') }}" method="POST" class="form-horizontal">
                @csrf
                <div class="form-group">
                    <label class="col-sm-2 control-label">Ikon</label>
                    <div class="col-sm-2">
                        <input type="text" name="icon" class="form-control" value="📚" maxlength="10">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Navn</label>
                    <div class="col-sm-8">
                        <input type="text" name="name" class="form-control" placeholder="Kursgruppe-navn" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Beskrivelse</label>
                    <div class="col-sm-8">
                        <textarea name="description" class="form-control" rows="2" placeholder="Kort beskrivelse..."></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-8">
                        <button type="submit" class="btn btn-primary">Opprett</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Course groups list --}}
    <div class="row">
        @forelse($courseGroups as $group)
            <div class="col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div style="font-size: 28px; line-height: 1;">{{ $group->icon ?: '📚' }}</div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 4px;">{{ $group->name }}</h4>
                                <p class="text-muted" style="font-size: 13px; margin: 0 0 6px;">{{ $group->description ?: 'Ingen beskrivelse' }}</p>
                                @php
                                    $postCount = \App\Models\Post::where('course_group_id', $group->id)->count();
                                    $memberList = \DB::table('course_group_members')
                                        ->where('course_group_id', $group->id)
                                        ->join('users', 'course_group_members.user_id', '=', 'users.id')
                                        ->select('users.first_name', 'users.last_name')
                                        ->limit(5)->get();
                                    $totalMembers = \DB::table('course_group_members')->where('course_group_id', $group->id)->count();
                                @endphp
                                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:4px;">
                                    <span class="label label-default">{{ $totalMembers }} medlemmer</span>
                                    <span class="label label-info">{{ $postCount }} innlegg</span>
                                </div>
                            </div>
                        </div>

                        {{-- Members preview --}}
                        @if($memberList->count() > 0)
                        <div style="margin-top:10px;padding:8px 10px;background:#faf9f7;border-radius:6px;font-size:12px;color:#666;">
                            <strong>Medlemmer:</strong>
                            {{ $memberList->map(fn($m) => $m->first_name . ' ' . $m->last_name)->implode(', ') }}{{ $totalMembers > 5 ? ' + ' . ($totalMembers - 5) . ' til' : '' }}
                        </div>
                        @endif

                        <hr style="margin: 10px 0;">

                        {{-- Edit form (inline) --}}
                        <div id="edit-group-{{ $group->id }}" style="display: none; margin-bottom: 10px;">
                            <form action="{{ route('admin.community.course-groups.update', $group->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group" style="margin-bottom: 8px;">
                                    <input type="text" name="icon" class="form-control input-sm" value="{{ $group->icon }}" placeholder="Ikon">
                                </div>
                                <div class="form-group" style="margin-bottom: 8px;">
                                    <input type="text" name="name" class="form-control input-sm" value="{{ $group->name }}" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 8px;">
                                    <textarea name="description" class="form-control input-sm" rows="2">{{ $group->description }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-xs btn-primary">Lagre</button>
                                <button type="button" class="btn btn-xs btn-default" onclick="document.getElementById('edit-group-{{ $group->id }}').style.display='none'">Avbryt</button>
                            </form>
                        </div>

                        <div style="display: flex; gap: 6px;">
                            <button class="btn btn-xs btn-default" onclick="document.getElementById('edit-group-{{ $group->id }}').style.display='block'" title="Rediger">
                                <i class="fa fa-pencil"></i> Rediger
                            </button>
                            <form action="{{ route('admin.community.course-groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Er du sikker på at du vil slette denne kursgruppen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" title="Slett">
                                    <i class="fa fa-trash"></i> Slett
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body text-center text-muted">
                        Ingen kursgrupper opprettet ennå.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@stop

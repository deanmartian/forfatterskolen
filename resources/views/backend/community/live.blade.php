@extends('backend.layout')

@section('title')
<title>Live fellesskap &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Fellesskap')

@section('styles')
<style>
    .community-iframe-wrap {
        margin: -20px -28px;
        height: calc(100vh - 60px);
    }
    .community-iframe-wrap iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    .community-nav-bar {
        display: flex;
        gap: 8px;
        padding: 10px 16px;
        background: #fff;
        border-bottom: 1px solid #e8e4de;
        align-items: center;
    }
    .community-nav-bar a {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        color: #4a4a4a;
        text-decoration: none;
        transition: background 0.15s;
    }
    .community-nav-bar a:hover { background: rgba(134,39,54,0.08); color: #862736; }
    .community-nav-bar a.active { background: #862736; color: #fff; }
</style>
@stop

@section('content')
<div class="col-sm-12" style="padding:0;">
    <ul class="nav nav-tabs" style="margin-bottom: 0; padding: 0 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
        <li class="active"><a href="{{ route('admin.community.live') }}">🔴 Live fellesskap</a></li>
    </ul>

    <div class="community-nav-bar">
        <a href="#" onclick="loadPage('/account/community')" class="active" id="nav-home"><i class="fa fa-home"></i> Hjem</a>
        <a href="#" onclick="loadPage('/account/community/discussions')" id="nav-discussions"><i class="fa fa-comments"></i> Diskusjoner</a>
        <a href="#" onclick="loadPage('/account/community/members')" id="nav-members"><i class="fa fa-users"></i> Medlemmer</a>
        <a href="#" onclick="loadPage('/account/community/manuscripts')" id="nav-manuscripts"><i class="fa fa-book"></i> Manusrom</a>
        <a href="#" onclick="loadPage('/account/community/course-groups')" id="nav-groups"><i class="fa fa-th-large"></i> Kursgrupper</a>
        <a href="#" onclick="loadPage('/account/community/notifications')" id="nav-notifications"><i class="fa fa-bell"></i> Varsler</a>
    </div>

    <div class="community-iframe-wrap">
        <iframe src="{{ config('app.url') }}/auth/login/email/{{ encrypt(Auth::user()->email) }}?redirect=community" id="communityFrame" allow="clipboard-write"></iframe>
    </div>
</div>

<script>
function loadPage(path) {
    document.getElementById('communityFrame').src = '{{ config("app.url") }}' + path;
    document.querySelectorAll('.community-nav-bar a').forEach(function(a) { a.classList.remove('active'); });
    event.target.closest('a').classList.add('active');
}
</script>
@stop

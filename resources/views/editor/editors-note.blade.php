@extends('editor.layout')

@section('title')
<title>{{ trans('site.admin-menu.editors-note') }} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', trans('site.admin-menu.editors-note'))

@section('content')

<div class="ed-section">
    <div class="ed-section__header">
        <h3 class="ed-section__title">Redaktørnotat</h3>
    </div>
    <div class="ed-section__body">
        <div class="ed-note-content">
            {!! $note ?? '<p style="color:var(--ink-muted);">Ingen redaktørnotat tilgjengelig.</p>' !!}
        </div>
    </div>
</div>

@stop

@extends('editor.layout')

@section('title')
<title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div id="app-container">
    <div class="col-sm-12 dashboard-left">
        <div class="col-md-6">
            <project-time-register :current-project="{{ json_encode($project) }}"
            :project-time-list="{{ json_encode($projectTimeRegisters) }}"></project-time-register>
        </div>
    </div>
</div>
@endsection
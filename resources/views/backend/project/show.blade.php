@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

    <div id="app-container">
        <project-details :current-project="{{ json_encode($project) }}"></project-details>
    </div>
@stop

@section('scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
@stop
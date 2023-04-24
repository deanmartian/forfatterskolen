@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Publishing Package Services &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

<div class="page-toolbar">
    <h3><i class="fa fa-file-text-o"></i> Services</h3>
    <div class="clearfix"></div>
</div>

<div class="col-md-12" id="app-container">
    <publishing-services></publishing-services>
</div>

@stop

@section('scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
@stop
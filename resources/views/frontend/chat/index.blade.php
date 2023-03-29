@extends('frontend.layout')

@section('title')
    <title>
        Chat
    </title>
@stop

@section('styles')
<style>
    .main-container {
        min-height: 50vh;
    }
</style>
@stop

@section('content')

    <div class="chat-page" id="app-container">
        <div class="container main-container">
            <chat></chat>
        </div>
    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop
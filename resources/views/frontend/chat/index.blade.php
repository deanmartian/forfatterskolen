@extends('frontend.layout')

@section('page_title', 'Chat')
@section('robots', '<meta name="robots" content="noindex, follow">')

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
    <script type="text/javascript" src="{{ asset('js/app.js?v='.filemtime(public_path('js/app.js'))) }}"></script>
@stop
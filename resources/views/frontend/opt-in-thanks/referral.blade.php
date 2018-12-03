@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Blog</title>
@stop

@section('content')
    <div class="opt-in-referral" style="background-image: url({{ asset('images-new/opt-in-thanks/'.$data['image']) }})">
        <div class="container">
            <iframe class="uvembed{{$data['camp_id']}}" frameborder="0" src="https://static.upviral.com/loader.html"
                    style="display: block; margin: 0 auto;"></iframe>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        let width = $(window).width() >= 1024 ? '500px' : '100%';
        window.UpviralConfig = {
            camp: "{{$data['camp']}}",
            widget_style:'iframe',
        width:width}
    </script>
    <script language="javascript" src="https://snippet.upviral.com/upviral.js"></script>
@stop
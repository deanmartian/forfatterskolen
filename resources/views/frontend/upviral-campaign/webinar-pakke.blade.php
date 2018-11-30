@extends('frontend.layout')

@section('title')
    <title>Upviral &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        .card {
            border: 1px solid #beb7b3;
            border-radius: 8px;
            -webkit-border-radius: 8px;
            padding: 22px;
        }

        .form-control {
            border: 1px solid #999;
            border-radius: 4px !important;
            display: block;
            font-size: 15px;
            height: 47px!important;
            line-height: 1.42857143;
            margin-bottom: 5px;
            margin-top: 0;
            padding: 6px 12px;
            width: 100%;
        }

        [name=upviralsubmit] {
            background-color: #29bdb1;
            border-radius: 4px !important;
            border: 1px solid #29bdb1;
            font-size: 16px!important;
            min-height: 47px;
            opacity: .8;
            padding: 10px 5px!important;
            text-align: center;
            text-transform: uppercase;
            width: 100%;
            word-wrap: break-word;
        }
    </style>
@stop

@section('content')
    @if (Request::input('ref_id'))
        <div class="container mb-3">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="card">
                    <form name='upviralForm61426' id='' method='post' action='https://app.upviral.com/site/parse_new_users/call/ajax/campId/61426'>
                        <div class='form-group'>
                            <input type='text' name='name'  class='form-control' value='' placeholder="Name" required>
                        </div>
                        <div class='form-group'>
                            <input type='email' name='email' class='form-control' value='' placeholder="Email" required>
                        </div>
                        <div class='form-group'>
                            <input type='submit' name='upviralsubmit'  value='Submit' class="btn" style="color: #fff">
                            <input type='hidden' name='reflink' value='{{ Request::input('ref_id') }}'>
                        </div>
                    </form>
                </div>

                <script>window.UpviralConfig = { camp : 'GA$GM$' }</script>
                <script language='javascript' src='https://snippet.upviral.com/upviral.js'></script>
            </div>
        </div>
    @else
        <iframe class="uvembed61426" frameborder="0" src="https://static.upviral.com/loader.html" style="display: block; margin: 0 auto"></iframe>
        <script>
            window.UpviralConfig = {
                camp: "GA$GM$",
                widget_style:'iframe',
                width:"500px"}
        </script>
        <script language="javascript" src="https://snippet.upviral.com/upviral.js"></script>
    @endif

@stop
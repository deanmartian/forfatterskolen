@extends('frontend.layout')

@section('title')
    <?php
    $pageMeta = \App\PageMeta::where('url', url()->current())->first();
    ?>

    @if ($pageMeta)
        <title>{{ $pageMeta->meta_title }}</title>
    @else
        <title>Reprise: Slik skriver du et førsteutkast</title>
    @endif
@stop

@section('styles')
    <style>
        .btn {
            color: #fff;
            border-radius: 24px;
            padding: 0px 20px;
            background-color: #C12938;
            font-size: 1em;
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            line-height: 1.5;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
    </style>
@stop

@section('content')

    <div class="manuscript-page">
        <div class="container main-container">
            @foreach($replays as $k => $replay)
                <div class="row" style="{{ $k > 0 ? 'margin-top: 150px' : ''}}">
                    <div class="col-sm-12">
                        <div class="h1 text-center mb-5 mt-0">
                            {!! $replay->title !!}
                        </div>
                    </div>
                    <div class="col-sm-12 top-page-container">
                        <iframe src="{{ $replay->video_link }}" frameborder="0"
                                allowfullscreen="allowfullscreen"></iframe>
                    </div>
                    @if($replay->file)
                        <div class="text-center w-100">
                            <a href="{{ url($replay->file) }}" class="d-block" download>
                                Last ned Power Point-presentasjon
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
            {{--<div class="row">
                <div class="col-sm-12">
                    <h1 class="text-center mb-5">
                        Reprise: Hvordan skape spennende karakterer? 18.04.2021
                    </h1>
                </div>
                <div class="col-sm-12 top-page-container">
                    <iframe src="https://fast.wistia.com/embed/medias/b0fgthihm9" frameborder="0"
                            allowfullscreen="allowfullscreen"></iframe>
                </div>
            </div>

            <div class="row" style="margin-top: 150px">
                <div class="col-sm-12">
                    <h1 class="text-center mb-5">
                        Reprise: Kunsten å stå i et minne – og lyve 15.04.2021
                    </h1>
                </div>
                <div class="col-sm-12 top-page-container">
                    <iframe src="https://fast.wistia.com/embed/medias/kiwq6qxnm6" frameborder="0"
                            allowfullscreen="allowfullscreen"></iframe>
                </div>

                <div class="text-center w-100">
                    <a href="{{ url('/storage/files/Minner_og_løgn_april_2021.pdf') }}" class="d-block" download>
                        Last ned Power Point-presentasjon
                    </a>
                </div>
            </div>--}}
        </div>
    </div>

@stop
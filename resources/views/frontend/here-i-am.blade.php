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
            <div class="row" style="margin-top: 50px">
                <div class="col-sm-12 top-page-container">
                    <iframe src="https://api.vadoo.tv/landing_page?vid=sCwMm1MVqSfFhpn3brXdtZlpixZBAuUK" frameborder="0"
                            allowfullscreen="allowfullscreen" style="height: 598px;"></iframe>

                    <div class="w-100 text-center">
                        <a href="https://www.norli.no/her-er-jeg-5" class="d-block" target="_blank">
                            <img src="/images-new/her-er-jeg-5.jpg" style="height: 200px">
                        </a>
                    </div>
                </div>

                <div class="text-center w-100">

                    <a href="https://www.norli.no/her-er-jeg-5" class="d-block" target="_blank">
                        Her kan du kjøpe boken SIGNERT
                    </a>

                </div>
            </div>

            <div class="row" style="margin-top: 50px">
                <div class="col-sm-12 top-page-container">
                    <iframe src="https://api.vadoo.tv/landing_page?vid=MkL5DKo10HqfKhaECblbY35qElJOdsrk" frameborder="0"
                            allowfullscreen="allowfullscreen" style="height: 643px"></iframe>
                </div>
                <div class="text-center w-100">
                    <a href="https://www.norli.no/her-er-jeg-5" class="d-block" download>
                        Her kan du kjøpe boken SIGNERT
                    </a>
                </div>
            </div>
        </div>
    </div>

@stop
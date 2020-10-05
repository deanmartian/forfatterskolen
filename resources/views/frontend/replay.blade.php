@extends('frontend.layout')

@section('title')
    <?php
    $pageMeta = \App\PageMeta::where('url', url()->current())->first();
    ?>

    @if ($pageMeta)
        <title>{{ $pageMeta->meta_title }}</title>
    @else
        <title>Reprise: Slik forløser du ditt kreative potensial</title>
    @endif
@stop

@section('content')

    <div class="manuscript-page">
        <div class="container main-container">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="text-center mb-5">
                        Reprise: "Dritten" i midten 04.10.2020
                    </h1>
                </div>
                <div class="col-sm-12 top-page-container">
                    <iframe src="https://video.easywrite.no/file/Webinarer/Gratis/drittenimidten.html" frameborder="0"
                            allowfullscreen="allowfullscreen"></iframe>
                </div>
            </div>
        </div>
    </div>

@stop
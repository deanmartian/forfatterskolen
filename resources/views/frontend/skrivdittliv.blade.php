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
                        Reprise: Hvem tror du er? 07.02.2021
                    </h1>
                </div>
                <div class="col-sm-12 top-page-container">
                    <iframe src="https://video.easywrite.no/file/SkrivdittlivGruppekurs/Hvem-tror-du-at-du-er.html" frameborder="0"
                            allowfullscreen="allowfullscreen"></iframe>
                </div>
            </div>
        </div>
    </div>

@stop
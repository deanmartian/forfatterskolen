@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen &rsaquo; Poems</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
@stop

@section('content')
    <div class="poems-page">
        <div class="header">
            <div class="container">
                <div class="col-md-6">
                    <h1 class="font-barlow-regular">Diktforum</h1>
                    <p class="mt-5">
                        Vi har så mange elever som skriver de vakreste dikt, så vi syns de fortjener litt oppmerksomhet.
                        Hver uke presenterer vi derfor et nytt dikt fra en av våre elever som vi publiserer her, for å
                        få vist frem litt av mangfoldet blant elevene våre.
                    </p>
                </div>
            </div> <!-- end container -->
        </div> <!-- end header-->

        <div class="container">
            <div class="row">
                @foreach($poems as $poem)
                    <div class="col-md-4 mt-5">
                        <div class="card">
                            <div class="card-header">
                                <div class="img-container" style="background-image: url({{ asset($poem->author_image) }})"></div>
                                <div class="text-center author-container">
                                    <h2 class="theme-text font-barlow-medium">{{ $poem->title }}</h2>
                                    <h2>{{ $poem->author }}</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="poem-text-container">
                                    {!! $poem->poem !!}
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col-md-4 -->
                @endforeach
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div>
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <script>
        $(".poem-text-container").mCustomScrollbar({
            theme: "minimal-dark",
            scrollInertia: 500,

        });
    </script>
@stop
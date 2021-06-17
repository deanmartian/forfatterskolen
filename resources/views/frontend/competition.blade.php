@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Publishing</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <style>
        /*@media only screen and (max-width: 768px){
            .publishing-page {
                padding-top: 280px !important;
                background-size: auto 340px !important;
                background-position-y: 10px;
            }
        }*/
    </style>
@stop

@section('content')

    <div class="publishing-page" data-bg="https://www.forfatterskolen.no/images-new/publishing-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <h1 class="page-title">
                        {!! trans('site.front.competition.title') !!}
                    </h1>
                    <p class="page-description mb-4">
                        {!! nl2br(trans('site.front.competition.description')) !!}
                    </p>
                </div>
            </div>


        </div>
    </div>

@stop

@section('scripts')
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <script>
        /*$(".book-authors-container").mCustomScrollbar({
            theme: "minimal-dark",
            scrollInertia: 500
        });*/

        // get all books that have quote
        $.each($(".book-quote"),function(k,v) {
            let book_quote = $(this);
            // check if the div have quote then add an after on the left-container
            if (book_quote.find('p').length > 0) {
                $(this).closest('.quote-row').find('.left-container').addClass('left-quote');
            }
        });

        // call the function once fully loaded
        $(window).on('load', function() {
            $('.grid').masonry({
                // options
                itemSelector : '.grid-item'
            });
        });
    </script>
@stop
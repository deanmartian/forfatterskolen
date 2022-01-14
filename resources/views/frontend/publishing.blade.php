@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Publishing</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
@stop

@section('content')

    <div class="publishing-page" data-bg="https://www.forfatterskolen.no/images-new/publishing-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <h1 class="page-title">
                        {{ trans('site.front.publishing.title') }}
                    </h1>
                    <p class="page-description mb-4">
                        {{ trans('site.front.publishing.main-description') }}
                    </p>
                    <p>
                        {{ trans('site.front.publishing.second-description') }}
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 book-authors-container">
                    <div class="grid">
                        @foreach($books as $book)
                            <div class="col-sm-6 grid-item">
                                <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="left-container">
                                            <?php
                                            $author_image = \App\Http\FrontendHelpers::checkJpegImg($book['author_image']);
                                            $book_image = \App\Http\FrontendHelpers::checkJpegImg($book['book_image']);
                                            ?>

                                            @if($book['book_image_link'])
                                                <a href="{{$book['book_image_link']}}" target="_blank">
                                                    @endif
                                                    <img data-src="https://www.forfatterskolen.no/{{ $book_image }}" alt="{{ $book['title'] }}"
                                                         class="img-responsive pull-right right-image">
                                                    @if($book['book_image_link'])
                                                </a>
                                            @endif

                                            <img data-src="https://www.forfatterskolen.no/{{ $author_image }}" alt="{{ $book['title'] }}" class="img-responsive">
                                        </div>
                                        <div class="right-container">
                                            <div class="book-title h1">{{ $book['title'] }}</div>
                                            <div>
                                                {!! $book['quote_description'] !!}
                                            </div>

                                            <div class="book-description">
                                                {!! $book['description'] !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{--<div class="row quote-row">
                                        <div class="left-container"></div>
                                        <div class="right-container">
                                            <div class="book-quote">
                                            {!! $book['quote_description'] !!}
                                            </div>
                                        </div>
                                    </div>--}}
                                </div>
                            </div>
                            </div>
                        @endforeach
                    </div>

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
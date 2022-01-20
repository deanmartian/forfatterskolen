@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Publishing</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <style>
        .addReadMore.showlesscontent .SecSec,
        .addReadMore.showlesscontent .readLess {
            display: none;
        }

        .addReadMore.showmorecontent .readMore {
            display: none;
        }

        .addReadMore .readMore,
        .addReadMore .readLess,
        .full-description .readLess {
            font-weight: bold;
            margin-left: 2px;
            color: #862736;
            cursor: pointer;
        }

        .addReadMoreWrapTxt.showmorecontent .SecSec,
        .addReadMoreWrapTxt.showmorecontent .readLess {
            display: block;
        }
    </style>
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
                            <div class="col-md-6 col-sm-12 grid-item">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="book-title h1 text-center">{{ $book['title'] }}</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <?php
                                                    $author_image = \App\Http\FrontendHelpers::checkJpegImg($book['author_image']);
                                                ?>
                                                <img data-src="https://www.forfatterskolen.no/{{ $author_image }}"
                                                     alt="{{ $book['title'] }}" class="img-responsive"
                                                     style="max-height: 105px; margin: auto">
                                            </div>

                                            <div class="quote-container p-5 addReadMore showlesscontent w-100">
                                                {!! $book['quote_description'] !!}

                                                {!! $book['description'] !!}
                                            </div>

                                            <div class="quote-container p-5 full-description d-none">
                                                {!! $book['quote_description'] !!}

                                                {!! $book['description'] !!}

                                                <span class="readLess" title="Click to Show Less"> {{ trans('site.homepage.read-less') }}</span>
                                            </div>

                                            <div class="book-images-container m-auto">
                                                @foreach($book->libraries as $library)
                                                    <?php
                                                        $book_image = \App\Http\FrontendHelpers::checkJpegImg($library->book_image);
                                                    ?>
                                                    @if($library->book_link)
                                                        <a href="{{ $library->book_link }}" target="_blank">
                                                            <img data-src="https://www.forfatterskolen.no/{{ $book_image }}" alt="{{ $book['title'] }}"
                                                                 class="img-responsive pull-right right-image mr-2"
                                                                 style="max-height: 105px; max-width: 105px">
                                                        </a>
                                                    @else
                                                        <img data-src="https://www.forfatterskolen.no/{{ $book_image }}" alt="{{ $book['title'] }}"
                                                             class="img-responsive pull-right right-image mr-2"
                                                             style="max-height: 105px; max-width: 105px">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> <!-- end grid -->
                </div> <!-- end .book-authors-container-->
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

    <script>
        // call the function once fully loaded
        $(window).on('load', function() {
            $('.grid').masonry({
                // options
                itemSelector : '.grid-item'
            });
        });

        function AddReadMore() {
            //This limit you can set after how much characters you want to show Read More.
            let carLmt = 280;
            // Text to show when text is collapsed
            let readMoreTxt = " ... {{ trans('site.homepage.read-more') }}";
            // Text to show when text is expanded
            let readLessTxt = " {{ trans('site.homepage.read-less') }}";


            //Traverse all selectors with this class and manupulate HTML part to show Read More
            $(".addReadMore").each(function() {
                let allstr = $(this).text();
                if (allstr.length > carLmt) {
                    let firstSet = allstr.substring(0, carLmt);
                    let secdHalf = allstr.substring(carLmt, allstr.length);
                    let strtoadd = firstSet + "<span class='SecSec'>" + secdHalf
                        + "</span><span class='readMore'  title='Click to Show More'>"
                        + readMoreTxt + "</span><span class='readLess' title='Click to Show Less'>" + readLessTxt + "</span>";
                    $(this).html(strtoadd);
                }
            });
            //Read More and Read Less Click Event binding
            $(document).on("click", ".readMore,.readLess", function() {
                $(this).closest(".card-body").find(".addReadMore, .full-description").toggleClass("d-none");
                $('.grid').masonry({
                    // options
                    itemSelector : '.grid-item'
                });
            });
        }

        $(function() {
            //Calling function after Page Load
            AddReadMore();
        });
    </script>
@stop
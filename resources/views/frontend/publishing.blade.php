@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Publishing</title>
@stop

@section('content')

    <div class="container">
        <div class="courses-hero text-center">
            <div class="row">
                <div class="col-sm-12">
                    <h2><span class="highlight">FOR</span>FATTERSKOLENS UTGITTE ELEVER</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row" id="list-author-books">
            @foreach($books->chunk(3) as $book_chunk)
                @foreach($book_chunk as $book)
                    <div class="col-sm-4 publishing-author-container">
                        <div class="publishing-author col-sm-12">
                            <div class="author-pix">
                                <?php
                                    $author_image = \App\Http\FrontendHelpers::checkJpegImg($book['author_image']);
                                    $book_image = \App\Http\FrontendHelpers::checkJpegImg($book['book_image']);
                                ?>
                                <img src="{{ $author_image }}" alt="{{ $book['title'] }}" class="img-responsive pull-left left-image">
                                    @if($book['book_image_link'])
                                        <a href="{{$book['book_image_link']}}" target="_blank">
                                            @endif
                                                <img src="{{ $book_image }}" alt="{{ $book['title'] }}" class="img-responsive pull-right right-image">
                                            @if($book['book_image_link'])
                                        </a>
                                    @endif
                            </div>
                            <div class="col-sm-12 no-left-right-padding">
                                <span class="book-icon"></span>
                                <h4 class="book-title">
                                    {{ $book['title'] }}
                                </h4>

                                <div class="author-desc">
                                    {!! $book['description'] !!}
                                    {!! $book['quote_description'] !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

@stop

@section('scripts')
    <script>
        $.getScript('//cdn.jsdelivr.net/isotope/1.5.25/jquery.isotope.min.js',function(){
            /* activate jquery isotope */
            $('#list-author-books').isotope({
                itemSelector : '.publishing-author-container'
            });

        });
    </script>
@stop
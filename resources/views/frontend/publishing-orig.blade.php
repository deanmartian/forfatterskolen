@extends('frontend.layout')

@section('title')
<title>Forfatterskolen Publishing</title>
@stop

@section('content')

    <div class="page-content">
        {{--<div class="page-header">
            <h1></h1>
        </div>--}}

        <div class="authors">
            @foreach($books as $k => $book)
                <?php $currentDisplay = $k+1; ?>
                <div class="author">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-3">
                                @if ($currentDisplay%2)
                                <div class="author-pix">
                                    <?php
                                        $author_image = $book['author_image'];
                                    ?>
                                    <img src="{{ $author_image }}" alt="{{ $book['title'] }}" class="img-responsive">
                                </div>
                                @else
                                    <?php
                                        $book_image = $book['book_image'];
                                        $getExtension = explode('.', $book_image);
                                        $extension = $getExtension[1];
                                        // check if jpeg file
                                        if ($extension == 'jpeg') {
                                            // if the jpeg can't be found replace it with jpg
                                            if (!File::exists(public_path($book_image))) {
                                                $book_image = $getExtension[0].'.jpg';
                                            }
                                        }
                                    ?>
                                    <div class="book-image-cover">
                                        @if($book['book_image_link'])
                                            <a href="{{$book['book_image_link']}}" target="_blank">
                                        @endif
                                                <img src="{{ $book_image }}" class="img-responsive center-block">
                                        @if($book['book_image_link'])
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <h4 class="author-name">
                                    {{ $book['title'] }}
                                </h4>
                                <div class="author-desc">
                                    {!! $book['description'] !!}
                                    {!! $book['quote_description'] !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                @if ($currentDisplay%2)
                                    <?php
                                        $book_image = $book['book_image'];
                                        $getExtension = explode('.', $book_image);
                                        $extension = $getExtension[1];
                                        // check if jpeg file
                                        if ($extension == 'jpeg') {
                                            // if the jpeg can't be found replace it with jpg
                                            if (!File::exists(public_path($book_image))) {
                                                $book_image = $getExtension[0].'.jpg';
                                            }
                                        }
                                    ?>
                                    <div class="book-image-cover">
                                        @if($book['book_image_link'])
                                            <a href="{{$book['book_image_link']}}" target="_blank">
                                                @endif
                                                <img src="{{ $book_image }}" class="img-responsive center-block">
                                                @if($book['book_image_link'])
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <?php
                                        $author_image = $book['author_image'];
                                        $getExtension = explode('.', $author_image);
                                        $extension = $getExtension[1];
                                        // check if jpeg file
                                        if ($extension == 'jpeg') {
                                            // if the jpeg can't be found replace it with jpg
                                            if (!File::exists(public_path($author_image))) {
                                                $author_image = $getExtension[0].'.jpg';
                                            }
                                        }
                                    ?>
                                    <div class="author-pix">
                                        <img src="{{ $author_image }}" alt="{{ $book['title'] }}" class="img-responsive">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div> <!-- end of first authors list -->
    </div>

@stop
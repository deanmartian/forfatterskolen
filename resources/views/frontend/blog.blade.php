@extends('frontend.layout')

@section('title')
<title>Forfatterskolen Blog</title>
@stop

@section('content')

    <div class="blog-page">
        <div class="container main-container">
            <div class="row">
                <div class="col-sm-12 top-page-container">
                    <div class="main-blog" style="background-image: url({{ asset($mainBlog->image) }})">
                        <div class="details text-center">
                            <h1 class="title">
                                {{ $mainBlog->title }}
                            </h1>

                            <div class="date-author-cont color-b4 my-4">
                                <span class="date mr-5">
                                    <i class="img-icon calendar"></i>
                                    {{ \App\Http\FrontendHelpers::formatDate($mainBlog->created_at) }}
                                </span>
                                <span class="author">
                                    <i class="img-icon author-image"></i>
                                    {{ $mainBlog->author_name ?: $mainBlog->user->full_name }}
                                </span>
                            </div>

                            <div class="description color-b4">
                                {!! strlen($mainBlog->description) > 200
                                ? substr(strip_tags(html_entity_decode($mainBlog->description)),0,200).'....'
                                : $mainBlog->description !!}
                            </div>

                            <a href="" class="btn buy-btn">
                                Les Mer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="container">
                test
            </div>
        </div>
    </div>

@stop
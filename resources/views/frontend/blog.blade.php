@extends('frontend.layout')

@section('title')
<title>Forfatterskolen Blog</title>
@stop

@section('content')

    <div class="row blog-bg">
        <div class="container">
            @foreach($blogs as $blog)
                <div class="row white-bg">
                    <div class="col-sm-6 blog-image">
                        <img src="{{ asset($blog->image) }}" alt="">
                    </div>
                    <div class="col-sm-6 blog-right-content">
                        <div class="blog-profile" style="background-image: url({{ asset($blog->author_image ?: $blog->user->profile_image) }});"></div>
                        <p class="name">{{ $blog->author_name ?: $blog->user->full_name }}</p>
                        <p class="name">{{ $blog->created_at }}</p>
                        <p class="blog-title">{{ strtoupper($blog->title) }}</p>
                        <div class="blog-description">
                            {!! strlen($blog->description) > 100 ? substr(strip_tags(html_entity_decode($blog->description)),0,300).'....' : $blog->description !!}
                            <div class="clearfix"></div>
                            <a href="{{ route('front.read-blog', $blog->id) }}">Read More</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@stop
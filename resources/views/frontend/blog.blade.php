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
                        <a href="{{ route('front.read-blog', $blog->id) }}">
                            <img src="{{ asset($blog->image) }}" alt="">
                        </a>
                    </div>
                    <div class="col-sm-6 blog-right-content">
                        <div class="blog-profile" style="background-image: url({{ asset($blog->author_image ?: $blog->user->profile_image) }});"></div>
                        <p class="name mb-0">{{ $blog->author_name ?: $blog->user->full_name }}</p>
                        <div class="clearfix"></div>
                        <p class="name">{{ $blog->created_at }}</p>
                        <p class="blog-title">
                            <a href="{{ route('front.read-blog', $blog->id) }}" style="color:#000">
                                {{ mb_convert_case($blog->title, MB_CASE_UPPER) }}
                            </a>
                        </p>
                        <div class="blog-description">
                            {!! strlen($blog->description) > 200 ? substr(strip_tags(html_entity_decode($blog->description)),0,200).'....' : $blog->description !!}
                            <div class="clearfix"></div>
                            <a href="{{ route('front.read-blog', $blog->id) }}">Les mer</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@stop
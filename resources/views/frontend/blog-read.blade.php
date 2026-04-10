@extends('frontend.layout')

@section('page_title', $blog->title . ' — Forfatterskolen')

@section('metas')
    <meta property="og:url"           content="{{ route('front.read-blog', $blog->id) }}" />
    <meta property="og:type"          content="article" />
    <meta property="og:title"         content="{{ $blog->title }}" />
    <meta property="og:description"   content="{{ substr(trim(strip_tags($blog->description)),0 , 160) }}" />
    <meta property="og:image"         content="{{ asset($blog->image) }}" />
@stop

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pages/blog-read.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@stop

@section('content')
<div class="blog-article">
    {{-- Hero --}}
    <div class="ba-hero">
        @if($blog->image)
            <img class="ba-hero-img" src="{{ asset($blog->image) }}" alt="{{ $blog->title }}">
        @endif
        <div class="ba-hero-overlay">
            <div class="ba-hero-inner">
                <span class="ba-tag">{{ $blog->category ?? 'Portrettintervju' }}</span>
                <h1>{{ $blog->title }}</h1>
                <div class="ba-hero-meta">
                    <div class="ba-hero-avatar">
                        @if($blog->author_image ?: ($blog->user->profile_image ?? null))
                            <img src="{{ asset($blog->author_image ?: $blog->user->profile_image) }}" alt="">
                        @else
                            {{ strtoupper(substr($blog->author_name ?: ($blog->user->full_name ?? '?'), 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        {{ $blog->author_name ?: ($blog->user->full_name ?? '') }}
                        <span>&middot;</span>
                        {{ \App\Http\FrontendHelpers::formatDate($blog->created_at) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="ba-content-wrap">
        <div class="ba-body">
            {!! $blog->description !!}

            <div class="ba-share">
                <span class="ba-share-label">Del:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('front.read-blog', $blog->id)) }}"
                   class="ba-share-btn ba-share-fb" target="_blank" rel="noopener" title="Del p&aring; Facebook">
                    <i class="fa fa-facebook"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('front.read-blog', $blog->id)) }}&text={{ urlencode($blog->title) }}"
                   class="ba-share-btn ba-share-tw" target="_blank" rel="noopener" title="Del p&aring; Twitter">
                    <i class="fa fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('front.read-blog', $blog->id)) }}&title={{ urlencode($blog->title) }}"
                   class="ba-share-btn ba-share-li" target="_blank" rel="noopener" title="Del p&aring; LinkedIn">
                    <i class="fa fa-linkedin"></i>
                </a>
            </div>
        </div>

        <a href="{{ route('front.blog') }}" class="ba-back">&larr; Tilbake til bloggen</a>

        {{-- Related posts --}}
        @if(isset($relatedBlogs) && $relatedBlogs->count())
        <div class="ba-related">
            <h3 class="ba-related-label">Flere innlegg</h3>
            <div class="ba-related-grid">
                @foreach($relatedBlogs as $related)
                <a href="{{ route('front.read-blog', $related->id) }}" class="ba-related-card">
                    <div class="ba-related-card-img">
                        @if($related->image)
                            <img src="{{ asset($related->image) }}" alt="{{ $related->title }}">
                        @endif
                    </div>
                    <div class="ba-related-card-body">
                        <h3>{{ $related->title }}</h3>
                        <span class="ba-date">{{ \App\Http\FrontendHelpers::formatDate($related->created_at) }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@stop

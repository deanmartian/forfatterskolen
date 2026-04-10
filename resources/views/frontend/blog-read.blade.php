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
<style>
    .blog-article *, .blog-article *::before, .blog-article *::after { box-sizing: border-box; }
    :root {
        --ba-brand: #862736;
        --ba-brand-light: #f8edef;
        --ba-brand-mid: #d4a0a8;
        --ba-text: #1a1a1a;
        --ba-text-muted: #6b6b6b;
        --ba-text-faint: #aaa;
        --ba-bg: #ffffff;
        --ba-bg-soft: #f7f5f2;
        --ba-border: #e8e4e0;
    }
    .blog-article {
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--ba-bg-soft);
        color: var(--ba-text);
        font-size: 16px;
        line-height: 1.7;
    }

    /* Hero */
    .ba-hero {
        position: relative;
        background: var(--ba-brand);
        overflow: hidden;
        min-height: 340px;
        display: flex;
        align-items: flex-end;
    }
    .ba-hero-img {
        position: absolute; inset: 0;
        width: 100%; height: 100%; object-fit: cover; object-position: top;
        opacity: 0.35;
    }
    .ba-hero-overlay {
        position: relative; z-index: 1;
        width: 100%;
        padding: 60px 40px 48px;
        background: linear-gradient(transparent, rgba(0,0,0,0.55));
    }
    .ba-hero-inner {
        max-width: 760px;
        margin: 0 auto;
    }
    .ba-hero .ba-tag {
        display: inline-block;
        font-size: 10px; letter-spacing: 0.14em;
        text-transform: uppercase; color: #fff;
        background: rgba(255,255,255,0.2); padding: 4px 10px;
        border-radius: 3px; font-weight: 500; margin-bottom: 16px;
        backdrop-filter: blur(4px);
    }
    .ba-hero h1 {
        font-family: 'Lora', serif;
        font-size: 36px; font-weight: 500;
        line-height: 1.3; color: #fff;
        margin: 0 0 20px;
    }
    .ba-hero-meta {
        display: flex; align-items: center; gap: 12px;
        color: rgba(255,255,255,0.85);
        font-size: 14px;
    }
    .ba-hero-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.4);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Lora', serif; font-size: 14px; color: #fff;
        overflow: hidden; flex-shrink: 0;
    }
    .ba-hero-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ba-hero-meta-text span { opacity: 0.6; margin: 0 6px; }

    /* Content layout */
    .ba-content-wrap {
        max-width: 760px;
        margin: 0 auto;
        padding: 48px 40px 60px;
    }

    /* Article body */
    .ba-body {
        background: var(--ba-bg);
        border: 1px solid var(--ba-border);
        border-radius: 12px;
        padding: 48px 52px;
        margin-bottom: 32px;
    }
    .ba-body h2, .ba-body h3, .ba-body h4 {
        font-family: 'Lora', serif;
        font-weight: 500;
        color: var(--ba-text);
        margin: 32px 0 12px;
        line-height: 1.4;
    }
    .ba-body h2 { font-size: 26px; }
    .ba-body h3 { font-size: 21px; }
    .ba-body h4 { font-size: 18px; }
    .ba-body p {
        margin: 0 0 18px;
        font-size: 16px;
        line-height: 1.8;
        color: var(--ba-text);
    }
    .ba-body strong, .ba-body b {
        font-weight: 600;
        color: var(--ba-text);
    }
    .ba-body em, .ba-body i {
        font-style: italic;
    }
    .ba-body a {
        color: var(--ba-brand);
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .ba-body a:hover { color: #6e1e2b; }
    .ba-body img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 24px 0;
    }
    .ba-body blockquote {
        border-left: 3px solid var(--ba-brand);
        padding: 16px 24px;
        margin: 24px 0;
        background: var(--ba-bg-soft);
        border-radius: 0 8px 8px 0;
        font-style: italic;
        color: var(--ba-text-muted);
    }
    .ba-body ul, .ba-body ol {
        margin: 0 0 18px 20px;
        padding: 0;
    }
    .ba-body li {
        margin-bottom: 6px;
        line-height: 1.7;
    }

    /* Share & nav */
    .ba-share {
        display: flex; align-items: center; gap: 12px;
        padding: 24px 0;
        border-top: 1px solid var(--ba-border);
        margin-top: 8px;
    }
    .ba-share-label {
        font-size: 13px; font-weight: 500;
        color: var(--ba-text-muted);
        margin-right: 4px;
    }
    .ba-share-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 38px; height: 38px; border-radius: 50%;
        text-decoration: none; color: #fff;
        font-size: 16px;
        transition: transform 0.15s, opacity 0.15s;
    }
    .ba-share-btn:hover { transform: scale(1.1); opacity: 0.85; color: #fff; }
    .ba-share-fb { background: #1877F2; }
    .ba-share-tw { background: #1DA1F2; }
    .ba-share-li { background: #0A66C2; }

    /* Back link */
    .ba-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 14px; font-weight: 500;
        color: var(--ba-brand);
        text-decoration: none;
        padding: 10px 18px;
        background: var(--ba-bg);
        border: 1px solid var(--ba-border);
        border-radius: 6px;
        transition: background 0.15s;
    }
    .ba-back:hover { background: var(--ba-brand-light); color: var(--ba-brand); }

    /* Related posts */
    .ba-related {
        margin-top: 40px;
    }
    .ba-related-label {
        font-size: 11px; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--ba-text-faint);
        font-weight: 500; margin-top: 0; margin-bottom: 16px;
        padding-bottom: 12px; border-bottom: 1px solid var(--ba-border);
    }
    .ba-related-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
    }
    .ba-related-card {
        background: #fff; border: 1px solid var(--ba-border);
        border-radius: 10px; overflow: hidden;
        text-decoration: none; color: inherit; display: block;
        transition: border-color 0.15s;
    }
    .ba-related-card:hover { border-color: var(--ba-brand-mid); }
    .ba-related-card-img {
        height: 110px; background: var(--ba-bg-soft);
        overflow: hidden; border-bottom: 1px solid var(--ba-border);
    }
    .ba-related-card-img img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
    .ba-related-card-body {
        padding: 14px 16px;
    }
    .ba-related-card-body h3 {
        font-family: 'Lora', serif;
        font-size: 14px; font-weight: 500; line-height: 1.45;
        color: var(--ba-text); margin: 0 0 6px;
    }
    .ba-related-card-body .ba-date {
        font-size: 12px; color: var(--ba-text-faint);
    }

    /* Responsive */
    @media (max-width: 900px) {
        .ba-hero h1 { font-size: 28px; }
        .ba-hero-overlay { padding: 40px 20px 32px; }
        .ba-content-wrap { padding: 32px 16px 48px; }
        .ba-body { padding: 32px 24px; }
        .ba-related-grid { grid-template-columns: 1fr; }
    }
</style>
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
            <h2 class="ba-related-label">Flere innlegg</h2>
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

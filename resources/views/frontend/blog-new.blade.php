@extends('frontend.layout')

@section('page_title', 'Blogg — Forfatterskolen')
@section('meta_desc', 'Les artikler om skriving, forfatterliv og bokbransjen på Forfatterskolens blogg.')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pages/blog.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@stop

@section('content')
<div class="blog-redesign">
    <div class="bl-page-header">
        <div class="bl-eyebrow">Skriveblogg</div>
        <h1>La deg motivere og inspirere</h1>
        <p>Skrivetips og historier fra utgitte elever — til støtte og inspirasjon for deg som vil gjøre alvor av skrivedrømmen.</p>
    </div>

    <div class="bl-layout">
        <main>
            {{-- Featured blog post --}}
            @if($mainBlog)
            <a href="{{ route('front.read-blog', $mainBlog->id) }}" class="bl-featured" style="text-decoration:none;color:inherit;">
                <div class="bl-featured-img">
                    @if($mainBlog->image)
                        <img src="{{ asset($mainBlog->image) }}" alt="{{ $mainBlog->title }}">
                    @else
                        <div class="bl-avatar-lg">{{ strtoupper(substr($mainBlog->author_name ?: ($mainBlog->user->full_name ?? '?'), 0, 2)) }}</div>
                    @endif
                </div>
                <div class="bl-featured-body">
                    <div>
                        <span class="bl-tag">{{ $mainBlog->category ?? 'Blogg' }}</span>
                        <h2>{{ $mainBlog->title }}</h2>
                        <p class="bl-excerpt">
                            {!! strlen($mainBlog->description) > 200
                                ? substr(strip_tags(html_entity_decode($mainBlog->description)), 0, 200) . '...'
                                : strip_tags($mainBlog->description) !!}
                        </p>
                    </div>
                    <div class="bl-meta">
                        <div class="bl-avatar-sm">
                            @if($mainBlog->author_image ?: ($mainBlog->user->profile_image ?? null))
                                <img src="{{ asset($mainBlog->author_image ?: $mainBlog->user->profile_image) }}" alt="">
                            @else
                                {{ strtoupper(substr($mainBlog->author_name ?: ($mainBlog->user->full_name ?? '?'), 0, 2)) }}
                            @endif
                        </div>
                        <div class="bl-meta-info">
                            <div class="bl-meta-name">{{ $mainBlog->author_name ?: ($mainBlog->user->full_name ?? '') }}</div>
                            <div class="bl-meta-date">{{ \App\Http\FrontendHelpers::formatDate($mainBlog->created_at) }}</div>
                        </div>
                        <span class="bl-read-btn">Les mer →</span>
                    </div>
                </div>
            </a>
            @endif

            <h3 class="bl-section-label">Tidligere innlegg</h3>

            <div class="bl-grid" id="blog-grid">
                @include('frontend.blog-post-redesign')
            </div>

            @if($blogs->hasPages())
            <div class="bl-pagination">
                {{ $blogs->links() }}
            </div>
            @endif
        </main>

        <aside>
            <div class="bl-promo-block">
                <div class="bl-eyebrow">Neste start</div>
                <h4>Årskurs 2026</h4>
                <p>Et helt år med veiledning, fellesskap og faglig utvikling — for deg som vil bli forfatter på ordentlig.</p>
                <a href="{{ route('front.course.index') }}">Se mer og meld deg på</a>
            </div>

            <div class="bl-sidebar-block">
                <h4>Gratis skrivetips fra rektor</h4>
                <p>66 tips rett i innboksen. Ingen spam — kun inspirasjon og faglig påfyll.</p>
                <div class="bl-sidebar-form">
                    <input type="text" placeholder="Fornavn">
                    <input type="email" placeholder="E-postadresse">
                    <label>
                        <input type="checkbox">
                        Jeg aksepterer <a href="{{ route('front.terms', 'privacy-policy') }}" style="color:var(--bl-brand)">vilkårene</a>
                    </label>
                    <button>Ja, send meg tips!</button>
                </div>
            </div>
        </aside>
    </div>
</div>
@stop

@section('scripts')
@stop

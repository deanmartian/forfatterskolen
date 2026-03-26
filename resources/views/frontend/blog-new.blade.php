@extends('frontend.layout')

@section('title')
    <title>Blogg — Forfatterskolen</title>
@stop

@section('styles')
<style>
    /* ── Blog Redesign ── */
    .blog-redesign *, .blog-redesign *::before, .blog-redesign *::after { box-sizing: border-box; }
    :root {
        --bl-brand: #862736;
        --bl-brand-light: #f8edef;
        --bl-brand-mid: #d4a0a8;
        --bl-text: #1a1a1a;
        --bl-text-muted: #6b6b6b;
        --bl-text-faint: #aaa;
        --bl-bg: #ffffff;
        --bl-bg-soft: #f7f5f2;
        --bl-border: #e8e4e0;
    }
    .blog-redesign {
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--bl-bg-soft);
        color: var(--bl-text);
        font-size: 15px;
        line-height: 1.6;
    }

    /* Page header */
    .bl-page-header {
        background: #fff;
        border-bottom: 1px solid var(--bl-border);
        padding: 52px 40px 40px;
    }
    .bl-eyebrow {
        font-size: 11px; letter-spacing: 0.14em;
        text-transform: uppercase; color: var(--bl-brand);
        font-weight: 500; margin-bottom: 14px;
    }
    .bl-page-header h1 {
        font-family: 'Lora', serif;
        font-size: 30px; font-weight: 500;
        line-height: 1.35; color: var(--bl-text);
        max-width: 560px;
    }
    .bl-page-header p {
        margin-top: 12px; font-size: 15px;
        color: var(--bl-text-muted); max-width: 520px;
        line-height: 1.65;
    }

    /* Layout */
    .bl-layout {
        max-width: 1100px;
        margin: 0 auto;
        padding: 40px 40px;
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 48px;
    }

    /* Featured */
    .bl-featured {
        background: #fff;
        border: 1px solid var(--bl-border);
        border-radius: 10px;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        margin-bottom: 32px;
    }
    .bl-featured-img {
        background: var(--bl-brand-light);
        min-height: 280px;
        display: flex; align-items: center; justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .bl-featured-img img {
        width: 100%; height: 100%; object-fit: cover;
        position: absolute; inset: 0;
    }
    .bl-featured-img .bl-avatar-lg {
        width: 72px; height: 72px; border-radius: 50%;
        background: var(--bl-brand);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Lora', serif; font-size: 24px; color: #fff;
        position: relative; z-index: 1;
        border: 3px solid rgba(255,255,255,0.4);
    }
    .bl-featured-body {
        padding: 32px;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .bl-tag {
        display: inline-block;
        font-size: 10px; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--bl-brand);
        background: var(--bl-brand-light); padding: 3px 9px;
        border-radius: 3px; font-weight: 500; margin-bottom: 14px;
    }
    .bl-featured-body h2 {
        font-family: 'Lora', serif;
        font-size: 22px; font-weight: 500; line-height: 1.4;
        color: var(--bl-text); margin-bottom: 14px;
    }
    .bl-excerpt {
        font-size: 14px; line-height: 1.7;
        color: var(--bl-text-muted); margin-bottom: 24px;
    }
    .bl-meta {
        display: flex; align-items: center; gap: 10px;
        padding-top: 20px;
        border-top: 1px solid var(--bl-border);
    }
    .bl-avatar-sm {
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--bl-bg-soft); border: 1px solid var(--bl-border);
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 500; color: var(--bl-text-muted);
        flex-shrink: 0; overflow: hidden;
    }
    .bl-avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
    .bl-meta-info { flex: 1; }
    .bl-meta-name { font-size: 13px; font-weight: 500; color: var(--bl-text); }
    .bl-meta-date { font-size: 12px; color: var(--bl-text-faint); }
    .bl-read-btn {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 13px; font-weight: 500; color: var(--bl-brand);
        text-decoration: none;
        border: 1px solid var(--bl-brand-mid);
        padding: 7px 14px; border-radius: 4px;
        white-space: nowrap;
        transition: background 0.15s;
    }
    .bl-read-btn:hover { background: var(--bl-brand-light); color: var(--bl-brand); }

    /* Grid */
    .bl-section-label {
        font-size: 11px; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--bl-text-faint);
        font-weight: 500; margin-bottom: 16px;
        padding-bottom: 12px; border-bottom: 1px solid var(--bl-border);
    }
    .bl-grid {
        display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
    }
    .bl-card {
        background: #fff; border: 1px solid var(--bl-border);
        border-radius: 10px; overflow: hidden;
        transition: border-color 0.15s;
        text-decoration: none; color: inherit; display: block;
    }
    .bl-card:hover { border-color: var(--bl-brand-mid); }
    .bl-card-img {
        height: 120px; background: var(--bl-bg-soft);
        display: flex; align-items: center; justify-content: center;
        border-bottom: 1px solid var(--bl-border);
        overflow: hidden;
    }
    .bl-card-img img { width: 100%; height: 100%; object-fit: cover; }
    .bl-card-body { padding: 16px 18px; }
    .bl-card-body h3 {
        font-family: 'Lora', serif;
        font-size: 15px; font-weight: 500; line-height: 1.45;
        color: var(--bl-text); margin: 8px 0 8px;
    }
    .bl-card-body .bl-excerpt { font-size: 12.5px; margin-bottom: 14px; }
    .bl-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding-top: 12px; border-top: 1px solid var(--bl-border);
        font-size: 11.5px;
    }
    .bl-card-footer .bl-author { font-weight: 500; color: var(--bl-text-muted); }
    .bl-card-footer .bl-date { color: var(--bl-text-faint); }

    /* Sidebar */
    .bl-sidebar-block {
        background: #fff; border: 1px solid var(--bl-border);
        border-radius: 10px; padding: 24px; margin-bottom: 20px;
    }
    .bl-sidebar-block h4 {
        font-family: 'Lora', serif;
        font-size: 16px; font-weight: 500; margin-bottom: 8px;
        color: var(--bl-text);
    }
    .bl-sidebar-block p {
        font-size: 13px; color: var(--bl-text-muted); margin-bottom: 18px;
        line-height: 1.6;
    }
    .bl-sidebar-form input[type=text],
    .bl-sidebar-form input[type=email] {
        width: 100%; padding: 9px 12px;
        border: 1px solid var(--bl-border); border-radius: 5px;
        font-size: 13px; font-family: 'DM Sans', sans-serif;
        margin-bottom: 8px; color: var(--bl-text);
        background: var(--bl-bg-soft);
        outline: none;
    }
    .bl-sidebar-form input:focus { border-color: var(--bl-brand); background: #fff; }
    .bl-sidebar-form label {
        display: flex; align-items: flex-start; gap: 8px;
        font-size: 11.5px; color: var(--bl-text-faint); margin-bottom: 14px;
    }
    .bl-sidebar-form label input[type=checkbox] { margin-top: 2px; flex-shrink: 0; }
    .bl-sidebar-form button {
        width: 100%; background: var(--bl-brand); color: #fff;
        border: none; padding: 10px; border-radius: 5px;
        font-size: 13px; font-weight: 500; cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }
    .bl-sidebar-form button:hover { background: #6e1e2b; }

    .bl-promo-block {
        background: var(--bl-brand-light);
        border: 1px solid var(--bl-brand-mid);
        border-radius: 10px; padding: 24px; margin-bottom: 20px;
    }
    .bl-promo-block .bl-eyebrow { margin-bottom: 8px; }
    .bl-promo-block h4 {
        font-family: 'Lora', serif; font-size: 17px;
        font-weight: 500; color: var(--bl-text); margin-bottom: 8px;
    }
    .bl-promo-block p {
        font-size: 13px; color: var(--bl-text-muted);
        margin-bottom: 16px; line-height: 1.6;
    }
    .bl-promo-block a {
        display: block; text-align: center;
        background: var(--bl-brand); color: #fff;
        padding: 10px; border-radius: 5px;
        text-decoration: none; font-size: 13px; font-weight: 500;
    }
    .bl-promo-block a:hover { background: #6e1e2b; }

    .bl-pagination {
        margin-top: 28px;
        display: flex; gap: 8px;
    }
    .bl-pagination a, .bl-pagination span {
        padding: 7px 14px; border-radius: 4px;
        font-size: 13px; text-decoration: none;
        border: 1px solid var(--bl-border); color: var(--bl-text-muted);
        background: #fff;
    }
    .bl-pagination .active span {
        background: var(--bl-brand); color: #fff; border-color: var(--bl-brand);
    }
    .bl-pagination a:hover { border-color: var(--bl-brand-mid); }

    /* Responsive */
    @media (max-width: 900px) {
        .bl-layout { grid-template-columns: 1fr; padding: 24px 16px; gap: 32px; }
        .bl-featured { grid-template-columns: 1fr; }
        .bl-featured-img { min-height: 200px; }
        .bl-page-header { padding: 32px 16px 28px; }
        .bl-page-header h1 { font-size: 24px; }
    }
    @media (max-width: 600px) {
        .bl-grid { grid-template-columns: 1fr; }
    }
</style>
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

            <div class="bl-section-label">Tidligere innlegg</div>

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            var link = e.target.closest('.bl-pagination a');
            if (!link) return;
            e.preventDefault();
            var page = link.href.split('page=')[1];
            fetch('?page=' + page, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(html) {
                    document.getElementById('blog-grid').innerHTML = html;
                    window.scrollTo({ top: document.getElementById('blog-grid').offsetTop - 100, behavior: 'smooth' });
                });
        });
    });
</script>
@stop

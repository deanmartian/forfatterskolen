@extends('frontend.layout')

@section('title')
    <title>{{ $solution->title }} Articles &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="container">
        <div class="col-sm-12 margin-bottom articles">
            <div class="breadcrumb">
                <a href="{{ route('front.support') }}">{{ trans('site.front.support.support-text') }}</a> >
                &nbsp;<span>{{ $solution->title }}</span>
            </div>
            <h2 class="heading">{{ $solution->title }}</h2>
            <p class="info-text">
                {{ $solution->description }}
            </p>

            @if ($articles->count())
                <section class="article-list">
                    @foreach ($articles as $article)
                    <a href="{{ route('front.support-article',['id' => $solution->id, 'article_id' => $article->id]) }}"
                       class="article-row">
                        <h2>{{ $article->title }}</h2>
                        <p>
                            {{ strlen($article->details) > 180 ? substr(strip_tags($article->details),0,180).'...' :
                            strip_tags($article->details) }}
                        </p>
                    </a>
                    @endforeach
                </section>
            @endif
        </div>
    </div>
@stop
@extends('frontend.layout')

@section('title')
    <title>{{ $article->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="container">
        <div class="col-sm-12 margin-bottom articles">
            <div class="breadcrumb">
                <a href="{{ route('front.support') }}">{{ trans('site.front.support.support-text') }}</a> >
                &nbsp;<a href="{{ route('front.support-articles', $solution->id) }}">
                    <span>{{ $solution->title }}</span></a>
            </div>

            <div class="col-sm-12">
                <h2>{{ $article->title }}</h2> <br>
                {!! $article->details !!}
            </div>
        </div>
    </div>
@stop
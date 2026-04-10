@extends('editor.layout')

@section('page_title'){{ trans('site.admin-menu.webinars') }} &rsaquo; Forfatterskolen Admin@endsection

@section('page-title', trans('site.admin-menu.webinars'))

@section('content')

<div class="ed-section">
    <div class="ed-section__header">
        <h3 class="ed-section__title">
            Mine webinarer
            <span class="ed-section__count">{{ $webinars->count() }}</span>
        </h3>
    </div>
    <div class="ed-section__body ed-section__body--padded">
        <div class="ed-grid-3">
            @foreach($webinars as $webinar)
                <div class="ed-webinar-card">
                    <div class="ed-webinar-card__img">
                        @if($webinar->image)
                            <img src="{{ $webinar->image }}" alt="{{ $webinar->course->title ?? '' }}">
                        @else
                            <i class="fa fa-video-camera" style="color:white; opacity:0.3; font-size:32px;"></i>
                        @endif
                    </div>
                    <div class="ed-webinar-card__body">
                        <h4 class="ed-webinar-card__title">{{ $webinar->course->title ?? $webinar->title ?? '' }}</h4>
                        <div class="ed-webinar-card__meta">
                            <i class="fa fa-calendar"></i>
                            {{ $webinar->start_date ? \Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y \k\l. H:i') : '' }}
                        </div>
                        @if($webinar->presenter_url)
                            <a href="{{ $webinar->presenter_url }}" target="_blank" style="font-size:12px; word-break:break-all;">
                                {{ $webinar->presenter_url }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($webinars->isEmpty())
            <div style="padding:40px; text-align:center; color:var(--ink-muted); font-size:14px;">
                Ingen webinarer tildelt
            </div>
        @endif
    </div>
</div>

@stop

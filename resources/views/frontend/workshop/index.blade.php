@extends('frontend.layout')

@section('title')
    <title>Workshops &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <div class="workshop-page">
        <div class="container">
            <div class="row header">
                <div class="col-md-6">
                    <h1>
                        {{ trans('site.front.workshop.title') }}
                    </h1>
                    <p class="mt-5">
                        {{ trans('site.front.workshop.description') }}
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="row">
                        @foreach( $workshops as $workshop )
                            @if (!$workshop->is_free)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <img src="{{ $workshop->image }}" alt="">
                                        </div>
                                        <div class="card-body">
                                            <h2>{{ $workshop->title }}</h2>
                                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($workshop->description), 180)}}</p>

                                            <a href="{{ route('front.workshop.show', $workshop->id) }}" class="btn buy-btn">
                                                {{ trans('site.front.view') }}
                                            </a>
                                        </div>
                                        <div class="card-footer text-center">
                                            <div class="col-xs-6 border-right">
                                                <i class="img-icon16 icon-calendar"></i>
                                                <span>{{ \App\Http\FrontendHelpers::formatDate($workshop->date) }}</span>
                                            </div>
                                            <div class="col-xs-6">
                                                <i class="img-icon16 icon-clock"></i>
                                                <span>{{ \App\Http\FrontendHelpers::getTimeFromDT($workshop->date) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@extends('frontend.layout')

@section('title')
    <title>Konkurranser &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container learner-competition">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="font-barlow-regular d-block">
                        {{ trans('site.learner.competitions-text') }}
                    </h1>
                </div>
            </div>

            <div class="row">
                @foreach($competitions as $competition)
                    <div class="col-sm-12 col-md-6 col-lg-4 mt-5">
                        <div class="card card-global border-0">
                            <div class="card-header border-0 webinar-thumb">
                                <a href="{{ $competition->link }}">
                                    <div style="background-image: url({{ $competition->image }})"></div>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="competition-header">
                                    <h4>
                                        <i class="book"></i> {{ trans('site.front.genre') }}:
                                        <span class="theme-text">
                                            {{ $competition->genre ? \App\Http\FrontendHelpers::assignmentType($competition->genre) : '' }}
                                        </span>
                                    </h4>
                                    <h4>
                                        <i class="calendar"></i>
                                        {{ trans('site.learner.deadline') }}
                                        {{ ucwords(strtr(trans('site.learner.submission-date-value'), [
                                       '_date_' => \Carbon\Carbon::parse($competition->start_date)->format('d.m.Y'),
                                        '_time_' => \Carbon\Carbon::parse($competition->start_date)->format('H:i')
                                    ])) }}
                                    </h4>
                                </div> <!-- end competition-header -->

                                <p class="note-color my-4">
                                    {{ strlen($competition->description) > 250 ?  substr($competition->description,0,250)."..."
                                    : $competition->description }}
                                </p>
                            </div> <!-- end card-body -->
                            <div class="card-footer border-0 p-0">
                                <a class="btn site-btn-global-w-arrow w-100 rounded-0" href="{{ $competition->link }}"
                                   target="_blank">{{ trans('site.front.view') }}</a>
                            </div> <!-- end card-footer -->
                        </div> <!-- end card -->
                    </div> <!-- end col-md-4 -->
                @endforeach
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div>
@stop


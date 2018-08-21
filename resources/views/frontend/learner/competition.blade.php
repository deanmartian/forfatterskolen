@extends('frontend.layout')

@section('styles')
    <style>
        .course-meta {
            min-height: 245px !important;
        }

        .course-meta .btn {
            position: absolute;
            bottom: 25px;
            right: 27px;
        }

        .webinar-thumb img {
            height:155px;
            width: 100%;
            border-radius: 5px;
        }
    </style>
@stop

@section('title')
    <title>Konkurranser &rsaquo; Forfatterskolen</title>
@stop


@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">
                <h3 class="no-margin-top">Konkurranser</h3>

                <div class="row">
                    @foreach($competitions->chunk(3) as $competition_chunk)
                        <div class="col-sm-12">
                            @foreach($competition_chunk as $competition)
                                <div class="col-md-4">
                                    <div class="webinar-thumb">
                                        <a href="{{ $competition->link }}">
                                            <img src="{{ $competition->image }}" alt="" class="img img-responsive">
                                        </a>
                                    </div>
                                    <div class="dashboard-courses" style="padding-top: 40px">

                                        <div class="course-meta">
                                            <div style="margin-bottom: 3px;"><strong style="font-size: 16px;">{{ $competition->title }}</strong>
                                                <br>
                                            Sjanger: <i>{{ $competition->genre ? \App\Http\FrontendHelpers::assignmentType($competition->genre) : '' }}</i>
                                            </div>
                                            <div style="margin-bottom: 7px;">
                                                <i class="fa fa-calendar"></i>
                                                Frist
                                                {{ \Carbon\Carbon::parse($competition->start_date)->format('d.m.Y') }}
                                                Klokken
                                                {{ \Carbon\Carbon::parse($competition->start_date)->format('H:i') }}
                                            </div>
                                            <p class="margin-bottom">
                                                {{ strlen($competition->description) > 250 ?  substr($competition->description,0,250)."..."
                                                : $competition->description }}
                                            </p>

                                            <div class="text-right margin-top">
                                                <a class="btn btn-warning" href="{{ $competition->link }}" target="_blank">Les mer</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
        <div class="clearfix"></div>
    </div>

@stop


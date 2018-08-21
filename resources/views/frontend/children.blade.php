@extends('frontend.layout')

@section('title')
    <title>SOS Children</title>
@stop

@section('content')
    <div class="container" id="children-container">
        <div class="children-hero text-center">
            <div class="row">
                <div class="col-sm-12">
                    <h2>
                        <span class="highlight">SOS</span> Barnebyer
                    </h2>
                </div>
            </div>
        </div>

        <div class="text-center children-description">
            @if($mainDescription)
                {!! $mainDescription->description !!}
            @endif
        </div>

        <div class="first-document">
            @if($primaryVideo)
            <div class="pull-left left-content">
            <iframe src="{{ $primaryVideo->video_url }}?embedType=legacy_api&videoFoam=true&videoWidth=720&chromeless=false&controlsVisibleOnLoad=true&playbar=true&fullscreenButton=true&playerColor=2ca0d9"
                    frameborder="0" height="384" width="681" allowfullscreen="true"></iframe>
            </div>
            <div class="pull-left right-content">
                <h2>{{ $primaryVideo->title }}</h2>
                <p class="text-justify">
                    {{ $primaryVideo->description }}
                </p>
            </div>
            @endif
        </div>

        <div class="clearfix"></div>

        <div class="documents-section">
            <div class="heading">
                <span class="heading-title">DOKUMENTAR</span>
            </div>

            <div class="clearfix"></div>

            <div class="document-list-container">

                @foreach($videoRecords as $videoRecord)
                    @if(!$videoRecord->is_primary)
                        <div class="document-list">
                            <div class="col-sm-4 video-container">
                                <iframe src="{{ $videoRecord->video_url }}?embedType=legacy_api&videoFoam=true&videoWidth=720&chromeless=false&controlsVisibleOnLoad=true&playbar=true&fullscreenButton=true&playerColor=2ca0d9"
                                        allowfullscreen="true"></iframe>
                            </div>
                            <div class="col-sm-8 video-description-container">
                                <span class="video-title">{{ $videoRecord->title }}</span>
                                <p class="video-description">
                                    {{ $videoRecord->description }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="text-center children-description">
                @if($mainDescription)
                    {!! $mainDescription->bottom_description !!}
                @endif
            </div>
        </div>
    </div>
@stop
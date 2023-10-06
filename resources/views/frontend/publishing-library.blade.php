@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Publishing</title>
@stop


@section('content')
    <div class="publishing-page-new" id="app-container">
        <div class="header" data-bg="https://www.forfatterskolen.no/images-new/publishing-bg.png">
            <div class="container">
                <h1 class="text-center">
                    {{ trans('site.front.publishing.title') }}
                </h1>

                <p class="mb-0">
                    {{ trans('site.front.publishing.main-description') }}
                </p>
            </div>
        </div>

        <div class="details-wrapper">
            <div class="container">
                <h3>
                    {{ trans('site.front.publishing.second-description') }}
                </h3>

                <publishing-list :books="{{ json_encode($books) }}"></publishing-list>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ asset('/js/app.js?v='.time()) }}"></script>
@stop
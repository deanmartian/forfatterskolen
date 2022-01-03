@extends('backend.layout')

@section('title')
    <title>News &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{asset('vendor/laraberg/css/laraberg.css')}}">
    <style>
        .components-editor-notices__dismissible {
            display: none;
        }

    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> News</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <div class="container">
            <form action="{{ route('admin.news.save') }}" method="POST">
                {{ csrf_field() }}
                <textarea id="news_editor" name="details" hidden>
                    {{ $model ? $model->getRawContent() : '' }}
                </textarea>

                <button class="btn btn-success pull-right mt-2" type="submit">
                    {{ trans('site.submit') }}
                </button>
            </form>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://unpkg.com/react@16.8.6/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@16.8.6/umd/react-dom.production.min.js"></script>
    <script src="{{ asset('vendor/laraberg/js/laraberg.js') }}"></script>
    <script>
        Laraberg.init('news_editor', { laravelFilemanager: true });
        // un-register the image gallery because of error
        let blocks = [
            "core/gallery",
        ];
        document.addEventListener("DOMContentLoaded", () => {
            window.wp.data.dispatch('core/blocks').removeBlockTypes(blocks);
        });
    </script>
@stop
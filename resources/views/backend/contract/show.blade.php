@extends('backend.layout')

@section('title')
    <title>{{ $contract->title }} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <style>
        body {
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        }
        .container {
            max-width: 900px;
        }

        .top-image {
            width: 100%;
        }

        .float-left {
            float: left;
        }

        .float-right {
            float: right;
        }
    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ route('admin.contract.index') }}" class="btn btn-default" style="margin-right: 10px">
            << {{ trans('site.back') }}
        </a>

        <h3><em>{{ $contract->title }}</em></h3>
    </div>

    <div class="container padding-top">
        <div class="panel panel-default" style="padding: 20px">
            @if ($contract->image)
                <img src="{{ asset($contract->image) }}" alt="" class="top-image">
            @endif

            {!! $contract->details !!}

            <div class="float-left">
                <h4>
                    {{ $contract->signature_label }}
                </h4>
                <img src="{{ asset($contract->admin_signature) }}" style="height: 100px">

                <div>
                    <h4>
                        {{ trans('site.front.form.name') }}: {{ $contract->admin_name }}
                    </h4>
                    <h4>
                        {{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->admin_signed_date) }}
                    </h4>
                </div>
            </div>

            <div class="float-right">
                <h4>
                    {{ $contract->signature_label }}
                </h4>
                <img src="{{ asset($contract->signature) }}" style="height: 100px">

                <div>
                    <h4>
                        {{ trans('site.front.form.name') }}: {{ $contract->receiver_name }}
                    </h4>
                    <h4>
                        {{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->signed_date) }}
                    </h4>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
@stop
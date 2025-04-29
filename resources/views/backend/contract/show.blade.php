@extends($layout)

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
        <a href="{{ $backRoute }}" class="btn btn-default" style="margin-right: 10px">
            << {{ trans('site.back') }}
        </a>

        <h3><em>{{ $contract->title }}</em></h3>
    </div>

    <div class="container padding-top">
        <div class="panel panel-default" style="padding: 20px">
            @if ($contract->image)
                <img src="{{ asset($contract->image) }}" alt="" class="top-image">
            @endif

            @php
            $contractDetails = $contract->details;

            if($contract->project_id) {
                $project = $contract->project;
                $name = $contract->receiver_name;
                $address = $project->user->full_address;
                $sendDate = FrontendHelpers::formatDate($contract->send_date);
                $adminName = $contract->admin_name;
                $adminSignature = "<img src='".asset($contract->admin_signature)."' class='admin-signature'>";
                $userSignature = $contract->signature ? "<img src='" . asset($contract->signature) . "' class='user-signature'>" 
                    : '[user_signature]';

                $contractDetails = str_replace([
                    '[name]',
                    '[address]',
                    '[send_date]',
                    '[user_name]',
                    '[admin_name]',
                    '[admin_signature]',
                    '[user_signature]'
                ], [
                    $name,
                    $address,
                    $sendDate,
                    $name,
                    $adminName,
                    $adminSignature,
                    $userSignature
                ], $contractDetails);
            }
        @endphp
        {!! $contractDetails !!}

            @if($contract->is_file)
                <iframe src="{{ $contract->signed_file }}" frameborder="0" width="100%" height="800" allowfullscreen></iframe>
            @else
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
            @endif

            <div class="clearfix"></div>
        </div>
    </div>
@stop
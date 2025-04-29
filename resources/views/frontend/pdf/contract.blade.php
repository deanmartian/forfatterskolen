<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>{{ $contract->title }}</title>
    <style>

        blockquote {
            padding: 10px 20px;
            margin: 0 0 20px;
            font-size: 17.5px;
            border-left: 5px solid #eee;
        }

        p {
            margin-bottom: 10px;
        }

        .top-image {
            width: 100%;
            height: 250px;
        }

        .float-left {
            float:left
        }

        .float-right {
            float: right;
        }

        .admin-signature {
            height: 150px;
            width: 272px;
            object-fit: contain;
            margin-left: -18px;
            /* margin-top: -15px; */
        }
    </style>
</head>

<body>
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

    @if ($contract->signature)
        <div class="float-left">
            <p>
                {{ $contract->signature_label }}
            </p>
            <img src="{{ asset($contract->admin_signature) }}" style="height: 100px; margin-top: 7px">

            <div>
                <p style="margin-top: 0">
                    {{ trans('site.front.form.name') }}: {{ $contract->admin_name }}
                </p>
                <p style="margin-top: 0">
                    {{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->admin_signed_date) }}
                </p>
            </div>
        </div>

        <div class="float-right">
            <p>
                {{ $contract->signature_label }}
            </p>

            <img src="{{ asset($contract->signature) }}" style="height: 100px; margin-top: 7px">

            <div>
                <p style="margin-top: 0">
                    {{ trans('site.front.form.name') }}: {{ $contract->receiver_name }}
                </p>
                <p style="margin-top: 0">
                    {{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->signed_date) }}
                </p>
            </div>
        </div>

        <div class="clearfix"></div>

    @endif
</body>

</html>
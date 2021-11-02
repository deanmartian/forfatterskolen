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
    </style>
</head>

<body>
@if ($contract->image)
    <img src="{{ asset($contract->image) }}" alt="" class="top-image">
@endif
{!! $contract->details !!}

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
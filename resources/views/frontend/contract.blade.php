<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forfatterskolen</title>

    <link rel="stylesheet" href="{{asset('css/vendor-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/font-awesome/css/font-awesome.min.css')}}">
    <style>
        body {
            margin: 0;
            font-size: 15px;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        }

        .actions-bar {
            background-color: #fff;
            border-bottom: 1px solid #d7e2ea;
            box-shadow: 0 0 16px 0 rgba(19,48,66,.075);
            color: #172336;
            left: 0;
            position: sticky;
            right: 0;
            top: 0;
            z-index: 10;
        }

        .actions-bar .content {
            -ms-flex-align: center;
            align-items: center;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -ms-flex-pack: center;
            justify-content: center;
            max-width: 918px;
            min-height: 52px;
            padding: 4px;
        }

        .content {
            width: 100%;
            max-width: 1100px;
            padding: 18px;
            margin: 0 auto;
        }

        .actions-bar .content .bar-block {
            padding: 4px;
        }

        .button-group, .button-group .button-group_content {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
        }

        .button-group.isMerged .button-group_content {
            margin: 0;
        }
        .button-group .button-group_content {
            border-color: inherit;
            margin: -4px;
            height: 100%;
        }

        .actions-bar .content .bar-block a {
            background-color: #f4f8fc;
            border-color: #d7e2ea;
            box-shadow: none;
            color: #5a6f90;
        }

        .actions-bar .content .bar-block a:hover {
            background-color: #edf2f7;
            color: #172336;
        }

        .button-group .button {
            margin: 0;
            margin-left: -1px;
            border-radius: 0;
            -ms-flex-pack: center;
            justify-content: center;
            -ms-flex: 0 auto;
            flex: 0 auto;
            white-space: normal;
            padding: 11px;
            border: 1px solid #616b84;
        }

        .button-group .button:first-child{
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .button-group .button:last-child{
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }

        .block-body {
            max-width: 900px;
            margin: 0 auto;
        }

        .block-body .top-image {
            width: 100%;
        }

        .signature-wrapper {
            margin-top: 10px;
        }

        .signature {
            margin-right: 14px;
            display: inline-block;
            vertical-align: top;
        }

        .signature-canvas {
            background-color: #fff;
            border: 1px solid #d7e2ea;
            border-radius: 3px;
            padding: 9px;
            position: relative;
            height: 50px;
            width: 145px;
            display: flex;
            page-break-inside: avoid;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .signature-canvas::before {
            content: "";
            border-top-style: dashed;
            border-top-width: 1px;
            border-top-color: inherit;
            position: absolute;
            bottom: 25px;
            left: 0;
            right: 0;
        }

        .button-green {
            color: #fff !important;
            background-color: #4dbf39;
            border: 1px solid #d6eed1;
            border-radius: 5px;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,.1);
            padding: 11px;
            min-height: 18px;
            position: relative;
            cursor: pointer;
            transition: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            display: -ms-inline-flexbox;
            display: inline-flex;
            -ms-flex-align: center;
            align-items: center;
            text-align: initial;
        }

        .button-green:hover {
            background-color: #48ab36;
        }

        .link-content {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            margin: -4px 0;
            position: relative;
            line-height: normal;
            opacity: .7;
        }

        .fa-arrow-right {
            margin-right: 10px;
        }

        .disabled, .disabled *, :disabled, :disabled * {
            pointer-events: none;
        }

        .contract-options button {
            text-decoration: none;
            display: block;
            text-align: left;
            border-radius: 0;
            background-color: #fff;
        }

        h2 {
            font-size: 1.55em;
        }

        .btn {
            font-size: 1.2rem;
        }

        .modal .close {
            font-size: 2rem;
        }

        .alert-dismissible {
            z-index: 9;
            min-width: 300px;
            position: fixed;
            top: 60px;
            right: 0;
        }

        /* Styles for signature plugin v1.2.0. */
        .kbw-signature {
            display: inline-block;
            border: 1px solid #a0a0a0;
            -ms-touch-action: none;
        }
        .kbw-signature-disabled {
            opacity: 0.35;
        }

        .admin-signature {
            height: 200px;
            width: 272px;
            object-fit: contain;
            margin-left: -18px;
            margin-top: -45px;
        }

        @media print {
            .actions-bar, .signature-note-container {
                display: none;
            }
        }

    </style>

</head>
<body>

<div class="page-content">
    <div class="actions-bar">
        <div class="content">
            <div class="bar-block">
                <div class="button-group">
                    <div class="button-group_content">
                        <a href="{{ route('front.contract.download', $contract->code) }}" class="button">
                            <i class="fa fa-download"></i>
                        </a>
                        <a href="javascript:void(0)" class="button" onclick="printPage()">
                            <i class="fa fa-print"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block-body">

        @if ($contract->image)
            <img src="{{ asset($contract->image) }}" alt="" class="top-image">
        @endif

        @php
            $contractDetails = $contract->details;

            if($contract->project_id) {
                $project = $contract->project;
                $name = $contract->receiver_name; //$project->user->full_name;
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


            <div style="padding: 30px 0; width: auto">
                @if (!$contract->signature)
                    <h2>
                        {{ $contract->signature_label }}
                    </h2>
                    <div class="signature-note-container">
                        <div style="margin-top: 2px">{!! trans('site.contract.signature-note') !!}</div>

                        <div class="signature-wrapper">
                            <div class="signature">
                                <div class="signature-canvas">
                                    <div class="signature-cta">
                                        <a class="button button-green" data-target="#signContractModal" data-toggle="modal">
                                            <div class="link-content">
                                                <i class="fa fa-arrow-right"></i><span>Sign here</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else

                    <div class="float-left">
                        <h2>
                            {{ $contract->signature_label }}
                        </h2>
                        <img src="{{ asset($contract->admin_signature) }}" style="height: 100px">

                        <div>
                            <h3>
                                {{ trans('site.front.form.name') }}: {{ $contract->admin_name }}
                            </h3>
                            <h3>
                                {{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->admin_signed_date) }}
                            </h3>
                        </div>
                    </div>

                    <div class="float-right">
                        <h2>
                            {{ $contract->signature_label }}
                        </h2>
                        <img src="{{ asset($contract->signature) }}" style="height: 100px">

                        <div>
                            <h3>
                                {{ trans('site.front.form.name') }}: {{ $contract->receiver_name }}
                            </h3>
                            <h3>
                                {{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->signed_date) }}
                            </h3>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                @endif
            </div>

    </div>
</div>

<div id="signContractModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><em>Sign Contract</em></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('front.contract.sign', $contract->code) }}">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <label class="" for="">Signature:</label>
                        <br/>
                        <div id="sig" ></div>
                        <br/>
                        <button id="clear" class="btn btn-danger">Clear Signature</button>
                        <textarea id="signature64" name="signed" style="display: none"></textarea>
                    </div>

                    <button class="btn btn-success mt-3 float-right">{{ trans('site.save') }}</button>
                </form>
            </div>
        </div>

    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" style="font-size: 2rem">×</button>
        <strong>{{ $message }}</strong>
    </div>
@endif

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.signature.js') }}"></script>
<script>
    function printPage() {
        window.print();
    }

    let sig = $('#sig').signature({syncField: '#signature64', syncFormat: 'PNG'});
    $('#clear').click(function(e) {
        e.preventDefault();
        sig.signature('clear');
        $("#signature64").val('');
    });
</script>

</body>
</html>
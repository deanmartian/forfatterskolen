@extends($layout)

@section('page_title', $contract->title . ' &rsaquo; Forfatterskolen Admin')

@section('styles')
    <style>
        body { font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; }
        .container { max-width: 900px; }
        .top-image { width: 100%; }
        .float-left { float: left; }
        .float-right { float: right; }
        .contract-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .contract-meta dt { color: #666; font-size: 12px; text-transform: uppercase; }
        .contract-meta dd { margin-bottom: 8px; font-weight: 500; }
    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ $backRoute }}" class="btn btn-default" style="margin-right: 10px">
            << {{ trans('site.back') }}
        </a>

        <h3><em>{{ $contract->title }}</em></h3>

        <div class="navbar-form navbar-right">
            <span class="label label-{{ $contract->status_badge }}" style="font-size: 14px; padding: 6px 12px;">
                {!! $contract->status_label !!}
            </span>

            @if($contract->end_date && $contract->signature)
                <a href="{{ route('admin.contract.create', ['renew_from' => $contract->id]) }}"
                   class="btn btn-success btn-sm" style="margin-left: 10px">
                    <i class="fa fa-refresh"></i> Forny kontrakt
                </a>
            @endif

            @if($contract->is_file)
                <a href="{{ $contract->signed_file }}" class="btn btn-info btn-sm" download style="margin-left: 5px">
                    <i class="fa fa-download"></i> Last ned PDF
                </a>
            @else
                <a href="{{ route('admin.contract.download-pdf', $contract->id) }}" class="btn btn-info btn-sm" style="margin-left: 5px">
                    <i class="fa fa-download"></i> Last ned PDF
                </a>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="container padding-top">
        {{-- Contract metadata --}}
        <div class="contract-meta">
            <div class="row">
                <div class="col-sm-3">
                    <dl>
                        <dt>Type</dt>
                        <dd>
                            <span class="label label-{{ $contract->contract_type == 'firma' ? 'primary' : ($contract->contract_type == 'person' ? 'info' : 'default') }}">
                                {{ $contract->contract_type_label }}
                            </span>
                        </dd>
                    </dl>
                </div>
                <div class="col-sm-3">
                    <dl>
                        <dt>Mottaker</dt>
                        <dd>{{ $contract->receiver_name ?: '-' }}</dd>
                    </dl>
                </div>
                <div class="col-sm-3">
                    <dl>
                        <dt>Timepris</dt>
                        <dd>{{ $contract->timepris ? number_format($contract->timepris, 0, ',', ' ').' kr/t' : '-' }}</dd>
                    </dl>
                </div>
                <div class="col-sm-3">
                    <dl>
                        <dt>Periode</dt>
                        <dd>
                            @if($contract->start_date || $contract->end_date)
                                {{ $contract->start_date ? $contract->start_date->format('d.m.Y') : '' }}
                                -
                                {{ $contract->end_date ? $contract->end_date->format('d.m.Y') : '' }}
                            @else
                                -
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
            @if($contract->contract_type == 'firma' && $contract->org_nr)
                <div class="row">
                    <div class="col-sm-3">
                        <dl>
                            <dt>Org.nr</dt>
                            <dd>{{ $contract->org_nr }}</dd>
                        </dl>
                    </div>
                </div>
            @endif
            @if($contract->renewed_from_id)
                <div class="row">
                    <div class="col-sm-12">
                        <small class="text-muted">
                            <i class="fa fa-refresh"></i> Fornyet fra kontrakt #{{ $contract->renewed_from_id }}
                        </small>
                    </div>
                </div>
            @endif
        </div>

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
                    <h4>{{ $contract->signature_label }}</h4>
                    <img src="{{ asset($contract->admin_signature) }}" style="height: 100px">
                    <div>
                        <h4>{{ trans('site.front.form.name') }}: {{ $contract->admin_name }}</h4>
                        <h4>{{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->admin_signed_date) }}</h4>
                    </div>
                </div>

                <div class="float-right">
                    <h4>{{ $contract->signature_label }}</h4>
                    <img src="{{ asset($contract->signature) }}" style="height: 100px">
                    <div>
                        <h4>{{ trans('site.front.form.name') }}: {{ $contract->receiver_name }}</h4>
                        <h4>{{ trans('site.date') }}: {{ \App\Http\FrontendHelpers::formatDate($contract->signed_date) }}</h4>
                    </div>
                </div>
            @endif

            <div class="clearfix"></div>
        </div>
    </div>
@stop

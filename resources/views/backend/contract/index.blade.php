@extends('backend.layout')
@section('uses-tinymce', true)

@section('page_title', 'Admins &rsaquo; Kontrakter')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .stat-box {
            background: #fff;
            border-radius: 6px;
            padding: 18px;
            text-align: center;
            border: 1px solid #e3e8ee;
            margin-bottom: 15px;
        }
        .stat-box .stat-number {
            font-size: 28px;
            font-weight: bold;
            display: block;
        }
        .stat-box .stat-label {
            color: #777;
            font-size: 13px;
            text-transform: uppercase;
        }
        .stat-box.stat-danger { border-left: 4px solid #d9534f; }
        .stat-box.stat-warning { border-left: 4px solid #f0ad4e; }
        .stat-box.stat-success { border-left: 4px solid #5cb85c; }
        .stat-box.stat-info { border-left: 4px solid #5bc0de; }

        .expiry-row-expired td { background-color: #fdf2f2 !important; }
        .expiry-row-expiring td { background-color: #fef9e7 !important; }

        .filter-bar {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .filter-bar .form-group {
            margin-bottom: 0;
            margin-right: 10px;
        }
        .expiring-widget {
            background: #fff;
            border: 1px solid #f0ad4e;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .expiring-widget h4 {
            margin-top: 0;
            color: #f0ad4e;
        }
        .expiring-widget .list-group-item {
            border-left: 3px solid #f0ad4e;
        }
        .btn-actions .btn {
            margin-right: 3px;
            margin-bottom: 3px;
        }
    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-handshake-o"></i> Kontrakter</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">

        <ul class="nav nav-tabs margin-top">
            <li @if( !in_array(Request::input('tab'), ['template'])) class="active" @endif>
                <a href="?tab=list">
                    Oversikt
                </a>
            </li>
            <li @if( Request::input('tab') == 'template' ) class="active" @endif>
                <a href="?tab=template">
                    Maler
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade in active">
                @if( Request::input('tab') == 'template' )

                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <button type="button"
                                    class="btn btn-success margin-bottom contractTemplateBtn" data-toggle="modal"
                                    data-target="#contractTemplateModal"
                                    data-action="{{ route('admin.contract-template.save') }}"
                            >
                                <i class="fa fa-plus"></i> Ny mal
                            </button>

                            <div class="alert alert-info" style="margin-top: 10px">
                                <strong>Plassholdere:</strong>
                                @foreach(\App\ContractTemplate::placeholders() as $key => $desc)
                                    <code>{{ $key }}</code> = {{ $desc }}&nbsp;&nbsp;
                                @endforeach
                            </div>

                            <div class="table-users table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Navn</th>
                                        <th>Vis i prosjekt</th>
                                        <th width="250"></th>
                                    </tr>
                                    </thead>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td>{{ $template->title }}</td>
                                            <td>
                                                @if($template->show_in_project)
                                                    <span class="label label-success">Ja</span>
                                                @else
                                                    <span class="label label-default">Nei</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.contract.create', ['template_id' => $template->id]) }}"
                                                   class="btn btn-success btn-xs">
                                                    <i class="fa fa-plus"></i> Opprett kontrakt
                                                </a>
                                                <button class="btn btn-primary btn-xs contractTemplateBtn"
                                                        data-toggle="modal"
                                                        data-target="#contractTemplateModal"
                                                        data-action="{{ route('admin.contract-template.save', $template->id) }}"
                                                        data-fields="{{ json_encode($template) }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger btn-xs deleteContractTemplateBtn"
                                                        data-toggle="modal"
                                                        data-target="#deleteContractTemplateModal"
                                                        data-action="{{ route('admin.contract-template.delete', $template->id) }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Dashboard stats --}}
                    <div class="row" style="margin-top: 15px">
                        <div class="col-md-2 col-sm-4 col-xs-6">
                            <div class="stat-box stat-info">
                                <span class="stat-number">{{ $stats['total'] }}</span>
                                <span class="stat-label">Totalt</span>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6">
                            <a href="?status_filter=signed" style="text-decoration:none;color:inherit">
                                <div class="stat-box stat-success">
                                    <span class="stat-number">{{ $stats['signed'] }}</span>
                                    <span class="stat-label">Signert</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-6">
                            <a href="?status_filter=unsigned" style="text-decoration:none;color:inherit">
                                <div class="stat-box">
                                    <span class="stat-number">{{ $stats['unsigned'] }}</span>
                                    <span class="stat-label">Usignert</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <a href="?status_filter=expiring" style="text-decoration:none;color:inherit">
                                <div class="stat-box stat-warning">
                                    <span class="stat-number">{{ $stats['expiring'] }}</span>
                                    <span class="stat-label">Utl&oslash;per snart</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <a href="?status_filter=expired" style="text-decoration:none;color:inherit">
                                <div class="stat-box stat-danger">
                                    <span class="stat-number">{{ $stats['expired'] }}</span>
                                    <span class="stat-label">Utl&oslash;pt</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Expiring soon widget --}}
                    @if($expiringSoon->count() > 0)
                        <div class="expiring-widget">
                            <h4><i class="fa fa-exclamation-triangle"></i> Kontrakter som utl&oslash;per snart</h4>
                            <div class="list-group">
                                @foreach($expiringSoon as $exp)
                                    <div class="list-group-item">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <strong>{{ $exp->title }}</strong>
                                                <br><small>{{ $exp->receiver_name }}</small>
                                            </div>
                                            <div class="col-sm-3">
                                                <span class="label label-{{ $exp->end_date->diffInDays(now()) <= 30 ? 'danger' : 'warning' }}">
                                                    Utl&oslash;per {{ $exp->end_date->format('d.m.Y') }}
                                                    ({{ $exp->end_date->diffInDays(now()) }} dager)
                                                </span>
                                            </div>
                                            <div class="col-sm-5 text-right">
                                                <a href="{{ route('admin.contract.create', ['renew_from' => $exp->id]) }}"
                                                   class="btn btn-success btn-xs">
                                                    <i class="fa fa-refresh"></i> Forny
                                                </a>
                                                <a href="{{ route('admin.contract.show', $exp->id) }}"
                                                   class="btn btn-info btn-xs">
                                                    <i class="fa fa-eye"></i> Vis
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">

                            {{-- Action buttons --}}
                            <div style="margin-bottom: 15px">
                                <a class="btn btn-success" href="{{ route('admin.contract.create') }}">
                                    <i class="fa fa-plus"></i> Ny kontrakt
                                </a>
                                <div class="btn-group">
                                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-file-text-o"></i> Fra mal <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach(\App\ContractTemplate::all() as $tmpl)
                                            <li>
                                                <a href="{{ route('admin.contract.create', ['template_id' => $tmpl->id]) }}">
                                                    {{ $tmpl->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Filter bar --}}
                            <form method="GET" class="filter-bar form-inline">
                                <div class="form-group">
                                    <select name="type" class="form-control input-sm">
                                        <option value="">Alle typer</option>
                                        <option value="firma" {{ request('type') == 'firma' ? 'selected' : '' }}>Firma</option>
                                        <option value="person" {{ request('type') == 'person' ? 'selected' : '' }}>Person</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="status_filter" class="form-control input-sm">
                                        <option value="">Alle statuser</option>
                                        <option value="draft" {{ request('status_filter') == 'draft' ? 'selected' : '' }}>Utkast</option>
                                        <option value="sent" {{ request('status_filter') == 'sent' ? 'selected' : '' }}>Sendt</option>
                                        <option value="signed" {{ request('status_filter') == 'signed' ? 'selected' : '' }}>Signert</option>
                                        <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Aktiv</option>
                                        <option value="expiring" {{ request('status_filter') == 'expiring' ? 'selected' : '' }}>Utl&oslash;per snart</option>
                                        <option value="expired" {{ request('status_filter') == 'expired' ? 'selected' : '' }}>Utl&oslash;pt</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-default btn-sm">
                                    <i class="fa fa-filter"></i> Filtrer
                                </button>
                                @if(request('type') || request('status_filter'))
                                    <a href="{{ route('admin.contract.index') }}" class="btn btn-link btn-sm">Nullstill</a>
                                @endif
                            </form>

                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Kontrakt</th>
                                    <th>Type</th>
                                    <th>Mottaker</th>
                                    <th>Timepris</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contracts as $contract)
                                    @php
                                        $rowClass = '';
                                        if ($contract->isExpired()) $rowClass = 'expiry-row-expired';
                                        elseif ($contract->isExpiringSoon()) $rowClass = 'expiry-row-expiring';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>
                                            <a href="{{ $contract->signature ? route('admin.contract.show', $contract->id) :
                                                route('admin.contract.edit', $contract->id) }}">
                                                {{ $contract->title }}
                                            </a>
                                            @if($contract->renewed_from_id)
                                                <br><small class="text-muted"><i class="fa fa-refresh"></i> Fornyet</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="label label-{{ $contract->contract_type == 'firma' ? 'primary' : ($contract->contract_type == 'person' ? 'info' : 'default') }}">
                                                {{ $contract->contract_type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $contract->receiver_name }}
                                            @if($contract->receiver_email)
                                                <br><small class="text-muted">{{ $contract->receiver_email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contract->timepris)
                                                {{ number_format($contract->timepris, 0, ',', ' ') }} kr/t
                                            @endif
                                        </td>
                                        <td>
                                            @if($contract->start_date || $contract->end_date)
                                                @if($contract->start_date) {{ $contract->start_date->format('d.m.Y') }} @endif
                                                -
                                                @if($contract->end_date)
                                                    {{ $contract->end_date->format('d.m.Y') }}
                                                    @if($contract->isExpired())
                                                        <br><small class="text-danger"><i class="fa fa-exclamation-circle"></i> Utl&oslash;pt</small>
                                                    @elseif($contract->isExpiringSoon())
                                                        <br><small class="text-warning"><i class="fa fa-clock-o"></i> {{ $contract->end_date->diffInDays(now()) }} dager igjen</small>
                                                    @endif
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="label label-{{ $contract->status_badge }}">
                                                {!! $contract->status_label !!}
                                            </span>
                                        </td>
                                        <td class="btn-actions text-right" style="white-space: nowrap">
                                            @if (!$contract->signature && $contract->receiver_email)
                                                <form method="POST" action="{{ route('admin.contract.send-contract', $contract->id) }}"
                                                      style="display:inline"
                                                      onsubmit="return confirm('Send p&aring;minnelse til {{ $contract->receiver_email }}?')">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="name" value="{{ $contract->receiver_name }}">
                                                    <input type="hidden" name="email" value="{{ $contract->receiver_email }}">
                                                    <input type="hidden" name="subject" value="P&aring;minnelse: {{ $contract->title }}">
                                                    <input type="hidden" name="message" value="P&aring;minnelse: Din kontrakt ({{ $contract->title }}) venter p&aring; signering.">
                                                    <button type="submit" class="btn btn-warning btn-xs" title="Send p&aring;minnelse">
                                                        <i class="fa fa-bell"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($contract->signature && $contract->end_date)
                                                <a href="{{ route('admin.contract.create', ['renew_from' => $contract->id]) }}"
                                                   class="btn btn-success btn-xs" title="Forny kontrakt">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                            @endif

                                            @if ($contract->signature)
                                                @if($contract->is_file)
                                                    <a href="{{ $contract->signed_file }}" class="btn btn-info btn-xs" download title="Last ned PDF">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.contract.download-pdf', $contract->id) }}" class="btn btn-info btn-xs" title="Last ned PDF">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            @if (Auth::user()->isSuperUser())
                                                <input type="checkbox" data-toggle="toggle" data-on="Vis" data-off="Skjul" data-size="mini" name="status"
                                                       class="status-toggle" data-id="{{ $contract->id }}"
                                                       @if ($contract->status === 1) checked @endif>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="pull-right">
                                {{ $contracts->appends(request()->except('page'))->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Contract Template Modal --}}
    <div id="contractTemplateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Kontraktmal</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" class="form-control" name="title" value="" required>
                        </div>

                        <div class="alert alert-info">
                            <strong>Tilgjengelige plassholdere:</strong><br>
                            @foreach(\App\ContractTemplate::placeholders() as $key => $desc)
                                <code>{{ $key }}</code> = {{ $desc }}&nbsp;&nbsp;
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.details') }}</label>
                            <textarea name="details" rows="12" class="form-control editor" id="editContentEditor"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Signatur-etikett</label>
                            <input type="text" name="signature_label" value="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Vis i prosjekt?</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" name="show_in_project">
                        </div>

                        <button type="submit" class="btn btn-success pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Template Modal --}}
    <div id="deleteContractTemplateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Slett kontraktmal</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <p>Er du sikker p&aring; at du vil slette denne malen?</p>
                        <button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        // tinymce load editor
        let tiny_editor_config_contract = {
            path_absolute: "{{ URL::to('/') }}",
            height: '500',
            selector: '.editor',
            plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table directionality',
                'emoticons template paste textpattern'],
            toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript ' +
            'superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
            'alignjustify  | removeformat',
            toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor ',
            relative_urls: false,
        };
        tinymce.init(tiny_editor_config_contract);

        $(".contractTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#contractTemplateModal');
            modal.find('form').attr('action', action);

            if ($(this).data('fields')) {
                let fields = $(this).data('fields');
                let form = modal.find('form');
                form.find('[name=title]').val(fields.title);
                form.find('[name=signature_label]').val(fields.signature_label);
                form.find('[name=show_in_project]').prop('checked', false).change();
                if (fields.show_in_project) {
                    form.find('[name=show_in_project]').prop('checked', true).change();
                }

                let content = '';
                if (fields.details) {
                    content = fields.details;
                }
                tinymce.get('editContentEditor').setContent(content);
            } else {
                modal.find(".form-control").val('');
                tinymce.get('editContentEditor').setContent('');
            }
        });

        $(".deleteContractTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#deleteContractTemplateModal');
            modal.find('form').attr('action', action);
        });

        $(".status-toggle").change(function(){
            let contract_id = $(this).attr('data-id');
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;

            $.ajax({
                type:'POST',
                url:'/contract/' + contract_id + '/status',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { 'status' : check_val },
                success: function(data){}
            });
        });
    </script>
@stop

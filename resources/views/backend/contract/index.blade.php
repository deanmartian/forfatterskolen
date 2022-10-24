@extends('backend.layout')

@section('title')
    <title>Admins &rsaquo; Forfatterskolen Contract</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-handshake-o"></i> Contract</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">

        <ul class="nav nav-tabs margin-top">
            <li @if( !in_array(Request::input('tab'), ['template'])) class="active" @endif>
                <a href="?tab=list">
                    List
                </a>
            </li>

            <li @if( Request::input('tab') == 'template' ) class="active" @endif>
                <a href="?tab=template">
                    Templates
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
                                Template
                            </button>

                            <div class="table-users table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th width="200"></th>
                                    </tr>
                                    </thead>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td>
                                                {{ $template->title }}
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-xs contractTemplateBtn"
                                                        data-toggle="modal"
                                                        data-target="#contractTemplateModal"
                                                        data-action="{{ route('admin.contract-template.save',
														$template->id) }}"
                                                        data-fields="{{ json_encode($template) }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>

                                                <button class="btn btn-danger btn-xs deleteContractTemplateBtn"
                                                        data-toggle="modal"
                                                        data-target="#deleteContractTemplateModal"
                                                        data-action="{{ route('admin.contract-template.delete',
													$template->id) }}">
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
                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <a class="btn btn-success margin-bottom" href="{{ route('admin.contract.create') }}">
                                Create Contract
                            </a>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Receiver Name</th>
                                    <th>End Date</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contracts as $contract)
                                    <tr>
                                        <td>
                                            <a href="{{ $contract->signature ? route('admin.contract.show', $contract->id) :
                                            route('admin.contract.edit', $contract->id) }}">
                                                {{ $contract->title }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $contract->receiver_name }}
                                        </td>
                                        <td>{{ $contract->end_date }}</td>
                                        <td>
                                            @if ($contract->signature)
                                                <label class="label label-success">Signed</label>
                                            @else
                                                <label class="label label-warning">Unsigned</label>
                                            @endif

                                            @if (Auth::user()->isSuperUser())
                                                    <input type="checkbox" data-toggle="toggle" data-on="Show" data-off="Hide" data-size="mini" name="status"
                                                           class="status-toggle" data-id="{{ $contract->id }}"
                                                           @if ($contract->status === 1) checked @endif>
                                            @endif

                                            @if ($contract->signature)
                                                @if($contract->is_file)
                                                    <a href="{{ $contract->signed_file }}" class="button btn btn-info btn-xs" download>Download PDF</a>
                                                @else
                                                    <a href="{{ route('admin.contract.download-pdf', $contract->id) }}" class="button btn btn-info btn-xs">Download PDF</a>
                                                @endif
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

    <div id="contractTemplateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Contract Template
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" class="form-control" name="title"
                                   value="" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.details') }}</label>
                            <textarea name="details" rows="12" class="form-control editor" id="editContentEditor"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Signature Label</label>
                            <input type="text" name="signature_label" value="" class="form-control">
                            <div style="margin-top: 2px">Signatures will appear here once this document is signed.</div>
                        </div>

                        <div class="form-group">
                            <label>Show in Project?</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" name="show_in_project">
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

    <div id="deleteContractTemplateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Contract</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>
                            {!! trans('site.delete-item-question') !!}
                        </p>

                        <button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div> <!-- end delete assignment template -->
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
                success: function(data){
                }
            });
        });
    </script>
@stop
@extends($layout)

@section('title')
    <title>{{ $title }} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('simplemde/simplemde.min.css')}}">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('css/font-awesome/css/font-awesome.min.css')}}">
<style>
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
        height: 70px;
        width: 172px;
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
        color: #fff;
        background-color: #4dbf39;
        border: 1px solid #d6eed1;
        border-radius: 5px;
        box-shadow: 0 2px 4px 0 rgba(0,0,0,.1);
        padding: 11px;
        min-height: 35px;
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
        text-decoration: none;
    }

    .button-green:hover {
        background-color: #48ab36;
        text-decoration: none;
        color: #fff;
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

    h3 {
        font-weight: bold;
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
</style>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ $backRoute }}" class="btn btn-default" style="margin-right: 10px">
            << {{ trans('site.back') }}
        </a>

        @if($action !== 'create')
            <h3>Edit <em>{{ $contract['title']}}</em></h3>
        @else
            <h3>Add Contract</h3>
        @endif

        <div class="navbar-form navbar-right">
            @if($action !== 'create')
                <div class="btn-group contract-options pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cog"></i> Settings
                    </button>
                    <ul class="dropdown-menu">
                        @if ($contract['admin_signature'])
                            <li>
                                <button type="button" class="btn btn-block" data-toggle="modal"
                                        data-target="#sendContractModal">
                                    <i class="fa fa-paper-plane"></i> Send Contract
                                </button>
                            </li>
                        @endif
                        <li>
                            <button type="button" class="btn btn-block" data-toggle="modal"
                                    data-target="#deleteContractModal">
                                <i class="fa fa-trash"></i> {{ trans('site.delete') }}
                            </button>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="container padding-top">
        <div class="row">

            <form method="POST" action="{{ $route }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if ($action !== 'create')
                    {{ method_field('PUT') }}
                @endif

                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            @if ($action == 'create')
                                <div class="form-group">
                                    <label></label>
                                    <input type="checkbox" name="is_file" data-toggle="toggle" data-on="Upload Contract"
                                           data-off="Use Editor" data-width="200" class="is-file-toggle"
                                           @if ($contract['is_file']) checked @endif>
                                </div>
                                <div class="use-editor-container {{ $contract['is_file'] ? 'hide' : '' }}">
                                    <div class="form-group">
                                        <label>
                                            Template
                                        </label>
                                        <select class="form-control select2 template">
                                            <option value="" selected disabled>- Search Template -</option>
                                            @foreach($templates as $template)
                                                <option value="{{$template->id}}" data-fields="{{ json_encode($template) }}">
                                                    {{$template->title}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="is_file" value="{{ $contract['is_file'] }}">
                            @endif
                            
                            <div class="form-group">
                                <label>{{ trans('site.title') }}</label>
                                <input type="text" class="form-control" name="title"
                                       value="{{ $contract['title'] }}" required>
                            </div>

                            <div class="use-editor-container {{ $contract['is_file'] ? 'hide' : '' }}">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="end_date"
                                           value="{{ $contract['end_date'] }}">
                                </div>
                            </div>

                            <div class="upload-contract-container {{ $contract['is_file'] ? '' : 'hide' }}">
                                <div class="form-group">
                                    <label>Sent file</label>
                                    <input type="file" class="form-control" name="sent_file" accept="application/pdf">
                                    @if ($action != 'create')
                                        {!! $contract['sent_file_link'] !!}
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>Signed file</label>
                                    <input type="file" class="form-control" name="signed_file" accept="application/pdf">
                                    @if ($action != 'create')
                                        {!! $contract['signed_file_link'] !!}
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>Mark as signed</label> <br>
                                    <input type="checkbox" name="signature" data-toggle="toggle" data-on="Yes"
                                           data-off="No"
                                           @if ($contract['signature']) checked @endif>
                                </div>
                            </div>

                            <div class="use-editor-container {{ $contract['is_file'] ? 'hide' : '' }}">
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" class="form-control" name="image">
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('site.details') }}</label>
                                    <textarea name="details" rows="12" id="editContentEditor"
                                              class="form-control tinymce">{{ $contract['details'] }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Signature Label</label>
                                    <input type="text" name="signature_label" value="{{ $contract['signature_label'] }}" class="form-control">
                                </div>

                                @if ($action !== 'create')
                                    @if (!$contract['admin_signature'])
                                        <div style="margin-top: 2px">Signatures will appear here once this document is signed.</div>
                                        <div class="signature-wrapper">
                                            <div class="signature">
                                                <div class="signature-canvas">
                                                    <div class="signature-cta">
                                                        <a class="button button-green signContractBtn"
                                                           data-target="#signContractModal" data-toggle="modal">
                                                            <div class="link-content">
                                                                <i class="fa fa-arrow-right"></i><span>Sign here</span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <img src="{{ asset($contract['admin_signature']) }}" style="height: 100px"> <br>
                                        <button class="btn btn-info btn-xs editSignContractBtn" type="button" data-toggle="modal"
                                                data-target="#signContractModal" data-fields="{{ json_encode($contract) }}">Edit Signature</button>
                                    @endif
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary btn-block margin-top">{{ trans('site.save') }}</button>
                        </div>
                    </div>
                </div> <!-- end col-sm-12 col-md-8 -->
            </form>
        </div>
    </div> <!-- end container -->

    @if ($action !== 'create')
        <div id="deleteContractModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{{ trans('site.delete') }} <em>{{$contract['title']}}</em></h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{route('admin.contract.destroy', $contract['id'])}}"
                            onsubmit="disableSubmit(this)">
                            {{csrf_field()}}
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="redirectRoute" value="{{ $backRoute }}">
                            <p>
                                {!! trans('site.delete-question') !!}
                            </p>
                            <button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="sendContractModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Send Contract</h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{route('admin.contract.send-contract', $contract['id'])}}"
                            onsubmit="disableSubmit(this)">
                            {{csrf_field()}}
                            @php
                                $name = isset($project) && $project->user ? $project->user->full_name : '';
                                $email = isset($project) && $project->user ? $project->user->email : '';
                                $project_id = isset($project) ? $project->id : '';
                            @endphp
                            <input type="hidden" name="project_id" value="{{ $project_id }}">

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $name }}" required>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $email }}" required>
                            </div>

                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="subject" class="form-control" value="{{ $contract['title'] }}" required>
                            </div>

                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message" cols="30" rows="10"
                                          class="form-control">Your contract ({{ $contract['title'] }}) is ready to view</textarea>
                            </div>

                            <div class="form-group">
                                <label>Attach a PDF copy</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="attach_pdf">
                            </div>

                            <button type="submit" class="btn btn-success pull-right">{{ trans('site.send') }}</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <div id="signContractModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><em>Sign Contract</em></h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.contract.sign', $contract['id']) }}">
                            {{ csrf_field() }}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>
                                        Admin Name
                                    </label>
                                    <input type="text" class="form-control" name="admin_name" required>
                                </div>

                                <label class="" for="">Signature:</label>
                                <br/>
                                <div id="sig" ></div>
                                <br/>
                                <button id="clear" class="btn btn-sm btn-danger">Clear Signature</button>
                                <textarea id="signature64" name="signed" style="display: none"></textarea>
                            </div>

                            <button class="btn btn-success mt-3 pull-right">{{ trans('site.save') }}</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    @endif

@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    {{--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>--}}
    <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.signature.js') }}"></script>
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

        $("select.template").change(function() {
            let template = $(this).children("option:selected");
            let fields = template.data('fields');
            $('[name=title]').val(fields.title);

            let content = '';
            if (fields.details) {
                content = fields.details;
            }
            tinymce.get('editContentEditor').setContent(content);

            $('[name=signature_label]').val(fields.signature_label ? fields.signature_label : 'Signature');
        });

        let sig = $('#sig').signature({syncField: '#signature64', syncFormat: 'PNG'});
        $('#clear').click(function(e) {
            e.preventDefault();
            sig.signature('clear');
            $("#signature64").val('');
        });

        $(".editSignContractBtn").click(function() {
            let fields = $(this).data('fields');
            $("#signContractModal").find("input[name=admin_name]").val(fields.admin_name);
        });

        $(".is-file-toggle").change(function(){
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;
            let upload_contract_container = $(".upload-contract-container");
            let use_editor_container = $(".use-editor-container");

            upload_contract_container.addClass('hide');
            use_editor_container.removeClass('hide');
            if (check_val) {
                upload_contract_container.removeClass('hide');
                use_editor_container.addClass('hide');
            }
        });
    </script>
@stop

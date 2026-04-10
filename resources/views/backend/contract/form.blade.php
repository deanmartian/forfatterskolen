@extends($layout)

@section('page_title'){{ $title }} &rsaquo; Forfatterskolen Admin@endsection

@section('styles')
<link rel="stylesheet" href="{{asset('simplemde/simplemde.min.css')}}">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
{{-- FA4 fjernet — FA5 lastes via backend-css.blade.php --}}
<style>
    .signature-wrapper { margin-top: 10px; }
    .signature { margin-right: 14px; display: inline-block; vertical-align: top; }
    .signature-canvas {
        background-color: #fff; border: 1px solid #d7e2ea; border-radius: 3px;
        padding: 9px; position: relative; height: 70px; width: 172px;
        display: flex; page-break-inside: avoid; justify-content: center;
        align-items: center; overflow: hidden;
    }
    .signature-canvas::before {
        content: ""; border-top-style: dashed; border-top-width: 1px;
        border-top-color: inherit; position: absolute; bottom: 25px; left: 0; right: 0;
    }
    .button-green {
        color: #fff; background-color: #4dbf39; border: 1px solid #d6eed1;
        border-radius: 5px; box-shadow: 0 2px 4px 0 rgba(0,0,0,.1); padding: 11px;
        min-height: 35px; cursor: pointer; display: inline-flex; align-items: center;
        text-decoration: none;
    }
    .button-green:hover { background-color: #48ab36; text-decoration: none; color: #fff; }
    .link-content { display: flex; align-items: center; margin: -4px 0; position: relative; line-height: normal; opacity: .7; }
    .fa-arrow-right { margin-right: 10px; }
    .disabled, .disabled *, :disabled, :disabled * { pointer-events: none; }
    .contract-options button { text-decoration: none; display: block; text-align: left; border-radius: 0; background-color: #fff; }
    h3 { font-weight: bold; }
    .kbw-signature { display: inline-block; border: 1px solid #a0a0a0; -ms-touch-action: none; }
    .kbw-signature-disabled { opacity: 0.35; }
    .recipient-fields { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px; }
    .recipient-fields h4 { margin-top: 0; }
    .type-specific-field { display: none; }
    .preview-panel { background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 4px; max-height: 500px; overflow-y: auto; }
</style>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ $backRoute }}" class="btn btn-default" style="margin-right: 10px">
            << {{ trans('site.back') }}
        </a>

        @if($action !== 'create')
            <h3>Rediger <em>{{ $contract['title']}}</em></h3>
        @else
            <h3>Opprett kontrakt</h3>
        @endif

        <div class="navbar-form navbar-right">
            @if($action !== 'create')
                <div class="btn-group contract-options pull-right">
                    <button type="button" class="btn btn-default dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cog"></i> Innstillinger
                    </button>
                    <ul class="dropdown-menu">
                        @if (isset($contract['admin_signature']) && $contract['admin_signature'])
                            <li>
                                <button type="button" class="btn btn-block" data-toggle="modal"
                                        data-target="#sendContractModal">
                                    <i class="fa fa-paper-plane"></i> Send kontrakt
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

            <form method="POST" action="{{ $route }}" enctype="multipart/form-data" id="contractForm">
                {{ csrf_field() }}
                @if ($action !== 'create')
                    {{ method_field('PUT') }}
                @endif

                @if(isset($contract['renewed_from_id']) && $contract['renewed_from_id'])
                    <input type="hidden" name="renewed_from_id" value="{{ $contract['renewed_from_id'] }}">
                @endif

                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            @if ($action == 'create')
                                <div class="form-group">
                                    <label></label>
                                    <input type="checkbox" name="is_file" data-toggle="toggle" data-on="Last opp kontrakt"
                                           data-off="Bruk editor" data-width="200" class="is-file-toggle"
                                           @if ($contract['is_file']) checked @endif>
                                </div>
                                <div class="use-editor-container {{ $contract['is_file'] ? 'hide' : '' }}">
                                    <div class="form-group">
                                        <label>Mal</label>
                                        <select class="form-control select2 template">
                                            <option value="" selected disabled>- Velg mal -</option>
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

                            <div class="use-editor-container {{ isset($contract['is_file']) && $contract['is_file'] ? 'hide' : '' }}">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Startdato</label>
                                            <input type="date" class="form-control" name="start_date"
                                                   value="{{ $contract['start_date'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Sluttdato</label>
                                            <input type="date" class="form-control" name="end_date"
                                                   value="{{ $contract['end_date'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="upload-contract-container {{ isset($contract['is_file']) && $contract['is_file'] ? '' : 'hide' }}">
                                <div class="form-group">
                                    <label>Sendt fil</label>
                                    <input type="file" class="form-control" name="sent_file" accept="application/pdf">
                                    @if ($action != 'create' && isset($contract['sent_file_link']))
                                        {!! $contract['sent_file_link'] !!}
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Signert fil</label>
                                    <input type="file" class="form-control" name="signed_file" accept="application/pdf">
                                    @if ($action != 'create' && isset($contract['signed_file_link']))
                                        {!! $contract['signed_file_link'] !!}
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Merk som signert</label> <br>
                                    <input type="checkbox" name="signature" data-toggle="toggle" data-on="Ja" data-off="Nei"
                                           @if (isset($contract['signature']) && $contract['signature']) checked @endif>
                                </div>
                            </div>

                            <div class="use-editor-container {{ isset($contract['is_file']) && $contract['is_file'] ? 'hide' : '' }}">
                                <div class="form-group">
                                    <label>Bilde</label>
                                    <input type="file" class="form-control" name="image">
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('site.details') }}</label>
                                    <textarea name="details" rows="12" id="editContentEditor"
                                              class="form-control tinymce">{{ $contract['details'] }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Signatur-etikett</label>
                                    <input type="text" name="signature_label" value="{{ $contract['signature_label'] }}" class="form-control">
                                </div>

                                @if ($action !== 'create')
                                    @if (!$contract['admin_signature'])
                                        <div style="margin-top: 2px">Signaturer vises her n&aring;r dokumentet er signert.</div>
                                        <div class="signature-wrapper">
                                            <div class="signature">
                                                <div class="signature-canvas">
                                                    <div class="signature-cta">
                                                        <a class="button button-green signContractBtn"
                                                           data-target="#signContractModal" data-toggle="modal">
                                                            <div class="link-content">
                                                                <i class="fa fa-arrow-right"></i><span>Signer her</span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <img src="{{ asset($contract['admin_signature']) }}" style="height: 100px"> <br>
                                        <button class="btn btn-info btn-xs editSignContractBtn" type="button" data-toggle="modal"
                                                data-target="#signContractModal" data-fields="{{ json_encode($contract) }}">Rediger signatur</button>
                                    @endif
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary btn-block margin-top">{{ trans('site.save') }}</button>
                        </div>
                    </div>
                </div>

                {{-- Right sidebar: Recipient & contract details --}}
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Kontrakttype</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>Type</label>
                                <select class="form-control" name="contract_type" id="contractType">
                                    <option value="">- Velg type -</option>
                                    <option value="firma" {{ ($contract['contract_type'] ?? '') == 'firma' ? 'selected' : '' }}>
                                        Firma (425 kr/t)
                                    </option>
                                    <option value="person" {{ ($contract['contract_type'] ?? '') == 'person' ? 'selected' : '' }}>
                                        Person (325 kr/t)
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Timepris (kr)</label>
                                <input type="number" step="0.01" class="form-control" name="timepris"
                                       value="{{ $contract['timepris'] ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Mottaker</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>Navn</label>
                                <input type="text" class="form-control" name="receiver_name"
                                       value="{{ $contract['receiver_name'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label>E-post</label>
                                <input type="email" class="form-control" name="receiver_email"
                                       value="{{ $contract['receiver_email'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text" class="form-control" name="receiver_address"
                                       value="{{ $contract['receiver_address'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label>Mobil</label>
                                <input type="text" class="form-control" name="mobile"
                                       value="{{ $contract['mobile'] ?? '' }}">
                            </div>

                            {{-- Firma field --}}
                            <div class="form-group type-field-firma" style="{{ ($contract['contract_type'] ?? '') == 'firma' ? '' : 'display:none' }}">
                                <label>Org.nr</label>
                                <input type="text" class="form-control" name="org_nr"
                                       value="{{ $contract['org_nr'] ?? '' }}">
                            </div>

                            {{-- Person field --}}
                            <div class="form-group type-field-person" style="{{ ($contract['contract_type'] ?? '') == 'person' ? '' : 'display:none' }}">
                                <label>F&oslash;dselsnummer</label>
                                <input type="text" class="form-control" name="fodselsnummer"
                                       value="{{ $contract['fodselsnummer'] ?? '' }}">
                            </div>
                        </div>
                    </div>

                    @if($action == 'create')
                        <button type="button" class="btn btn-default btn-block" id="previewBtn">
                            <i class="fa fa-eye"></i> Forh&aring;ndsvisning
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div id="previewModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Forh&aring;ndsvisning</h4>
                </div>
                <div class="modal-body">
                    <div id="previewContent" class="preview-panel"></div>
                </div>
            </div>
        </div>
    </div>

    @if ($action !== 'create')
        <div id="deleteContractModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
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
                            <p>{!! trans('site.delete-question') !!}</p>
                            <button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="sendContractModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Send kontrakt</h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{route('admin.contract.send-contract', $contract['id'])}}"
                            onsubmit="disableSubmit(this)">
                            {{csrf_field()}}
                            @php
                                $name = $contract['receiver_name'] ?? '';
                                $email = $contract['receiver_email'] ?? '';
                                if (!$name && isset($project) && $project->user) $name = $project->user->full_name;
                                if (!$email && isset($project) && $project->user) $email = $project->user->email;
                                $project_id = isset($project) ? $project->id : '';
                            @endphp
                            <input type="hidden" name="project_id" value="{{ $project_id }}">

                            <div class="form-group">
                                <label>Navn</label>
                                <input type="text" name="name" class="form-control" value="{{ $name }}" required>
                            </div>
                            <div class="form-group">
                                <label>E-post</label>
                                <input type="email" name="email" class="form-control" value="{{ $email }}" required>
                            </div>
                            <div class="form-group">
                                <label>Emne</label>
                                <input type="text" name="subject" class="form-control" value="{{ $contract['title'] }}" required>
                            </div>
                            <div class="form-group">
                                <label>Melding</label>
                                <textarea name="message" cols="30" rows="10"
                                          class="form-control">Din kontrakt ({{ $contract['title'] }}) er klar til gjennomgang og signering.</textarea>
                            </div>
                            <div class="form-group">
                                <label>Legg ved PDF-kopi</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Ja" data-off="Nei" name="attach_pdf">
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
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><em>Signer kontrakt</em></h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.contract.sign', $contract['id']) }}">
                            {{ csrf_field() }}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Admin-navn</label>
                                    <input type="text" class="form-control" name="admin_name" required>
                                </div>
                                <label for="">Signatur:</label><br/>
                                <div id="sig"></div><br/>
                                <button id="clear" class="btn btn-sm btn-danger">Slett signatur</button>
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

        // Template selection
        $("select.template").change(function() {
            let template = $(this).children("option:selected");
            let fields = template.data('fields');
            $('[name=title]').val(fields.title);

            let content = fields.details || '';
            tinymce.get('editContentEditor').setContent(content);
            $('[name=signature_label]').val(fields.signature_label ? fields.signature_label : 'Signature');

            // Auto-detect type from template title
            let title = fields.title.toLowerCase();
            if (title.indexOf('firma') !== -1) {
                $('#contractType').val('firma').trigger('change');
                $('[name=timepris]').val('425');
            } else if (title.indexOf('person') !== -1) {
                $('#contractType').val('person').trigger('change');
                $('[name=timepris]').val('325');
            }
        });

        // Contract type toggle
        $('#contractType').change(function() {
            let type = $(this).val();
            $('.type-field-firma, .type-field-person').hide();
            if (type === 'firma') {
                $('.type-field-firma').show();
                if (!$('[name=timepris]').val()) $('[name=timepris]').val('425');
            } else if (type === 'person') {
                $('.type-field-person').show();
                if (!$('[name=timepris]').val()) $('[name=timepris]').val('325');
            }
        });

        // Preview button
        $('#previewBtn').click(function() {
            let details = '';
            if (typeof tinymce !== 'undefined' && tinymce.get('editContentEditor')) {
                details = tinymce.get('editContentEditor').getContent();
            }

            // Simple client-side placeholder replacement for preview
            let replacements = {
                '{{name}}': $('[name=receiver_name]').val() || '{{name}}',
                '{{address}}': $('[name=receiver_address]').val() || '{{address}}',
                '{{org_nr}}': $('[name=org_nr]').val() || '{{org_nr}}',
                '{{fodselsnummer}}': $('[name=fodselsnummer]').val() || '{{fodselsnummer}}',
                '{{email}}': $('[name=receiver_email]').val() || '{{email}}',
                '{{mobile}}': $('[name=mobile]').val() || '{{mobile}}',
                '{{timepris}}': $('[name=timepris]').val() || '{{timepris}}',
                '{{start_date}}': $('[name=start_date]').val() || '{{start_date}}',
                '{{end_date}}': $('[name=end_date]').val() || '{{end_date}}'
            };

            for (let key in replacements) {
                details = details.split(key).join(replacements[key]);
            }

            $('#previewContent').html(details);
            $('#previewModal').modal('show');
        });

        // Signature
        @if($action !== 'create')
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
        @endif

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

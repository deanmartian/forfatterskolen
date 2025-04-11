@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default mr-2">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-file-text-o"></i> {{ $stepTitle }}</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="table-responsive">
            <div class="table-users table-responsive">
                <div class="table-users table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th width="500">Print Ready</th>
                                <th width="300"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($covers as $cover)
                                <tr>
                                    <td>
                                        @php
                                            $coverFiles = explode(',', $cover->value);
                                        @endphp
                                        @foreach ($coverFiles as $coverFile)
                                            @if (strpos($coverFile, 'project-'))
                                                <a href="{{ url('/dropbox/download/' . trim($coverFile)) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                                                <a href="{{ url('/dropbox/shared-link/' . trim($coverFile)) }}" 
                                                    target="_blank" 
                                                style="margin-right: 5px">
                                                    {{ basename($coverFile) }}
                                                </a>
                                            @else
                                                @if ($coverFile)
                                                    <a href="{{ $coverFile }}" class="btn btn-success btn-xs" download>
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <a href="{{ asset($coverFile) }}" target="_blank" style="margin-right: 5px">
                                                        {{ basename($coverFile) }}
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($cover->print_ready)
                                            <a href="{{ url('/dropbox/download/' . trim($cover->print_ready)) }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>&nbsp;
                                            {!! basename($cover->print_ready) !!}
                                        @else
                                            <button class="btn btn-success btn-xs graphicWorkBtn" data-toggle="modal" 
                                                data-target="#graphicWorkModal" data-type="cover-print-ready" data-id="{{ $cover->id }}">
                                                Add File
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-xs view-cover-btn" data-toggle="modal" 
                                            data-target="#coverDetailsModal"
                                            data-id="{{ $cover->id }}">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                                data-target="#graphicWorkModal"
                                                data-type="cover" data-id="{{ $cover->id }}"
                                                data-record="{{ json_encode($cover) }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                                data-target="#deleteGraphicWorkModal" data-type="cover"
                                                data-action="{{ route($deleteGraphicRoute, [$cover->project_id, $cover->id]) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                        <div id="cover-data-{{ $cover->id }}" style="display: none">
                                            {{-- Copy your table HTML here and make sure to use raw data --}}
                                            {!! view('backend.project.progress-plan._cover-details', 
                                                ['cover' => $cover]) !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="graphicWorkModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveGraphicRoute, $project->id) }}"
                          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <input type="hidden" name="type">

                        <div class="cover-container">
                            <div class="form-group">
                                <label>Cover</label>
                                <input type="file" class="form-control" name="cover[]" accept="image/*" multiple>
                            </div>
                            
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" cols="30" rows="10" class="form-control"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Størrelse</label>
                                <select class="form-control" name="cover_format" id="cover-format-select">
                                    <option value="">Valgfri størrelse</option>
                                        @foreach (AdminHelpers::projectFormats() as $format)
                                            <option value="{{ $format['id'] }}">
                                                {{ $format['option'] }}
                                            </option>
                                        @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Bredde (mm)</label>
                                <input type="text" class="form-control" name="cover_width" id="cover-width-input" 
                                onkeypress="return numeralsOnly(event)">
                            </div>
        
                            <div class="form-group">
                                <label>Høyde (mm)</label>
                                <input type="text" class="form-control" name="cover_height" id="cover-height-input" 
                                onkeypress="return numeralsOnly(event)">
                            </div>

                            <div class="form-group">
                                <label>ISBN</label>
                                <select class="form-control" name="isbn_id">
                                    <option value="" disabled selected>- Select ISBN -</option>
                                    @foreach ($isbns as $isbn)
                                        <option value="{{ $isbn->id }}">
                                            {{ $isbn->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <label>Backside Text Type</label> <br>
                                
                            </div> --}}

                            <div class="form-group">
                                <label>Backside Text (optional)</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Text" data-off="Document"
                                       name="backside_type" data-width="100" class="backsideToggle" checked
                                       >

                                <textarea name="backside_text" cols="30" rows="3" class="form-control backside-text"
                                style="margin-top: 10px"></textarea>
                                <input type="file" name="backside_file" class="form-control backside-file"
                            accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document" 
                            style="display: none; margin-top: 10px">
                            </div>

                            <div class="form-group">
                                <label>Backside Image (optional)</label>
                                <input type="file" class="form-control" name="backside_image[]" accept="image/*" multiple>
                            </div>

                            <div class="form-group">
                                <label>Instruction (for graphic designer)</label>
                                <textarea name="instruction" cols="30" rows="10" class="form-control"></textarea>
                            </div>

                            {{-- <div class="form-group">
                                <label>Approved Final</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_approved" data-width="84">
                            </div> --}}
                        </div>

                        <div class="description-container">
                            <div class="form-group">
                                <label>Print Ready</label>
                                <input type="file" class="form-control" name="cover_print_ready" accept="application/pdf">
                            </div>
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

    <div id="coverDetailsModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Cover Details
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="coverModalContent" style="overflow: auto">
                    {{-- Table content will be loaded here dynamically --}}
                </div>
            </div>
        </div>
    </div>

    <div id="deleteGraphicWorkModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>Are you sure you want to delete this record?</p>

                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete') }}
                        </button>

                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(".graphicWorkBtn").click(function() {
        let id = $(this).data('id');
        let type = $(this).data('type');
        let record = $(this).data('record');
        let modal = $("#graphicWorkModal");
        let form = modal.find("form");
        let checkbox = '';

        let coverContainer = $(".cover-container");
        let descriptionContainer = $(".description-container");

        coverContainer.addClass('hide');
        descriptionContainer.addClass('hide');

        switch (type) {
            case 'cover':
                modal.find('.modal-title').text('Cover');
                coverContainer.removeClass('hide');
                checkbox = 'is_approved';
                break;

            case 'cover-print-ready':
                modal.find('.modal-title').text('Print Ready');
                descriptionContainer.removeClass('hide');
                break;
        }

        form.find('[name=type]').val(type);
        if (id) {
            form.find('[name=id]').val(id);
            form.find('[name=format]').val(record.format);

            if (['cover', 'barcode'].includes(type)) {
                form.find('[name=' + checkbox + ']').prop('checked', false).change();
                if (record.is_checked) {
                    form.find('[name=' + checkbox + ']').prop('checked', true).change();
                }

                if (type == 'cover') {
                    form.find("[name=description]").val(record.description);
                    form.find("[name=cover_format]").val(record.format);
                    form.find("[name=isbn_id]").val(record.isbn_id);
                    form.find("[name=instruction]").val(record.instruction);
                    
                    if (record.backside_type == 'text') {
                        form.find("[name=backside_text]").val(record.backside_text);
                        $(".backsideToggle").prop("checked", true).change();
                    } else {
                        form.find("[name=backside_text]").val("");
                        $(".backsideToggle").prop("checked", false).change();
                    }
                }
            }

            if (type == 'cover') {
                var formatSelect = document.getElementById('cover-format-select');
                var widthInput = document.getElementById('cover-width-input');
                var heightInput = document.getElementById('cover-height-input');

                var formatExists = false;

                // Check if the format matches any predefined options
                for (var i = 0; i < formatSelect.options.length; i++) {
                    if (formatSelect.options[i].value === record.format) {
                        formatSelect.value = record.format;
                        formatExists = true;

                        // If it's a predefined format like '125x200', split it for width/height
                        var dimensions = record.format.split('x');
                        if (dimensions.length == 2) {
                            widthInput.value = dimensions[0];
                            heightInput.value = dimensions[1];
                        }
                        break;
                    }
                }
                
                if (!formatExists) {
                    formatSelect.value = ''; // Select "other" option

                    // Assuming `printData` contains custom width and height
                    if (record.format) {
                        var dimensions = record.format.split('x');
                        if (dimensions.length == 2) {
                            widthInput.value = dimensions[0];
                            heightInput.value = dimensions[1];
                        }
                    } else {
                        // You can also fallback to width and height fields if needed
                        widthInput.value = record.width || ''; // Use width from printData
                        heightInput.value = record.height || ''; // Use height from printData
                    }
                }
            }

        }
    });

    $(".deleteGraphicWorkBtn").click(function() {
        let type = $(this).data('type');
        let modal = $("#deleteGraphicWorkModal");
        let form = modal.find("form");
        let action = $(this).data('action');
        let pageTitle = 'Cover';

        modal.find('.modal-title').text('Delete ' + pageTitle);
        form.attr('action', action);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.view-cover-btn');
        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const html = document.getElementById(`cover-data-${id}`).innerHTML;
                document.getElementById('coverModalContent').innerHTML = html;
            });
        });
    });
</script>
@endsection
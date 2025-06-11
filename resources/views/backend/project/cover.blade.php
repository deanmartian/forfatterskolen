@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Cover Details</h3>
        <a href="{{ route('admin.project.graphic-work', $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>

        <div class="pull-right" style="margin-right: 20px">
            <button class="btn btn-primary graphicWorkBtn" data-toggle="modal"
                    data-target="#graphicWorkModal"
                    data-type="cover" data-id="{{ $cover->id }}"
                    data-record="{{ json_encode($cover) }}">
                <i class="fa fa-edit"></i>
            </button>
        </div>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Cover</th>
                    <th>Description</th>
                    <th>Format</th>
                    <th>ISBN</th>
                    <th>Backside Text</th>
                    <th>Backside Image</th>
                    <th>Instruction</th>
                    <th>Print Ready</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @php
                                $coverFiles = explode(',', $cover->value);
                            @endphp
                            @foreach ($coverFiles as $coverFile)
                                @if (strpos($coverFile, 'project-'))
                                    <a href="{{ route('dropbox.download_file', trim($coverFile)) }}">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                    </a>&nbsp;
                                    <a href="{{ route('dropbox.shared_link', $coverFile) }}" target="_blank" 
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
                            {{ $cover->description }}
                        </td>
                        <td>
                            {{ !is_array(AdminHelpers::projectFormats($cover->format)) ?
                                AdminHelpers::projectFormats($cover->format) 
                                : $cover->format . ' mm' }}
                        </td>
                        <td>
                            {{ $cover->isbn?->value }}
                        </td>
                        <td>
                            @if ($cover->backside_type == 'text')
                                {{ $cover->backside_text }}
                            @else
                                <a href="{{ route('dropbox.download_file', trim($cover->backside_text)) }}">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>&nbsp;
                                <a href="{{ route('dropbox.shared_link', $cover->backside_text) }}" target="_blank">
                                    {{ basename($cover->backside_text) }}
                                </a>
                            @endif
                        </td>
                        <td>
                            @if ($cover->backside_image)
                                @php
                                    $backsideImages = explode(',', $cover->backside_image);
                                @endphp
                                @foreach ($backsideImages as $backsideImage)
                                    <a href="{{ route('dropbox.download_file', trim($backsideImage)) }}">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                    </a>&nbsp;
                                    <span>{{ basename($backsideImage) }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            {{ $cover->instruction }}
                        </td>
                        <td>
                            @if ($cover->print_ready)
                                <a href="{{ route('dropbox.download_file', trim($cover->print_ready)) }}">
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
                        <td></td>
                    </tr>
                </tbody>
            </table>
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
@endsection

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    $(".graphicWorkBtn").click(function() {
        let id = $(this).data('id');
        let type = $(this).data('type');
        let record = $(this).data('record');
        let modal = $("#graphicWorkModal");
        let form = modal.find("form");

        let coverContainer = $(".cover-container");
        let descriptionContainer = $(".description-container");

        coverContainer.addClass('hide');
        descriptionContainer.addClass('hide');

        switch (type) {
            case 'cover':
                    modal.find('.modal-title').text('Cover');
                    coverContainer.removeClass('hide');
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

    $(".backsideToggle").change(function() {
        if ($(this).prop('checked')) {
            $(".backside-text").show();
            $(".backside-file").hide();
        } else {
            $(".backside-text").hide();
            $(".backside-file").show();
        }
    });

    $('#cover-format-select').on('change', function () {
        var selectedFormat = this.value;
        var widthInput = document.getElementById('cover-width-input');
        var heightInput = document.getElementById('cover-height-input');
        
        // If the selected value is "other", clear the width and height inputs
        if (selectedFormat !== "") {
            // Split the selected format (e.g., '125x200' => ['125', '200'])
            var dimensions = selectedFormat.split('x');
            widthInput.value = dimensions[0];  // Set the width
            heightInput.value = dimensions[1]; // Set the height
        } else {
            widthInput.value = '';
            heightInput.value = '';
        }
    });
</script>
@stop
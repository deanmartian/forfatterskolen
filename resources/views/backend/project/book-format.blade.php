@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Page Format Details</h3>
        <a href="{{ route('admin.project.graphic-work', $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>

        <div class="pull-right" style="margin-right: 20px">
            <button class="btn btn-primary bookFormattingBtn" data-toggle="modal"
                    data-target="#bookFormattingModal"
                    data-record="{{ json_encode($bookFormatting) }}"
                    data-action="{{ route($saveBookFormattingRoute, $project->id) }}">
                <i class="fa fa-edit"></i>
            </button>
        </div>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                    <tr>
                        <th>Interior</th>
                        <th>Corporate Page</th>
                        <th>Format</th>
                        <th>Format Image</th>
                        <th>Description</th>
                        <th>Designer</th>
                        <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            {!! $bookFormatting->file_link !!}
                        </td>
                        <td>
                            {!! $bookFormatting->corporate_page_link !!}
                        </td>
                        <td>
                            {{ !is_array(AdminHelpers::projectFormats($bookFormatting->format)) ?
                                AdminHelpers::projectFormats($bookFormatting->format) 
                                : ($bookFormatting->format ? $bookFormatting->format . ' mm' : null) }}
                        </td>
                        <td>
                            {!! $bookFormatting->format_image_link !!}
                        </td>
                        <td>
                            {!! $bookFormatting->description !!}
                        </td>
                        <td>
                            {{ $bookFormatting->designer?->full_name }}
                        </td>
                        <td>
                            {!! $bookFormatting->feedback_file_link !!}
                            @if ($bookFormatting->feedback && $bookFormatting->feedback_status == 'pending')
                                <button class="btn btn-xs btn-success approveFeedbackBtn" data-toggle="modal" 
                                    data-target="#approveFeedbackModal"
                                    data-action="{{ route('admin.project.book-formatting.approve-feedback', $bookFormatting->id) }}"
                                    style="margin-left: 5px">
                                    <i class="fa fa-check"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="bookFormattingModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Book Formatting
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label>Interior</label>
                            <input type="file" name="file[]" class="form-control"
                            accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            multiple>
                        </div>

                        <div class="form-group">
                            <label>Corporate Page</label>
                            <input type="file" name="corporate_page" class="form-control"
                            accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </div>

                        <div class="form-group">
                            <label>Graphic Designer</label>
                            <select name="designer_id" class="form-control select2 template">
                                <option value="" selected="" disabled>- Select Designer -</option>
                                @foreach($designers as $designer)
                                    <option value="{{ $designer->id }}">
                                        {{$designer->full_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Størrelse (optional)</label>
                            <select class="form-control" name="format" id="format-select">
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
                            <input type="text" class="form-control" name="width" id="width-input" 
                            onkeypress="return numeralsOnly(event)">
                        </div>
    
                        <div class="form-group">
                            <label>Høyde (mm)</label>
                            <input type="text" class="form-control" name="height" id="height-input" 
                            onkeypress="return numeralsOnly(event)">
                        </div>

                        <div class="form-group format-image-container hide">
                            <label>Format Image</label>
                            <input type="file" name="format_image" class="form-control"
                            accept="image/*">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" cols="30" rows="10"></textarea>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(".bookFormattingBtn").click(function(){
        let action = $(this).data('action');
        let record = $(this).data('record');
        let modal = $('#bookFormattingModal');
        modal.find('form').attr('action', action);

        if (record) {
            modal.find('[name=id]').val(record.id);
            modal.find('[name=designer_id]').val(record.designer_id).change();
            modal.find('[name=format]').val(record.format).change();
            modal.find('[name=description]').val(record.description);

            var formatSelect = document.getElementById('format-select');
            var widthInput = document.getElementById('width-input');
            var heightInput = document.getElementById('height-input');

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
    });

    $("#bookFormattingModal").find("[name=format]").change(function() {
        $("#bookFormattingModal").find(".format-image-container").removeClass('hide');
    });

    $('#format-select').on('change', function () {
        var selectedFormat = this.value;
        var widthInput = document.getElementById('width-input');
        var heightInput = document.getElementById('height-input');
        
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

    function numeralsOnly(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }
</script>
@endsection
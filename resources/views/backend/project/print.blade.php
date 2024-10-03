@extends($layout)

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-file-text-o"></i> Print</h3>
    <a href="{{ $backRoute }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Back
    </a>

    <button class="btn btn-primary btn-sm pull-right printBtn" data-toggle="modal" data-target="#printModal"
        data-print="{{ json_encode($print) }}">
        Edit
    </button>
</div>

@if ($print)
    <div class="col-sm-12 margin-top">
        <section>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>ISBN</label>
                                <p>
                                    {{ $print->isbn }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Antall</label>
                                <p>{{ $print->number }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Sider</label>
                                <p>{{ $print->pages }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Størrelse</label>
                                <p>
                                    {{ !is_array(AdminHelpers::projectFormats($print->format)) ?
                                        AdminHelpers::projectFormats($print->format) : $print->width . 'x' . $print->height . ' mm' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Bredde (mm)</label>
                                <p>
                                    {{ $print->width }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Høyde (mm)</label>
                                <p>
                                    {{ $print->height }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Antall titler</label>
                                <p>
                                    {{ $print->originals }}
                                </p>
                            </div>
                        </div>
                    </div> <!-- end row -->

                    <div class="row">
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Innbinding</label>
                                <p>
                                    {{ $print->binding }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Garnhefting</label>
                                <p>
                                    {{ $print->yarn_stapling ? 'Ja' : 'Nei' }}
                                </p>
                            </div>
                        </div>
                    </div> <!-- end row -->

                    <div class="row">
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Papirtype innhold</label>
                                <p>
                                    {{ AdminHelpers::projectMedias($print->media) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Trykk av innhold </label>
                                <p>
                                    {{ AdminHelpers::projectPrintMethods($print->print_method) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Innhold trykkes med</label>
                                <p>
                                    {{ AdminHelpers::projectPrintColors($print->color) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>Antall fargesider</label>
                                <p>
                                    {{ $print->number_of_color_pages }}
                                </p>
                            </div>
                        </div>
                    </div> <!-- end row -->
                </div> <!-- end panel-body -->
            </div> <!-- end panel -->        
        </section>
    </div> 
@endif

<div id="printModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Print Details
                </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route($savePrintRoute, $project->id) }}"
                    enctype="multipart/form-data">
                      {{ csrf_field() }}

                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" class="form-control" name="isbn" required>
                    </div>

                    <div class="form-group">
                        <label>Antall</label>
                        <input type="text" class="form-control" name="number" onkeypress="return numeralsOnly(event)" required>
                    </div>

                    <div class="form-group">
                        <label>Sider</label>
                        <input type="text" class="form-control" name="pages" onkeypress="return numeralsOnly(event)" required>
                    </div>

                    <div class="form-group">
                        <label>Størrelse</label>
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
                        <input type="text" class="form-control" name="width" id="width-input" onkeypress="return numeralsOnly(event)">
                    </div>

                    <div class="form-group">
                        <label>Høyde (mm)</label>
                        <input type="text" class="form-control" name="height" id="height-input" onkeypress="return numeralsOnly(event)">
                    </div>

                    <div class="form-group">
                        <label>Antall titler</label>
                        <select class="form-control valid" name="originals">
                            @for ($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}">
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Innbinding</label>
                        <select class="form-control" name="binding">
                            @foreach (AdminHelpers::projectBindings() as $binding)
                                <option value="{{ $binding['id'] }}">
                                    {{ $binding['option'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Garnhefting</label>
                        <select class="form-control" name="yarn_stapling">
                            <option selected="selected" value="0">Nei</option>
                            <option value="1">Ja</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Papirtype innhold</label>
                        <select class="form-control" name="media">
                            @foreach (AdminHelpers::projectMedias() as $media)
                                <option value="{{ $media['id'] }}">
                                    {{ $media['option'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Trykk av innhold</label>
                        <select class="form-control valid" name="print_method">
                            @foreach (AdminHelpers::projectPrintMethods() as $method)
                                <option value="{{ $method['id'] }}">
                                    {{ $method['option'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Innhold trykkes med</label>
                        <select class="form-control valid" name="color">
                            @foreach (AdminHelpers::projectPrintColors() as $color)
                                <option value="{{ $color['id'] }}">
                                    {{ $color['option'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Antall fargesider</label>
                        <input type="text" class="form-control" name="number_of_color_pages" onkeypress="return numeralsOnly(event)">
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
<script>
    function numeralsOnly(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    $(".printBtn").click(function() {
        let data = $(this).data('print');
        let modal = $("#printModal");
        
        if (data) {
            console.log(data);
            modal.find('[name=isbn]').val(data.isbn);
            modal.find('[name=number]').val(data.number);
            modal.find('[name=pages]').val(data.pages);
            modal.find('[name=format]').val(data.format);
            modal.find('[name=width]').val(data.width);
            modal.find('[name=height]').val(data.height);
            modal.find('[name=originals]').val(data.originals);
            modal.find('[name=binding]').val(data.binding);
            modal.find('[name=yarn_stapling]').val(data.yarn_stapling);
            modal.find('[name=media]').val(data.media);
            modal.find('[name=print_method]').val(data.print_method);
            modal.find('[name=color]').val(data.color);
            modal.find('[name=number_of_color_pages]').val(data.number_of_color_pages);
        }
    });

    document.getElementById('format-select').addEventListener('change', function () {
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

    // Handle form submission
    $('form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission
        
        var formData = new FormData(this); // Capture form data

        // Clear any previous error messages
        $('.error-message').remove();

        // AJAX request
        $.ajax({
            url: $(this).attr('action'), // Form action URL
            method: 'POST', // Form method (POST)
            data: formData,
            processData: false, // Do not process data automatically
            contentType: false, // Allow form data with file upload (if necessary)
            success: function (response) {
                if (response.success) {
                    alert(response.message); // Show success message
                    location.reload();
                } else {
                    alert('Error: ' + response.message); // Show error message
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    // Display validation errors
                    $.each(errors, function (field, messages) {
                        var inputField = $('[name="' + field + '"]');
                        inputField.after('<span class="error-message" style="color:red;">' + messages.join('<br>') + '</span>');
                    });

                    alert("Error in form");
                } else {
                    alert('An unknown error occurred.');
                }
            }
        });
    });
</script>
@stop
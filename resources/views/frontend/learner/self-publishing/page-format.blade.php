@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Cover &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 dashboard-course">
                <div class="card global-card">
                    <div class="card-header">
                        <h1 class="d-inline-block">
                            Page Format
                        </h1>

                        @if ($standardProject)
                            <button type="button" class="btn btn-success pull-right bookFormattingBtn" data-toggle="modal" 
                            data-target="#bookFormattingModal">
                                + Add Page Format
                            </button>
                        @endif
                    </div>
                    <div class="card-body py-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Interior</th>
                                    <th width="300"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookFormattingList as $bookFormatting)
                                    <tr>
                                        <td>
                                            {!! $bookFormatting->file_link !!}
                                        </td>
                                        <td>
                                            <a href="{{ route('learner.self-publishing.page-format-show', $bookFormatting->id) }}" 
                                                class="btn btn-info btn-xs">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end card -->
            </div>
        </div>
    </div>
</div>

@if ($standardProject)
<div id="bookFormattingModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Book Formatting
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('learner.self-publishing.save-page-format', $standardProject->id) }}" 
                    onsubmit="disableSubmit(this)" enctype="multipart/form-data">
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
@endif

@endsection

@section('scripts')
<script>
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
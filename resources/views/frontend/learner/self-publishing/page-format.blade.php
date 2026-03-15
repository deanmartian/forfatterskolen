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
                            {{ trans('site.author-portal.page-format') }}
                        </h1>

                        @if ($standardProject)
                            <button type="button" class="btn btn-success float-end bookFormattingBtn" data-bs-toggle="modal" 
                            data-bs-target="#bookFormattingModal">
                                + {{ trans('site.add-page-format') }}
                            </button>
                        @endif
                    </div>
                    <div class="card-body py-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.interior') }}</th>
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
                                                class="btn btn-info btn-sm">
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
                    {{ trans('site.book-formatting') }}
                </h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('learner.self-publishing.save-page-format', $standardProject->id) }}" 
                    onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id">

                    <div class="form-group">
                        @include('frontend.learner.self-publishing.partials._file-upload', [
                            'uploadName'  => 'file[]',
                            'acceptTypes' => '.pdf,.indd',
                            'maxMb'       => 200,
                            'label'       => trans('site.author-portal.interior'),
                        ])
                    </div>

                    <div class="form-group">
                        @include('frontend.learner.self-publishing.partials._file-upload', [
                            'uploadName'  => 'corporate_page',
                            'acceptTypes' => '.pdf,.indd',
                            'maxMb'       => 200,
                            'label'       => trans('site.corporate-page'),
                        ])
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.size-optional') }}</label>
                        <select class="form-control" name="format" id="format-select">
                            <option value="">{{ trans('site.size-options') }}</option>
                                @foreach (AdminHelpers::projectFormats() as $format)
                                    <option value="{{ $format['id'] }}">
                                        {{ $format['option'] }}
                                    </option>
                                @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.author-portal.width-mm') }}</label>
                        <input type="text" class="form-control" name="width" id="width-input" 
                        onkeypress="return numeralsOnly(event)">
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.author-portal.height-mm') }}</label>
                        <input type="text" class="form-control" name="height" id="height-input" 
                        onkeypress="return numeralsOnly(event)">
                    </div>

                    <div class="form-group format-image-container hide">
                        @include('frontend.learner.self-publishing.partials._file-upload', [
                            'uploadName'  => 'format_image',
                            'acceptTypes' => '.jpg,.jpeg,.png,.gif',
                            'maxMb'       => 20,
                            'label'       => trans('site.format-image'),
                        ])
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.description') }}</label>
                        <textarea name="description" class="form-control" cols="30" rows="10"></textarea>
                    </div>

                    <div class="text-end">
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

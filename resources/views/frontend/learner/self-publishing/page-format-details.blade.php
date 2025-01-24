@extends('frontend.learner.self-publishing.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .fa-arrow-left::before {
            color: #5f0000c2;
        }
    </style>
@stop

@section('title')
    <title>Page Format Details &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 dashboard-course">
                <a href="{{ route('learner.self-publishing.page-format') }}" class="btn btn-default mb-3">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

                <div class="card global-card">
                    <div class="page-toolbar mb-3">
                        <h3 class="float-left"><i class="fa fa-file-text-o"></i> Page Format</h3>

                        <button class="btn btn-primary pull-right bookFormattingBtn" data-toggle="modal"
                                data-target="#bookFormattingModal"
                                data-record="{{ json_encode($bookFormatting) }}"
                                data-action="{{ route('learner.self-publishing.save-page-format', $standardProject->id) }}">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>

                    <table class="table">
                        <thead>
                            <th>Interior</th>
                            <th>Corporate Page</th>
                            <th>Format</th>
                            <th>Format Image</th>
                            <th>Description</th>
                            <th>Designer</th>
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
                                    {{ $bookFormatting->format ? AdminHelpers::projectFormats($bookFormatting->format) : null }}
                                </td>
                                <td>
                                    {!! $bookFormatting->format_image_link !!}
                                </td>
                                <td>
                                    {!! $bookFormatting->description !!}
                                </td>
                                <td>
                                    {{ optional($bookFormatting->designer)->full_name }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        <label>Størrelse (optional)</label>
                        <select class="form-control" name="format">
                            <option value="" selected disabled>Valgfri størrelse</option>
                                @foreach (AdminHelpers::projectFormats() as $format)
                                    <option value="{{ $format['id'] }}">
                                        {{ $format['option'] }}
                                    </option>
                                @endforeach
                        </select>
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
        }
    });

    $("#bookFormattingModal").find("[name=format]").change(function() {
        $("#bookFormattingModal").find(".format-image-container").removeClass('hide');
    });
</script>
@endsection
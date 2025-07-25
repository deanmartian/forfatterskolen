@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Registration</h3>
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="col-sm-12 margin-top">
        <button type="button" class="btn btn-success registrationBtn" data-toggle="modal" data-target="#registrationModal"
                data-field="isbn">+ Add ISBN</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>ISBN</th>
                    <th width="700">Type</th>
                    <th>Book Price</th>
                    <th>Mentor Book Base</th>
                    <th>Upload files to mentor book base</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($isbns as $isbn)
                    <tr>
                        <td>{!! $isbn->value !!}</td>
                        <td>{{ $isbn->isbn_type }}</td>
                        <td>{{ $isbn->book_price ? FrontendHelpers::currencyFormat($isbn->book_price) : '' }}</td>
                        <td>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="mini"
                            name="mentor_book_base" data-width="60"data-record="{{ json_encode($isbn->childMentorBookBase) }}" 
                            class="mentorToggle" data-field="mentor_book_base"
                            @if ($isbn->childMentorBookBase->value) checked @endif>
                        </td>
                        <td>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" data-size="mini"
                            name="upload_files_to_mentor_book_base" data-width="60" 
                            data-record="{{ json_encode($isbn->childUploadMentorBookBase) }}" 
                            class="mentorToggle" data-field="upload_files_to_mentor_book_base"
                            @if ($isbn->childUploadMentorBookBase->value) checked @endif>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-xs registrationBtn" data-toggle="modal"
                                    data-target="#registrationModal" data-record="{{ json_encode($isbn) }}"
                                    data-field="isbn" data-id="{{ $isbn->id }}" data-isbn_type="{{ $isbn->type }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteRegistrationBtn" data-toggle="modal"
                                    data-target="#deleteRegistrationModal" data-field="isbn"
                                    data-action="{{ route($deleteRegistrationRoute, [$isbn->project_id, $isbn->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success registrationBtn" data-toggle="modal" data-target="#registrationModal"
                data-field="central-distribution">+ Add Central distribution</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Central distribution</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($centralDistributions as $centralDistribution)
                    <tr>
                        <td>{!! $centralDistribution->value !!}</td>
                        <td>
                            <button class="btn btn-primary btn-xs registrationBtn" data-toggle="modal"
                                    data-target="#registrationModal" data-record="{{ json_encode($centralDistribution) }}"
                                    data-field="central-distribution" data-id="{{ $centralDistribution->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteRegistrationBtn" data-toggle="modal"
                                    data-target="#deleteRegistrationModal" data-field="central-distribution"
                                    data-action="{{ route($deleteRegistrationRoute, [$centralDistribution->project_id, $centralDistribution->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="cultural-council">+ Add Cultural Council</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Cultural Council</th>
                    <th>Is Finished</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($culturalCouncils as $culturalCouncil)
                    <tr>
                        <td>{!! $culturalCouncil->file_link !!}</td>
                        <td>{{ $culturalCouncil->is_finished_text }}</td>
                        <td>
                            <a href="{{ $culturalCouncil->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($culturalCouncil) }}"
                                    data-type="cultural-council" data-id="{{ $culturalCouncil->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="cultural-council"
                                    data-action="{{ route($deleteMarketingRoute, [$culturalCouncil->project_id, $culturalCouncil->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- <button type="button" class="btn btn-success registrationBtn" data-toggle="modal" data-target="#registrationModal"
                data-field="mentor-book-base">+ Add Mentor book base</button> --}}
        {{-- <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Mentor book base</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($mentorBookBases as $mentorBookBase)
                    <tr>
                        <td>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                            name="mentor_book_base" data-width="100" data-record="{{ json_encode($mentorBookBase) }}" 
                            class="mentorToggle" data-field="mentor_book_base"
                            @if ($mentorBookBase->value) checked @endif>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-xs registrationBtn" data-toggle="modal"
                                    data-target="#registrationModal" data-record="{{ json_encode($mentorBookBase) }}"
                                    data-field="mentor-book-base" data-id="{{ $mentorBookBase->id }}">
                                <i class="fa fa-edit"></i>
                            </button> 
                            <button class="btn btn-danger btn-xs deleteRegistrationBtn" data-toggle="modal"
                                    data-target="#deleteRegistrationModal" data-field="mentor-book-base"
                                    data-action="{{ route($deleteRegistrationRoute, [$mentorBookBase->project_id, $mentorBookBase->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>--}}

        {{-- <button type="button" class="btn btn-success registrationBtn" data-toggle="modal" data-target="#registrationModal"
                data-field="upload-files-to-mentor-book-base">+ Add Upload files to mentor book base</button> --}}
        {{-- <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Upload files to mentor book base</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($uploadFilesToMentorBookBases as $uploadFilesToMentorBookBase)
                    <tr>
                        <td>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                            name="upload_files_to_mentor_book_base" data-width="100" 
                            data-field="upload_files_to_mentor_book_base"
                            data-record="{{ json_encode($uploadFilesToMentorBookBase) }}" 
                            class="mentorBaseToggle" 
                            @if ($uploadFilesToMentorBookBase->value) checked @endif>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-xs registrationBtn" data-toggle="modal"
                                    data-target="#registrationModal" data-record="{{ json_encode($uploadFilesToMentorBookBase) }}"
                                    data-field="upload-files-to-mentor-book-base" data-id="{{ $uploadFilesToMentorBookBase->id }}">
                                <i class="fa fa-edit"></i>
                            </button> 
                            <button class="btn btn-danger btn-xs deleteRegistrationBtn" data-toggle="modal"
                                    data-target="#deleteRegistrationModal" data-field="upload-files-to-mentor-book-base"
                                    data-action="{{ route($deleteRegistrationRoute, [$uploadFilesToMentorBookBase->project_id, $uploadFilesToMentorBookBase->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>--}}
    </div>

    <div id="registrationModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveRegistrationRoute, $project->id) }}"
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <input type="hidden" name="field">

                        <div class="isbn-container">
                            <div class="form-group">
                                <label>ISBN</label>
                                <input type="text" class="form-control" name="isbn">
                            </div>

                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    @foreach ($isbnTypes as $k => $isbnType)
                                        <option value="{{ $k }}">
                                            {{ $isbnType }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Book Price</label>
                                <input type="number" class="form-control" name="book_price" step=".01">
                            </div>
                        </div>

                        <div class="form-group central-distribution-container">
                            <label>Central Distribution</label>
                            <select name="central_distribution" class="form-control">
                                <option value="">- Select ISBN-</option>
                                @foreach ($isbns as $isbn)
                                    <option value="{{ $isbn->value }}">
                                        {{ $isbn->value }} | {{ $isbn->isbn_type }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- <input type="number" class="form-control" name="central_distribution"> --}}
                        </div>

                        <div class="form-group mentor-book-base-container">
                            <label>Mentor Book Base</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="mentor_book_base" data-width="100" class="mentorToggle">
                            {{-- <textarea name="mentor_book_base" class="form-control" cols="30" rows="10"></textarea> --}}
                        </div>

                        <div class="form-group upload-files-to-mentor-book-base-container">
                            <label>Upload files to Mentor Book Base</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="upload_files_to_mentor_book_base" data-width="100">
                            {{-- <input type="date" class="form-control" name="upload_files_to_mentor_book_base"> --}}
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

    <div id="deleteRegistrationModal" class="modal fade" role="dialog">
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

    <div id="marketingModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveMarketingRoute, $project->id) }}" enctype="multipart/form-data"
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <input type="hidden" name="type">

                        <div class="form-group">
                            <label>Cultural Council</label>
                            <input type="file" class="form-control" name="cultural_council"
                                    accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
                    application/vnd.oasis.opendocument.text">

                        </div>

                        <div class="form-group">
                            <label>Is Finished</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                    name="is_finished_cultural_council" data-width="84">
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

    <div id="deleteMarketingModal" class="modal fade" role="dialog">
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
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $(".registrationBtn").click(function() {
            let id = $(this).data('id');
            let field = $(this).data('field');
            let record = $(this).data('record');
            let modal = $("#registrationModal");
            let form = modal.find("form");

            let isbnContainer = $(".isbn-container");
            let centralDistributionContainer = $(".central-distribution-container");
            let mentorBookBaseContainer = $(".mentor-book-base-container");
            let uploadFilesToMentorBookBaseContainer = $(".upload-files-to-mentor-book-base-container");

            isbnContainer.addClass('hide');
            centralDistributionContainer.addClass('hide');
            mentorBookBaseContainer.addClass('hide');
            uploadFilesToMentorBookBaseContainer.addClass('hide');

            switch (field) {
                case 'isbn':
                    let type = $(this).data('isbn_type');

                    modal.find('.modal-title').text('ISBN');
                    isbnContainer.removeClass('hide');
                    if (!id) {
                        type = 1;
                    }

                    isbnContainer.find('[name=type]').val(type);
                    break;

                case 'central-distribution':
                    modal.find('.modal-title').text('Central Distribution');
                    centralDistributionContainer.removeClass('hide');
                    break;

                case 'mentor-book-base':
                    modal.find('.modal-title').text('Mentor Book Base');
                    mentorBookBaseContainer.removeClass('hide');
                    break;

                case 'upload-files-to-mentor-book-base':
                    modal.find('.modal-title').text('Upload files to Mentor Book Base');
                    uploadFilesToMentorBookBaseContainer.removeClass('hide');
                    break;
            }

            form.find('[name=field]').val(field);
            form.find('[name=mentor_book_base]').prop("checked", false).change();
            form.find('[name=upload_files_to_mentor_book_base]').prop("checked", false).change();
            if (id) {
                form.find('[name=id]').val(id);
            }

            if (record) {
                form.find('[name=isbn]').val(record.value);
                form.find('[name=central_distribution]').val(record.value);
                form.find('[name=book_price]').val(record.book_price);
                form.find('[name=mentor_book_base]').prop("checked", false).change();
                form.find('[name=upload_files_to_mentor_book_base]').prop("checked", false).change();
                //form.find('[name=upload_files_to_mentor_book_base]').val(record.value);
                if (record.value == 1) {
                    form.find('[name=mentor_book_base]').prop("checked", true).change();
                    form.find('[name=upload_files_to_mentor_book_base]').prop("checked", true).change();
                }
            }
        });

        $(".deleteRegistrationBtn").click(function() {
            let field = $(this).data('field');
            let modal = $("#deleteRegistrationModal");
            let form = modal.find("form");
            let action = $(this).data('action');
            let pageTitle = '';

            switch (field) {
                case 'isbn':
                    pageTitle = 'Isbn';
                    break;

                case 'central-distribution':
                    pageTitle = 'Central Distribution';
                    break;

                case 'mentor-book-base':
                    pageTitle = 'Mentor Book Base';
                    break;

                case 'upload-files-to-mentor-book-base':
                    pageTitle = 'Upload Files to Mentor Book Base';
                    break;
            }

            modal.find('.modal-title').text('Delete ' + pageTitle);
            form.attr('action', action);
        });

        $(".mentorToggle, .mentorBaseToggle").change(function() {
            const record = $(this).data('record');
            let newStatus = $(this).prop('checked') ? 1 : 0;
            const url = "{{ route($saveRegistrationRoute, $project->id) }}";
            const field = $(this).data('field');
            
            if (record) {
                $.ajax({
                    url: url, 
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: {
                        id: record.id,
                        field: record.field,
                        [field]: newStatus,
                    },
                    success: function (response) {
                        
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        $(".marketingBtn").click(function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let record = $(this).data('record');
            let modal = $("#marketingModal");
            let form = modal.find("form");
            let is_finished_field = '';
            let value_field = '';

            modal.find('.modal-title').text('Cultural Council');
            is_finished_field = 'is_finished_cultural_council';

            form.find('[name=type]').val(type);
            if (id) {
                form.find('[name=id]').val(id);
                form.find('[name='+ is_finished_field +']').prop('checked', record.is_finished).change();
            } else {
                form.find('[name=id]').val('');
                form.find('[name='+ is_finished_field +']').prop('checked', false).change();
                form.find('[name=date]').val('');
            }
        });

        $(".deleteMarketingBtn").click(function() {
            let type = $(this).data('type');
            let modal = $("#deleteMarketingModal");
            let form = modal.find("form");
            let action = $(this).data('action');
            let pageTitle = 'Cultural Council';
            modal.find('.modal-title').text('Delete ' + pageTitle);
            form.attr('action', action);
        });
    </script>
@stop
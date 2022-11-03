@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Marketing</h3>
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="col-sm-12 margin-top">
        <button type="button" class="btn btn-success">+ Add E-mail bookstore</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add E-mail library</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add E-mail press</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Review copies are sent</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Review copies are sent</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Set up online store</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Set up online store</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Set up Facebook</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Set up Facebook</th>
                </tr>
                </thead>
                <tbody>
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
                        <th>{{ $culturalCouncil->is_finished_text }}</th>
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

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="application-free-word">+ Add Application Free Word</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Application Free Word</th>
                    <th>Is Finished</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($freeWords as $freeWord)
                    <tr>
                        <td>{!! $freeWord->file_link !!}</td>
                        <th>{{ $freeWord->is_finished_text }}</th>
                        <td>
                            <a href="{{ $freeWord->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($freeWord) }}"
                                    data-type="application-free-word" data-id="{{ $freeWord->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="application-free-word"
                                    data-action="{{ route($deleteMarketingRoute, [$freeWord->project_id, $freeWord->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Agreement on time registration</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Agreement on time registration</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="print-ebook">+ Add Print/Ebook</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Print/Ebook</th>
                    <th>Is Finished</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($printEBooks as $printEBook)
                    <tr>
                        <td>{!! $printEBook->file_link !!}</td>
                        <th>{{ $printEBook->is_finished_text }}</th>
                        <td>
                            <a href="{{ $printEBook->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($printEBook) }}"
                                    data-type="print-ebook" data-id="{{ $printEBook->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="print-ebook"
                                    data-action="{{ route($deleteMarketingRoute, [$printEBook->project_id, $printEBook->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="sample-book-approved">+ Add Sample book approved</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Sample book approved</th>
                    <th>Is Finished</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($sampleBookApproved as $sampleBook)
                    <tr>
                        <td>{!! $sampleBook->file_link !!}</td>
                        <th>{{ $sampleBook->is_finished_text }}</th>
                        <td>
                            <a href="{{ $sampleBook->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($sampleBook) }}"
                                    data-type="sample-book-approved" data-id="{{ $sampleBook->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="sample-book-approved"
                                    data-action="{{ route($deleteMarketingRoute, [$sampleBook->project_id, $sampleBook->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Manuscripts are sent to print</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Manuscripts are sent to print</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="pdf-print-is-approved">+ Add PDF is approved</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>PDF is approved</th>
                    <th>Is Finished</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($pdfPrintIsApproved as $pdfPrint)
                    <tr>
                        <td>{!! $pdfPrint->file_link !!}</td>
                        <th>{{ $pdfPrint->is_finished_text }}</th>
                        <td>
                            <a href="{{ $pdfPrint->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($pdfPrint) }}"
                                    data-type="pdf-print-is-approved" data-id="{{ $pdfPrint->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="pdf-print-is-approved"
                                    data-action="{{ route($deleteMarketingRoute, [$pdfPrint->project_id, $pdfPrint->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="number-of-author-books">+ Add Number of books by author</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Number of books by author</th>
                    <th>Is Finished</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($numberOfAuthorBooks as $numberOfAuthorBook)
                    <tr>
                        <td>{!! $numberOfAuthorBook->value !!}</td>
                        <th>{{ $numberOfAuthorBook->is_finished_text }}</th>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($numberOfAuthorBook) }}"
                                    data-type="number-of-author-books" data-id="{{ $numberOfAuthorBook->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="number-of-author-books"
                                    data-action="{{ route($deleteMarketingRoute, [$numberOfAuthorBook->project_id, $numberOfAuthorBook->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Update the book base</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Update the book base</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add E-book ordered</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>E-book ordered</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add E-book received and registered</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>E-book received and registered</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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

                        <div class="cultural-council-container">
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
                        </div>

                        <div class="application-free-word-container">
                            <div class="form-group">
                                <label>Application to free word</label>
                                <input type="file" class="form-control" name="free_word"
                                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Is Finished</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_free_word" data-width="84">
                            </div>
                        </div>

                        <div class="print-ebook-container">
                            <div class="form-group">
                                <label>Print EBook</label>
                                <input type="file" class="form-control" name="print_ebook"
                                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Is Finished</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_print_ebook" data-width="84">
                            </div>
                        </div>

                        <div class="sample-book-approved-container">
                            <div class="form-group">
                                <label>Sample Book Approved</label>
                                <input type="file" class="form-control" name="sample_book_approved"
                                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Is Finished</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_sample_book_approved" data-width="84">
                            </div>
                        </div>

                        <div class="pdf-print-is-approved-container">
                            <div class="form-group">
                                <label>PDF Print Approved</label>
                                <input type="file" class="form-control" name="pdf_print_is_approved"
                                       accept="application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Is Finished</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_pdf_print_is_approved" data-width="84">
                            </div>
                        </div>

                        <div class="number-of-author-books-container">
                            <div class="form-group">
                                <label>Number of books by author</label>
                                <input type="number" class="form-control" name="number_of_author_books">
                            </div>

                            <div class="form-group">
                                <label>Is Finished</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_number_of_author_books" data-width="84">
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
        $(".marketingBtn").click(function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let record = $(this).data('record');
            let modal = $("#marketingModal");
            let form = modal.find("form");
            let is_finished_field = '';

            let culturalCouncilContainer = $(".cultural-council-container");
            let applicationFreeWordContainer = $(".application-free-word-container");
            let printEBookContainer = $(".print-ebook-container");
            let sampleBookApprovedContainer = $(".sample-book-approved-container");
            let pdfPrintIsApprovedContainer = $(".pdf-print-is-approved-container");
            let numberOfAuthorBooksContainer = $(".number-of-author-books-container");

            culturalCouncilContainer.addClass('hide');
            applicationFreeWordContainer.addClass('hide');
            printEBookContainer.addClass('hide');
            sampleBookApprovedContainer.addClass('hide');
            pdfPrintIsApprovedContainer.addClass('hide');
            numberOfAuthorBooksContainer.addClass('hide');

            switch (type) {
                case 'cultural-council':
                    modal.find('.modal-title').text('Cultural Council');
                    culturalCouncilContainer.removeClass('hide');
                    is_finished_field = 'is_finished_cultural_council';
                    break;

                case 'application-free-word':
                    modal.find('.modal-title').text('Application Free Word');
                    applicationFreeWordContainer.removeClass('hide');
                    is_finished_field = 'is_finished_free_word';
                    break;

                case 'print-ebook':
                    modal.find('.modal-title').text('Print EBook');
                    printEBookContainer.removeClass('hide');
                    is_finished_field = 'is_finished_print_ebook';
                    break;

                case 'sample-book-approved':
                    modal.find('.modal-title').text('Sample Book Approved');
                    sampleBookApprovedContainer.removeClass('hide');
                    is_finished_field = 'is_finished_sample_book_approved';
                    break;

                case 'pdf-print-is-approved':
                    modal.find('.modal-title').text('PDF Print Approved');
                    pdfPrintIsApprovedContainer.removeClass('hide');
                    is_finished_field = 'is_finished_pdf_print_is_approved';
                    break;

                case 'number-of-author-books':
                    modal.find('.modal-title').text('Number of author books');
                    numberOfAuthorBooksContainer.removeClass('hide');
                    is_finished_field = 'is_finished_number_of_author_books';
                    break;
            }

            form.find('[name=type]').val(type);
            if (id) {
                form.find('[name=id]').val(id);
                form.find('[name='+ is_finished_field +']').prop('checked', false).change();
                if (type === 'number-of-author-books') {
                    form.find('[name=number_of_author_books]').val(record.value);
                }
                if (record.is_finished) {
                    form.find('[name='+ is_finished_field +']').prop('checked', true).change();
                }
            }
        });

        $(".deleteMarketingBtn").click(function() {
            let type = $(this).data('type');
            let modal = $("#deleteMarketingModal");
            let form = modal.find("form");
            let action = $(this).data('action');
            let pageTitle = '';

            switch (type) {
                case 'cultural-council':
                    pageTitle = 'Cultural Council';
                    break;

                case 'application-free-word':
                    pageTitle = 'Application Free Word';
                    break;

                case 'print-ebook':
                    pageTitle = 'Print EBook';
                    break;

                case 'sample-book-approved':
                    pageTitle = 'Sample Book Approved';
                    break;

                case 'pdf-print-is-approved':
                    pageTitle = 'PDF print approved';
                    break;

                case 'number-of-author-books':
                    pageTitle = 'Number of books by author';
                    break;
            }

            modal.find('.modal-title').text('Delete ' + pageTitle);
            form.attr('action', action);
        });
    </script>
@stop
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
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="email-bookstore">+ Add E-mail bookstore</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Date</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($emailBookstores as $emailBookstore)
                    <tr>
                        <td>{!! $emailBookstore->file_link !!}</td>
                        <td>{{ $emailBookstore->date }}</td>
                        <td>
                            <a href="{{ $emailBookstore->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($emailBookstore) }}"
                                    data-type="email-bookstore" data-id="{{ $emailBookstore->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="email-bookstore"
                                    data-action="{{ route($deleteMarketingRoute, [$emailBookstore->project_id, $emailBookstore->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="email-library">+ Add E-mail library</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Date</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($emailLibraries as $emailLibrary)
                    <tr>
                        <td>{!! $emailLibrary->file_link !!}</td>
                        <td>{{ $emailLibrary->date }}</td>
                        <td>
                            <a href="{{ $emailLibrary->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($emailLibrary) }}"
                                    data-type="email-library" data-id="{{ $emailLibrary->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="email-library"
                                    data-action="{{ route($deleteMarketingRoute, [$emailLibrary->project_id, $emailLibrary->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="email-press">+ Add E-mail press</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Date</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($emailPresses as $emailPress)
                    <tr>
                        <td>{!! $emailPress->file_link !!}</td>
                        <td>{{ $emailPress->date }}</td>
                        <td>
                            <a href="{{ $emailPress->value }}" class="btn btn-success btn-xs" download>
                                <i class="fa fa-download"></i>
                            </a>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($emailPress) }}"
                                    data-type="email-press" data-id="{{ $emailPress->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="email-press"
                                    data-action="{{ route($deleteMarketingRoute, [$emailPress->project_id, $emailPress->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($reviewCopiesSent->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="review-copies-sent">+ Add Review copies are sent</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Review copies are sent</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($reviewCopiesSent as $reviewCopies)
                    <tr>
                        <td>{{ $reviewCopies->is_finished_text }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($reviewCopies) }}"
                                    data-type="review-copies-sent" data-id="{{ $reviewCopies->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="review-copies-sent"
                                    data-action="{{ route($deleteMarketingRoute, [$reviewCopies->project_id, $reviewCopies->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($setupOnlineStore->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="setup-online-store">+ Add Set up online store</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Set up online store</th>
                    <th>Link Address</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($setupOnlineStore as $setupStore)
                    <tr>
                        <td>{{ $setupStore->is_finished_text }}</td>
                        <td><a href="{{ $setupStore->value }}">{{ $setupStore->value }}</a></td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($setupStore) }}"
                                    data-type="setup-online-store" data-id="{{ $setupStore->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="setup-online-store"
                                    data-action="{{ route($deleteMarketingRoute, [$setupStore->project_id, $setupStore->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($setupFacebook->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="setup-facebook">+ Add Set up Facebook</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Set up Facebook</th>
                    <th>Link Address</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($setupFacebook as $setupFB)
                    <tr>
                        <td>{{ $setupFB->is_finished_text }}</td>
                        <td><a href="{{ $setupFB->value }}">{{ $setupFB->value }}</a></td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($setupFB) }}"
                                    data-type="setup-facebook" data-id="{{ $setupFB->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="setup-facebook"
                                    data-action="{{ route($deleteMarketingRoute, [$setupFB->project_id, $setupFB->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="advertisement-facebook">+ Advertisement Facebook</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th width="500">Details</th>
                    <th>Is finished</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($advertisementFacebook as $advertisementFB)
                    <tr>
                        <td>{!! $advertisementFB->file_link !!}</td>
                        <td>{{ $advertisementFB->details }}</td>
                        <td>{{ $advertisementFB->is_finished_text }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($advertisementFB) }}"
                                    data-type="advertisement-facebook" data-id="{{ $advertisementFB->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="advertisement-facebook"
                                    data-action="{{ route($deleteMarketingRoute, [$advertisementFB->project_id, $advertisementFB->id]) }}">
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
                        <td>{{ $freeWord->is_finished_text }}</td>
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

        @if ($agreementOnTimeRegistration->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="agreement-on-time-registration">+ Add Agreement on time registration</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Agreement on time registration</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($agreementOnTimeRegistration as $agreementOnTime)
                    <tr>
                        <td>{{ $agreementOnTime->is_finished_text }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($agreementOnTime) }}"
                                    data-type="agreement-on-time-registration" data-id="{{ $agreementOnTime->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="agreement-on-time-registration"
                                    data-action="{{ route($deleteMarketingRoute, [$agreementOnTime->project_id, $agreementOnTime->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
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
                        <td>{{ $printEBook->is_finished_text }}</td>
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
                        <td>{{ $sampleBook->is_finished_text }}</td>
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

        @if($manuscriptSentToPrint->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="manuscripts-sent-to-print">+ Add Manuscripts are sent to print</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Manuscripts are sent to print</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($manuscriptSentToPrint as $manuscriptSent)
                    <tr>
                        <td>{{ $manuscriptSent->is_finished_text }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($manuscriptSent) }}"
                                    data-type="manuscripts-sent-to-print" data-id="{{ $manuscriptSent->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="manuscripts-sent-to-print"
                                    data-action="{{ route($deleteMarketingRoute, [$manuscriptSent->project_id, $manuscriptSent->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
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
                        <td>{{ $pdfPrint->is_finished_text }}</td>
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
                        <td>{{ $numberOfAuthorBook->is_finished_text }}</td>
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

        @if ($updateTheBookBase->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="update-the-book-base">+ Add Update the book base</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Update the book base</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($updateTheBookBase as $updateBookBase)
                    <tr>
                        <td>{{ $updateBookBase->is_finished_text }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($updateBookBase) }}"
                                    data-type="update-the-book-base" data-id="{{ $updateBookBase->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="update-the-book-base"
                                    data-action="{{ route($deleteMarketingRoute, [$updateBookBase->project_id, $updateBookBase->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if ($ebookOrdered->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="ebook-ordered">+ Add E-book ordered</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>E-book ordered</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($ebookOrdered as $ebookOrder)
                    <tr>
                        <td>{{ $ebookOrder->is_finished_text }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($ebookOrder) }}"
                                    data-type="ebook-ordered" data-id="{{ $ebookOrder->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="ebook-ordered"
                                    data-action="{{ route($deleteMarketingRoute, [$ebookOrder->project_id, $ebookOrder->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if ($ebookReceived->count() === 0)
        <button type="button" class="btn btn-success marketingBtn" data-toggle="modal" data-target="#marketingModal"
                data-type="ebook-received">+ Add E-book received and registered</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>E-book received and registered</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($ebookReceived as $ebookReceive)
                    <tr>
                        <td>{{ $ebookReceive->is_finished_text }}</td>
                        <td>
                            <button class="btn btn-primary btn-xs marketingBtn" data-toggle="modal"
                                    data-target="#marketingModal" data-record="{{ json_encode($ebookReceive) }}"
                                    data-type="ebook-received" data-id="{{ $ebookReceive->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingBtn" data-toggle="modal"
                                    data-target="#deleteMarketingModal" data-type="ebook-received"
                                    data-action="{{ route($deleteMarketingRoute, [$ebookReceive->project_id, $ebookReceive->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
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

                        <div class="email-bookstore-container">
                            <div class="form-group">
                                <label>Email Bookstore</label> <br>
                                <input type="file" class="form-control" name="email_bookstore"
                                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="email_bookstore_date">
                            </div>
                        </div>

                        <div class="email-library-container">
                            <div class="form-group">
                                <label>Email Library</label> <br>
                                <input type="file" class="form-control" name="email_library"
                                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="email_library_date">
                            </div>
                        </div>

                        <div class="email-press-container">
                            <div class="form-group">
                                <label>Email Press</label> <br>
                                <input type="file" class="form-control" name="email_press"
                                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="email_press_date">
                            </div>
                        </div>

                        <div class="review-copies-sent-container">
                            <div class="form-group">
                                <label>Review copies sent</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_review_copies_sent" data-width="84">
                            </div>
                        </div>

                        <div class="setup-online-store-container">
                            <div class="form-group">
                                <label>Link Address</label>
                                <input type="url" class="form-control" name="link_address">
                            </div>

                            <div class="form-group">
                                <label>Setup online store</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_setup_online_store" data-width="84">
                            </div>
                        </div>

                        <div class="setup-facebook-container">
                            <div class="form-group">
                                <label>Link Address</label>
                                <input type="url" class="form-control" name="link_address">
                            </div>

                            <div class="form-group">
                                <label>Setup facebook</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_setup_facebook" data-width="84">
                            </div>
                        </div>

                        <div class="advertisement-facebook-container">
                            <div class="form-group">
                                <label>File</label>
                                <input type="file" class="form-control" name="advertisement_facebook"
                                       accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text">
                            </div>

                            <div class="form-group">
                                <label>Details</label>
                                <textarea name="details" cols="30" rows="10" class="form-control"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Is finished</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_advertisement_facebook" data-width="84">
                            </div>
                        </div>

                        <div class="manuscripts-sent-to-print-container">
                            <div class="form-group">
                                <label>Manuscripts are sent to print</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_manuscripts_sent_to_print" data-width="84">
                            </div>
                        </div>

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

                        <div class="agreement-on-time-registration-container">
                            <div class="form-group">
                                <label>Is Finished</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_agreement_on_time_registration" data-width="84">
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

                        <div class="update-the-book-base-container">
                            <div class="form-group">
                                <label>Update the book base</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_update_the_book_base" data-width="84">
                            </div>
                        </div>

                        <div class="ebook-ordered-container">
                            <div class="form-group">
                                <label>Ebook Ordered</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_ebook_ordered" data-width="84">
                            </div>
                        </div>

                        <div class="ebook-received-container">
                            <div class="form-group">
                                <label>Ebook Received</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_finished_ebook_received" data-width="84">
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
            let value_field = '';

            let emailBookstoreContainer = $(".email-bookstore-container");
            let emailLibraryContainer = $(".email-library-container");
            let emailPressContainer = $(".email-press-container");
            let reviewCopiesSentContainer = $(".review-copies-sent-container");
            let setupOnlineStoreContainer = $(".setup-online-store-container");
            let setupFacebookContainer = $(".setup-facebook-container");
            let advertisementFacebookContainer = $(".advertisement-facebook-container");
            let manuscriptsSentToPrintContainer = $(".manuscripts-sent-to-print-container");
            let culturalCouncilContainer = $(".cultural-council-container");
            let applicationFreeWordContainer = $(".application-free-word-container");
            let agreementRegistrationContainer = $(".agreement-on-time-registration-container");
            let printEBookContainer = $(".print-ebook-container");
            let sampleBookApprovedContainer = $(".sample-book-approved-container");
            let pdfPrintIsApprovedContainer = $(".pdf-print-is-approved-container");
            let numberOfAuthorBooksContainer = $(".number-of-author-books-container");
            let updateTheBookBaseContainer = $(".update-the-book-base-container");
            let ebookOrderedContainer = $(".ebook-ordered-container");
            let ebookReceivedContainer = $(".ebook-received-container");

            emailBookstoreContainer.addClass('hide');
            emailLibraryContainer.addClass('hide');
            emailPressContainer.addClass('hide');
            reviewCopiesSentContainer.addClass('hide');
            setupOnlineStoreContainer.addClass('hide');
            setupFacebookContainer.addClass('hide');
            advertisementFacebookContainer.addClass('hide');
            manuscriptsSentToPrintContainer.addClass('hide');
            culturalCouncilContainer.addClass('hide');
            applicationFreeWordContainer.addClass('hide');
            agreementRegistrationContainer.addClass('hide');
            printEBookContainer.addClass('hide');
            sampleBookApprovedContainer.addClass('hide');
            pdfPrintIsApprovedContainer.addClass('hide');
            numberOfAuthorBooksContainer.addClass('hide');
            updateTheBookBaseContainer.addClass('hide');
            ebookOrderedContainer.addClass('hide');
            ebookReceivedContainer.addClass('hide');

            switch (type) {
                case 'email-bookstore':
                    modal.find('.modal-title').text('Email Bookstore');
                    emailBookstoreContainer.removeClass('hide');
                    is_finished_field = 'is_finished';
                    break;

                case 'email-library':
                    modal.find('.modal-title').text('Email Library');
                    emailLibraryContainer.removeClass('hide');
                    is_finished_field = 'is_finished';
                    break;

                case 'email-press':
                    modal.find('.modal-title').text('Email Press');
                    emailPressContainer.removeClass('hide');
                    is_finished_field = 'is_finished';
                    break;

                case 'review-copies-sent':
                    modal.find('.modal-title').text('Review copies sent');
                    reviewCopiesSentContainer.removeClass('hide');
                    is_finished_field = 'is_finished_review_copies_sent';
                    break;

                case 'setup-online-store':
                    modal.find('.modal-title').text('Setup online store');
                    setupOnlineStoreContainer.removeClass('hide');
                    is_finished_field = 'is_finished_setup_online_store';
                    value_field = 'link_address';
                    break;

                case 'setup-facebook':
                    modal.find('.modal-title').text('Setup facebook');
                    setupFacebookContainer.removeClass('hide');
                    is_finished_field = 'is_finished_setup_facebook';
                    value_field = 'link_address';
                    break;

                case 'advertisement-facebook':
                    modal.find('.modal-title').text('Advertisement facebook');
                    advertisementFacebookContainer.removeClass('hide');
                    is_finished_field = 'is_finished_advertisement_facebook';
                    break;

                case 'manuscripts-sent-to-print':
                    modal.find('.modal-title').text('Manuscripts sent to print');
                    manuscriptsSentToPrintContainer.removeClass('hide');
                    is_finished_field = 'is_finished_manuscripts_sent_to_print';
                    break;

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

                case 'agreement-on-time-registration':
                    modal.find('.modal-title').text('Agreement on time registration');
                    agreementRegistrationContainer.removeClass('hide');
                    is_finished_field = 'is_finished_agreement_on_time_registration';
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
                    value_field = 'number_of_author_books';
                    break;

                case 'update-the-book-base':
                    modal.find('.modal-title').text('Update the book base');
                    updateTheBookBaseContainer.removeClass('hide');
                    is_finished_field = 'is_finished_update_the_book_base';
                    break;

                case 'ebook-ordered':
                    modal.find('.modal-title').text('Ebook Ordered');
                    ebookOrderedContainer.removeClass('hide');
                    is_finished_field = 'is_finished_ebook_ordered';
                    break;

                case 'ebook-received':
                    modal.find('.modal-title').text('Ebook Received');
                    ebookReceivedContainer.removeClass('hide');
                    is_finished_field = 'is_finished_ebook_received';
                    break;
            }

            form.find('[name=type]').val(type);
            if (id) {
                form.find('[name=id]').val(id);
                if (is_finished_field) {
                    form.find('[name='+ is_finished_field +']').prop('checked', false).change();
                }
                if (['number-of-author-books', 'setup-online-store', 'setup-facebook'].includes(type)) {
                    form.find('[name=' + value_field + ']').val(record.value);
                }

                if (['email-bookstore', 'email-library', 'email-press'].includes(type)) {
                    const inputName = type.replace('email-', 'email_') + '_date';
                    form.find(`[name="${inputName}"]`).val(record.date);
                }

                if (type === 'advertisement-facebook') {
                    form.find('[name=details]').val(record.details);
                }

                if (record.is_finished) {
                    form.find('[name='+ is_finished_field +']').prop('checked', true).change();
                }
            } else {
                form.find('[name=id]').val('');
                if (is_finished_field) {
                    form.find('[name='+ is_finished_field +']').prop('checked', false).change();
                }
                form.find('[name=date]').val('');
            }
        });

        $(".deleteMarketingBtn").click(function() {
            let type = $(this).data('type');
            let modal = $("#deleteMarketingModal");
            let form = modal.find("form");
            let action = $(this).data('action');
            let pageTitle = '';

            switch (type) {
                case 'email-bookstore':
                    pageTitle = 'Email Bookstore';
                    break;

                case 'email-library':
                    pageTitle = 'Email Library';
                    break;

                case 'email-press':
                    pageTitle = 'Email Press';
                    break;

                case 'review-copies-sent':
                    pageTitle = 'Review copies sent';
                    break;

                case 'setup-online-store':
                    pageTitle = 'Setup online store';
                    break;

                case 'setup-facebook':
                    pageTitle = 'Setup facebook';
                    break;

                case 'advertisement-facebook':
                    pageTitle = 'Advertisement facebook';
                    break;

                case 'manuscripts-sent-to-print':
                    pageTitle = 'Review copies sent';
                    break;

                case 'cultural-council':
                    pageTitle = 'Cultural Council';
                    break;

                case 'application-free-word':
                    pageTitle = 'Application Free Word';
                    break;

                case 'agreement-on-time-registration':
                    pageTitle = 'Agreement on time registration';
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

                case 'update-the-book-base':
                    pageTitle = 'Update the book base';
                    break;

                case 'ebook-ordered':
                    pageTitle = 'Ebook Ordered';
                    break;

                case 'ebook-received':
                    pageTitle = 'Ebook Received';
                    break;

            }

            modal.find('.modal-title').text('Delete ' + pageTitle);
            form.attr('action', action);
        });
    </script>
@stop
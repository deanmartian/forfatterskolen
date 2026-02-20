@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Time Register &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        .fa-file-red:before {
            content: "\f15b";
        }

        .fa-file-red {
            color: #862736 !important;
            font-size: 20px;
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <a href="{{ route('learner.project.show', $project->id) }}"
                   class="btn btn-secondary mb-3">
                    <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
                </a>

                <div class="col-md-12 dashboard-course no-left-padding">
                    <h3 class="mt-3">
                        {{ trans('site.author-portal.email-bookstore') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table table-side-bordered table-white">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('site.files', 1) }}</th>
                                    <th>{{ trans('site.date') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for email bookstore -->

                    <h3 class="mt-5">
                        {{ trans('site.author-portal.email-library') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table table-side-bordered table-white">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('site.files', 1) }}</th>
                                    <th>{{ trans('site.date') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for email library-->

                    <h3 class="mt-5">
                        {{ trans('site.author-portal.email-press') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('site.files', 1) }}</th>
                                    <th>{{ trans('site.date') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for email-press -->

                    <h3 class="mt-5">
                        {{ trans('site.author-portal.review-copies') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.review-copies-sent') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($reviewCopiesSent as $reviewCopies)
                                    <tr>
                                        <td>{{ $reviewCopies->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for review copies sent-->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.setup-online-store') }}</th>
                                    <th>{{ trans('site.author-portal.link-address') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($setupOnlineStore as $setupStore)
                                    <tr>
                                        <td>{{ $setupStore->is_finished_text }}</td>
                                        <td><a href="{{ $setupStore->value }}">{{ $setupStore->value }}</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for Set up online store -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table table-side-bordered table-white">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.setup-facebook') }}</th>
                                    <th>{{ trans('site.author-portal.link-address') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($setupFacebook as $setupFB)
                                    <tr>
                                        <td>{{ $setupFB->is_finished_text }}</td>
                                        <td><a href="{{ $setupFB->value }}">{{ $setupFB->value }}</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for Set up Facebook -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('site.files', 1) }}</th>
                                    <th width="500">{{ trans('site.details') }}</th>
                                    <th>{{ trans('site.author-portal.is-finished') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($advertisementFacebook as $advertisementFB)
                                    <tr>
                                        <td>{!! $advertisementFB->file_link !!}</td>
                                        <td>{{ $advertisementFB->details }}</td>
                                        <td>{{ $advertisementFB->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for advertisement facebook -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.cultural-council') }}</th>
                                    <th>{{ trans('site.author-portal.is-finished') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for cultural council-->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.application-free-word') }}</th>
                                    <th>{{ trans('site.author-portal.is-finished') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for free words-->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.agreement-on-time-registration') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($agreementOnTimeRegistration as $agreementOnTime)
                                    <tr>
                                        <td>{{ $agreementOnTime->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for Agreement on time registration -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.print-ebook') }}</th>
                                    <th>{{ trans('site.author-portal.is-finished') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for print ebook -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.sample-book-approved') }}</th>
                                    <th>{{ trans('site.author-portal.is-finished') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for Sample book approved -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.manuscripts-sent-to-print') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($manuscriptSentToPrint as $manuscriptSent)
                                    <tr>
                                        <td>{{ $manuscriptSent->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for Manuscripts are sent to print -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.pdf-approved') }}</th>
                                    <th>{{ trans('site.author-portal.is-finished') }}</th>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for PDF is approved -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.number-of-books-by-author') }}</th>
                                    <th>{{ trans('site.author-portal.is-finished') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($numberOfAuthorBooks as $numberOfAuthorBook)
                                    <tr>
                                        <td>{!! $numberOfAuthorBook->value !!}</td>
                                        <td>{{ $numberOfAuthorBook->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for Number of books by author -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.update-book-base') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($updateTheBookBase as $updateBookBase)
                                    <tr>
                                        <td>{{ $updateBookBase->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for Update the book base -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.ebook-ordered') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ebookOrdered as $ebookOrder)
                                    <tr>
                                        <td>{{ $ebookOrder->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for ebook ordered -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table table-side-bordered table-white">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.ebook-received-and-registered') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ebookReceived as $ebookReceive)
                                    <tr>
                                        <td>{{ $ebookReceive->is_finished_text }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for ebook received and registered -->
                </div> <!-- end col-md-12 -->
            </div>
        </div>
    </div>
@stop
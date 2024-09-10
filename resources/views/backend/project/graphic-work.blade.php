@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Graphic Work</h3>
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="col-sm-12 margin-top">
        <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                data-type="cover">+ Add Cover</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Cover</th>
                    <th width="500">Print Ready</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($covers as $cover)
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
                            @if ($cover->description)
                                <a href="{{ route('dropbox.download_file', trim($cover->description)) }}">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>&nbsp;
                                {!! basename($cover->description) !!}
                            @else
                                <button class="btn btn-success btn-xs graphicWorkBtn" data-toggle="modal" 
                                    data-target="#graphicWorkModal" data-type="description" data-id="{{ $cover->id }}">
                                    Add File
                                </button>
                            @endif
                        </td>
                        <td>                      
                            <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                    data-target="#graphicWorkModal"
                                    data-type="cover" data-id="{{ $cover->id }}"
                                    data-record="{{ json_encode($cover) }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                    data-target="#deleteGraphicWorkModal" data-type="cover"
                                    data-action="{{ route($deleteGraphicRoute, [$cover->project_id, $cover->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button class="btn btn-success bookFormattingBtn" data-toggle="modal"
                        data-target="#bookFormattingModal"
                        data-action="{{ route($saveBookFormattingRoute, $project->id) }}">
            Add Page Format
        </button>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Interior</th>
                    <th>Designer</th>
                    <th>{{ trans_choice('site.feedbacks', 1) }}</th>
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
                            {{ optional($bookFormatting->designer)->full_name }}
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
                        <td>
                            @if (strpos($bookFormatting->file, "project-"))
                                <a href="{{ route('dropbox.download_file', trim($bookFormatting->file)) }}" 
                                    class="btn btn-success btn-xs">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            @else
                                <a href="{{ $bookFormatting->file }}" class="btn btn-success btn-xs" download>
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                            <button class="btn btn-primary btn-xs bookFormattingBtn" data-toggle="modal"
                                    data-target="#bookFormattingModal"
                                    data-record="{{ json_encode($bookFormatting) }}"
                                    data-action="{{ route($saveBookFormattingRoute, $project->id) }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal"
                                    data-target="#deleteModal"
                                    data-action="{{ route($deleteBookFormattingRoute, $bookFormatting->id) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div> <!-- end page format table -->

        <button type="button" class="btn btn-success indesignBtn" data-toggle="modal" data-target="#indesignModal"
                data-type="indesign">+ Add Indesign</button>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Cover</th>
                    <th>Interior</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($indesigns as $indesign)
                        <tr>
                            <td>
                                @php
                                    $coverFiles = explode(',', $indesign->value);
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
                                @if ($indesign->interior)
                                    <a href="{{ route('dropbox.download_file', trim($indesign->description)) }}">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                    </a>&nbsp;
                                    {!! $indesign->interior !!}
                                @endif
                            </td>
                            <td>                      
                                <button class="btn btn-primary btn-xs indesignBtn" data-toggle="modal"
                                        data-target="#indesignModal"
                                        data-type="indesign" data-id="{{ $indesign->id }}"
                                        data-record="{{ json_encode($indesign) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                        data-target="#deleteGraphicWorkModal" data-type="indesign"
                                        data-action="{{ route($deleteGraphicRoute, [$indesign->project_id, $indesign->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> <!-- end indesign-->

        @if(!$barCodes->count())
            <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                    data-type="barcode">+ Add Barcode</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Is Sent</th>
                    <th>Date</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($barCodes as $barCode)
                        <tr>
                            <td>{!! $barCode->image !!}</td>
                            <td>
                                {{ $barCode->is_checked ? 'Yes' : 'No' }}
                            </td>
                            <td>
                                {{ $barCode->date }}
                            </td>
                            <td>
                                @if (strpos($barCode->value, "project-"))
                                    <a href="{{ route('dropbox.download_file', trim($barCode->value)) }}" 
                                        class="btn btn-success btn-xs">
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <a href="{{ $barCode->value }}" class="btn btn-success btn-xs" download>
                                        <i class="fa fa-download"></i>
                                    </a>
                                @endif
                                <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                        data-target="#graphicWorkModal"
                                        data-type="barcode" data-id="{{ $barCode->id }}"
                                        data-record="{{ json_encode($barCode) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                        data-target="#deleteGraphicWorkModal" data-type="barcode"
                                        data-action="{{ route($deleteGraphicRoute, [$barCode->project_id, $barCode->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                data-type="print-ready">+ Print Ready</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>File</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($printReadyList as $printReady)
                        <tr>
                            <td>
                                @if ($printReady->value)
                                    @if (strpos($printReady->value, 'project-'))
                                        <a href="{{ route('dropbox.download_file', trim($printReady->value)) }}">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>&nbsp;
                                    @else
                                        <a href="{{ $printReady->value }}" class="btn btn-success btn-xs" download>
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @endif
                                @endif
                                
                                {!! $printReady->image !!}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                        data-target="#graphicWorkModal"
                                        data-type="print-ready" data-id="{{ $printReady->id }}"
                                        data-record="{{ json_encode($printReady) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                        data-target="#deleteGraphicWorkModal" data-type="print-ready"
                                        data-action="{{ route($deleteGraphicRoute, [$printReady->project_id, $printReady->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                data-type="rewrite-script">+ Add Rewrite script</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Rewrite script</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($rewriteScripts as $rewriteScript)
                    <tr>
                        <td>{!! $rewriteScript->file_link !!}</td>
                        <td>
                            @if (strpos($rewriteScript->value, "project-"))
                                <a href="{{ route('dropbox.download_file', trim($rewriteScript->value)) }}" 
                                    class="btn btn-success btn-xs">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            @else
                                <a href="{{ $rewriteScript->value }}" class="btn btn-success btn-xs" download>
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                            <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                    data-target="#graphicWorkModal"
                                    data-type="rewrite-script" data-id="{{ $rewriteScript->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                    data-target="#deleteGraphicWorkModal" data-type="rewrite-script"
                                    data-action="{{ route($deleteGraphicRoute, [$rewriteScript->project_id, $rewriteScript->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                data-type="trial-page">+ Add Trial pages</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Trial pages</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($trialPages as $trialPage)
                    <tr>
                        <td>{!! $trialPage->image !!}</td>
                        <td>
                            @if (strpos($trialPage->value, "project-"))
                                <a href="{{ route('dropbox.download_file', trim($trialPage->value)) }}" 
                                    class="btn btn-success btn-xs">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            @else
                                <a href="{{ $trialPage->value }}" class="btn btn-success btn-xs" download>
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                            <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                    data-target="#graphicWorkModal"
                                    data-type="trial-page" data-id="{{ $trialPage->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                    data-target="#deleteGraphicWorkModal" data-type="trial-page"
                                    data-action="{{ route($deleteGraphicRoute, [$trialPage->project_id, $trialPage->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div> --}}

        <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                data-type="sample-book-pdf">+ Add Sample book/PDF</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Sample book/PDF</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($sampleBookPDFs as $sampleBookPDF)
                    <tr>
                        <td>{!! $sampleBookPDF->file_link !!}</td>
                        <td>
                            @if (strpos($sampleBookPDF->value, "project-"))
                                <a href="{{ route('dropbox.download_file', trim($sampleBookPDF->value)) }}" 
                                    class="btn btn-success btn-xs">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            @else
                                <a href="{{ $sampleBookPDF->value }}" class="btn btn-success btn-xs" download>
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                            <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                    data-target="#graphicWorkModal"
                                    data-type="sample-book-pdf" data-id="{{ $sampleBookPDF->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteGraphicWorkBtn" data-toggle="modal"
                                    data-target="#deleteGraphicWorkModal" data-type="sample-book-pdf"
                                    data-action="{{ route($deleteGraphicRoute, [$sampleBookPDF->project_id, $sampleBookPDF->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel">
                    <div class="panel-header" style="padding: 10px;">
                        <em><b>Book Pictures</b></em>
                        <button class="btn btn-success btn-xs pull-right saveBookPictureBtn" data-toggle="modal"
                                data-target="#bookPicturesModal"
                                data-action="{{ route($saveBookPicturesRoute, $project->id) }}">
                            Add
                        </button>
                    </div>
                    <div class="panel-body">

                        <div class="table-users table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bookPictures as $bookPicture)
                                    <tr>
                                        <td>
                                            @if (strpos($bookPicture->image, 'storage'))
                                                <a href="{{ asset( $bookPicture->image ) }}">
                                                    <img src="{{ asset( $bookPicture->image ) }}" width="100" height="100">
                                                </a>
                                            @else
                                                <a href="{{ route('dropbox.shared_link', $bookPicture->image ) }}" target="_blank">
                                                    {{ basename($bookPicture->image) }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            {!! $bookPicture->description !!}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-xs saveBookPictureBtn" data-toggle="modal"
                                                    data-target="#bookPicturesModal"
                                                    data-record="{{ json_encode($bookPicture) }}"
                                                    data-action="{{ route($saveBookPicturesRoute, $project->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-xs deleteBookPictureBtn" data-toggle="modal"
                                                    data-target="#deleteModal"
                                                    data-action="{{ route($deleteBookPicturesRoute, $bookPicture->id) }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
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
                            {{-- <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" cols="30" rows="10" class="form-control"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Approved Final</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_approved" data-width="84">
                            </div> --}}
                        </div>

                        <div class="barcode-container">
                            <div class="form-group">
                                <label>Sent In</label> <br>
                                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                                       name="is_sent" data-width="84">
                            </div>

                            <div class="form-group">
                                <label>Barcode</label>
                                <input type="file" class="form-control" name="barcode" accept="image/*">
                            </div>
                        </div>

                        <div class="form-group rewrite-script-container">
                            <label>Rewrite Script</label>
                            <input type="file" class="form-control" name="rewrite_script" accept="application/pdf">
                        </div>

                        <div class="form-group trial-page-container">
                            <label>Trial Page</label>
                            <input type="file" class="form-control" name="trial_page" accept="image/*">
                        </div>

                        <div class="form-group print-ready-container">
                            <label>File</label>
                            <input type="file" class="form-control" name="print_ready" accept="application/pdf">
                        </div>

                        <div class="form-group sample-book-pdf-container">
                            <label>Sample Book/Pdf</label>
                            <input type="file" class="form-control" name="sample_book_pdf" accept="application/pdf">
                        </div>

                        <div class="description-container">
                            <div class="form-group">
                                <label>Print Ready</label>
                                <input type="file" class="form-control" name="print_ready" accept="application/pdf">
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

    <div id="indesignModal" class="modal fade" role="dialog">
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

                        <div class="form-group">
                            <label>Cover</label>
                            <input type="file" class="form-control" name="cover[]" accept="*" multiple>
                        </div>

                        <div class="form-group">
                            <label>Interior</label>
                            <input type="file" class="form-control" name="interior" accept="*" multiple>
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

    <div id="deleteGraphicWorkModal" class="modal fade" role="dialog">
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

    <div id="bookPicturesModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Book Picture
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label>Images</label>
                            <input type="file" name="images[]" class="form-control"
                                   accept="image/*" multiple>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete') }} <em></em></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        Are you sure you want to delete this record?
                        <div class="text-right margin-top">
                            <button class="btn btn-danger" type="submit">{{ trans('site.delete') }}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
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
                            <input type="file" name="file" class="form-control"
                                   accept="application/pdf">
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

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="approveFeedbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Approve Feedback
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        Are you sure you want to approve this feedback?
                        <div class="text-right margin-top">
                            <button class="btn btn-primary" type="submit">{{ trans('site.submit') }}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script type="text/javascript" src="{{asset('select2/dist/js/select2.min.js')}}"></script>
    <script>
        $(".graphicWorkBtn").click(function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let record = $(this).data('record');
            let modal = $("#graphicWorkModal");
            let form = modal.find("form");
            let checkbox = '';

            let coverContainer = $(".cover-container");
            let barcodeContainer = $(".barcode-container");
            let rewriteScriptContainer = $(".rewrite-script-container");
            let trialPageContainer = $(".trial-page-container");
            let printReadyContainer = $(".print-ready-container");
            let sampleBookPdfContainer = $(".sample-book-pdf-container");
            let descriptionContainer = $(".description-container");

            coverContainer.addClass('hide');
            barcodeContainer.addClass('hide');
            rewriteScriptContainer.addClass('hide');
            trialPageContainer.addClass('hide');
            printReadyContainer.addClass('hide');
            sampleBookPdfContainer.addClass('hide');
            descriptionContainer.addClass('hide');

            switch (type) {
                case 'cover':
                    modal.find('.modal-title').text('Cover');
                    coverContainer.removeClass('hide');
                    checkbox = 'is_approved';
                    break;

                case 'barcode':
                    modal.find('.modal-title').text('Barcode');
                    barcodeContainer.removeClass('hide');
                    checkbox = 'is_sent';
                    break;

                case 'trial-page':
                    modal.find('.modal-title').text('Trial Page');
                    trialPageContainer.removeClass('hide');
                    break;

                case 'print-ready':
                    modal.find('.modal-title').text('Print Ready');
                    printReadyContainer.removeClass('hide');
                    break;

                case 'sample-book-pdf':
                    modal.find('.modal-title').text('Sample Book/PDF');
                    sampleBookPdfContainer.removeClass('hide');
                    break;

                case 'rewrite-script':
                    modal.find('.modal-title').text('Rewrite Script');
                    rewriteScriptContainer.removeClass('hide');
                    break;

                case 'description':
                    modal.find('.modal-title').text('Print Ready');
                    descriptionContainer.removeClass('hide');
                    break;
            }

            form.find('[name=type]').val(type);
            if (id) {
                form.find('[name=id]').val(id);

                if (['cover', 'barcode'].includes(type)) {
                    form.find('[name=' + checkbox + ']').prop('checked', false).change();
                    if (record.is_checked) {
                        form.find('[name=' + checkbox + ']').prop('checked', true).change();
                    }
                }

            }
        });

        $(".indesignBtn").click(function(){
            let id = $(this).data('id');
            let type = $(this).data('type');
            let record = $(this).data('record');
            let modal = $("#indesignModal");
            let form = modal.find("form");
            modal.find('.modal-title').text('Indesign');
            form.find('[name=id]').val(id);
            form.find('[name=type]').val(type);
        });

        $(".deleteGraphicWorkBtn").click(function() {
            let type = $(this).data('type');
            let modal = $("#deleteGraphicWorkModal");
            let form = modal.find("form");
            let action = $(this).data('action');
            let pageTitle = '';

            switch (type) {
                case 'cover':
                    pageTitle = 'Cover';
                    break;
                case 'barcode':
                    pageTitle = 'Barcode';
                    break;
                case 'rewrite-script':
                    pageTitle = 'Rewrite Script';
                    break;
                case 'trial-page':
                    pageTitle = 'Trial Page';
                    break;
                case 'sample-book-pdf':
                    pageTitle = 'Sample Book/PDF';
                    break;
            }

            modal.find('.modal-title').text('Delete ' + pageTitle);
            form.attr('action', action);
        });

        $(".saveBookPictureBtn").click(function() {
            let action = $(this).data('action');
            let record = $(this).data('record');
            let modal = $('#bookPicturesModal');
            modal.find('form').attr('action', action);

            if (record) {
                modal.find('[name=id]').val(record.id);
            }
        });

        $(".deleteBookPictureBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#deleteModal');
            modal.find('form').attr('action', action);
        });

        $(".bookFormattingBtn").click(function(){
            let action = $(this).data('action');
            let record = $(this).data('record');
            let modal = $('#bookFormattingModal');
            modal.find('form').attr('action', action);

            if (record) {
                modal.find('[name=id]').val(record.id);
            }
        });

        $(".approveFeedbackBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#approveFeedbackModal');
            modal.find('form').attr('action', action);
        });

        $(".deleteBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#deleteModal');
            modal.find('form').attr('action', action);
        });
    </script>
@stop
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
    </div>
    <div class="col-sm-12 margin-top">
        <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                data-type="cover">+ Add Cover</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Cover</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($covers as $cover)
                    <tr>
                        <td>{!! $cover->image !!}</td>
                        <td>
                            <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                    data-target="#graphicWorkModal"
                                    data-type="cover" data-id="{{ $cover->id }}">
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

        <!-- TODO: only one record per project -->
        @if(!$barCodes->count())
            <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                    data-type="barcode">+ Add Barcode</button>
        @endif
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Barcode</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($barCodes as $barCode)
                        <tr>
                            <td>{!! $barCode->image !!}</td>
                            <td>
                                <button class="btn btn-primary btn-xs graphicWorkBtn" data-toggle="modal"
                                        data-target="#graphicWorkModal"
                                        data-type="barcode" data-id="{{ $barCode->id }}">
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
                data-type="rewrite-script">+ Add Rewrite script</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Rewrite script</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($rewriteScripts as $rewriteScript)
                    <tr>
                        <td>{!! $rewriteScript->file_link !!}</td>
                        <td>
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
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($trialPages as $trialPage)
                    <tr>
                        <td>{!! $trialPage->image !!}</td>
                        <td>
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
        </div>

        <button type="button" class="btn btn-success graphicWorkBtn" data-toggle="modal" data-target="#graphicWorkModal"
                data-type="sample-book-pdf">+ Add Sample book/PDF</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Sample book/PDF</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($sampleBookPDFs as $sampleBookPDF)
                    <tr>
                        <td>{!! $sampleBookPDF->file_link !!}</td>
                        <td>
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

                        <div class="form-group cover-container">
                            <label>Cover</label>
                            <input type="file" class="form-control" name="cover" accept="image/*">
                        </div>

                        <div class="form-group barcode-container">
                            <label>Barcode</label>
                            <input type="file" class="form-control" name="barcode" accept="image/*">
                        </div>

                        <div class="form-group rewrite-script-container">
                            <label>Rewrite Script</label>
                            <input type="file" class="form-control" name="rewrite_script" accept="application/pdf">
                        </div>

                        <div class="form-group trial-page-container">
                            <label>Trial Page</label>
                            <input type="file" class="form-control" name="trial_page" accept="image/*">
                        </div>

                        <div class="form-group sample-book-pdf-container">
                            <label>Sample Book/Pdf</label>
                            <input type="file" class="form-control" name="sample_book_pdf" accept="application/pdf">
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
@stop

@section('scripts')
    <script>
        $(".graphicWorkBtn").click(function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let modal = $("#graphicWorkModal");
            let form = modal.find("form");

            let coverContainer = $(".cover-container");
            let barcodeContainer = $(".barcode-container");
            let rewriteScriptContainer = $(".rewrite-script-container");
            let trialPageContainer = $(".trial-page-container");
            let sampleBookPdfContainer = $(".sample-book-pdf-container");

            coverContainer.addClass('hide');
            barcodeContainer.addClass('hide');
            rewriteScriptContainer.addClass('hide');
            trialPageContainer.addClass('hide');
            sampleBookPdfContainer.addClass('hide');

            switch (type) {
                case 'cover':
                    modal.find('.modal-title').text('Cover');
                    coverContainer.removeClass('hide');
                    break;
                case 'barcode':
                    modal.find('.modal-title').text('Barcode');
                    barcodeContainer.removeClass('hide');
                    break;

                case 'trial-page':
                    modal.find('.modal-title').text('Trial Page');
                    trialPageContainer.removeClass('hide');
                    break;
                case 'sample-book-pdf':
                    modal.find('.modal-title').text('Sample Book/PDF');
                    sampleBookPdfContainer.removeClass('hide');
                    break;
                case 'rewrite-script':
                    modal.find('.modal-title').text('Rewrite Script');
                    rewriteScriptContainer.removeClass('hide');
                    break;
            }

            form.find('[name=type]').val(type);
            if (id) {
                form.find('[name=id]').val(id);
            }
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
    </script>
@stop
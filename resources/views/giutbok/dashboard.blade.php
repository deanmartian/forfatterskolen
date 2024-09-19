@extends('giutbok.layout')

@section('title')
    <title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/editor.css')}}">
    <style>
        .panel {
            overflow-x: auto;
        }
    </style>
@stop

@section('content')
    <div class="col-sm-12 dashboard-left">
        <div class="row">
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h4>Self Publishing</h4></div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('site.title') }}</th>
                                        <th>{{ trans('site.description') }}</th>
                                        <th>File</th>
                                        <th>Project</th>
                                        <th>{{ trans('site.expected-finish') }}</th>
                                        <th width="90"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($selfPublishingList as $publishing)
                                        <tr>
                                            <td>
                                                {{ $publishing->title }}
                                            </td>
                                            <td>
                                                {{ $publishing->description }}
                                            </td>
                                            <td>
                                                <a href="{{ route('g-admin.self-publishing.download-manuscript', $publishing->id) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a> &nbsp;{!! $publishing->file_link !!}
                                            </td>
                                            <td>
                                                @if($publishing->project)
                                                    <a href="{{ route('g-admin.project.show', $publishing->project->id) }}">
                                                        {{ $publishing->project->name }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $publishing->expected_finish }}
                                            </td>
                                            <td>
                                                <a href="{{ route('g-admin.self-publishing.learners', $publishing->id) }}" class="btn btn-success btn-xs">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                                <button class="btn btn-primary btn-xs editSelfPublishingBtn" data-toggle="modal"
                                                        data-target="#selfPublishingModal" data-fields="{{ json_encode($publishing) }}"
                                                        data-action="{{ route('g-admin.self-publishing.update', $publishing->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-xs deleteSelfPublishingBtn" data-toggle="modal"
                                                        data-target="#deleteSelfPublishingModal"
                                                        data-action="{{ route('g-admin.self-publishing.destroy', $publishing->id) }}">
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
                </div> <!-- end self-publishing -->

                <!-- My corrections -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h4>{{ trans('site.my-correction') }}</h4></div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.manus', 2) }}</th>
                                        <th>{{ trans_choice('site.learners', 1) }}</th>
                                        <th>{{ trans('site.expected-finish') }}</th>
                                        <th>{{ trans('site.status') }}</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- end correction -->

                <!-- My Copy Editing -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h4>{{ trans('site.my-copy-editing') }}</h4></div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.manus', 2) }}</th>
                                        <th>{{ trans_choice('site.learners', 1) }}</th>
                                        <th>{{ trans('site.expected-finish') }}</th>
                                        <th>{{ trans('site.status') }}</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- end copy editing -->

                <!-- Page Formatting -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h4>Page Format</h4></div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.manus', 2) }}</th>
                                        <th>Corporate Page</th>
                                        <th>Format</th>
                                        <th>Format Image</th>
                                        <th>Description</th>
                                        <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pageFormats as $pageFormat)
                                            <tr>
                                                <td>
                                                    {{-- <a href="{{ route('dropbox.download_file', trim($pageFormat->file)) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>&nbsp;
                                                    <a href="{{ route('dropbox.shared_link', trim($pageFormat->file)) }}" 
                                                        target="_blank">
                                                        {{ basename($pageFormat->file) }}
                                                    </a> --}}
                                                    {!! $pageFormat->file_link !!}
                                                </td>
                                                <td>
                                                    {!! $pageFormat->corporate_page_link !!}
                                                </td>
                                                <td>
                                                    {{ $pageFormat->format 
                                                        ? AdminHelpers::projectFormats($pageFormat->format) : null }}
                                                </td>
                                                <td>
                                                    {!! $pageFormat->format_image_link !!}
                                                </td>
                                                <td>
                                                    {!! $pageFormat->description !!}
                                                </td>
                                                <td>
                                                    @if($pageFormat->feedback)
                                                        <span class="label label-default">{{ trans('site.pending') }}</span>
                                                        <button class="btn btn-xs btn-success pageFormatFeedbackBtn"
																data-target = "#pageFormatFeedbackModal"
																data-toggle = "modal"
																data-action="{{ route('g-admin.book-format.add-feedback', 
                                                                $pageFormat->id) }}">
															<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
														</button>
                                                    @else
                                                        <button class="btn btn-warning btn-xs d-block
														pageFormatFeedbackBtn"
																data-target="#pageFormatFeedbackModal"
																data-toggle="modal"
																data-name=""
																data-action="{{ route('g-admin.book-format.add-feedback', 
                                                                $pageFormat->id) }}">
															+ {{ trans('site.add-feedback') }}
														</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Page Formatting -->

                <!-- Project Book -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h4>Project Book</h4></div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Book</th>
                                            <th>Description</th>
                                            <th>Width (mm)</th>
                                            <th>Height (mm)</th>
                                            <th>Page Count</th>
                                            <th>Designer Description</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projectWholeBooks as $projectWholeBook)
                                            <tr>
                                                <td>
                                                    {!! $projectWholeBook->file_link !!}
                                                </td>
                                                <td>
                                                    {{ $projectWholeBook->description }}
                                                </td>
                                                <td>
                                                    {{ $projectWholeBook->width }}
                                                </td>
                                                <td>
                                                    {{ $projectWholeBook->height }}
                                                </td>
                                                <td>
                                                    {{ $projectWholeBook->page_count }}
                                                </td>
                                                <td>
                                                    {{ $projectWholeBook->designer_description }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-xs editWholeBookBtn" data-toggle="modal" 
                                                        data-target="#editWholeBookModal"
                                                        data-action="{{ route('g-admin.project-whole-book.update', 
                                                        $projectWholeBook->id) }}"
                                                        data-record="{{ json_encode($projectWholeBook) }}">
                                                        <i class="fa fa-edit"></i>
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
                <!-- End Project Book -->
            </div>
        </div>
    </div>

    <div id="selfPublishingModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" name="title" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea name="description"cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" name="manuscript[]" class="form-control" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple>
                        </div>

                        <div class="form-group" id="learner-list">
                            <label>
                                {{ trans_choice('site.learners', 2) }}
                            </label>
                            <select name="learners[]" class="form-control select2 template" multiple="multiple">
                                @foreach($learners as $learner)
                                    <option value="{{$learner->id}}">
                                        {{$learner->full_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.expected-finish') }}
                            </label>
                            <input type="date" class="form-control" name="expected_finish">
                        </div>

                        <div class="form-group">
                            <label>
                                Project
                            </label>
                            <select name="project_id" class="form-control select2">
                                <option value="" selected disabled> - Select Project - </option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="price" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Editor Share</label>
                            <input type="number" name="editor_share" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteSelfPublishingModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete') }} <em></em></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
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

    <div id="pageFormatFeedbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Feedback <em></em></h4>
                </div>
                <div class="modal-body">
                    <form  method="POST" action=""  enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label name="manuscriptLabel">{{ trans_choice('site.feedbacks', 1) }}</label>
                            <input type="file" class="form-control" required name="file"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                       application/pdf, application/vnd.oasis.opendocument.text">
                            {{ trans('site.docx-pdf-odt-text') }} <br>
                        </div>
                        
                        <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editWholeBookModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Project Whole Book <em></em></h4>
                </div>
                <div class="modal-body">
                    <form  method="POST" action=""  enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="pageCount">How many pages?</label>
                            <input 
                              type="number" 
                              id="pageCount" 
                              name="page_count"
                              class="form-control" 
                              placeholder="Enter number of pages"
                              required>
                        </div>

                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="designer_description" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".editSelfPublishingBtn").click(function() {
            let modal = $("#selfPublishingModal");
            let form = modal.find('form');
            let fields = $(this).data('fields');

            modal.find('.modal-title').text('Edit Self Publishing');
            form.find('[name=_method]').remove();
            form.prepend("<input type='hidden' name='_method' value='PUT'>");
            $("#learner-list").hide();

            let action = $(this).data('action');
            form.attr('action', action);
            form.find('input[name=title]').val(fields.title);
            form.find('textarea[name=description]').val(fields.description);
            form.find('select[name=editor_id]').val(fields.editor_id).trigger('change');
            form.find('input[name=expected_finish]').val(fields.expected_finish);
            form.find('select[name=project_id]').val(fields.project_id).trigger('change');
            form.find('input[name=price]').val(fields.price);
            form.find('input[name=editor_share]').val(fields.editor_share);
        });

        $(".deleteSelfPublishingBtn").click(function() {
            let action = $(this).data('action');
            let modal = $("#deleteSelfPublishingModal");

            let form = modal.find('form');
            form.attr('action', action);
        })

        $(".pageFormatFeedbackBtn").click(function() {
            let action = $(this).data('action');
            let modal = $("#pageFormatFeedbackModal");

            let form = modal.find('form');
            form.attr('action', action);
        });

        $(".editWholeBookBtn").click(function() {
            let modal = $("#editWholeBookModal");
            let action = $(this).data('action');
            let record = $(this).data('record');

            let form = modal.find('form');
            form.attr('action', action);
            form.find("[name=page_count]").val(record.page_count);
            form.find("[name=width]").val(record.width);
            form.find("[name=height]").val(record.height);
            form.find("[name=designer_description]").text(record.designer_description);
        });
        
    </script>
@stop
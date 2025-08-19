@extends('backend.layout')

@section('title')
    <title>Files &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> {{ trans('site.learner.files-text') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <button class="btn btn-primary margin-top addFileBtn" data-toggle="modal" data-target="#fileModal"
        data-label="{{ trans('site.add-file') }}">
            {{ trans('site.add-file') }}
        </button>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>URL</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                        <?php
                            $extension = explode('.', basename($file->file_location));
                            $fileDisplay = '';

                            if (end($extension) == 'pdf' || end($extension) == 'odt') {
                                $fileDisplay = '<a href="/js/ViewerJS/#../../'.trim($file->file_location).'">'
                                    .basename($file->file_location).'</a>';
                            } else {
                                $fileDisplay = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='
                                    .url('').trim("/".$file->file_location).'">'.basename($file->file_location).'</a>';
                            }
                        ?>
                        <tr>
                            <td>
                                {{ config('app.live_url').'/file/'.$file->hash }}
                            </td>
                            <td>
                                {!! $fileDisplay !!}
                            </td>
                            <td>
                                <input type="text" value="{{ config('app.live_url').'/file/'.$file->hash }}"
                                       style="position: absolute; left: -10000px;">
                                <button type="button" class="btn btn-success btn-xs copyToClipboard">
                                    <i class="fa fa-clipboard"></i>
                                </button>
                                <button type="button" class="btn btn-info btn-xs editFileBtn" data-toggle="modal"
                                        data-label="{{ trans('site.edit-file') }}"
                                        data-action="{{ route('admin.file.update', $file->id) }}"
                                        data-target="#fileModal"><i class="fa fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-xs deleteFileBtn" data-toggle="modal"
                                        data-action="{{ route('admin.file.destroy', $file->id) }}"
                                        data-target="#deleteFileModal"><i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="fileModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.files', 1) }}</label>
                            <input type="file" class="form-control" required name="file"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
                            * {{ trans('site.docx-pdf-odt-text') }}
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteFileModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        {{ trans('site.delete-file') }}
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}
                        {{ method_field('DELETE') }}

                            <p>
                                {{ trans('site.delete-file-question') }}
                            </p>

                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete-file') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script>
        let modal = $("#fileModal");
        let title = '';
        $(".addFileBtn").click(function(){
            title = $(this).data('label');
            modal.find('form').attr('action', '');
            modal.find('form').find('[name=_method]').remove();
            modal.find('.modal-title').text(title);
        });

        $(".editFileBtn").click(function(){
            title = $(this).data('label');
            let action = $(this).data('action');
            modal.find('form').attr('action', action);
            modal.find('form').prepend('<input type="hidden" name="_method" value="PUT">');
            modal.find('.modal-title').text(title);
        });

        $(".deleteFileBtn").click(function(){
            title = $(this).data('label');
            let action = $(this).data('action');
            $("#deleteFileModal").find('form').attr('action', action);
        });

        // not working on hidden fields
        $(".copyToClipboard").click(function(){
            let copyText = $(this).closest('td').find('[type=text]');
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");

            toastr.success('Copied to clipboard.', "Success");
            if (window.getSelection) {
                if (window.getSelection().empty) {  // Chrome
                    window.getSelection().empty();
                } else if (window.getSelection().removeAllRanges) {  // Firefox
                    window.getSelection().removeAllRanges();
                }
            } else if (document.selection) {  // IE?
                document.selection.empty();
            }
        });
    </script>
@stop
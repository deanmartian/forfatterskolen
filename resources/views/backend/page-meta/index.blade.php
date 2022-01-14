@extends('backend.layout')

@section('title')
    <title>Page Meta &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Page Meta </h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <button type="button" class="btn btn-success margin-top" data-toggle="modal"
                data-target="#addPageMetaModal">Add Page Meta</button>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Link</th>
                    <th>Meta Title</th>
                    <th>Meta Description</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pageMetas as $pageMeta)
                    <tr>
                        <td>
                            <a href="{{ $pageMeta->url }}">{{ $pageMeta->url }}</a>
                        </td>
                        <td>
                            {{ $pageMeta->meta_title }}
                        </td>
                        <td>{{ $pageMeta->meta_description }}</td>
                        <td>
                            <a href="{{ asset($pageMeta->meta_image) }}" target="_blank">
                                {{ $pageMeta->meta_image }}
                            </a>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-xs pull-right editPageMetaBtn"
                                    data-toggle="modal" data-target="#editPageMetaModal" data-fields="{{ json_encode($pageMeta) }}"
                                    data-action="{{ route('admin.page_meta.update', $pageMeta->id) }}"
                            data-filename="{{ \App\Http\AdminHelpers::extractFileName($pageMeta->meta_image) }}"
                            data-fileloc="{{ asset($pageMeta->meta_image) }}">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <div class="clearfix"></div>
                            <button type="button" class="btn btn-danger btn-xs pull-right deletePageMetaBtn"
                                    data-toggle="modal" data-target="#deletePageMetaModal"
                                    data-action="{{ route('admin.page_meta.delete', $pageMeta->id) }}"
                                    style="margin-top: 5px">
                                <i class="fa fa-close"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="addPageMetaModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Page Meta</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.page_meta.store') }}" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Page Url</label>
                            <input type="text" name="url" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" minlength="40" maxlength="70" required>
                        </div>
                        <div class="form-group">
                            <label>Meta Image</label>
                            <input type="file" name="meta_image" accept="image/jpg, image/jpeg, image/png">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea class="form-control" name="meta_description" rows="6" maxlength="160"
                                      minlength="70"
                                      onkeyup="countChar(this)" required></textarea>
                            <div class="charNum">160 characters left</div>
                        </div>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editPageMetaModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Page Meta</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label>Page Url</label>
                            <input type="text" name="url" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" minlength="40" maxlength="70" required>
                        </div>
                        <div class="form-group">
                            <label>Meta Image</label>
                            <input type="file" name="meta_image" accept="image/jpg, image/jpeg, image/png">
                            <p class="image-display text-center">
                            </p>
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea class="form-control" name="meta_description" rows="6" maxlength="160"
                                      minlength="70"
                                      onkeyup="countChar(this)" required></textarea>
                            <div class="charNum">160 characters left</div>
                        </div>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deletePageMetaModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Page Meta</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{method_field('DELETE')}}
                        Are you sure to delete this page meta?
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".editPageMetaBtn").click(function(){
            let fields = $(this).data('fields');
            let modal = $('#editPageMetaModal');
            let action = $(this).data('action');
            let filename = $(this).data('filename');
            let fileloc = $(this).data('fileloc');
            modal.find('form').attr('action', action);
            modal.find('input[name=url]').val(fields.url);
            modal.find('input[name=meta_title]').val(fields.meta_title);
            modal.find('textarea[name=meta_description]').text(fields.meta_description);
            modal.find('.image-display').empty().append("<a href='"+fileloc+"'>"+filename+"</a>");
        });

        $(".deletePageMetaBtn").click(function(){
            let modal = $('#deletePageMetaModal');
            let action = $(this).data('action');
            modal.find('form').attr('action', action);
        });

        $('#editPageMetaModal').on('show.bs.modal', function () {
            let len = $(this).find('textarea').val().length;
            let charText = "characters left";
            if (350 - len === 1) {
                charText = "character left";
            }
            $(this).find('.charNum').text(160 - len + " "+charText);
        });

        function countChar(val) {
            let len = val.value.length;
            if (len >= 160) {
                val.value = val.value.substring(0, 160);
                $('.charNum').text(0 + " character left");
            } else {
                let charText = "characters left";
                if (160 - len === 1) {
                    charText = "character left";
                }
                $('.charNum').text(160 - len + " "+charText);
            }
        }
    </script>
@stop
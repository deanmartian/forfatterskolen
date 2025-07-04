@extends('backend.layout')

@section('title')
    <title>Files &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-tasks"></i> {{ trans('site.genre') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <button class="btn btn-primary margin-top addGenreBtn pull-right" data-toggle="modal" data-target="#genreModal"
                        data-label="Add Genre">
                    Add Genre
                </button>

                <div class="clearfix"></div>

                <div class="table-users table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('site.name') }}</th>
                            <th width="250"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($genres as $genre)
                            <tr>
                                <td>
                                    {{ $genre->id }}
                                </td>
                                <td>
                                    {{ $genre->name }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-xs editGenreBtn" data-toggle="modal"
                                            data-label="{{ trans('site.edit-genre') }}"
                                            data-action="{{ route('admin.genre.update', $genre->id) }}"
                                            data-genre="{{ $genre->name }}"
                                            data-target="#genreModal"><i class="fa fa-pencil"></i>
                                    </button>

                                    <button type="button" class="btn btn-danger btn-xs deleteGenreBtn" data-toggle="modal"
                                            data-action="{{ route('admin.genre.destroy', $genre->id) }}"
                                            data-target="#deleteGenreModal"><i class="fa fa-trash"></i>
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

    <div id="genreModal" class="modal fade" role="dialog" data-backdrop="static">
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
                            <label>{{ trans('site.name') }}</label>
                            <input type="text" name="name" class="form-control" required>
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

    <div id="deleteGenreModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Delete Genre
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}
                        {{ method_field('DELETE') }}

                        <p>
                            Are you sure you want to delete this genre?
                        </p>

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
        let modal = $("#genreModal");
        let title = '';

        $(".addGenreBtn").click(function(){
            title = $(this).data('label');
            modal.find('form').attr('action', '');
            modal.find('form').find('[name=_method]').remove();
            modal.find('.modal-title').text(title);
        });

        $(".editGenreBtn").click(function(){
            title = $(this).data('label');
            let action = $(this).data('action');
            let genre = $(this).data('genre');
            modal.find('form').attr('action', action);
            modal.find('form').prepend('<input type="hidden" name="_method" value="PUT">');
            modal.find('.modal-title').text(title);
            modal.find('[name=name]').val(genre);
        });

        $(".deleteGenreBtn").click(function(){
            title = $(this).data('label');
            let action = $(this).data('action');
            $("#deleteGenreModal").find('form').attr('action', action);
        });
    </script>
@stop
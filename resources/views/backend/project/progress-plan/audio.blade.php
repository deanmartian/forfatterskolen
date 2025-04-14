@extends($layout)

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default mr-2">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-file-text-o"></i> {{ $stepTitle }}</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <section>
            <button type="button" class="btn btn-success audioBtn" data-toggle="modal" data-target="#audioModal"
                    data-type="files">+ Add Audio Files</button>
            <div class="table-responsive margin-top">
                <table class="table table-side-bordered table-white">
                    <thead>
                    <tr>
                        <th>Audio</th>
                        <th width="300"></th>
                    </tr>
                    </thead>
                    @foreach ($files as $file)
                        <tr>
                            <td>
                                <a href="{{ url('/dropbox/download/' . trim($file->value)) }}">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>&nbsp;
    
                                {!! $file->file_link !!}
                            </td>
                            <td>                      
                                <button class="btn btn-primary btn-xs audioBtn" data-toggle="modal"
                                        data-target="#audioModal"
                                        data-type="files" data-id="{{ $file->id }}"
                                        data-record="{{ json_encode($file) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteAudioBtn" data-toggle="modal"
                                        data-target="#deleteAudioModal" data-type="files"
                                        data-action="{{ route($deleteAudioRoute, [$file->project_id, $file->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </section>

        <section>
            <button type="button" class="btn btn-success audioBtn" data-toggle="modal" data-target="#audioModal"
                    data-type="cover">+ Add Audio Cover</button>
            <div class="table-responsive margin-top">
                <table class="table table-side-bordered table-white">
                    <thead>
                    <tr>
                        <th>Audio Cover</th>
                        <th width="300"></th>
                    </tr>
                    </thead>
                    @foreach ($covers as $cover)
                        <tr>
                            <td>
                                <a href="{{ url('/dropbox/download/' . trim($cover->value)) }}">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>&nbsp;
    
                                {!! $cover->file_link !!}
                            </td>
                            <td>                      
                                <button class="btn btn-primary btn-xs audioBtn" data-toggle="modal"
                                        data-target="#audioModal"
                                        data-type="cover" data-id="{{ $cover->id }}"
                                        data-record="{{ json_encode($cover) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteAudioBtn" data-toggle="modal"
                                        data-target="#deleteAudioModal" data-type="cover"
                                        data-action="{{ route($deleteAudioRoute, [$cover->project_id, $cover->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </section>
    </div>

    <div id="audioModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveAudioRoute, $project->id) }}"
                        enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                          {{ csrf_field() }}
                          <input type="hidden" name="id">
                          <input type="hidden" name="type">
    
                        <div class="form-group files-container">
                            <label>File</label>
                            <input type="file" class="form-control" name="files">
                        </div>
    
                        <div class="form-group cover-container">
                            <label>Cover</label>
                            <input type="file" class="form-control" name="cover">
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
    
    <div id="deleteAudioModal" class="modal fade" role="dialog">
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
@endsection

@section('scripts')
<script>
    $(".audioBtn").click(function() {
        let id = $(this).data('id');
        let type = $(this).data('type');
        let record = $(this).data('record');
        let modal = $("#audioModal");
        let form = modal.find("form");

        let filesContainer = $(".files-container");
        let coverContainer = $(".cover-container");

        filesContainer.addClass('hide');
        coverContainer.addClass('hide');

        switch (type) {
            case 'files':
                modal.find('.modal-title').text('Files');
                filesContainer.removeClass('hide');
                break;

            case 'cover':
                modal.find('.modal-title').text('Cover');
                coverContainer.removeClass('hide');
                break;
        }

        form.find('[name=type]').val(type);
        if (id) {
            form.find('[name=id]').val(id);
        }
    });

    $(".deleteAudioBtn").click(function() {
        let type = $(this).data('type');
        let modal = $("#deleteAudioModal");
        let form = modal.find("form");
        let action = $(this).data('action');
        let pageTitle = '';

        switch (type) {
            case 'files':
                pageTitle = 'Files';
                break;

            case 'cover':
                pageTitle = 'Cover';
                break;
        }

        modal.find('.modal-title').text('Delete ' + pageTitle);
        form.attr('action', action);
    });
</script>
@endsection
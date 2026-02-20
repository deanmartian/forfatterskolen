@extends('frontend.learner.self-publishing.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <a href="{{ route('learner.progress-plan') }}" class="btn btn-secondary mb-3">
            <i class="fa fa-arrow-left"></i> Back
        </a>

        <div class="card">
            <div class="card-header">
                {{ $stepTitle }}
            </div>

            <div class="card-body">
                <section>
                    <button type="button" class="btn btn-success btn-xs pull-right audioBtn" data-toggle="modal" 
                        data-target="#audioModal" data-type="files">+ Add Audio Files</button>
                    <div class="table-responsive margin-top">
                        <table class="table table-side-bordered table-white">
                            <thead>
                                <tr>
                                    <th>Audio</th>
                                    <th width="300"></th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-3">
                    <button type="button" class="btn btn-success btn-xs pull-right audioBtn" data-toggle="modal" 
                        data-target="#audioModal" data-type="cover">+ Add Audio Cover</button>
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
                                        <a href="{{ route('dropbox.download_file', trim($cover->value)) }}">
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
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<div id="audioModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route($saveAudioRoute, $standardProject->id) }}"
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
</script>
@endsection
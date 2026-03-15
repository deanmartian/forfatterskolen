@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Audio &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 dashboard-course">
                <h1 class="d-inline-block">
                    {{ trans('site.audio-book') }}
                </h1>

                <div class="card global-card">
                    <div class="card-header">
                        @if ($standardProject)
                            <button type="button" class="btn btn-success float-end audioBtn" data-bs-toggle="modal" 
                            data-bs-target="#audioModal" data-type="files">
                                + {{ trans('site.add-audio-files') }}
                            </button>
                        @endif
                    </div>
                    <div class="card-body py-0">
                        <table class="table table-side-bordered table-white">
                            <thead>
                            <tr>
                                <th>{{ trans('site.audio') }}</th>
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
                                        <button class="btn btn-primary btn-sm audioBtn" data-bs-toggle="modal"
                                                data-bs-target="#audioModal"
                                                data-type="files" data-id="{{ $file->id }}"
                                                data-record="{{ json_encode($file) }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm deleteAudioBtn" data-bs-toggle="modal"
                                                data-bs-target="#deleteAudioModal" data-type="files"
                                                data-action="{{ route('learner.self-publishing.delete-audio', 
                                                [$file->project_id, $file->id]) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div> <!-- end global-card -->

                <div class="card global-card mt-4">
                    <div class="card-header">
                        @if ($standardProject)
                            <button type="button" class="btn btn-success float-end audioBtn" data-bs-toggle="modal" 
                            data-bs-target="#audioModal" data-type="cover">+ {{ trans('site.add-audio-cover') }}</button>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-side-bordered table-white">
                            <thead>
                            <tr>
                                <th>{{ trans('site.audio-cover') }}</th>
                                <th width="300"></th>
                            </tr>
                            </thead>
                            @foreach ($covers as $cover)
                                <tr>
                                    <td>
                                        @if ($cover->value)
                                            <a href="{{ url('/dropbox/download/' . trim($cover->value)) }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>&nbsp;

                                            {!! $cover->file_link !!}
                                        @endif
                                    </td>
                                    <td>                      
                                        <button class="btn btn-primary btn-sm audioBtn" data-bs-toggle="modal"
                                                data-bs-target="#audioModal"
                                                data-type="cover" data-id="{{ $cover->id }}"
                                                data-record="{{ json_encode($cover) }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm deleteAudioBtn" data-bs-toggle="modal"
                                                data-bs-target="#deleteAudioModal" data-type="cover"
                                                data-action="{{ route('learner.self-publishing.delete-audio', 
                                                    [$cover->project_id, $cover->id]) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($standardProject)
    <div id="audioModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                    </h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.self-publishing.save-audio', $standardProject->id) }}"
                        enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <input type="hidden" name="type">

                        <div class="form-group files-container">
                            <label>{{ trans_choice('site.files',1) }}</label>
                            <input type="file" class="form-control" name="files">
                        </div>

                        <div class="form-group cover-container">
                            <label>{{ trans('site.homepage.illustration-cover-design') }}</label>
                            <input type="file" class="form-control" name="cover">
                        </div>

                        <button type="submit" class="btn btn-success float-end margin-top">
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
                    <h4 class="modal-title">
                    </h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>{{ trans('site.delete-question') }}</p>

                        <button type="submit" class="btn btn-danger float-end margin-top">
                            {{ trans('site.delete') }}
                        </button>

                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    let translations = {
        files : "{{ trans_choice('site.files',2) }}",
        cover : "{{ trans('site.homepage.illustration-cover-design') }}",
        delete: "{{ trans('site.delete') }}"
        };

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
                modal.find('.modal-title').text(translations.files);
                filesContainer.removeClass('hide');
                break;

            case 'cover':
                modal.find('.modal-title').text(translations.cover);
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
                pageTitle = translations.files;
                break;

            case 'cover':
                pageTitle = translations.cover;
                break;
        }

        modal.find('.modal-title').text(translations.delete + ' ' + pageTitle);
        form.attr('action', action);
    });
</script>
@endsection
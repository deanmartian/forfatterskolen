@extends('frontend.learner.self-publishing.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('page_title', 'Lydbok &rsaquo; Forfatterskolen')

@section('content')
<div class="learner-container">
    <div class="container">
        <a href="{{ route('learner.progress-plan') }}" class="btn btn-outline-brand mb-3">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Tilbake
        </a>

        <div class="card">
            <div class="sp-card-header">
                {{ $stepTitle }}
            </div>

            <div class="sp-card-body">
                <section>
                    <button type="button" class="btn btn-brand btn-sm float-end audioBtn" data-bs-toggle="modal"
                        data-bs-target="#audioModal" data-type="files">+ Legg til lydfiler</button>
                    <div class="table-responsive margin-top">
                        <table class="table table-side-bordered table-white">
                            <thead>
                                <tr>
                                    <th>Lyd</th>
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
                                            <button class="btn btn-brand btn-sm audioBtn" data-bs-toggle="modal"
                                                    data-bs-target="#audioModal"
                                                    data-type="files" data-id="{{ $file->id }}"
                                                    data-record="{{ json_encode($file) }}"
                                                    aria-label="Rediger lydfil">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-3">
                    <button type="button" class="btn btn-brand btn-sm float-end audioBtn" data-bs-toggle="modal"
                        data-bs-target="#audioModal" data-type="cover">+ Legg til lydbok-omslag</button>
                    <div class="table-responsive margin-top">
                        <table class="table table-side-bordered table-white">
                            <thead>
                            <tr>
                                <th>Lydbok-omslag</th>
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
                                        <button class="btn btn-brand btn-sm audioBtn" data-bs-toggle="modal"
                                                data-bs-target="#audioModal"
                                                data-type="cover" data-id="{{ $cover->id }}"
                                                data-record="{{ json_encode($cover) }}"
                                                aria-label="Rediger lydbok-omslag">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
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

<div id="audioModal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content sp-modal">
            <div class="sp-modal__header">
                <h3 class="sp-modal__title">
                    <i class="fas fa-headphones" style="color:var(--brand-primary);margin-right:6px"></i>
                    <span class="sp-modal__title-text"></span>
                </h3>
                <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route($saveAudioRoute, $standardProject->id) }}"
                enctype="multipart/form-data" onsubmit="disableSubmit(this)" data-sp-validate>
                  {{ csrf_field() }}
                  <input type="hidden" name="id">
                  <input type="hidden" name="type">
                <div class="sp-modal__body">
                    <div class="sp-form-group files-container">
                        <label class="sp-label">Fil</label>
                        <input type="file" class="sp-input" name="files"
                        data-sp-file-preview="audioFilesPreview">
                        <div id="audioFilesPreview"></div>
                    </div>

                    <div class="sp-form-group cover-container">
                        <label class="sp-label">Omslag</label>
                        <input type="file" class="sp-input" name="cover"
                        data-sp-file-preview="audioCoverPreview">
                        <div id="audioCoverPreview"></div>
                    </div>
                </div>
                <div class="sp-modal__footer">
                    <button type="button" class="btn-outline-brand" data-bs-dismiss="modal">Avbryt</button>
                    <button type="submit" class="btn-brand">{{ trans('site.save') }}</button>
                </div>
            </form>
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
                modal.find('.sp-modal__title-text').text('Filer');
                filesContainer.removeClass('hide');
                break;

            case 'cover':
                modal.find('.sp-modal__title-text').text('Omslag');
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
@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>E-bok &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <a href="{{ route('learner.progress-plan') }}" class="btn btn-outline-brand mb-3">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Tilbake
            </a>

            <div class="card">
                <div class="sp-card-header">
                    E-bok
                </div>
                <div class="sp-card-body">
                    <section>
                        <button type="button" class="btn btn-brand float-end ebookBtn" data-bs-toggle="modal"
                        data-bs-target="#ebookModal"
                        data-type="epub">+ Legg til Epub</button>
                        <div class="table-responsive margin-top">
                            <table class="table table-side-bordered table-white">
                                <thead>
                                    <tr>
                                        <th>Epub</th>
                                        <th width="300"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($epubs as $epub)
                                        <tr>
                                            <td>
                                                <a href="/dropbox/download/{{ trim($epub->value) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                
                                                {!! $epub->file_link !!}
                                            </td>
                                            <td>                      
                                                <button class="btn btn-brand btn-sm ebookBtn" data-bs-toggle="modal"
                                                        data-bs-target="#ebookModal"
                                                        data-type="epub" data-id="{{ $epub->id }}"
                                                        data-record="{{ json_encode($epub) }}"
                                                        aria-label="Rediger ePub">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="mt-5">
                        <button type="button" class="btn btn-brand ebookBtn float-end" data-bs-toggle="modal"
                        data-bs-target="#ebookModal"
                                data-type="mobi">+ Legg til Mobi</button>
                        <div class="table-responsive margin-top">
                            <table class="table table-side-bordered table-white">
                                <thead>
                                <tr>
                                    <th>Mobi</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mobis as $mobi)
                                        <tr>
                                            <td>
                                                <a href="/dropbox/download/{{ trim($mobi->value) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                
                                                {!! $mobi->file_link !!}
                                            </td>
                                            <td>                      
                                                <button class="btn btn-brand btn-sm ebookBtn" data-bs-toggle="modal"
                                                        data-bs-target="#ebookModal"
                                                        data-type="mobi" data-id="{{ $mobi->id }}"
                                                        data-record="{{ json_encode($mobi) }}"
                                                        aria-label="Rediger Mobi">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="mt-5">
                        <button type="button" class="btn btn-brand ebookBtn float-end" data-bs-toggle="modal"
                            data-bs-target="#ebookModal"
                                data-type="cover">+ Legg til omslag</button>
                        <div class="table-responsive margin-top">
                            <table class="table table-side-bordered table-white">
                                <thead>
                                <tr>
                                    <th>Omslag</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($covers as $cover)
                                        <tr>
                                            <td>
                                                <a href="{{ route('dropbox.download_file', trim($cover->value)) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                
                                                {!! $cover->file_link !!}
                                            </td>
                                            <td>                      
                                                <button class="btn btn-brand btn-sm ebookBtn" data-bs-toggle="modal"
                                                        data-bs-target="#ebookModal"
                                                        data-type="cover" data-id="{{ $cover->id }}"
                                                        data-record="{{ json_encode($cover) }}"
                                                        aria-label="Rediger omslag">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div id="ebookModal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content sp-modal">
                <div class="sp-modal__header">
                    <h3 class="sp-modal__title">
                        <i class="fas fa-book" style="color:var(--brand-primary);margin-right:6px"></i>
                        <span class="sp-modal__title-text"></span>
                    </h3>
                    <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route($saveEbookRoute, $standardProject->id) }}"
                    enctype="multipart/form-data" onsubmit="disableSubmit(this)" data-sp-validate>
                      {{ csrf_field() }}
                      <input type="hidden" name="id">
                      <input type="hidden" name="type">
                    <div class="sp-modal__body">
                        <div class="sp-form-group epub-container">
                            <label class="sp-label">Fil</label>
                            <input type="file" class="sp-input" name="epub"
                            data-sp-file-preview="ebookEpubPreview">
                            <div id="ebookEpubPreview"></div>
                        </div>

                        <div class="sp-form-group mobi-container">
                            <label class="sp-label">Fil</label>
                            <input type="file" class="sp-input" name="mobi"
                            data-sp-file-preview="ebookMobiPreview">
                            <div id="ebookMobiPreview"></div>
                        </div>

                        <div class="sp-form-group cover-container">
                            <label class="sp-label">Fil</label>
                            <input type="file" class="sp-input" name="cover"
                            data-sp-file-preview="ebookCoverPreview">
                            <div id="ebookCoverPreview"></div>
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
    $(".ebookBtn").click(function() {
        let id = $(this).data('id');
        let type = $(this).data('type');
        let record = $(this).data('record');
        let modal = $("#ebookModal");
        let form = modal.find("form");

        let epubContainer = $(".epub-container");
        let mobiContainer = $(".mobi-container");
        let coverContainer = $(".cover-container");

        epubContainer.addClass('hide');
        mobiContainer.addClass('hide');
        coverContainer.addClass('hide');

        switch (type) {
            case 'epub':
                modal.find('.sp-modal__title-text').text('Epub');
                epubContainer.removeClass('hide');
                break;

            case 'mobi':
                modal.find('.sp-modal__title-text').text('Mobi');
                mobiContainer.removeClass('hide');
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
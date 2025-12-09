@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>E-book &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 dashboard-course">
                <h1 class="d-inline-block">
                    {{ trans('site.e-book') }}
                </h1>

                <div class="card global-card">
                    <div class="card-header">
                        @if ($standardProject)
                            <button type="button" class="btn btn-success pull-right ebookBtn" data-toggle="modal" 
                                data-target="#ebookModal" data-type="epub">+ {{ trans('site.add-epub') }}</button>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-side-bordered table-white">
                            <thead>
                                <tr>
                                    <th>{{ trans('site.epub') }}</th>
                                    <th width="300"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($epubs as $epub)
                                    <tr>
                                        <td>
                                            <a href="{{ url('/dropbox/download/' . trim($epub->value)) }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>&nbsp;

                                            {!! $epub->file_link !!}
                                        </td>
                                        <td>                      
                                            <button class="btn btn-primary btn-xs ebookBtn" data-toggle="modal"
                                                    data-target="#ebookModal"
                                                    data-type="epub" data-id="{{ $epub->id }}"
                                                    data-record="{{ json_encode($epub) }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-xs deleteEbookBtn" data-toggle="modal"
                                                    data-target="#deleteEbookModal" data-type="epub"
                                                    data-action="{{ route('learner.self-publishing.delete-ebook', 
                                                    [$epub->project_id, $epub->id]) }}">
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
        </div>
    </div>
</div>

@if ($standardProject)
    <div id="ebookModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.self-publishing.save-ebook', $standardProject->id) }}"
                        enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <input type="hidden" name="type">

                        <div class="form-group epub-container">
                            <label>{{ trans_choice('site.files', 1) }}</label>
                            <input type="file" class="form-control" name="epub">
                        </div>

                        <div class="form-group mobi-container">
                            <label>{{ trans_choice('site.files', 1) }}</label>
                            <input type="file" class="form-control" name="mobi">
                        </div>

                        <div class="form-group cover-container">
                            <label>{{ trans_choice('site.files', 1) }}</label>
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
@endif

<div id="deleteEbookModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <p>{{ trans('site.delete-question') }}</p>

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
    let translations = {
        epub : "{{ trans('site.epub') }}",
        mobi : "{{ trans('site.mobi') }}",
        cover : "{{ trans('site.homepage.illustration-cover-design') }}",
        delete: "{{ trans('site.delete') }}"
        };

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
                modal.find('.modal-title').text(translations.epub);
                epubContainer.removeClass('hide');
                break;

            case 'mobi':
                modal.find('.modal-title').text(translations.mobi);
                mobiContainer.removeClass('hide');
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

    $(".deleteEbookBtn").click(function() {
        let type = $(this).data('type');
        let modal = $("#deleteEbookModal");
        let form = modal.find("form");
        let action = $(this).data('action');
        let pageTitle = '';

        switch (type) {
            case 'epub':
                pageTitle = translations.epub;
                break;

            case 'mobi':
                pageTitle = translations.mobi;
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
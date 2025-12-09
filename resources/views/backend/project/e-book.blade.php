@extends($layout)

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-file-text-o"></i> {{ trans('site.e-book') }}</h3>
    <a href="{{ $backRoute }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
    </a>
</div>

<div class="col-sm-12 margin-top">
    <section>
        <button type="button" class="btn btn-success ebookBtn" data-toggle="modal" data-target="#ebookModal"
                data-type="epub">+ {{ trans('site.add-epub') }}</button>
        <div class="table-responsive margin-top">
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
                                        data-action="{{ route($deleteEbookRoute, [$epub->project_id, $epub->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section>
        <button type="button" class="btn btn-success ebookBtn" data-toggle="modal" data-target="#ebookModal"
                data-type="mobi">+ {{ trans('site.add-mobi') }}</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>{{ trans('site.mobi') }}</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($mobis as $mobi)
                        <tr>
                            <td>
                                <a href="{{ url('/dropbox/download/' . trim($mobi->value)) }}">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>&nbsp;

                                {!! $mobi->file_link !!}
                            </td>
                            <td>                      
                                <button class="btn btn-primary btn-xs ebookBtn" data-toggle="modal"
                                        data-target="#ebookModal"
                                        data-type="mobi" data-id="{{ $mobi->id }}"
                                        data-record="{{ json_encode($mobi) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteEbookBtn" data-toggle="modal"
                                        data-target="#deleteEbookModal" data-type="mobi"
                                        data-action="{{ route($deleteEbookRoute, [$mobi->project_id, $mobi->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section>
        <button type="button" class="btn btn-success ebookBtn" data-toggle="modal" data-target="#ebookModal"
                data-type="cover">+ Add Cover</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Cover</th>
                    <th width="300"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($covers as $cover)
                        <tr>
                            <td>
                                <a href="{{ url('/dropbox/download/' . trim($cover->value)) }}">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>&nbsp;

                                {!! $cover->file_link !!}
                            </td>
                            <td>                      
                                <button class="btn btn-primary btn-xs ebookBtn" data-toggle="modal"
                                        data-target="#ebookModal"
                                        data-type="cover" data-id="{{ $cover->id }}"
                                        data-record="{{ json_encode($cover) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteEbookBtn" data-toggle="modal"
                                        data-target="#deleteEbookModal" data-type="cover"
                                        data-action="{{ route($deleteEbookRoute, [$cover->project_id, $cover->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<div id="ebookModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                </h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route($saveEbookRoute, $project->id) }}"
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

<div id="deleteEbookModal" class="modal fade" role="dialog">
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
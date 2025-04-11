@extends('frontend.learner.self-publishing.layout')

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
                    E-book
                </div>
                <div class="card-body">
                    <section>
                        <button type="button" class="btn btn-success pull-right ebookBtn" data-toggle="modal" 
                        data-target="#ebookModal"
                        data-type="epub">+ Add Epub</button>
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
                                                <button class="btn btn-primary btn-xs ebookBtn" data-toggle="modal"
                                                        data-target="#ebookModal"
                                                        data-type="epub" data-id="{{ $epub->id }}"
                                                        data-record="{{ json_encode($epub) }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="mt-5">
                        <button type="button" class="btn btn-success ebookBtn pull-right" data-toggle="modal" 
                        data-target="#ebookModal"
                                data-type="mobi">+ Add Mobi</button>
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
                                                <button class="btn btn-primary btn-xs ebookBtn" data-toggle="modal"
                                                        data-target="#ebookModal"
                                                        data-type="mobi" data-id="{{ $mobi->id }}"
                                                        data-record="{{ json_encode($mobi) }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="mt-5">
                        <button type="button" class="btn btn-success ebookBtn pull-right" data-toggle="modal" 
                            data-target="#ebookModal"
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
                                                <a href="{{ route('dropbox.download_file', trim($cover->value)) }}">
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

    <div id="ebookModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveEbookRoute, $standardProject->id) }}"
                        enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                          {{ csrf_field() }}
                          <input type="hidden" name="id">
                          <input type="hidden" name="type">
    
                        <div class="form-group epub-container">
                            <label>File</label>
                            <input type="file" class="form-control" name="epub">
                        </div>
    
                        <div class="form-group mobi-container">
                            <label>File</label>
                            <input type="file" class="form-control" name="mobi">
                        </div>
    
                        <div class="form-group cover-container">
                            <label>File</label>
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
                modal.find('.modal-title').text('Epub');
                epubContainer.removeClass('hide');
                break;

            case 'mobi':
                modal.find('.modal-title').text('Mobi');
                mobiContainer.removeClass('hide');
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
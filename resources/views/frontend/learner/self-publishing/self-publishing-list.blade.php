@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Project &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 dashboard-course">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1 class="d-inline-block">
                                {{ trans('site.learner.editor-text') }}
                            </h1>

                            @if ($standardProject)
                                <a href="{{ route('learner.self-publishing.publishing.order') }}" class="btn btn-primary float-right">
                                    Order
                                </a>
                            @endif
                        </div>
                        <div class="card-body py-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.title') }}</th>
                                    <th>{{ trans('site.description') }}</th>
                                    <th>{{ trans_choice('site.files', 0) }}</th>
                                    <th>{{ trans_choice('site.feedbacks', 0) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($selfPublishingList as $publishing)
                                    <tr>
                                        <td>
                                            {{ $publishing->title }}
                                        </td>
                                        <td>
                                            {{ $publishing->description }}
                                        </td>
                                        <td>
                                            {!! $publishing->dropbox_file_link_with_download !!}
                                            @if(!$publishing->feedback && $publishing->manuscript)
                                                <br>
                                                <button class="btn btn-primary btn-xs uploadSelfPublishingManuscriptBtn"
                                                        data-toggle="modal"
                                                        data-target="#uploadSelfPublishingManuscriptModal"
                                                        data-action="{{ route('learner.project.self-publishing.upload-manuscript', $publishing->id) }}">
                                                    {{ trans('site.front.form.upload-manuscript') }}
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if($publishing->feedback)
                                                @if($publishing->feedback->is_approved)
                                                    <a href="{{ $publishing->feedback->manuscript }}"
                                                       class="btn btn-primary btn-xs margin-top" download="">
                                                        {{ trans('site.learner.download-feedback') }}
                                                    </a>
                                                @else
                                                    <label class="label label-warning" style="margin-right: 5px;">
                                                        {{ trans('site.pending') }}
                                                    </label>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card -->
                </div>
            </div>
        </div>
    </div>

    <div id="uploadSelfPublishingManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.front.form.upload-manuscript') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" name="manuscript[]" class="form-control"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div> <!-- end uploadSelfPublishingManuscriptModal -->
@stop

@section('scripts')
    <script>
        $(".uploadSelfPublishingManuscriptBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#uploadSelfPublishingManuscriptModal');
            modal.find('form').attr('action', action);
        });
    </script>
@stop
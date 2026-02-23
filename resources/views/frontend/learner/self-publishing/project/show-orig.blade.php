@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Project &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        .fa-file-red:before {
            content: "\f15b";
        }

        .fa-file-red {
            color: #862736 !important;
            font-size: 20px;
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <a href="{{ route('learner.project.graphic-work', $project->id) }}"
                   class="btn mb-3 site-btn-global mr-2">
                    {{ trans('site.author-portal.graphic-work') }}
                </a>

                <a href="{{ route('learner.project.registration', $project->id) }}"
                   class="btn mb-3 site-btn-global mr-2">
                    {{ trans('site.author-portal.registration') }}
                </a>

                <a href="{{ route('learner.project.marketing', $project->id) }}"
                   class="btn mb-3 site-btn-global mr-2">
                    {{ trans('site.author-portal.marketing') }}
                </a>

                <a href="{{ route('learner.project.marketing-plan', $project->id) }}"
                   class="btn mb-3 site-btn-global mr-2">
                    {{ trans('site.author-portal.marketing-plan') }}
                </a>

                <a href="{{ route('learner.project.contract', $project->id) }}"
                   class="btn mb-3 site-btn-global mr-2">
                    {{ trans('site.author-portal.contract') }}
                </a>

                <a href="{{ route('learner.project.storage', $project->id) }}"
                    class="btn mb-3 site-btn-global mr-2">
                     {{ trans('site.author-portal.storage') }}
                 </a>

                {{--<a href="{{ route('learner.project.invoice', $project->id) }}"
                   class="btn mb-3 site-btn-global">
                    Invoice
                </a>--}}
                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1 class="d-inline-block">
                                {{-- {{ trans('site.self-publishing-text') }} --}}
                                {{ trans('site.learner.editor-text') }}
                            </h1>

                            {{-- <a href="{{ route('learner.service.order', [$project->id, 3]) }}" class="btn btn-primary float-right">
                                Order
                            </a> --}}
                        </div>
                        <div class="card-body">
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
                                @foreach($project->selfPublishingList as $publishing)
                                    <tr>
                                        <td>
                                            {{ $publishing->title }}
                                        </td>
                                        <td>
                                            {{ $publishing->description }}
                                        </td>
                                        <td>
                                            {!! $publishing->dropbox_file_link_with_download !!}
                                            @if(!$publishing->feedback)
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
                                                    @if (strpos($publishing->feedback->manuscript, 'project-'))
                                                        <a href="{{ route('dropbox.download_file', 
                                                            trim($publishing->feedback->manuscript)) }}"
                                                        class="btn btn-primary btn-xs margin-top" download="">
                                                            {{ trans('site.learner.download-feedback') }}
                                                        </a>
                                                    @else
                                                        <a href="{{ $publishing->feedback->manuscript }}"
                                                        class="btn btn-primary btn-xs margin-top" download="">
                                                            {{ trans('site.learner.download-feedback') }}
                                                        </a>
                                                    @endif
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
                    </div> <!-- end self-publishing -->

                    <div class="card global-card mt-5">
                        <div class="card-header">
                            <h1 class="d-inline-block">
                                {{ trans('site.learner.copy-editing') }}
                            </h1>

                            {{-- <a href="{{ route('learner.service.order', [$project->id, 1]) }}" class="btn btn-primary float-right">
                                Order
                            </a> --}}
                        </div>
                        <div class="card-body py-0">
                            <table class="table table-global">
                                <thead>
                                <tr>
                                    <th>
                                        {{ trans('site.learner.script') }}
                                    </th>
                                    <th>
                                        {{ trans('site.learner.date-ordered') }}
                                    </th>
                                    <th>
                                        {{ trans('site.learner.status') }}
                                    </th>
                                    <th>
                                        {{ trans('site.learner.expected-finish') }}
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->copyEditings as $editing)
                                        <?php $extension = explode('.', basename($editing->file)); ?>
                                        <tr>
                                            <td>
                                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                    <a href="/js/ViewerJS/#../../{{ $editing->file }}">{{ basename($editing->file) }}</a>
                                                @elseif( end($extension) == 'docx' )
                                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$editing->file}}">{{ basename($editing->file) }}</a>
                                                @endif

                                                @if(!$editing->is_locked)
                                                        <br>
                                                    <button class="btn btn-primary btn-xs uploadOtherServiceManuscriptBtn" data-toggle="modal"
                                                            data-target="#uploadOtherServiceManuscriptModal"
                                                            data-action="{{ route('learner.project.other-service.upload-manuscript',
                                                             ['id' => $editing->id, 'type' => 1]) }}">
                                                        {{ trans('site.front.form.upload-manuscript') }}
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \App\Http\FrontendHelpers::formatDate($editing->created_at) }}
                                            </td>
                                            <td>
                                                @if( $editing->status == 2 )
                                                    <span class="label label-success">{{ trans('site.learner.finished') }}</span>
                                                @elseif( $editing->status == 1 )
                                                    <span class="label label-primary">{{ trans('site.learner.started') }}</span>
                                                @elseif( $editing->status == 0 )
                                                    <span class="label label-warning">{{ trans('site.learner.not-started') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($editing->expected_finish)
                                                    {{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($editing->expected_finish) }}
                                                    <br>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($editing->file)
                                                    @if (strpos($editing->file, 'Forfatterskolen_app'))
                                                        <a href="{{ url('dropbox/download/' . trim($editing->file)) }}">
                                                            {{ trans('site.learner.download-original-script') }}
                                                        </a>
                                                    @else
                                                        <a href="{{ route('learner.other-service.download-doc',
                                                        ['id' => $editing->id, 'type' => 1]) }}">
                                                            {{ trans('site.learner.download-original-script') }}
                                                        </a>
                                                    @endif
                                                @endif

                                                @if ($editing->feedback)
                                                    <br>
                                                    <a href="{{ route('learner.other-service.download-feedback', $editing->feedback->id) }}"
                                                       style="color:#eea236">
                                                        {{ trans('site.learner.download-feedback') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end copy-editing -->

                    <div class="card global-card mt-5">
                        <div class="card-header">
                            <h1 class="d-inline-block">
                                {{ trans('site.front.correction.title') }}
                            </h1>

                            {{-- <a href="{{ route('learner.service.order', [$project->id, 2]) }}" class="btn btn-primary float-right">
                                Order
                            </a> --}}
                        </div>
                        <div class="card-body py-0">
                            <table class="table table-global">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.learner.script') }}</th>
                                    <th>{{ trans('site.learner.date-ordered') }}</th>
                                    <th>{{ trans('site.learner.status') }}</th>
                                    <th>{{ trans('site.learner.expected-finish') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($project->corrections as $correction)
                                    <?php $extension = explode('.', basename($correction->file)); ?>
                                    <tr>
                                        <td>
                                            @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                <a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
                                            @elseif( end($extension) == 'docx' )
                                                <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
                                            @endif

                                            @if(!$correction->is_locked)
                                                <br>
                                                <button class="btn btn-primary btn-xs uploadOtherServiceManuscriptBtn" data-toggle="modal"
                                                        data-target="#uploadOtherServiceManuscriptModal"
                                                        data-action="{{ route('learner.project.other-service.upload-manuscript',
                                                         ['id' => $correction->id, 'type' => 2]) }}">
                                                    {{ trans('site.front.form.upload-manuscript') }}
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
                                        </td>
                                        <td>
                                            @if( $correction->status == 2 )
                                                <span class="label label-success">{{ trans('site.learner.finished') }}</span>
                                            @elseif( $correction->status == 1 )
                                                <span class="label label-primary">{{ trans('site.learner.started') }}</span>
                                            @elseif( $correction->status == 0 )
                                                <span class="label label-warning">{{ trans('site.learner.not-started') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($correction->expected_finish)
                                                {{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
                                                <br>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($correction->file)
                                                @if (strpos($correction->file, 'Forfatterskolen_app'))
                                                    <a href="{{ url('dropbox/download/' . trim($correction->file)) }}">
                                                        {{ trans('site.learner.download-original-script') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('learner.other-service.download-doc',
                                                    ['id' => $correction->id, 'type' => 2]) }}">
                                                        {{ trans('site.learner.download-original-script') }}
                                                    </a>
                                                @endif
                                            @endif

                                            @if ($correction->feedback)
                                                <br>
                                                <a href="{{ route('learner.other-service.download-feedback', $correction->feedback->id) }}"
                                                   style="color:#eea236">
                                                    {{ trans('site.learner.download-feedback') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end correction -->

                </div>
            </div> <!-- end row -->
        </div>
    </div>

    <div id="uploadSelfPublishingManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.learner.course-show.upload-manuscript') }}
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
    </div>

    <div id="uploadOtherServiceManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.learner.course-show.upload-manuscript') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" name="manuscript" class="form-control"
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
    </div>
@stop

@section('scripts')
    <script>
        $(".uploadSelfPublishingManuscriptBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#uploadSelfPublishingManuscriptModal');
            modal.find('form').attr('action', action);
        });

        $(".uploadOtherServiceManuscriptBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#uploadOtherServiceManuscriptModal');
            modal.find('form').attr('action', action);
        });
    </script>
@stop
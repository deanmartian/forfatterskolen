@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Korrektursteg &rsaquo; Selvpublisering &rsaquo; Forfatterskolen')
@section('robots')<meta name="robots" content="noindex, follow">@endsection

@section('content')
    <div class="learner-container">
        <div class="container">
            <a href="{{ route('learner.progress-plan') }}" class="btn btn-outline-brand mb-3">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Tilbake
            </a>

            <div class="card">
                <div class="sp-card-header">
                    {{ trans('site.front.correction.title') }}

                    <button class="btn btn-brand btn-sm float-end uploadOtherServiceManuscriptBtn" data-bs-toggle="modal"
                            data-bs-target="#uploadOtherServiceManuscriptModal"
                            data-action="{{ route('learner.project.progress-plan.other-service.upload-manuscript', 2) }}">
                        {{ trans('site.front.form.upload-manuscript') }}
                    </button>
                </div>
                <div class="sp-card-body py-0">
                    <table class="sp-table">
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
                            @foreach($corrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
                                <tr>
                                    <td>
                                        @if (strpos($correction->file, 'Forfatterskolen_app'))
                                            <a href="/dropbox/shared-link/{{ $correction->file }}" target="_blank">
                                                {{ basename($correction->file) }}
                                            </a>
                                        @else
                                            @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                <a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
                                            @elseif( end($extension) == 'docx' )
                                                <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
                                            @endif
                                        @endif

                                        @if(!$correction->is_locked && $correction->status !=2)
                                            <br>
                                            <button class="btn btn-brand btn-sm uploadOtherServiceManuscriptBtn" data-bs-toggle="modal"
                                                    data-bs-target="#uploadOtherServiceManuscriptModal"
                                                    data-id="{{ $correction->id }}"
                                                    data-action="{{ route('learner.project.progress-plan.other-service.upload-manuscript', 2) }}">
                                                {{ trans('site.front.form.upload-manuscript') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
                                    </td>
                                    <td>
                                        @if( $correction->status == 2 )
                                            <span class="badge bg-success">{{ trans('site.learner.finished') }}</span>
                                        @elseif( $correction->status == 1 )
                                            <span class="badge bg-primary">{{ trans('site.learner.started') }}</span>
                                        @elseif( $correction->status == 0 )
                                            <span class="badge bg-warning">{{ trans('site.learner.not-started') }}</span>
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
            </div>
        </div>
    </div>

    <div id="uploadOtherServiceManuscriptModal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content sp-modal">
                <div class="sp-modal__header">
                    <h3 class="sp-modal__title">
                        <i class="fas fa-upload" style="color:var(--brand-primary);margin-right:6px"></i>
                        Last opp manuskript
                    </h3>
                    <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data" data-sp-validate>
                    {{ csrf_field() }}
                    <input type="hidden" name="project_id" value="{{ $standardProject->id }}">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="id">
                    <div class="sp-modal__body">
                        <div class="sp-form-group">
                            <label class="sp-label">{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" name="manuscript" class="sp-input"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple
                                   data-sp-file-preview="correctionFilePreview">
                            <div id="correctionFilePreview"></div>
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
@stop

@section('scripts')
    <script>
        $(".uploadOtherServiceManuscriptBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#uploadOtherServiceManuscriptModal');
            let record_id = $(this).data('id');
            modal.find('form').attr('action', action);
            modal.find('[name=id]').val(record_id);
        });
    </script>
@stop
@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Progress Plan Step &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <a href="{{ route('learner.progress-plan') }}" class="btn btn-outline-brand mb-3">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Tilbake
            </a>

            <div class="card sp-card">
                <div class="sp-card-header">
                    {{ $stepTitle }}

                    <button type="button" class="btn btn-brand btn-xs uploadManuscriptBtn pull-right"
                        data-toggle="modal" data-target="#uploadManuscriptModal"
                        data-action="{{ route('learner.progress-plan.manuscript.upload') }}"
                        style="width: auto;">
                        {{ trans('site.learner.upload-script') }}
                        <i class="fa fa-upload" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="sp-card-body">
                    <h3>Step 1. Manuscript</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Fil</td>
                                <td>Dato</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $manuscripts as $manuscript )
                                <tr>
                                    <td>
                                        {!! $manuscript->dropbox_file_link_with_download !!}</td>
                                    <td>
                                        {{ FrontendHelpers::formatDate($manuscript->created_at) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                
            </div>
        </div>
    </div>

    <div id="uploadManuscriptModal" class="modal fade global-modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
          <div class="modal-content sp-modal">
            <div class="sp-modal__header">
                <h3 class="sp-modal__title">
                    <i class="fas fa-upload" style="color:var(--brand-primary);margin-right:6px"></i>
                    {{ trans('site.learner.upload-script') }}
                </h3>
                <button type="button" class="sp-modal__close" data-dismiss="modal" aria-label="Lukk">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)" data-sp-validate>
                {{ csrf_field() }}
                <div class="sp-modal__body">
                    <div class="sp-form-group">
                      <label class="sp-label">
                          * {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
                      </label>
                        <input type="file" class="sp-input" required name="manuscript"
                      accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                      application/pdf, application/vnd.oasis.opendocument.text"
                      data-sp-file-preview="manuscriptFilePreview">
                        <div id="manuscriptFilePreview"></div>
                    </div>
                </div>
                <div class="sp-modal__footer">
                    <button type="button" class="btn-outline-brand" data-dismiss="modal">Avbryt</button>
                    <button type="submit" class="btn-brand">{{ trans('site.save') }}</button>
                </div>
            </form>
          </div>

        </div>
    </div>

    @if(Session::has('manuscript_test_error'))
        <div id="manuscriptTestErrorModal" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content sp-modal">
                    <div class="sp-modal__header" style="background:#fef2f2">
                        <h3 class="sp-modal__title">
                            <i class="fa fa-exclamation-triangle" style="color:#dc2626;margin-right:6px" aria-hidden="true"></i>
                            Feil
                        </h3>
                        <button type="button" class="sp-modal__close" data-dismiss="modal" aria-label="Lukk">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="sp-modal__body text-center">
                        {!! Session::get('manuscript_test_error') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
<script>
    $('.uploadManuscriptBtn').click(function(){
		var form = $('#uploadManuscriptModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

    @if(Session::has('manuscript_test_error'))
    	$('#manuscriptTestErrorModal').modal('show');
	@endif
</script>
@stop
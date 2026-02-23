@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Progress Plan Step &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <a href="{{ route('learner.progress-plan') }}" class="btn btn-secondary mb-3">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <div class="card card-global">
                <div class="card-header">
                    {{ $stepTitle }}

                    <button type="button" class="btn btn-primary btn-xs uploadManuscriptBtn pull-right"
                        data-toggle="modal" data-target="#uploadManuscriptModal"
                        data-action="{{ route('learner.progress-plan.manuscript.upload') }}"
                        style="width: auto;">
                        {{ trans('site.learner.upload-script') }}
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <div class="card-body">
                    <h3>Step 1. Manuscript</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>File</td>
                                <td>Upload Date</td>
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

    <div id="uploadManuscriptModal" class="modal fade global-modal" role="dialog">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">{{ trans('site.learner.upload-script') }}</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <div class="form-group">
                      <label>
                          * {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
                      </label>
                        <input type="file" class="form-control" required name="manuscript" 
                      accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, 
                      application/pdf, application/vnd.oasis.opendocument.text">
                    </div>
                    <button type="submit" class="btn submit-btn pull-right">{{ trans('site.learner.upload-script') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
          </div>
      
        </div>
    </div>

    @if(Session::has('manuscript_test_error'))
        <div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
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
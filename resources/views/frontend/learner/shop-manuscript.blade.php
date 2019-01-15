@extends('frontend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Manusutviklinger @stop


@section('content')
<div class="learner-container">
	<div class="container learner-manuscript-page">
		@include('frontend.partials.learner-search-new')

		@foreach(Auth::user()->shopManuscriptsTaken->chunk(3) as $shopManuscriptTaken_chunk)
			<div class="row">
				@foreach($shopManuscriptTaken_chunk as $shopManuscriptTaken)
					<div class="col-md-4 mt-5">
						<div class="card card-global">
							<div class="card-body">
								<h3 class="mb-1">{{ $shopManuscriptTaken->shop_manuscript->title }}</h3>
								@if($shopManuscriptTaken->expected_finish)
									<p>
										<span class="label label-danger">Forventet ferdig:</span> {{ $shopManuscriptTaken->expected_finish }}
									</p>
								@endif

									@if( $shopManuscriptTaken->status == 'Finished' )
										<span class="label label-success">Finished</span>
									@elseif( $shopManuscriptTaken->status == 'Started' )
										<span class="label label-primary">Started</span>
									@elseif( $shopManuscriptTaken->status == 'Not started' )
										<span class="label label-warning">Ikke startet</span>
								@endif

								<div class="note-color mt-4">
									@if( $shopManuscriptTaken->status != 'Not started' )
										Ord: {{ $shopManuscriptTaken->words }} <br>
									@endif
									{{ $shopManuscriptTaken->shop_manuscript->description }}
								</div>
							</div> <!-- end panel-body-->
							<div class="card-footer">
								@if( $shopManuscriptTaken->is_active )
									@if( $shopManuscriptTaken->status == 'Not started' )
										<button type="button" class="btn btn-primary uploadManuscriptBtn" data-toggle="modal" data-target="#uploadManuscriptModal" data-action="{{ route('learner.shop-manuscript.upload', $shopManuscriptTaken->id) }}">Last opp manus</button>
									@else
										<a class="btn btn-primary" href="{{ route('learner.shop-manuscript.show', $shopManuscriptTaken->id) }}">Se Manuskript</a>
										@if (!$shopManuscriptTaken->is_manuscript_locked)
											<button class="btn btn-success updateManuscriptBtn" type="button" data-toggle="modal"
													data-target="#updateUploadedManuscriptModal" data-fields="{{ json_encode($shopManuscriptTaken) }}"
													data-action="{{ route('learner.shop-manuscript.update-uploaded-manuscript', $shopManuscriptTaken->id) }}"><i class="fa fa-pencil"></i></button>
											<button class="btn btn-danger deleteManuscriptBtn" type="button" data-toggle="modal"
													data-target="#deleteUploadedManuscriptModal"
													data-action="{{ route('learner.shop-manuscript.delete-uploaded-manuscript', $shopManuscriptTaken->id) }}"><i class="fa fa-trash"></i></button>
										@endif
									@endif
								@else
									<a class="btn btn-warning disabled" style="color: #fff">Pending</a>
								@endif
							</div>
						</div> <!-- end panel -->
					</div> <!-- end column -->
				@endforeach
			</div>
		@endforeach

		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card global-card">
					<div class="card-header">
						<h1>
							Språkvask
						</h1>
					</div>
					<div class="card-body py-0">
						<table class="table table-global">
							<thead>
								<tr>
									<th>Manus</th>
									<th>Dato bestilt</th>
									<th>Status</th>
									<th>Forventet ferdig</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							@foreach(Auth::user()->copyEditings as $editing)
                                <?php $extension = explode('.', basename($editing->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $editing->file }}">{{ basename($editing->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$editing->file}}">{{ basename($editing->file) }}</a>
										@endif
									</td>
									<td>
										{{ \App\Http\FrontendHelpers::formatDate($editing->created_at) }}
									</td>
									<td>
										@if( $editing->status == 2 )
											<span class="label label-success">Finished</span>
										@elseif( $editing->status == 1 )
											<span class="label label-primary">Started</span>
										@elseif( $editing->status == 0 )
											<span class="label label-warning">Not started</span>
										@endif
									</td>
									<td>
										@if ($editing->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($editing->expected_finish) }}
											<br>
										@endif
									</td>
									<td>
										<a href="{{ route('learner.other-service.download-doc',
										   ['id' => $editing->id, 'type' => 1]) }}">Last ned orginal manus</a>

										@if ($editing->feedback)
											<br>
											<a href="{{ route('learner.other-service.download-feedback', $editing->feedback->id) }}"
											   style="color:#eea236">
												Last ned tilbakemelding
											</a>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div> <!-- end global-card -->
			</div> <!-- end col-md-12 -->
		</div> <!-- end row -->

		<div class="row mt-5">
			<div class="col-md-12">
				<div class="card global-card">
					<div class="card-header">
						<h1>
							Korrektur
						</h1>
					</div>
					<div class="card-body py-0">
						<table class="table table-global">
							<thead>
							<tr>
								<th>Manus</th>
								<th>Dato bestilt</th>
								<th>Status</th>
								<th>Forventet ferdig</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach(Auth::user()->corrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
										@endif
									</td>
									<td>
										{{ \App\Http\FrontendHelpers::formatDate($correction->created_at) }}
									</td>
									<td>
										@if( $correction->status == 2 )
											<span class="label label-success">Finished</span>
										@elseif( $correction->status == 1 )
											<span class="label label-primary">Started</span>
										@elseif( $correction->status == 0 )
											<span class="label label-warning">Not started</span>
										@endif
									</td>
									<td>
										@if ($correction->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
											<br>
										@endif
									</td>
									<td>
										<a href="{{ route('learner.other-service.download-doc',
										   ['id' => $correction->id, 'type' => 2]) }}">Last ned orginal manus</a>

										@if ($correction->feedback)
											<br>
											<a href="{{ route('learner.other-service.download-feedback', $correction->feedback->id) }}"
											   style="color:#eea236">
												Last ned tilbakemelding
											</a>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div> <!-- end global-card -->
			</div> <!-- end col-md-12 -->
		</div> <!-- end row -->
	</div>
</div>


<div id="uploadManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Last opp manus</h3>
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="">
      		{{ csrf_field() }}
      		<div class="form-group">
				<label>
					* Godkjente fil formater er DOC, DOCX, PDF og ODT.
				</label>
      			<input type="file" class="form-control" required name="manuscript" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      		</div>
			<div class="form-group">
				<label for="">Sjanger</label>
				<select class="form-control" name="genre" required>
					<option value="" disabled="disabled" selected>Velg sjanger</option>
					@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
						<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
					@endforeach
				</select>
			</div>
			<div class="form-group">
				<label for="">Synopsis (valgfritt)</label>
				<input type="file" class="form-control" name="synopsis" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
			</div>
			<div class="form-group">
				<label for="">Noen ord om manuset (valgfritt)</label>
				<textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
			</div>
      		<button type="submit" class="btn btn-primary pull-right">Last opp manus</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>

<div id="updateUploadedManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Last opp manus</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="">
					{{ csrf_field() }}
					<div class="form-group">
						<label>* Godkjente fil formater er DOC, DOCX, PDF og ODT.</label>
						<input type="file" class="form-control" required name="manuscript" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
					</div>
					<div class="form-group">
						<label for="">Sjanger</label>
						<select class="form-control" name="genre" required>
							<option value="" disabled="disabled" selected>Velg sjanger</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="">Synopsis (valgfritt)</label>
						<input type="file" class="form-control" name="synopsis" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
					</div>
					<div class="form-group">
						<label for="">Noen ord om manuset (valgfritt)</label>
						<textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<button type="submit" class="btn btn-primary pull-right">Last opp manus</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="deleteUploadedManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Last opp manus</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="">
					{{ csrf_field() }}
					Are you sure you want to delete the manuscript?
					<div class="clearfix"></div>
					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="exceedModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Oppgrader</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">

				<div id="exceed_message">
					<p>
						Manuset ditt har overskrevet antall tillatte ord <br>
						Oppgraderingen vil koste kroner {{ session('exceed') }},- <br>
						{{ session('max_words') }} ord
					</p>
					<button class="btn btn-default" data-dismiss="modal">Lukk</button>
					<a href="{{ url('upgrade-manuscript/'.session('plan').'/checkout') }}" class="btn btn-primary pull-right">Oppgrader manus</a>
				</div>
				<div class="clearfix"></div>

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

@if (session('exceed'))
	<input type="hidden" name="exceed">
@endif

@stop

@section('scripts')
<script>
	var has_exceed = $("input[name=exceed]").length;

	if (has_exceed) {
	    $("#exceedModal").modal();
	}

	@if(Session::has('manuscript_test_error'))
    	$('#manuscriptTestErrorModal').modal('show');
	@endif

	$('.uploadManuscriptBtn').click(function(){
		var form = $('#uploadManuscriptModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

	$(".updateManuscriptBtn").click(function(){
        var modal = $('#updateUploadedManuscriptModal');
        var form = $('#updateUploadedManuscriptModal form');
	    var fields = $(this).data('fields');
        var action = $(this).data('action');
	    if (fields.genre) {
            modal.find('select').val(fields.genre);
		}
        form.attr('action', action);
		modal.find('textarea[name=description]').text(fields.description);
	});

    $('.deleteManuscriptBtn').click(function(){
        var form = $('#deleteUploadedManuscriptModal form');
        var action = $(this).data('action');
        form.attr('action', action);
    });

</script>
@stop


@extends('frontend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Manusutviklinger @stop

@section('styles')
	<style>
		.col-md-4 {
			padding-left: 0;
		}

		.nav-tabs {
			border-bottom: 1px solid #ddd;
		}

		.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
			color: #555;
			cursor: default;
			background-color: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
		}

		.nav-tabs>li>a {
			background: #f0f2f4;
			color: #666;
			outline: medium none;
			padding: 10px 24px;
			line-height: 1.42857143;
			border: 1px solid transparent;
			border-bottom: 0 none;
			border-radius: 4px 4px 0 0;
			position: relative;
			display: block;
			margin-right: 8px;
		}
	</style>
@stop

@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12">

			@include('frontend.partials.learner-search')
		
			<div class="row">
				@foreach(Auth::user()->shopManuscriptsTaken->chunk(3) as $shopManuscriptTaken_chunk)
					<div class="col-sm-12">
						@foreach($shopManuscriptTaken_chunk as $shopManuscriptTaken)
							<div class="col-md-4">
								<div class="panel panel-default">
									<div class="panel-body">
										<h3 class="no-margin-top" style="margin-bottom: 4px">{{ $shopManuscriptTaken->shop_manuscript->title }}</h3>
										@if($shopManuscriptTaken->expected_finish)
											<p>
												<span class="label label-danger">Expected Finish:</span> {{ $shopManuscriptTaken->expected_finish }}
											</p>
										@endif
										<p>
											@if( $shopManuscriptTaken->status == 'Finished' )
												<span class="label label-success">Finished</span>
											@elseif( $shopManuscriptTaken->status == 'Started' )
												<span class="label label-primary">Started</span>
											@elseif( $shopManuscriptTaken->status == 'Not started' )
												<span class="label label-warning">Ikke startet</span>
										@endif

										<div style=" margin-top: 5px">
											&nbsp;
											@if( $shopManuscriptTaken->status != 'Not started' )
												Ord: {{ $shopManuscriptTaken->words }} <br>
											@endif
											{{ $shopManuscriptTaken->shop_manuscript->description }}
										</div>
										</p>
										<div>
											@if( $shopManuscriptTaken->is_active )
												@if( $shopManuscriptTaken->status == 'Not started' )
													<button type="button" class="btn btn-primary uploadManuscriptBtn" data-toggle="modal" data-target="#uploadManuscriptModal" data-action="{{ route('learner.shop-manuscript.upload', $shopManuscriptTaken->id) }}">Last opp manus</button>
												@else
													<a class="btn btn-primary" href="{{ route('learner.shop-manuscript.show', $shopManuscriptTaken->id) }}">Se Manuskript</a>
													@if (!$shopManuscriptTaken->is_manuscript_locked)
														<div class="clearfix margin-top"></div>
														<button class="btn btn-success updateManuscriptBtn" type="button" data-toggle="modal"
																data-target="#updateUploadedManuscriptModal" data-fields="{{ json_encode($shopManuscriptTaken) }}"
																data-action="{{ route('learner.shop-manuscript.update-uploaded-manuscript', $shopManuscriptTaken->id) }}"><i class="fa fa-pencil"></i></button>
														<button class="btn btn-danger deleteManuscriptBtn" type="button" data-toggle="modal"
																data-target="#deleteUploadedManuscriptModal"
																data-action="{{ route('learner.shop-manuscript.delete-uploaded-manuscript', $shopManuscriptTaken->id) }}"><i class="fa fa-trash"></i></button>
													@endif
												@endif
											@else
												<a class="btn btn-warning disabled">Pending</a>
											@endif
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>

			<div class="row">
				<div class="col-sm-12">
					<h3 class="no-margin-top">Språkvask og Korrektur</h3>
				</div>
			</div>

			<div class="col-sm-12">
				<nav>
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#nav-copy-editing" data-toggle="tab">Språkvask</a>
						</li>
						<li>
							<a href="#nav-correction" data-toggle="tab">Korrektur</a>
						</li>
					</ul>
				</nav>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="nav-copy-editing">
						<div class="panel panel-default" style="border-top: 0">
							<div class="panel-body">
								<div class="table-users table-responsive">
									<table class="table no-margin-bottom">
										<thead>
										<tr>
											<th>Manus</th>
											<th>Learner</th>
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
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="nav-correction">
						<div class="panel panel-default" style="border-top: 0">
							<div class="panel-body">
								<div class="table-users table-responsive">
									<table class="table no-margin-bottom">
										<thead>
										<tr>
											<th>Manus</th>
											<th>Date Ordered</th>
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

		</div>
	</div>
	<div class="clearfix"></div>
</div>


<div id="uploadManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Last opp manus</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="">
      		{{ csrf_field() }}
      		<div class="form-group">
      		* Godkjente fil formater er DOCX, PDF og ODT.</div>
      		<div class="form-group">
      			<input type="file" class="form-control" required name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
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
				<input type="file" class="form-control" name="synopsis" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
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
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Last opp manus</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="">
					{{ csrf_field() }}
					<div class="form-group">
						* Godkjente fil formater er DOCX, PDF og ODT.</div>
					<div class="form-group">
						<input type="file" class="form-control" required name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
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
						<input type="file" class="form-control" name="synopsis" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
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
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Last opp manus</h4>
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
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Oppgrader</h4>
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


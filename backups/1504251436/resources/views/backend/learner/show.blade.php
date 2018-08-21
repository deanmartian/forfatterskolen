@extends('backend.layout')

@section('title')
<title>{{ $learner->first_name }} &rsaquo; Learners &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-users"></i> All Learners</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="GET" action="{{route('admin.learner.index')}}">
				<div class="input-group">
				  	<input type="text" class="form-control" name="search" value="{{Request::input('search')}}" placeholder="Search learner..">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="col-md-10 col-md-offset-1">
	<div class="row">
		<div class="col-md-12">
		<a href="{{route('admin.learner.index')}}" class="btn btn-default margin-bottom margin-top"><i class="fa fa-angle-left"></i> All Learners</a>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="text-center">
						<div class="learner-profile-image" style="background-image: url({{$learner->profile_image}})"></div>
						<h2>{{$learner->fullName}}</h2>
						{{$learner->email}}
					</div>
				</div>
				<div class="panel-footer">
					<i class="fa fa-map-marker"></i> 
					@if($learner->address->street)
					{{$learner->address->street}},
					@endif
					@if($learner->address->city)
					{{$learner->address->city}},
					@endif
					@if($learner->address->zip)
					{{$learner->address->zip}}
					@endif
					<br />
					<i class="fa fa-phone"></i>
					@if($learner->address->phone)
					{{$learner->address->phone}}
					@endif
				</div>
			</div>
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPasswordModal">Edit Password</button>
			<button type="button" class="btn btn-info" data-toggle="modal" data-target="#editContactModal">Edit Contact Info</button>
			<button type="button" class="margin-top btn btn-danger" data-toggle="modal" data-target="#deleteLearnerModal">Delete Learner</button>

            @if(session()->has('profile_success'))
            <br />
            <br />
		    <div class="alert alert-success">
		        {{ session()->get('profile_success') }}
		    </div>
			@endif
			
			@if ( $errors->any() )
            <br />
            <br />
            <div class="alert alert-danger no-bottom-margin">
                <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
                </ul>
            </div>
            @endif
		</div>
		<div class="col-md-9">
			<h4 class="no-margin-top">Courses Taken</h4>
			<div class="row">
				@foreach($learner->coursesTaken as $courseTaken)
				<div class="col-sm-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<h4 style="margin-bottom: 7px"><a href="{{route('admin.course.show', $courseTaken->package->course->id)}}?section=learners">{{$courseTaken->package->course->title}}</a></h4>
							<p class="no-margin-bottom">
								Plan: {{ $courseTaken->package->variation }} <br />
								@if( $courseTaken->hasStarted )
								Started at: {{ $courseTaken->started_at }} <br />
								@else
								Started at: <em>Not yet started</em> <br />
								@endif
								Status: @if($courseTaken->is_active)
								Active
								@else
								Pending
								@endif
								@if( $courseTaken->start_date )
								<br />
								Start date: {{ $courseTaken->start_date }}
								@endif
								@if( $courseTaken->end_date )
								<br />
								End date: {{ $courseTaken->end_date }}
								@endif
							</p>
							<button type="button" class="btn btn-xs btn-primary setAvailabilityBtn" style="margin-top: 7px" 
							data-title="{{ $courseTaken->package->course->title }}"
							data-toggle="modal" 
							data-target="#setAvailabilityModal" 
							data-action="{{ route('admin.course_taken.set_availability', $courseTaken->id) }}"
							@if( $courseTaken->start_date )
							data-start_date="{{ date_format(date_create($courseTaken->start_date), 'Y-m-d') }}" 
							@endif
							@if( $courseTaken->end_date )
							data-end_date="{{ date_format(date_create($courseTaken->end_date), 'Y-m-d') }}"
							@endif
							>
							Set availability</button> 

							@if( !$courseTaken->is_active )
							<form method="POST" action="{{ route('activate_course_taken') }}" style="margin-top: 7px">
								{{ csrf_field() }}
								<input type="hidden" name="coursetaken_id" value="{{ $courseTaken->id }}">
								<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
							</form>
							@endif

							<div class="margin-top"><strong>Lessons</strong></div>
							<div class="table-responsive">
								<table class="table table-bordered">
									@foreach( $courseTaken->package->course->lessons as $lesson )
									<tr>
										<td><a href="{{ route('admin.lesson.edit', ['course_id' => $courseTaken->package->course->id, 'lesson_id' => $lesson->id]) }}">{{ $lesson->title }}</a></td>
										<td>
											@if( FrontendHelpers::hasLessonAccess($courseTaken, $lesson) )
											<button class="btn btn-primary btn-xs defaultAllowAccessBtn" data-toggle="modal" data-target="#lessonDefaultAccessModal" data-action="{{ route('admin.course_taken.default_lesson_access', ['course_taken_id' => $courseTaken->id, 'lesson_id' => $lesson->id]) }}">Default access</button>
											@else
											<button class="btn btn-success btn-xs allowAccessBtn" data-toggle="modal" data-target="#lessonAccessModal" data-action="{{ route('admin.course_taken.allow_lesson_access', ['course_taken_id' => $courseTaken->id, 'lesson_id' => $lesson->id]) }}">Allow access</button>
											@endif
										</td>
									</tr>
									@endforeach
								</table>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>


			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addShopManuscriptModal">+ Add Shop Manuscript</button>
					<h4>Shop Manuscripts</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Manuscript</th>
								<th>Date Ordered</th>
								<th>Status</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->shopManuscriptsTaken as $shopManuscriptTaken)
							<tr>
								<td>
									@if($shopManuscriptTaken->is_active)
									<a href="{{ route('shop_manuscript_taken', ['id' => $learner->id, 'shop_manuscript_taken_id' => $shopManuscriptTaken->id]) }}">{{$shopManuscriptTaken->shop_manuscript->title}}</a>
									@else
									{{$shopManuscriptTaken->shop_manuscript->title}}
									@endif
								</td>
								<td>{{$shopManuscriptTaken->created_at}}</td>
								<td>
									@if($shopManuscriptTaken->is_active)
									Active
									@else
									Pending
									@endif
								</td>
								<td class="text-right">
									@if(!$shopManuscriptTaken->is_active)
						        	<form method="POST" action="{{ route('activate_shop_manuscript_taken') }}" class="inline-block">
										{{ csrf_field() }}
										<input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscriptTaken->id }}">
										<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
									</form>
									@endif
						        	<form method="POST" action="{{ route('delete_shop_manuscript_taken') }}" class="inline-block">
										{{ csrf_field() }}
										<input type="hidden" name="shop_manuscript_id" value="{{ $shopManuscriptTaken->id }}">
										<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
									</form>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>



			<div class="panel panel-default">
				<div class="panel-body">
					<h4>Workshops</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Workshop</th>
								<th>Date Ordered</th>
								<th>Status</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->workshopsTaken as $workshopTaken)
							<tr>
								<td>
									<a href="{{ route('admin.workshop.show', $workshopTaken->workshop_id) }}">{{ $workshopTaken->workshop->title }}</a>
								</td>
								<td>{{$workshopTaken->created_at}}</td>
								<td>
									@if($workshopTaken->is_active)
									Active
									@else
									Pending
									@endif
								</td>
								<td class="text-right">
									@if(!$workshopTaken->is_active)
						        	<form method="POST" action="{{ route('admin.package_workshop.approve', $workshopTaken->id) }}" class="inline-block">
										{{ csrf_field() }}
										<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
									</form>
									@endif
						        	<form method="POST" action="{{ route('admin.package_workshop.disapprove', $workshopTaken->id) }}" class="inline-block">
										{{ csrf_field() }}
										<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
									</form>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>


			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addInvoiceModal">+ Add Invoice</button>
					<h4>Invoices</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Invoice #</th>
								<th>Status</th>
								<th>Created At</th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->invoices as $invoice)
							<?php
							$fikenURL = false;
							foreach( $fikenInvoices as $fikenInvoice ) :
							    if( $invoice->fiken_url == $fikenInvoice->_links->alternate->href ) :
							      $fikenURL = true;
							      break;
							    endif;
							endforeach;
							$fikenError = false;
							if( $fikenURL ) :
							  $sale = FrontendHelpers::FikenConnect($fikenInvoice->sale);
							  $status = $sale->paid ? "BETALT" : "UBETALT";
							else :
							  $fikenError = true;
							endif;
							?>
							<tr>
		    					<td>
		    						@if( !$fikenError )
		    						<a href="{{route('admin.invoice.show', $invoice->id)}}">{{ $fikenInvoice->invoiceNumber }}</a>
		    						@endif
		    					</td>
								<td>
		    						@if( !$fikenError )
									@if($sale->paid)
									<span class="label label-success">{{$status}}</span>
									@else
									<span class="label label-danger">{{$status}}</span>
									@endif
									@endif
								</td>
								<td>{{$invoice->created_at}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>


			<div class="panel panel-default">
				<div class="panel-body">
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addManuscriptModal">+ Upload Manuscript</button>
					<h4>Manuscripts</h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Manuscript</th>
								<th>Words Count</th>
								<th>Grade</th>
								<th>Feedbacks</th>
								<th>Course</th>
								<th>Date Uploaded</th>
							</tr>
						</thead>
						<tbody>
							@foreach($learner->manuscripts as $manuscript)
							<tr>
								<td>{{ $manuscript->id }}</td>
								<td>
									<?php $extension = explode('.', basename($manuscript->filename)); ?>
									@if( end($extension) == 'pdf' )
									<i class="fa fa-file-pdf-o"></i> 
									@elseif( end($extension) == 'docx' )
									<i class="fa fa-file-word-o"></i> 
									@elseif( end($extension) == 'odt' )
									<i class="fa fa-file-text-o"></i> 
									@endif
									<a href="{{ route('admin.manuscript.show', $manuscript->id) }}">{{ basename($manuscript->filename) }}</a>
								</td>
								<td>{{$manuscript->word_count}}</td>
								<td>
									@if($manuscript->grade)
									{{$manuscript->grade}}
									@else
									<em>Not set</em>
									@endif
								</td>
								<td>{{count($manuscript->feedbacks)}}</td>
								<td><a href="{{route('admin.course.show', $manuscript->courseTaken->package->course->id)}}">{{$manuscript->courseTaken->package->course->title}}</a></td>
								<td>{{$manuscript->created_at}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="lessonDefaultAccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Set default access for this lesson</h4>
      </div>

      <div class="modal-body">
      	<form method="POST">
      		{{ csrf_field() }}
      		Set default learner access for this lesson?
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">Confirm</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>



<div id="lessonAccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Allow access for this lesson</h4>
      </div>

      <div class="modal-body">
      	<form method="POST">
      		{{ csrf_field() }}
      		Allow learner access for this lesson?
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">Confirm</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>



<div id="setAvailabilityModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Set dates for <strong></strong></h4>
      </div>

      <div class="modal-body">
      	<form method="POST">
      		{{ csrf_field() }}
      		<div class="form-group">
      			<label>Start date</label>
      			<input type="date" class="form-control" name="start_date">
      		</div>
      		<div class="form-group">
      			<label>End date</label>
      			<input type="date" class="form-control" name="end_date">
      		</div>
      		<div class="text-right">
      			<button type="submit" class="btn btn-primary">Save</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>



<div id="addShopManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Shop Manuscript</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.shop-manuscript.add_learner', $learner->id) }}">
      		{{ csrf_field() }}
      		<?php 
			$shopManuscripts = \App\ShopManuscript::all();
			?>
      		<div class="form-group">
      			<label>Shop manuscript</label>
      			<select class="form-control select2" name="shop_manuscript_id" required>
      				<option value="" selected disabled>- Search shop manuscript -</option>
					@foreach($shopManuscripts as $shopManuscript)
					<option value="{{ $shopManuscript->id }}">{{ $shopManuscript->title }}</option>>
					@endforeach
  				</select>
      		</div>
      		<div class="form-group">
      			<label>File</label>
      			<div><em>* Godkjente fil formater er DOCX, PDF og ODT.</em></div>
      			<input type="file" class="form-control" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Add shop manuscript</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>





<div id="addInvoiceModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Invoice for {{ $learner->fullname }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.invoice.store') }}">
      		{{ csrf_field() }}
      		<input type="hidden" name="learner_id" value="{{ $learner->id }}">
      		<div class="form-group">
  				<label>Fiken URL</label>
  				<input type="text" name="fiken_url" class="form-control" required>
      		</div>
      		<div class="form-group">
  				<label>PDF URL</label>
  				<input type="text" name="pdf_url" class="form-control" required>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Create Invoice</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>



<div id="addManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Manuscript</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.manuscript.store') }}">
      		{{ csrf_field() }}
      		<div class="form-group">
      		* Accepted file formats are DOCX, PDF, ODT.</div>
      		<div class="form-group row">
      			<div class="col-sm-6">
      				<input type="file" class="form-control" required name="file" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      			</div>
      		</div>
      		<div class="form-group row">
      			<div class="col-sm-6">
      				<select class="form-control" name="coursetaken_id" required>
      					<option disabled selected value="">- Select course -</option>
						@foreach($learner->coursesTaken as $courseTaken)
						<option value="{{ $courseTaken->id }}">{{ $courseTaken->package->course->title }}</option>>
						@endforeach
      				</select>
      			</div>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Upload manuscript</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>



<div id="editPasswordModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit password</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="field" value="password">
      		<div class="form-group">
      			<label>New password</label>
      			<input type="password" class="form-control" name="password" required>
      		</div>
      		<div class="form-group">
      			<label>Confirm password</label>
      			<input type="password" class="form-control" name="password_confirmation" required>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Save</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="editContactModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit contact info</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.update', $learner->id) }}">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="field" value="contact">
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>First name</label>
	      			<input type="tel" class="form-control" name="first_name" value="{{ $learner->first_name }}">
      			</div>
      			<div class="col-sm-6">
	      			<label>Last name</label>
	      			<input type="text" class="form-control" name="last_name" value="{{ $learner->last_name }}">
	      		</div>
      		</div>
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>Phone</label>
	      			<input type="tel" class="form-control" name="phone" value="{{ $learner->address->phone }}">
      			</div>
      			<div class="col-sm-6">
	      			<label>Street</label>
	      			<input type="text" class="form-control" name="street" value="{{ $learner->address->street }}">
	      		</div>
      		</div>
      		<div class="row form-group">
      			<div class="col-sm-6">
	      			<label>ZIP</label>
	      			<input type="text" class="form-control" name="zip" value="{{ $learner->address->zip }}">
	      		</div>
      			<div class="col-sm-6">
	      			<label>City</label>
	      			<input type="text" class="form-control" name="city" value="{{ $learner->address->city }}">
	      		</div>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Save</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<div id="deleteLearnerModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete learner</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.learner.delete', $learner->id) }}">
      		{{ csrf_field() }}
      		{{ method_field('DELETE') }}
      		Are you sure to delete this learner? <br />
      		<em>Warning: This cannot be undone.</em>

      		<div class="checkbox margin-top">
				<label><input type="checkbox" id="moveToggle" name="moveStatus">Move courses taken, shop manuscripts, and invoices to an account</label>
			</div>

      		<div id="moveRelationships" class="hidden">
	      		<div class="form-group margin-top">
	      			<select class="form-control select2" name="move_learner_id">
	      				<option value="" disabled selected>- Select learner -</option>
	      				@foreach( App\User::where('id', '<>', $learner->id)->orderBy('created_at', 'desc')->get() as $moveLearner )
	      				<option value="{{ $moveLearner->id }}">{{ $moveLearner->full_name }}</option>
	      				@endforeach
	      			</select>
	      		</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="courses_taken">Courses Taken</label>
				</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="shop_manuscripts">Shop Manuscripts</label>
				</div>
	      		<div class="checkbox">
					<label><input type="checkbox" name="moveItems[]" value="invoices">Invoices</label>
				</div>
      		</div>

      		<button type="submit" class="btn btn-danger pull-right">Delete</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>
@stop

@section('scripts')
<script>
	jQuery(document).ready(function(){


		$('.defaultAllowAccessBtn').click(function(){
			var action = $(this).data('action');
			$('#lessonDefaultAccessModal form').attr('action', action)
		});


		$('.allowAccessBtn').click(function(){
			var action = $(this).data('action');
			$('#lessonAccessModal form').attr('action', action)
		});


		$('.setAvailabilityBtn').click(function(){
			var title = $(this).data('title');
			var start_date = $(this).data('start_date');
			var end_date = $(this).data('end_date');
			var action = $(this).data('action');
			var modal = $('#setAvailabilityModal');
			var form = modal.find('form');

			modal.find('.modal-title strong').text(title);
			form.attr('action', action);
			form.find('input[name=start_date]').val(start_date);
			form.find('input[name=end_date]').val(end_date);
		});

		$("#moveToggle").change(function() {
		    if(this.checked) {
		    	$('select[name=move_learner_id]').prop('required', true);
		    	$('#moveRelationships').removeClass('hidden');
		    } else {
		    	$('select[name=move_learner_id]').prop('required', false);
		    	$('#moveRelationships').addClass('hidden');
		    }
		});

		var deleteForm = $('#deleteLearnerModal form');

		deleteForm.on('submit', function(e){
			if( $('#moveToggle').is(':checked') ){
				var checkedItems = deleteForm.find('input[name="moveItems[]"]:checked');
				if( checkedItems.length < 1 || $('select[name=move_learner_id]').val() == null ) {
					if( checkedItems.length < 1 ){
						deleteForm.find('input[name="moveItems[]"]').parent().css('color', 'red');
					}
					e.preventDefault();
					return false;
				}
			}
		});
	});
</script>
@stop
@extends('backend.layout')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
@stop

@section('title')
<title>{{ $workshop->title }} &rsaquo; Workshops &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

<div class="container margin-top">
	{{--<div class="row">
		@if ( $errors->any() )
		<div class="col-sm-4 margin-top">
		  <div class="alert alert-danger bottom-margin">
		      <ul>
		      @foreach($errors->all() as $error)
		      <li>{{$error}}</li>
		      @endforeach
		      </ul>
		  </div>
		</div>
		@endif
	</div>--}}

	<div class="row">
		<div class="col-sm-12">
			<a href="{{ route('admin.workshop.index') }}" class="btn btn-info margin-bottom"><i class="fa fa-angle-left"></i>&nbsp;&nbsp;{{ trans('site.all-workshops') }}</a>
			<div class="workshop-hero text-center" style="background-image: url({{ $workshop->image }})">
				<span class="editWorkshopButton">
					<button type="button" class="btn btn-info" data-toggle="modal" data-target="#editWorkshopModal"><i class="fa fa-pencil"></i></button> 
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteWorkshopModal"><i class="fa fa-trash"></i></button> 
				</span>
				<div>
					<h2>{{ $workshop->title }}</h2>
					<div class="margin-bottom">{{ trans('site.starts-at') }} {{ date_format(date_create($workshop->date), 'h:i A, dS M Y') }}</div>
					<!-- <button type="button" class="btn btn-success btn-lg">Invite People</button> -->
				</div>
			</div>
		</div>
			
		<div class="col-sm-8">
			<!-- About This Workshop  -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ trans('site.about-this-workshop') }}</h4>
					<div class="margin-top margin-bottom">{!! $workshop->description !!}</div>
					<div class="workshop-meta">
						<div><strong>{{ trans('site.price') }}<span class="pull-right">:</span></strong>{{ AdminHelpers::currencyFormat($workshop->price) }}</div>
						<div><strong>{{ trans('site.when') }}<span class="pull-right">:</span></strong>{{ date_format(date_create($workshop->date), 'h:i A, dS M Y') }}</div>
						<div><strong>Faktura Due Date <span class="pull-right">:</span></strong>{{ $workshop->faktura_date ? date_format(date_create($workshop->faktura_date), 'dS M Y') : '' }}</div>
						<div><strong>{{ trans('site.duration') }}<span class="pull-right">:</span></strong>{{ $workshop->duration }} hours</div>
						<div><strong>Fiken product<span class="pull-right">:</span></strong>{{ $workshop->fiken_product }}</div>
						<div><strong>{{ trans('site.total-seats') }}<span class="pull-right">:</span></strong>{{ $workshop->seats }}</div>
					</div>
				</div>
			</div>

			<!-- Presenters  -->
			<div class="panel panel-default">
				<div class="panel-body">
					<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#addPresenterModal">{{ ucwords(trans('site.add-presenter')) }}</button>
					<h4>{{ trans('site.presenters') }}</h4>
					<div class="row margin-top">
						@foreach( $workshop->presenters as $presenter )
						<div class="col-sm-4 workshop-presenter">
							<div>
								<div class="presenter-thumb" @if( $presenter->image ) style="background-image: url({{ $presenter->image  }})" @endif></div>
								<div class="presenter-meta">
									<strong>{{ $presenter->first_name }} {{ $presenter->last_name }}</strong><br />
									{{ $presenter->email }}
									<div style="margin-top: 4px;">
										<button class="btn btn-danger btn-xs deletePresenterBtn" data-toggle="modal" data-target="#deletePresenterModal" data-id="{{$presenter->id}}" data-first-name="{{$presenter->first_name}}" data-last-name="{{$presenter->last_name}}" data-action="{{ route('admin.course.workshop-presenter.destroy', ['workshop_id' => $workshop->id, 'presenter_id' => $presenter->id]) }}"><i class="fa fa-trash"></i></button>

										<button class="btn btn-info btn-xs editPresenterBtn" data-toggle="modal" data-target="#editPresenterModal" data-image="{{$presenter->image}}" data-first-name="{{$presenter->first_name}}" data-last-name="{{$presenter->last_name}}" data-email="{{$presenter->email}}" data-action="{{ route('admin.course.workshop-presenter.update', ['workshop_id' => $workshop->id, 'presenter_id' => $presenter->id]) }}"><i class="fa fa-pencil"></i></button>
									</div>
								</div>

							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>


			<!-- Menu  -->
			<div class="panel panel-default">
				<div class="panel-body">
					<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#addMenuModal">{{ trans('site.add-menu') }}</button>
					<h4>{{ trans('site.practical-information') }}</h4>
					<div class="row margin-top">
						@foreach( $workshop->menus as $menu )
						<div class="col-sm-6 workshop-menu">
							<div>
								<div class="menu-thumb" style="background-image: url('{{ $menu->image  }}')"></div>
								<div class="menu-meta">
									<div class="pull-right">
										<button class="btn btn-danger btn-xs deleteMenuBtn" data-toggle="modal"
												data-target="#deleteMenuModal" data-title="{{ $menu->title }}"
												data-action="{{ route('admin.course.workshop-menu.destroy',
												 ['workshop_id' => $workshop->id, 'workshop_menu' => $menu->id]) }}">
											<i class="fa fa-trash"></i>
										</button>

										<button class="btn btn-info btn-xs editMenuBtn" data-toggle="modal"
												data-target="#editMenuModal" data-image="{{$menu->image}}"
												data-title="{{$menu->title}}" data-description="{{$menu->description}}"
												data-action="{{ route('admin.course.workshop-menu.update',
												['workshop_id' => $workshop->id, 'workshop_menu' => $menu->id]) }}">
											<i class="fa fa-pencil"></i>
										</button>
									</div>
									<h4 style="margin-bottom: 7px">{{ $menu->title }}</h4>
									<p class="no-margin-bottom">{!! nl2br($menu->description) !!}</p>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>

			<!-- Email  -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<button class="pull-right btn btn-xs btn-primary" data-toggle="modal" data-target="#editEmailModal">{{ trans('site.edit') }}</button>
					<h4>{{ trans_choice('site.emails', 1) }}</h4>
				</div>
				<div class="panel-body">
					<b>{{ trans('site.subject') }}:</b> {{ $workshop->email_title }} <br>
					<b>{{ trans('site.body') }}:</b> <br>
					{!! nl2br($workshop->email_body) !!}
				</div>
			</div>
			<!-- end of Email  -->
		</div>

		<div class="col-sm-4">
			<!-- People Registered -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ $workshop->attendees->count() }} {{ trans('site.people-registered') }}</h4>
					<div class="progress margin-top">
					  <?php $percent = floor(( $workshop->attendees->count() / $workshop->seats ) * 100); ?>
					  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $percent }}"
					  aria-valuemin="0" aria-valuemax="100" style="width:{{ $percent }}%">
					    <span>{{ $percent }}%</span>
					  </div>
					</div>
					<div style="line-height: 20px">
						<div>{{ trans('site.available-seats') }}: <strong>{{ $workshop->seats - $workshop->attendees->count() }}</strong></div>
					</div>
					<br />
					<h4>{{ trans('site.attendees') }}</h4>
					<hr style="margin: 7px 0" />
					@foreach( $workshop->taken as $taken )
					<div style="margin: 7px 0 10px 0">
						<button type="button" class="btn btn-xs btn-danger pull-right removeAttendeeBtn" data-attendee="{{ $taken->user->full_name }}" data-action="{{ route('admin.workshop.remove_attendee', ['workshop_taken_id' => $taken->id, 'attendee_id' => $taken->user->id]) }}" data-toggle="modal" data-target="#removeAttendeeModal"><i class="fa fa-trash"></i></button>
						<a href="{{ route('admin.learner.show', $taken->user->id) }}">{{ $taken->user->full_name }}</a> <br />
						{{ trans('site.menu') }}: <strong>{{ $taken->menu->title }}</strong> <br />
						{{ trans_choice('site.notes', 2) }}: <strong>{{ $taken->notes }}</strong>
					</div>
					@endforeach
					<form method="POST" action="{{ route('admin.workshop.download_pdf', $workshop->id) }}" class="inline">
						{{ csrf_field() }}
						<button class="btn btn-sm btn-primary" type="submit">{{ trans('site.export-to-pdf') }}</button>
					</form>

					<a href="{{ route('admin.workshop.download_excel', $workshop->id) }}"
					   class="btn btn-info btn-sm">
						Export to Excel
					</a>

					<button type="button" class="btn btn-default btn-sm" data-toggle="modal"
							data-target="#addLearnersToCourseModal">+ Add to Course</button>

					@if($workshop->attendees->count() > 0)
						<button type="button" class="btn btn-success btn-sm d-block" data-toggle="modal"
								data-target="#sendEmailModal" style="margin-top: 5px">{{ trans('site.send-email') }}</button>
					@endif
				</div>
			</div>


			<!-- Location -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ trans('site.location') }}</h4>
					<div class="margin-top">
						{{ $workshop->location }}
						<div id="map"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<!-- Email Log -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Email Log</h4>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-side-bordered table-white">
							<thead>
							<tr>
								<th>Subject</th>
								<th>Message</th>
								<th width="150">Date Sent</th>
								<th>From</th>
								<th>Attachment</th>
								<th>Recipient</th>
							</tr>
							</thead>
							<tbody>
							@foreach($emailLog as $log)
								<tr>
									<td>{{ $log->subject }}</td>
									<td>{!! nl2br($log->message) !!}</td>
									<td>{{ $log->date_sent }}</td>
									<td>
										{{ $log->from_name ?: 'Forfatterskolen' }} <br>
										{{ $log->from_email ?: 'post@forfatterskolen.no' }}
									</td>
									<td>
										<a href="{{ asset($log->attachment) }}" download>
											{{ $log->attachment
                                                ? \App\Http\AdminHelpers::extractFileName($log->attachment)
                                                : '' }}
										</a>
									</td>
									<td>
										@if($log->learners)
											<a href="#viewAttendeesModal" data-toggle="modal" class="viewAttendeeBtn"
											   data-action="{{ route('admin.workshop.send_email_log', $log->id) }}">
												View Attendees
											</a>
										@else
											All
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>

					<div class="pull-right">
						{{ $emailLog->render() }}
					</div>
				</div>
			</div>
			<!-- end Email Log -->
		</div>
	</div>
</div>


<!-- Remove attendee Modal -->
<div id="removeAttendeeModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="no-margin">{{ trans('site.remove-attendee') }}</h4>
		  	</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					{!! str_replace('_ATTENDEE_', '<strong></strong>', trans('site.remove-attendee-question')) !!}
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">{{ trans('site.remove') }}</button>
					</div>
				</form>
		   </div>
	  </div>
	</div>
</div>

<!-- View attendees modal-->
<div id="viewAttendeesModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="no-margin">Attendees</h4>
			</div>
			<div class="modal-body">
				<div class="attendee-container" style="height: 300px">

				</div>
			</div>
		</div>
	</div>
</div>
<!-- end view attendees modal-->


<!-- Delete Menu Modal -->
<div id="deleteMenuModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">{{ trans('site.delete-menu') }}</h4>
      </div>
      <div class="modal-body">
      		<form method="POST" action="">
		  		{{ csrf_field() }}
		  		{{ method_field('DELETE') }}
		      	{!! str_replace('_NAME_','<strong></strong>', trans('site.delete-menu-question')) !!}
		      	<div class="text-right margin-top">
					<button type="submit" class="btn btn-danger">{{ trans('site.delete-menu') }}</button>
		      	</div>
    		</form>
	   </div>
      </div>
    </div>
  </div>
</div>


<!-- Edit Menu Modal -->
<div id="editMenuModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">{{ trans('site.edit-menu') }} <em></em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.course.workshop-menu.store', ['workshop_id' => $workshop->id]) }}">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
	      	<div class="form-group">
	      		<div class="text-center">
			        <div class="image-file margin-bottom">
			          <div class="image-preview" style="height: 200px;background-color: #ccc;border: dashed 1px #aaa;" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
			          <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
			        </div>
		        </div>
	      	</div>
      		<div class="form-group">
	            <label>{{ trans('site.title') }}</label>
	      			<input type="text" name="title" placeholder="{{ trans('site.title') }}" required class="form-control">
	      		</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.description') }}</label>
	      		<textarea name="description" required class="form-control" rows="8" placeholder="{{ trans('site.description') }}"></textarea>
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">{{ trans('site.update-menu') }}</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>

<!-- Add Menu Modal -->
<div id="addMenuModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">{{ trans('site.add-menu') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.course.workshop-menu.store', ['workshop_id' => $workshop->id]) }}">
      		{{ csrf_field() }}
	      	<div class="form-group">
	      		<div class="text-center">
			        <div class="image-file margin-bottom">
			          <div class="image-preview" style="height: 200px;background-color: #ccc;border: dashed 1px #aaa;" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
			          <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
			        </div>
		        </div>
	      	</div>
      		<div class="form-group">
	            <label>{{ trans('site.title') }}</label>
	      			<input type="text" name="title" placeholder="{{ trans('site.title') }}" required class="form-control">
	      		</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.description') }}</label>
	      		<textarea name="description" required class="form-control" rows="8" placeholder="{{ trans('site.description') }}"></textarea>
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">{{ trans('site.add-menu') }}</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>




<!-- Edit Workshop Modal -->
<div id="editWorkshopModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.edit-workshop') }} <em>{{$workshop->title}}</em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('admin.workshop.update', $workshop->id)}}" enctype="multipart/form-data">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
	        <div class="row">
	            <div class="col-sm-6">
	          		<div class="form-group">
	                <label>{{ trans('site.title') }}</label>
	          			<input type="text" name="title" placeholder="{{ trans('site.title') }}" value="{{ $workshop->title }}" required class="form-control">
	          		</div>
	          		<div class="form-group">
	                <label>{{ trans('site.description') }}</label>
	          			<textarea class="form-control tinymce" name="description" placeholder="{{ trans('site.description') }}"
								  rows="5">{{ $workshop->description }}</textarea>
	          		</div>
	          		<div class="form-group">
	                <label>{{ trans('site.price') }}</label>
	          			<input type="number" step="0.01" name="price" placeholder="{{ trans('site.price') }}" value="{{ $workshop->price }}" min="0" required class="form-control">
	          		</div>
	              <div class="form-group">
	                <label>{{ trans('site.date') }}</label>
	                <input type="datetime-local" name="date" placeholder="{{ trans('site.date') }}" value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($workshop->date)) }}" min="0" required class="form-control">
	              </div>
					<div class="form-group">
						<label>Faktura Due Date</label>
						<input type="date" name="faktura_date" placeholder="Faktura Due Date" value="{{ $workshop->faktura_date ? strftime('%Y-%m-%d', strtotime($workshop->faktura_date)) : '' }}" class="form-control">
					</div>
	              <div class="form-group">
	                <label id="course-image">{{ trans('site.image') }}</label>
	                <div class="course-form-image image-file margin-bottom">
	                  <div class="image-preview" style="background-image: url({{ $workshop->image  }})" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
	                  <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
	                </div>
	              </div>

					<div class="form-group">
						<label>{{ trans('site.free') }}</label> <br>
						<input type="checkbox" data-toggle="toggle" data-on="Yes"
							   class="status-toggle" data-off="No" data-size="small" name="is_free"
							@if($workshop->is_free) {{ 'checked' }} @endif>
					</div>

	            </div>
	            <div class="col-sm-6">
	              <div class="form-group">
	                <label>{{ trans('site.duration-in-hours') }}</label>
	                <input type="number" name="duration" placeholder="Duration" value="{{ $workshop->duration }}" min="0" required class="form-control">
	              </div>
	              <div class="form-group">
	                <label>Fiken product</label>
	                <input type="text" name="fiken_product" placeholder="Fiken product" value="{{ $workshop->fiken_product }}" min="0" required class="form-control">
	              </div>
	              <div class="form-group">
	                <label>{{ trans('site.seats') }}</label>
	                <input type="number" name="seats" placeholder="{{ trans('site.seats') }}" value="{{ $workshop->seats }}" min="0" required class="form-control">
	              </div>
	              <div class="form-group">
	                <label>{{ trans('site.location') }}</label>
	                <input type="text" name="location" placeholder="{{ trans('site.location') }}" value="{{ $workshop->location }}" min="0" required class="form-control">
					<div id="map_edit"></div>
					<input type="hidden" name="gmap" value="{{ $workshop->gmap }}">
	              </div>
	          		<button type="submit" class="btn btn-primary pull-right">{{ trans('site.update-workshop') }}</button>
	      		  </div>
	        </div>
      	</form>
      </div>
    </div>

  </div>
</div>

<!-- Delete Workshop Modal -->
<div id="deleteWorkshopModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4>{{ trans('site.delete-workshop') }}</h4>
      </div>
      <div class="modal-body">
		  {!! trans('site.delete-workshop-question') !!}
        <div class="text-right margin-top">
          <form method="POST" action="{{route('admin.workshop.destroy', $workshop->id)}}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-danger">{{ trans('site.delete-workshop') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Add Presenter Modal -->
<div id="addPresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">{{ ucwords(trans('site.add-presenter')) }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('admin.course.workshop-presenter.store', ['workshop_id' => $workshop->id]) }}">
      		{{ csrf_field() }}
	      	<div class="form-group">
	      		<div class="text-center">
			        <div class="user-thumb-image image-file margin-bottom">
			          <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
			          <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
			        </div>
		        </div>
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.first-name') }}</label>
	      		<input type="text" name="first_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.last-name') }}</label>
	      		<input type="text" name="last_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans_choice('site.emails', 1) }}</label>
	      		<input type="email" name="email" required class="form-control"> 
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">{{ ucwords(trans('site.add-presenter')) }}</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>


<!-- Edit Presenter Modal -->
<div id="editPresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">{{ trans('site.edit-presenter') }}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
	      	<div class="form-group">
	      		<div class="text-center">
			        <div class="user-thumb-image image-file margin-bottom">
			          <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
			          <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
			        </div>
		        </div>
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.first-name') }}</label>
	      		<input type="text" name="first_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans('site.last-name') }}</label>
	      		<input type="text" name="last_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>{{ trans_choice('site.emails', 1) }}</label>
	      		<input type="email" name="email" required class="form-control"> 
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">{{ trans('site.update-presenter') }}</button>
	      	</div>
      	</form>
      </div>
    </div>
   </div>
</div>


<!-- Delete Presenter Modal -->
<div id="deletePresenterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">{{ trans('site.delete-presenter') }}</h4>
      </div>
      <div class="modal-body">
      		<form method="POST" action="">
		  		{{ csrf_field() }}
		  		{{ method_field('DELETE') }}
		      	{!! str_replace('_PRESENTER_','<strong></strong>',trans('site.delete-presenter-question-with-name')) !!}
		      	<div class="text-right">
					<button type="submit" class="btn btn-danger">{{ trans('site.delete-presenter') }}</button>
		      	</div>
    		</form>
	   </div>
      </div>
    </div>
  </div>
</div>

<!--send email modal-->

<div id="sendEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('admin.workshop.send_email', $workshop->id)}}" onsubmit="formSubmitted()"
				enctype="multipart/form-data">
					{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" required value="{{ $workshop->email_title }}">
					</div>

					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" id="" cols="30" rows="10" class="form-control tinymce" required>{{ nl2br($workshop->email_body) }}</textarea>
					</div>

					<div class="form-group">
						<label style="display: block">From</label>
						<input type="text" class="form-control" placeholder="Name" style="width: 49%; display: inline;"
							   name="from_name">
						<input type="email" class="form-control" placeholder="Email" style="width: 49%; display: inline;"
							   name="from_email">
					</div>

					<div class="form-group">
						<label>Attachment</label>
						<input type="file" class="form-control" name="attachment"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/msword,
                               application/pdf,
                               application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                               application/vnd.ms-powerpoint,
                               application/vnd.openxmlformats-officedocument.presentationml.presentation">
					</div>

					<div class="form-group">
						<label>Learners</label>
						<small class="text-muted">*Note: If no one is selected, it would send to all</small> <br>
						<input type="checkbox" name="check_all"> <label for="">Check/Uncheck All</label>
					</div>

					<div class="form-group attendee-container" style="height: 200px; margin-top: 10px">
						@if($workshop->attendees->count() > 0)
							@foreach( $workshop->attendees as $attendee)
								<input type="checkbox" name="learners[]" value="{{ $attendee->user->id }}">
								<label>{{ $attendee->user->full_name }}</label> <br>
							@endforeach
						@endif
					</div>

					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}" id="send_email_btn">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end email modal-->

<!-- edit workshop email modal -->
<div id="editEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans_choice('site.emails', 1) }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.workshop.update.email', $workshop->id) }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" name="email_title" class="form-control" value="{{ $workshop->email_title }}" required>
					</div>
					<div class="form-group">
						<label> {{ trans('site.body') }} </label>
						<textarea class="form-control" name="email_body" rows="6" required>{{ $workshop->email_body }}</textarea>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end edit workshop email modal -->

<!-- Add Learner Modal -->
<div id="addLearnersToCourseModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Learners to Course</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.workshop.add-learners-to-course', $workshop->id) }}"
					  onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					<div class="form-group">
						<select class="form-control select2" name="course_id" id="course-selector" required
								onchange="courseChanged()">
							<option value="" selected disabled>- Search Course -</option>
							@foreach( $courses as $course )
								<option value="{{ $course->id }}" data-packages="{{ json_encode($course->packages) }}">
									{{ $course->title }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<select class="form-control" name="package_id" required id="package-selected">
							<option value="" selected disabled>- Select Package -</option>
						</select>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">
							Submit
						</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

@stop

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
<script>

	$('.removeAttendeeBtn').click(function(){
		var attendee = $(this).data('attendee');
		var action = $(this).data('action');
		
		var form = $('#removeAttendeeModal');
		form.find('.modal-body strong').text(attendee);
		form.find('form').attr('action', action);
	});


	$('.deleteMenuBtn').click(function(){
		var id = $(this).data('id');
		var title = $(this).data('title');
		var action = $(this).data('action');
		
		var form = $('#deleteMenuModal');
		form.find('.modal-body strong').text(title );
		form.find('form').attr('action', action);
	});


	$('.editMenuBtn').click(function(){
		var image = $(this).data('image');
		var title = $(this).data('title');
		var description = $(this).data('description');
		var image = $(this).data('image');
		var action = $(this).data('action');
		var form = $('#editMenuModal');

		form.find('em').text(title);
		form.find('.image-preview').css('background-image', 'url('+image+')');
		form.find('input[name=title]').val(title);
		form.find('textarea[name=description]').val(description);
		form.find('form').attr('action', action);
	});



	$('.editPresenterBtn').click(function(){
		var image = $(this).data('image');
		var first_name = $(this).data('first-name');
		var last_name = $(this).data('last-name');
		var email = $(this).data('email');
		var action = $(this).data('action');
		var form = $('#editPresenterModal');
		if(image.length > 0){
			form.find('.image-preview').css('background-image', 'url('+image+')');
		} else {
			form.find('.image-preview').css('background-image', 'url({{asset('images/user.png')}})');
		}
		form.find('input[name=first_name]').val(first_name);
		form.find('input[name=last_name]').val(last_name);
		form.find('input[name=email]').val(email);
		form.find('form').attr('action', action);
	});


	$('.deletePresenterBtn').click(function(){
		var id = $(this).data('id');
		var first_name = $(this).data('first-name');
		var last_name = $(this).data('last-name');
		var action = $(this).data('action');
		
		var form = $('#deletePresenterModal');
		form.find('.modal-body strong').text(first_name+ ' ' +last_name);
		form.find('form').attr('action', action);
	});

    $(".attendee-container").mCustomScrollbar({
        theme: "minimal-dark",
        scrollInertia: 500,
    });

    $("[name=check_all]").click(function(){
        if ($(this).prop('checked')) {
            $("#sendEmailModal").find("[type=checkbox]").prop('checked', true);
        } else {
            $("#sendEmailModal").find("[type=checkbox]").prop('checked', false);
        }
    });

    $(".viewAttendeeBtn").click(function(){
        let action = $(this).data('action');
        let modal = $("#viewAttendeesModal");
        $.get(action, function(data){
            modal.find('.attendee-container').find('.mCSB_container').empty();
            $.each(data,function(k, v) {
                modal.find('.attendee-container').find('.mCSB_container')
					.append('<a href="'+k+'"><label style="cursor: pointer">'+v+'</label></a><br/>');
			});
		});
    });

	function initMap() {
		@if( $workshop->gmap )
		 var uluru = {!! $workshop->gmap !!};
		@else
        var uluru = {lat: 60.823404, lng: 7.749356}; // defaults to Norway
		@endif
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: uluru
        });

        var map_edit = new google.maps.Map(document.getElementById('map_edit'), {
          zoom: 4,
          center: uluru
        });


        var marker = new google.maps.Marker({
          	position: uluru,
          	map: map
        });

        var marker_edit = new google.maps.Marker({
          	position: uluru,
          	map: map_edit,
    		draggable: true,
        });

		google.maps.event.addListener(marker_edit, 'dragend', function( event ){
			var lat = event.latLng.lat();
			var lng = event.latLng.lng();
			$('input[name=gmap]').val('{"lat" : '+lat+', "lng" : '+lng+'}');
		});

    }
    $('#editWorkshopModal').on('shown.bs.modal', function(){
        initMap();
    });

    function formSubmitted() {
        var send_email = $("#send_email_btn");
        send_email.val('Sending....').attr('disabled', true);
    }

    function courseChanged() {
        let e = document.getElementById("course-selector"),
            selected = e.options[e.selectedIndex],
			packages = $(selected).data('packages');

        let packageSelector = $("#package-selected");
        packageSelector.empty();
        let options = '<option value="" selected disabled>- Select Package -</option>';

        $.each(packages, function(k, v){
            options += '<option value="'+v.id+'">'+v.variation+'</option>'
		});

        packageSelector.append(options);
    }

</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBic6B806M8wfuCe3WrwNVNDEfEuUmGi1s&callback=initMap">
</script>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@stop
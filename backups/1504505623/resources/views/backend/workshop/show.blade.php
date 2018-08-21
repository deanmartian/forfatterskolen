@extends('backend.layout')

@section('title')
<title>{{ $workshop->title }} &rsaquo; Workshops &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

<div class="container margin-top">
	<div class="row">
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
	</div>

	<div class="row">
		<div class="col-sm-12">
			<a href="{{ route('admin.workshop.index') }}" class="btn btn-info margin-bottom"><i class="fa fa-angle-left"></i>&nbsp;&nbsp;All Workshops</a>
			<div class="workshop-hero text-center" style="background-image: url({{ $workshop->image }})">
				<span class="editWorkshopButton">
					<button type="button" class="btn btn-info" data-toggle="modal" data-target="#editWorkshopModal"><i class="fa fa-pencil"></i></button> 
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteWorkshopModal"><i class="fa fa-trash"></i></button> 
				</span>
				<div>
					<h2>{{ $workshop->title }}</h2>
					<div class="margin-bottom">Starts at {{ date_format(date_create($workshop->date), 'h:i A, dS M Y') }}</div>
					<!-- <button type="button" class="btn btn-success btn-lg">Invite People</button> -->
				</div>
			</div>
		</div>
			
		<div class="col-sm-8">
			<!-- About This Workshop  -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>About This Workshop</h4>
					<div class="margin-top margin-bottom">{{ $workshop->description }}</div>
					<div class="workshop-meta">
						<div><strong>Price<span class="pull-right">:</span></strong>{{ AdminHelpers::currencyFormat($workshop->price) }}</div>
						<div><strong>When<span class="pull-right">:</span></strong>{{ date_format(date_create($workshop->date), 'h:i A, dS M Y') }}</div>
						<div><strong>Duration<span class="pull-right">:</span></strong>{{ $workshop->duration }} hours</div>
						<div><strong>Fiken product<span class="pull-right">:</span></strong>{{ $workshop->fiken_product }}</div>
						<div><strong>Total Seats<span class="pull-right">:</span></strong>{{ $workshop->seats }}</div>
					</div>
				</div>
			</div>

			<!-- Presenters  -->
			<div class="panel panel-default">
				<div class="panel-body">
					<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#addPresenterModal">Add Presenter</button>
					<h4>Presenters</h4>
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
					<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#addMenuModal">Add Menu</button>
					<h4>Menu</h4>
					<div class="row margin-top">
						@foreach( $workshop->menus as $menu )
						<div class="col-sm-6 workshop-menu">
							<div>
								<div class="menu-thumb" style="background-image: url('{{ $menu->image  }}')"></div>
								<div class="menu-meta">
									<div class="pull-right">
										<button class="btn btn-danger btn-xs deleteMenuBtn" data-toggle="modal" data-target="#deleteMenuModal" data-title="{{ $menu->title }}" data-action="{{ route('admin.course.workshop-menu.destroy', ['workshop_id' => $workshop->id, 'id' => $menu->id]) }}"><i class="fa fa-trash"></i></button>

										<button class="btn btn-info btn-xs editMenuBtn" data-toggle="modal" data-target="#editMenuModal" data-image="{{$menu->image}}" data-title="{{$menu->title}}" data-description="{{$menu->description}}" data-action="{{ route('admin.course.workshop-menu.update', ['workshop_id' => $workshop->id, 'id' => $menu->id]) }}"><i class="fa fa-pencil"></i></button>
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


			<!-- Attendees  -->
			<!-- <div class="panel panel-default">
				<div class="panel-body">
					<h4>Attendees</h4>
					<div class="row margin-top">
						<div class="col-sm-4 workshop-menu">
							<div>
								<div class="menu-thumb"></div>
								<div class="menu-meta">
									johndoe@email.com
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> -->
		</div>

		<div class="col-sm-4">
			<!-- People Registered -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>{{ $workshop->attendees->count() }} people registered</h4>
					<div class="progress margin-top">
					  <?php $percent = floor(( $workshop->attendees->count() / $workshop->seats ) * 100); ?>
					  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $percent }}"
					  aria-valuemin="0" aria-valuemax="100" style="width:{{ $percent }}%">
					    <span>{{ $percent }}%</span>
					  </div>
					</div>
					<div style="line-height: 20px">
						<div>Available Seats: <strong>{{ $workshop->seats - $workshop->attendees->count() }}</strong></div>
					</div>
					<br />
					<h4>Attendees</h4>
					<hr style="margin: 7px 0" />
					@foreach( $workshop->taken as $taken )
					<div style="margin: 7px 0 10px 0">
						<button type="button" class="btn btn-xs btn-danger pull-right removeAttendeeBtn" data-attendee="{{ $taken->user->full_name }}" data-action="{{ route('admin.workshop.remove_attendee', ['workshop_taken_id' => $taken->id, 'attendee_id' => $taken->user->id]) }}" data-toggle="modal" data-target="#removeAttendeeModal"><i class="fa fa-trash"></i></button>
						<a href="">{{ $taken->user->full_name }}</a> <br />
						Menu: <strong>{{ $taken->menu->title }}</strong> <br />
						Notes: <strong>{{ $taken->notes }}</strong>
					</div>
					@endforeach
				</div>
			</div>


			<!-- Location -->
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>Location</h4>
					<div class="margin-top">
						{{ $workshop->location }}
						<div id="map"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Remove attendee Modal -->
<div id="removeAttendeeModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">Remove attendee</h4>
      </div>
      <div class="modal-body">
      		<form method="POST" action="">
		  		{{ csrf_field() }}
		      	Are you sure to remove attendee <strong></strong>?
		      	<div class="text-right margin-top">
					<button type="submit" class="btn btn-danger">Remove</button>
		      	</div>
    		</form>
	   </div>
      </div>
    </div>
  </div>
</div>



<!-- Delete Menu Modal -->
<div id="deleteMenuModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      	<h4 class="no-margin">Delete Menu</h4>
      </div>
      <div class="modal-body">
      		<form method="POST" action="">
		  		{{ csrf_field() }}
		  		{{ method_field('DELETE') }}
		      	Are you sure to delete menu <strong></strong>?
		      	<div class="text-right margin-top">
					<button type="submit" class="btn btn-danger">Delete Menu</button>
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
      	<h4 class="no-margin">Edit Menu <em></em></h4>
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
	            <label>Title</label>
	      			<input type="text" name="title" placeholder="Title" required class="form-control">
	      		</div>
	      	<div class="form-group">
	      		<label>Description</label>
	      		<textarea name="description" required class="form-control" rows="8" placeholder="Description"></textarea>
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">Update Menu</button>
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
      	<h4 class="no-margin">Add Menu</h4>
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
	            <label>Title</label>
	      			<input type="text" name="title" placeholder="Title" required class="form-control">
	      		</div>
	      	<div class="form-group">
	      		<label>Description</label>
	      		<textarea name="description" required class="form-control" rows="8" placeholder="Description"></textarea>
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">Add Menu</button>
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
        <h4 class="modal-title">Edit Workshop <em>{{$workshop->title}}</em></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('admin.workshop.update', $workshop->id)}}" enctype="multipart/form-data">
      		{{ csrf_field() }}
      		{{ method_field('PUT') }}
	        <div class="row">
	            <div class="col-sm-6">
	          		<div class="form-group">
	                <label>Title</label>
	          			<input type="text" name="title" placeholder="Title" value="{{ $workshop->title }}" required class="form-control">
	          		</div>
	          		<div class="form-group">
	                <label>Description</label>
	          			<textarea class="form-control" name="description" placeholder="Description" rows="5">{{ $workshop->description }}</textarea>
	          		</div>
	          		<div class="form-group">
	                <label>Price</label>
	          			<input type="number" step="0.01" name="price" placeholder="Price" value="{{ $workshop->price }}" min="0" required class="form-control">
	          		</div>
	              <div class="form-group">
	                <label>Date</label>
	                <input type="datetime-local" name="date" placeholder="Date" value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($workshop->date)) }}" min="0" required class="form-control">
	              </div>
	              <div class="form-group">
	                <label id="course-image">Image</label>
	                <div class="course-form-image image-file margin-bottom">
	                  <div class="image-preview" style="background-image: url({{ $workshop->image  }})" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
	                  <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
	                </div>
	              </div>
	            </div>
	            <div class="col-sm-6">
	              <div class="form-group">
	                <label>Duration (in hours)</label>
	                <input type="number" name="duration" placeholder="Duration" value="{{ $workshop->duration }}" min="0" required class="form-control">
	              </div>
	              <div class="form-group">
	                <label>Fiken product</label>
	                <input type="text" name="fiken_product" placeholder="Fiken product" value="{{ $workshop->fiken_product }}" min="0" required class="form-control">
	              </div>
	              <div class="form-group">
	                <label>Seats</label>
	                <input type="number" name="seats" placeholder="Seats" value="{{ $workshop->seats }}" min="0" required class="form-control">
	              </div>
	              <div class="form-group">
	                <label>Location</label>
	                <input type="text" name="location" placeholder="Location" value="{{ $workshop->location }}" min="0" required class="form-control">
					<div id="map_edit"></div>
					<input type="hidden" name="gmap" value="{{ $workshop->gmap }}">
	              </div>
	          		<button type="submit" class="btn btn-primary pull-right">Update Workshop</button>
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
        <h4>Delete Workshop</h4>
      </div>
      <div class="modal-body">
        Are you sure to delete this workshop?<br />
        Warning: This cannot be undone.
        <div class="text-right margin-top">
          <form method="POST" action="{{route('admin.workshop.destroy', $workshop->id)}}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-danger">Delete Workshop</button>
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
      	<h4 class="no-margin">Add Presenter</h4>
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
	      		<label>First Name</label>
	      		<input type="text" name="first_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Last Name</label>
	      		<input type="text" name="last_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Email</label>
	      		<input type="email" name="email" required class="form-control"> 
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">Add Presenter</button>
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
      	<h4 class="no-margin">Edit Presenter</h4>
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
	      		<label>First Name</label>
	      		<input type="text" name="first_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Last Name</label>
	      		<input type="text" name="last_name" required class="form-control"> 
	      	</div>
	      	<div class="form-group">
	      		<label>Email</label>
	      		<input type="email" name="email" required class="form-control"> 
	      	</div>
	      	<div class="text-right">
				<button type="submit" class="btn btn-primary">Update Presenter</button>
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
      	<h4 class="no-margin">Delete Presenter</h4>
      </div>
      <div class="modal-body">
      		<form method="POST" action="">
		  		{{ csrf_field() }}
		  		{{ method_field('DELETE') }}
		      	Are you sure to delete presenter <strong></strong>?
		      	<div class="text-right">
					<button type="submit" class="btn btn-danger">Delete Presenter</button>
		      	</div>
    		</form>
	   </div>
      </div>
    </div>
  </div>
</div>

@stop

@section('scripts')
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
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBic6B806M8wfuCe3WrwNVNDEfEuUmGi1s&callback=initMap">
</script>
@stop
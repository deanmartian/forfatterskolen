@extends('backend.layout')

@section('title')
<title>Workshops &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

<div class="page-toolbar">
  <h3><i class="fa fa-file-text-o"></i> All Workshops</h3>
  <div class="navbar-form navbar-right">
      <div class="form-group">
        <form role="search" method="get" action="">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search course..">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
            </span>
        </div>
      </form>
    </div>
  </div>
  <div class="clearfix"></div>
</div>


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

<div class="col-sm-12 margin-top">
  <button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addWorkshopModal">+ Add Workshop</button> 

  <div class="table-responsive">
    <table class="table table-side-bordered table-white">
      <thead>
        <tr>
          <th>Title</th>
          <th>Price</th>
          <th>Date</th>
          <th>Duration</th>
          <th>Seats</th>
          <th>Location</th>
          <th>Attendees</th>
        </tr>
      </thead>
      <tbody>
        @if(count($workshops) > 0)
        @foreach($workshops as $workshop)
        <tr>
          <td><a href="{{ route('admin.workshop.show', $workshop->id) }}">{{ $workshop->title }}</a></td>
          <td>{{ AdminHelpers::currencyFormat($workshop->price) }}</td>
          <td>{{ date_format(date_create($workshop->date), 'h:i A, dS M Y') }}</td>
          <td>{{ $workshop->duration }} hours</td>
          <td>{{ $workshop->seats }}</td>
          <td>{{ $workshop->location }}</td>
          <td>{{ $workshop->attendees->count() }}</td>
        </tr>
        @endforeach
        @endif
      </tbody>
    </table>
  </div>
</div>




<!-- Add Workshop Modal -->
<div id="addWorkshopModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Workshop</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          {{csrf_field()}}
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" placeholder="Title" required class="form-control">
              </div>
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description" placeholder="Description" rows="5"></textarea>
              </div>
              <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" placeholder="Price" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>Date</label>
                <input type="datetime-local" name="date" placeholder="Date" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label id="course-image">Image</label>
                <div class="course-form-image image-file margin-bottom">
                  <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
                  <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label>Duration (in hours)</label>
                <input type="number" name="duration" placeholder="Duration" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>Fiken product</label>
                <input type="text" name="fiken_product" placeholder="Fiken product" value="{{ $workshop->fiken_product }}" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>Seats</label>
                <input type="number" name="seats" placeholder="Seats" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" placeholder="Location" min="0" required class="form-control">
                <div id="map_edit"></div>
                <input type="hidden" name="gmap">
              </div>
              <button type="submit" class="btn btn-primary pull-right">Add Workshop</button>
            </div>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

@stop

@section('scripts')
<script>
    function initMap() {
        var uluru = {lat: 60.823404, lng: 7.749356}; // defaults to Norway
        var map_edit = new google.maps.Map(document.getElementById('map_edit'), {
          zoom: 4,
          center: uluru
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
    $('#addWorkshopModal').on('shown.bs.modal', function(){
        initMap();
    });
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBic6B806M8wfuCe3WrwNVNDEfEuUmGi1s&callback=initMap">
</script>
@stop
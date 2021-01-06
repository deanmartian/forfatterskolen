@extends('backend.layout')

@section('styles')
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Workshops &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

<div class="page-toolbar">
  <h3><i class="fa fa-file-text-o"></i> {{ trans('site.all-workshops') }}</h3>
  <div class="navbar-form navbar-right">
      <div class="form-group">
        <form role="search" method="get" action="">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="{{ trans('site.search-course') }}..">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
            </span>
        </div>
      </form>
    </div>
  </div>
  <div class="clearfix"></div>
</div>


{{--@if ( $errors->any() )
<div class="col-sm-4 margin-top">
  <div class="alert alert-danger bottom-margin">
      <ul>
      @foreach($errors->all() as $error)
      <li>{{$error}}</li>
      @endforeach
      </ul>
  </div>
</div>
@endif--}}

<div class="col-sm-12 margin-top">
  <button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addWorkshopModal">+ {{ trans('site.add-workshop') }}</button>

  <div class="table-responsive">
    <table class="table table-side-bordered table-white">
      <thead>
        <tr>
          <th>{{ trans('site.title') }}</th>
          <th>{{ trans('site.price') }}</th>
          <th>{{ trans('site.date') }}</th>
          <th>{{ trans('site.duration') }}</th>
          <th>{{ trans('site.seats') }}</th>
          <th>{{ trans('site.location') }}</th>
          <th>{{ trans('site.attendees') }}</th>
          <th>{{ trans('site.for-sale') }}</th>
          <th>{{ trans('site.status') }}</th>
        </tr>
      </thead>
      <tbody>
        @if(count($workshops) > 0)
        @foreach($workshops as $workshop)
        <tr>
          <td>
            <a href="{{ route('admin.workshop.show', $workshop->id) }}">{{ $workshop->title }}</a>
          </td>
          <td>{{ AdminHelpers::currencyFormat($workshop->price) }}</td>
          <td>{{ date_format(date_create($workshop->date), 'h:i A, dS M Y') }}</td>
          <td>{{ $workshop->duration }} hours</td>
          <td>{{ $workshop->seats }}</td>
          <td>{{ $workshop->location }}</td>
          <td>{{ $workshop->attendees->count() }}</td>
          <td>
            <input type="checkbox" data-toggle="toggle" data-on="Yes"
                   class="for-sale-toggle" data-off="No"
                   data-id="{{$workshop->id}}" data-size="mini" @if(!$workshop->is_free) {{ 'checked' }} @endif>
          </td>
          <td>
            <input type="checkbox" data-toggle="toggle" data-on="Active"
                   class="status-toggle" data-off="Inactive"
                   data-id="{{$workshop->id}}" data-size="mini" @if($workshop->is_active) {{ 'checked' }} @endif>
          </td>
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
        <h4 class="modal-title">{{ trans('site.add-workshop') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" enctype="multipart/form-data">
          {{csrf_field()}}
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>{{ trans('site.title') }}</label>
                <input type="text" name="title" placeholder="{{ trans('site.title') }}" required class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans('site.description') }}</label>
                <textarea class="form-control tinymce" name="description" placeholder="{{ trans('site.description') }}" rows="5"></textarea>
              </div>
              <div class="form-group">
                <label>{{ trans('site.price') }}</label>
                <input type="number" step="0.01" name="price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans('site.date') }}</label>
                <input type="datetime-local" name="date" placeholder="{{ trans('site.date') }}" min="0" required class="form-control">
              </div>
                <div class="form-group">
                    <label>Faktura Due Date</label>
                    <input type="date" name="faktura_date" placeholder="Faktura Due Date" class="form-control">
                </div>
              <div class="form-group">
                <label id="course-image">{{ trans('site.image') }}</label>
                <div class="course-form-image image-file margin-bottom">
                  <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
                  <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
                </div>
              </div>

              <div class="form-group">
                <label>{{ trans('site.free') }}</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="status-toggle" data-off="No" data-size="small" name="is_free">
              </div>

            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label>{{ trans('site.duration-in-hours') }}</label>
                <input type="number" name="duration" placeholder="{{ trans('site.duration') }}" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>Fiken product</label>
                <input type="text" name="fiken_product" placeholder="Fiken product" value="{{ $workshop->fiken_product }}" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans('site.seats') }}</label>
                <input type="number" name="seats" placeholder="{{ trans('site.seats') }}" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans('site.location') }}</label>
                <input type="text" name="location" placeholder="{{ trans('site.location') }}" min="0" required class="form-control">
                <div id="map_edit"></div>
                <input type="hidden" name="gmap">
              </div>
              <button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-workshop') }}</button>
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

    $(".status-toggle").change(function(){
        var course_id = $(this).attr('data-id');
        var is_checked = $(this).prop('checked');
        var check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/workshop-status',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { "workshop_id" : course_id, 'is_active' : check_val },
            success: function(data){
            }
        });
    });

    $(".for-sale-toggle").change(function(){
        var course_id = $(this).attr('data-id');
        var is_checked = $(this).prop('checked');
        var check_val = is_checked ? 0 : 1;
        $.ajax({
            type:'POST',
            url:'/workshop-for-sale',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { "workshop_id" : course_id, 'is_free' : check_val },
            success: function(data){
            }
        });
    });
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBic6B806M8wfuCe3WrwNVNDEfEuUmGi1s&callback=initMap">
</script>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@stop
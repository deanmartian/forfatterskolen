@extends('backend.layout')

@section('styles')
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

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
          <th>For Sale</th>
          <th>Status</th>
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
                <textarea class="form-control" name="description" placeholder="Description" rows="5" id="editor"></textarea>
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
                    <label>Faktura Due Date</label>
                    <input type="date" name="faktura_date" placeholder="Faktura Due Date" class="form-control">
                </div>
              <div class="form-group">
                <label id="course-image">Image</label>
                <div class="course-form-image image-file margin-bottom">
                  <div class="image-preview" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
                  <input type="file" accept="image/*" name="image" accept="image/jpg, image/jpeg, image/png">
                </div>
              </div>

              <div class="form-group">
                <label>Free</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="status-toggle" data-off="No" data-size="small" name="is_free">
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
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
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

    // tinymce editor config and intitalization
    var editor_config = {
        path_absolute: "{{ URL::to('/') }}",
        height: '15em',
        selector: '#editor',
        plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern'],
        toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | ',
        toolbar2: 'link | alignleft aligncenter alignright ' +
        'alignjustify  | removeformat',
        toolbar3:'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
        relative_urls: false,
        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + '&type=Images';
            } else {
                cmsURL = cmsURL + '&type=Files';
            }

            tinyMCE.activeEditor.windowManager.open({
                file : cmsURL,
                title : 'Filemanager',
                width : x * 0.8,
                height : y * 0.8,
                resizable : 'yes',
                close_previous : 'no'
            });
        }
    };
    tinymce.init(editor_config);
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBic6B806M8wfuCe3WrwNVNDEfEuUmGi1s&callback=initMap">
</script>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@stop
@extends('backend.layout')

@section('title')
<title>Packages &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-4">
			@if ( $errors->any() )
            <div class="alert alert-danger bottom-margin">
                <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
                </ul>
            </div>
            @endif
        </div>
		<div class="col-sm-12">
			<button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addPackageModal">+ Add Package</button> 
		</div>
		@foreach($course->packages as $package)
		<div class="col-sm-12 col-md-12">
			<div class="panel panel-default panel-package">
				<div class="panel-heading">
					<div class="pull-right">
						<button type="button" data-target="#editPackageModal" data-toggle="modal" class="btn btn-info btn-xs btn-edit-package" 
            data-action="{{route('admin.course.package.update', ['course_id' => $course->id, 'package_id' => $package->id])}}" 
            data-variation="{{ $package->variation }}" 
            data-description="{{ $package->description }}" 
            data-manuscripts="{{ $package->manuscripts_count }}" 
            data-full_payment_price="{{ number_format($package->full_payment_price, 0, 0, '') }}" 
            data-months_3_price="{{ number_format($package->months_3_price, 0, 0, '') }}" 
            data-months_6_price="{{ number_format($package->months_6_price, 0, 0, '') }}" 
            data-full_price_product="{{ $package->full_price_product }}" 
            data-months_3_product="{{ $package->months_3_product }}" 
            data-months_6_product="{{ $package->months_6_product }}" 
            data-full_price_due_date="{{ $package->full_price_due_date }}" 
            data-months_3_due_date="{{ $package->months_3_due_date }}" 
            data-months_6_due_date="{{ $package->months_6_due_date }}" 
            data-workshops="{{ $package->workshops }}" 
            data-id="{{ $package->id }}" 
            data-due-date="{{ $package->full_price_due_date }}"><i class="fa fa-pencil"></i></button>

						<button type="button" data-target="#deletePackageModal" data-toggle="modal" class="btn btn-danger btn-xs btn-delete-package" data-action="{{route('admin.course.package.destroy', ['course_id' => $course->id, 'package_id' => $package->id])}}" data-variation="{{$package->variation}}" data-id="{{$package->id}}"><i class="fa fa-trash"></i></button>
					</div>

					<h4>{{$package->variation}}</h4>
				</div>
				<div class="panel-body row">
          <div class="col-sm-6"> 
  					{!! nl2br($package->description) !!}
            <div><em>Maximum manuscripts: {{$package->manuscripts_count}}</em></div>
            <div><em>Workshops: {{$package->workshops}}</em></div>
  					<div class="package-price">
              <div>
                <strong>Full Payment</strong><br />
                <span>Price: {{FrontendHelpers::currencyFormat($package->full_payment_price)}}</span><br />
                <span>Fiken Product ID: {{$package->full_price_product}}</span><br />
                <span>Due Date: {{$package->full_price_due_date}} days</span>
              </div>
              <div>
                <strong>3 Months</strong><br />
                <span>Price: {{FrontendHelpers::currencyFormat($package->months_3_price)}}</span><br />
                <span>Fiken Product ID: {{$package->months_3_product}}</span><br />
                <span>Due Date: {{$package->months_3_due_date}} days</span>
              </div>
              <div>
                <strong>6 Months</strong><br />
                <span>Price: {{FrontendHelpers::currencyFormat($package->months_6_price)}}</span><br />
                <span>Fiken Product ID: {{$package->months_6_product}}</span><br />
                <span>Due Date: {{$package->months_6_due_date}} days</span>
              </div>
  					</div>
          </div>
          <div class="col-sm-6">
            <h4>
              <button class="btn btn-primary btn-xs pull-right addShopManuscriptBtn" data-package_id="{{ $package->id }}" data-toggle="modal" data-target="#addShopManuscriptModal" data-action="{{ route('admin.package_shop_manuscript.store', $package->id) }}" data-shop_manuscripts_id="{{ json_encode($package->shop_manuscripts()->pluck('shop_manuscript_id')->toArray()) }}"><i class="fa fa-plus"></i></button>
              Shop Manuscripts
            </h4>
            <div class="table-responsive margin-top">
              <table class="table table-bordered table-condensed">
                <tbody>
                  @foreach( $package->shop_manuscripts as $shop_manuscript )
                  <tr>
                    <td>
                      <button class="btn btn-danger btn-xs pull-right btndeleteShopManuscript" data-toggle="modal" data-target="#deleteShopManuscriptModal" data-action="{{ route('admin.package_shop_manuscript.destroy', $shop_manuscript->id) }}"><i class="fa fa-trash"></i></button>
                      {{ $shop_manuscript->shop_manuscript->title }}
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            
            <hr />

            <h4>
              <button class="btn btn-primary btn-xs pull-right addRelatedCourseBtn" data-toggle="modal" data-target="#addIncludeCourseModal" data-action="{{ route('admin.package_course.store', $package->id) }}" data-package_id="{{ $package->id }}"><i class="fa fa-plus"></i></button>
              Included Courses
            </h4>
            <div class="table-responsive margin-top">
              <table class="table table-bordered table-condensed">
                <tbody>
                  @foreach( $package->included_courses as $included_course )
                  <tr>
                    <td>
                      <button class="btn btn-danger btn-xs pull-right btndeleteCourse" data-toggle="modal" data-target="#deleteCourseModal" data-action="{{ route('admin.package_course.destroy', $included_course->id) }}"><i class="fa fa-trash"></i></button>
                      {{ $included_course->included_package->course->title }} ({{ $included_course->included_package->variation }})
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
		@endforeach
	</div>
	<div class="clearfix"></div>
</div>




<!-- Delete Workshop Modal -->
<div id="deleteWorkshopModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete workshop</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          Are you sure to delete this workshop?
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Add Workshop Modal -->
<div id="addWorkshopModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add workshop</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          <label>Workshop</label>
          <select class="form-control" required="" name="workshop_id">
            <option value="" selected disabled>- Select workshop -</option> 
            @foreach(App\Workshop::orderBy('created_at', 'desc')->get() as $workshop)
            <option value="{{ $workshop->id }}">{{ $workshop->title }}</option> 
            @endforeach
          </select>
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">Add workshop</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Delete Shop Manuscript Modal -->
<div id="deleteShopManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete shop manuscript</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          Are you sure to delete this shop manuscript?
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Add Shop Manuscript Modal -->
<div id="addShopManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add shop manuscript</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          <label>Shop manuscript</label>
          <select class="form-control" required="" name="shop_manuscript_id">
            <option value="" selected disabled>- Select shop manuscript -</option> 
            @foreach(App\ShopManuscript::orderBy('created_at', 'desc')->get() as $shopManuscript)
            <option value="{{ $shopManuscript->id }}">{{ $shopManuscript->title }}</option> 
            @endforeach
          </select>
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">Add shop manuscript</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Add Package Modal -->
<div id="addPackageModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Package to {{$course->title}}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('admin.course.package.store', ['id' => $course->id])}}">
      		{{csrf_field()}}
          <input type="hidden" name="variation_id">
          <div class="row">
            <div class="col-sm-5">
              <div class="form-group">
                <label>Variation</label>
                <input type="text" name="variation" placeholder="Variation" required class="form-control">
              </div>
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description" placeholder="Description" required rows="5"></textarea>
              </div>
              <div class="form-group">
                <label>Maximum Manuscripts</label>
                <input type="number" name="manuscripts_count" placeholder="Maximum manuscripts" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>Workshops</label>
                <input type="number" name="workshops" placeholder="Workshops" min="0" class="form-control">
              </div>
            </div>

            <div class="col-sm-7">
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#fullprice">Full Payment</a></li>
                <li><a data-toggle="tab" href="#3months">3 Months</a></li>
                <li><a data-toggle="tab" href="#6months">6 Months</a></li>
              </ul>
              <div class="tab-content">
                <div id="fullprice" class="tab-pane fade in active">
                  <h4>Full Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="full_payment_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="full_price_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                </div>
                <div id="3months" class="tab-pane fade">
                  <h4>3 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_3_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_3_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_3_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                </div>
                <div id="6months" class="tab-pane fade">
                  <h4>6 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_6_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_6_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_6_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                </div>
              </div>
             
            </div>
          </div>
          <button type="submit" class="btn btn-primary pull-right">Create Package</button>
          <div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<!-- Edit Package Modal -->
<div id="editPackageModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Package <span></span></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="">
      		{{csrf_field()}}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="variation_id">
          <div class="row">
            <div class="col-sm-5">
          		<div class="form-group">
                <label>Variation</label>
          			<input type="text" name="variation" placeholder="Variation" required class="form-control">
          		</div>
          		<div class="form-group">
                <label>Description</label>
          			<textarea class="form-control" name="description" placeholder="Description" required rows="5"></textarea>
          		</div>
              <div class="form-group">
                <label>Maximum Manuscripts</label>
                <input type="number" name="manuscripts_count" placeholder="Maximum manuscripts" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>Workshops</label>
                <input type="number" name="workshops" placeholder="Workshops" min="0" class="form-control">
              </div>
            </div>

            <div class="col-sm-7">
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#fullprice_edit">Full Payment</a></li>
                <li><a data-toggle="tab" href="#3months_edit">3 Months</a></li>
                <li><a data-toggle="tab" href="#6months_edit">6 Months</a></li>
              </ul>
              <div class="tab-content">
                <div id="fullprice_edit" class="tab-pane fade in active">
                  <h4>Full Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="full_payment_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="full_price_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                </div>
                <div id="3months_edit" class="tab-pane fade">
                  <h4>3 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_3_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_3_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_3_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                </div>
                <div id="6months_edit" class="tab-pane fade">
                  <h4>6 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_6_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_6_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_6_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                </div>
              </div>
             
            </div>
          </div>
      		<button type="submit" class="btn btn-primary pull-right">Update Package</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>


<!-- Delete Package Modal -->
<div id="deletePackageModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete Package <span></span></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="">
      		{{csrf_field()}}
      		{{ method_field('DELETE') }}
      		<input type="hidden" name="variation_id">
      		<p>
      			WARNING: Deleting a package will also delete the learners who took this course.<br /><br />
				Are you sure to delete this package?
      		</p>
      		<button type="submit" class="btn btn-danger btn-block">Delete Package</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>




<!-- Add Related Course Modal -->
<div id="addIncludeCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Include course</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{ csrf_field() }}
          <div class="form-group">
            <label>Course</label>
            <select class="form-control" required id="related_course_select">
              <option value="" selected disabled>- Select course -</option> 
              @foreach(App\Course::where('id', '<>', $course->id)->orderBy('created_at', 'desc')->get() as $course)
              <option value="{{ $course->id }}" data-packages="{{ json_encode($course->packages()->pluck('variation', 'id')) }}">{{ $course->title }}</option> 
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Package</label>
            <select class="form-control" required name="include_package_id" id="related_package_select">
              <option value="" selected disabled>- Select package -</option> 
            </select>
          </div>
          <input type="hidden" name="package_id">
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">Include course</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>


<!-- Delete Course Modal -->
<div id="deleteCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete included course</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          Are you sure to delete this included course?
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@stop


@section('scripts')
<script>
$(document).ready(function(){

  $('.btndeleteCourse').click(function(){
    var action = $(this).data('action');
    $('#deleteCourseModal form').attr('action', action);
  });

  $('.addRelatedCourseBtn').click(function(){
    var package_id = $(this).data('package_id');
    var action = $(this).data('action');
    $('#addIncludeCourseModal form').attr('action', action);
    $('#addIncludeCourseModal').find('input[name=package_id]').val(package_id);
  });

  $('#related_course_select').change(function(){
    var packages = $('option:selected', this).data('packages');
    $('#related_package_select option:not(:first)').remove();
    for( var i = 0; i < Object.keys(packages).length; i++ ){
      var package_id = Object.keys(packages)[i];
      var variation = Object.values(packages)[i];
      $('#related_package_select').append('<option value="'+package_id+'">'+variation+'</option>');
    }
  });

  $('.btndeleteWorkshop').click(function(){
    var action = $(this).data('action');
    $('#deleteWorkshopModal form').attr('action', action);
  });


  $('.addWorkshopBtn').click(function(){
    var form = $('#addWorkshopModal form');
    var package_id = $(this).data('package_id');
    var workshops_id = $(this).data('workshops_id');
    var action = $(this).data('action');

    $('#addWorkshopModal select option:first:disabled').prop('selected', true);

    $('#addWorkshopModal select option:not(:first)').prop('disabled', false);

    $('#addWorkshopModal select option:not(:first)').filter(function() {
        return !($.inArray(parseInt($(this).val()), workshops_id) == -1);
    }).prop('disabled', true);

    form.attr('action', action);
    form.find('input[name=package_id]').val(package_id);
  });



  $('.btndeleteShopManuscript').click(function(){
    var action = $(this).data('action');
    $('#deleteShopManuscriptModal form').attr('action', action);
  });

  $('.addShopManuscriptBtn').click(function(){
    var form = $('#addShopManuscriptModal form');
    var package_id = $(this).data('package_id');
    var shop_manuscripts_id = $(this).data('shop_manuscripts_id');
    var action = $(this).data('action');

    $('#addShopManuscriptModal select option:first:disabled').prop('selected', true);

    $('#addShopManuscriptModal select option:not(:first)').prop('disabled', false);

    /*$('#addShopManuscriptModal select option:not(:first)').filter(function() {
        return !($.inArray(parseInt($(this).val()), shop_manuscripts_id) == -1);
    }).prop('disabled', true)*/;

    form.attr('action', action);
    form.find('input[name=package_id]').val(package_id);
  });

  $('.btn-edit-package').click(function(){
      var action = $(this).data('action');
      var variation = $(this).data('variation');
      var variation_id = $(this).data('id');
      var description = $(this).data('description');
      var manuscripts_count = $(this).data('manuscripts');
      var workshops = $(this).data('workshops');

      var full_payment_price = $(this).data('full_payment_price');
      var months_3_price = $(this).data('months_3_price');
      var months_6_price = $(this).data('months_6_price');

      var full_price_product = $(this).data('full_price_product');
      var months_3_product = $(this).data('months_3_product');
      var months_6_product = $(this).data('months_6_product');

      var full_price_due_date = $(this).data('full_price_due_date');
      var months_3_due_date = $(this).data('months_3_due_date');
      var months_6_due_date = $(this).data('months_6_due_date');

      var due_date = $(this).data('due-date');
      $('#editPackageModal form').attr('action', action);
      $('#editPackageModal h4 span').text(variation);
      $('#editPackageModal input[name=variation]').val(variation);
      $('#editPackageModal input[name=variation_id]').val(variation_id);
      $('#editPackageModal textarea[name=description]').val(description);
      $('#editPackageModal input[name=manuscripts_count]').val(manuscripts_count);

      $('#editPackageModal input[name=full_payment_price]').val(full_payment_price);
      $('#editPackageModal input[name=months_3_price]').val(months_3_price);
      $('#editPackageModal input[name=months_6_price]').val(months_6_price);

      $('#editPackageModal input[name=full_price_product]').val(full_price_product);
      $('#editPackageModal input[name=months_3_product]').val(months_3_product);
      $('#editPackageModal input[name=months_6_product]').val(months_6_product);

      $('#editPackageModal input[name=full_price_due_date]').val(full_price_due_date);
      $('#editPackageModal input[name=months_3_due_date]').val(months_3_due_date);
      $('#editPackageModal input[name=months_6_due_date]').val(months_6_due_date);
      $('#editPackageModal input[name=workshops]').val(workshops);
  });

  $('.btn-delete-package').click(function(){
      var action = $(this).data('action');
      var variation = $(this).data('variation');
      var price = $(this).data('price');
      var variation_id = $(this).data('id');
      $('#deletePackageModal form').attr('action', action);
      $('#deletePackageModal h4 span').text(variation);
      $('#deletePackageModal input[name=variation_id]').val(variation_id);
      $('#deletePackageModal input[name=price]').val(price);
  });
});
</script>
@stop
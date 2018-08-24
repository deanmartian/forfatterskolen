@extends('backend.layout')

@section('styles')
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

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
			<button type="button" class="btn btn-primary margin-bottom btn-add-package" data-toggle="modal" data-target="#addPackageModal">+ Add Package</button>
		</div>
		@foreach($course->packages as $k => $package)
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
            data-months_12_price="{{ number_format($package->months_12_price, 0, 0, '') }}"
            data-full_price_product="{{ $package->full_price_product }}" 
            data-months_3_product="{{ $package->months_3_product }}" 
            data-months_6_product="{{ $package->months_6_product }}"
            data-months_12_product="{{ $package->months_12_product }}"
            data-full_price_due_date="{{ $package->full_price_due_date }}" 
            data-months_3_due_date="{{ $package->months_3_due_date }}" 
            data-months_6_due_date="{{ $package->months_6_due_date }}"
            data-months_12_due_date="{{ $package->months_12_due_date }}"
            data-workshops="{{ $package->workshops }}"
            data-full_payment_sale_price="{{ number_format($package->full_payment_sale_price, 0, 0, '') }}"
            data-full_payment_sale_price_from="{{ $package->full_payment_sale_price_from }}"
            data-full_payment_sale_price_to="{{ $package->full_payment_sale_price_to }}"
            data-months_3_sale_price="{{ number_format($package->months_3_sale_price, 0, 0, '') }}"
            data-months_3_sale_price_from="{{ $package->months_3_sale_price_from }}"
            data-months_3_sale_price_to="{{ $package->months_3_sale_price_to }}"
            data-months_6_sale_price="{{ number_format($package->months_6_sale_price, 0, 0, '') }}"
            data-months_6_sale_price_from="{{ $package->months_6_sale_price_from }}"
            data-months_6_sale_price_to="{{ $package->months_6_sale_price_to }}"
            data-months_12_sale_price="{{ number_format($package->months_12_sale_price, 0, 0, '') }}"
            data-months_12_sale_price_from="{{ $package->months_12_sale_price_from }}"
            data-months_12_sale_price_to="{{ $package->months_12_sale_price_to }}"
            data-months_3_enable="{{ $package->months_3_enable }}"
            data-months_6_enable="{{ $package->months_6_enable }}"
            data-months_12_enable="{{ $package->months_12_enable }}"
            data-id="{{ $package->id }}"
            data-due-date="{{ $package->full_price_due_date }}"
                        data-has_student_discount="{{ $package->has_student_discount }}"
                                data-full_payment_upgrade_price="{{ $package->full_payment_upgrade_price }}"
                                data-months_3_upgrade_price="{{ $package->months_3_upgrade_price }}"
                                data-months_6_upgrade_price="{{ $package->months_6_upgrade_price }}"
                                data-months_12_upgrade_price="{{ $package->months_12_upgrade_price }}"
                                data-full_payment_standard_upgrade_price="{{ $package->full_payment_standard_upgrade_price }}"
                                data-months_3_standard_upgrade_price="{{ $package->months_3_standard_upgrade_price }}"
                                data-months_6_standard_upgrade_price="{{ $package->months_6_standard_upgrade_price }}"
                                data-months_12_standard_upgrade_price="{{ $package->months_12_standard_upgrade_price }}"
                        data-selected-course="{{ $package->course_type }}"
                        data-course-type="{{ $package->course_type }}"
                                data-disable-upgrade-price-date="{{ $package->disable_upgrade_price_date }}"
                                data-disable-upgrade-price="{{ $package->disable_upgrade_price }}"><i class="fa fa-pencil"></i></button>

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
                      <div>
                        <strong>12 Months</strong><br />
                        <span>Price: {{FrontendHelpers::currencyFormat($package->months_12_price)}}</span><br />
                        <span>Fiken Product ID: {{$package->months_12_product}}</span><br />
                        <span>Due Date: {{$package->months_12_due_date}} days</span>
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

            @if (!$package->has_coaching)
              <div class="clearfix"></div>

              <h4 style="margin-top:5px">
                <button class="btn btn-primary btn-xs pull-right includeCoachingBtn" data-toggle="modal" data-target="#includeCoachingModal" data-action="{{ route('admin.course.package.include-coaching', ['course_id' => $course->id, 'package_id' => $package->id]) }}"
                data-include="1"><i class="fa fa-plus"></i></button>
                Included Coaching Session
              </h4>
            @endif

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
                  @if ($package->has_coaching)
                    <tr>
                      <td>
                        <button class="btn btn-danger btn-xs pull-right includeCoachingBtn" data-toggle="modal" data-target="#includeCoachingModal" data-action="{{ route('admin.course.package.include-coaching', ['course_id' => $course->id, 'package_id' => $package->id]) }}"
                                data-include="0"><i class="fa fa-trash"></i></button>
                        1 hr coaching session
                      </td>
                    </tr>
                  @endif
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
              <div class="form-group">
                <label>Course Type</label>
                <select name="course_type"class="form-control" required>
                  <option value="" selected disabled>Select Course Type</option>
                  @foreach(\App\Http\AdminHelpers::courseType() as $courseType)
                    <option value="{{ $courseType['id'] }}"> {{ $courseType['option'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group disable-upgrade-container">
                <label>Disable Upgrade Price On (Date)</label>
                <input type="date" name="disable_upgrade_price_date" placeholder="Disable Upgrade Price (Date)" class="form-control">
              </div>
              <div class="form-group disable-upgrade-container">
                <label>Disable Upgrade Price</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="disable-upgrade-price-toggle" data-off="No"
                       name="disable_upgrade_price" data-width="84">
              </div>
              <div class="form-group">
                <label>Student Discount</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Enable"
                       class="for-sale-toggle" data-off="Disable"
                       name="has_student_discount" data-width="84" checked>
              </div>
            </div>

            <div class="col-sm-7">
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#fullprice">Full Payment</a></li>
                <li><a data-toggle="tab" href="#3months">3 Months</a></li>
                <li><a data-toggle="tab" href="#6months">6 Months</a></li>
                <li><a data-toggle="tab" href="#12months">12 Months</a></li>
              </ul>
              <div class="tab-content">
                <div id="fullprice" class="tab-pane fade in active">
                  <h4>Full Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="full_payment_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                    <div class="form-group">
                        <label>Sale Price</label>
                        <input type="number" step="0.01" name="full_payment_sale_price" placeholder="Sale Price" min="0" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Sale Price From</label>
                            <input type="date" name="full_payment_sale_price_from" placeholder="Sale Price From" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Sale Price To</label>
                            <input type="date" name="full_payment_sale_price_to" placeholder="Sale Price To" class="form-control">
                        </div>
                    </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="full_price_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="full_payment_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="full_payment_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                </div>
                <div id="3months" class="tab-pane fade">
                  <h4>3 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_3_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                    <div class="form-group">
                        <label>Sale Price</label>
                        <input type="number" step="0.01" name="months_3_sale_price" placeholder="Sale Price" min="0" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Sale Price From</label>
                            <input type="date" name="months_3_sale_price_from" placeholder="Sale Price From" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Sale Price To</label>
                            <input type="date" name="months_3_sale_price_to" placeholder="Sale Price To" class="form-control">
                        </div>
                    </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_3_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_3_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="months_3_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="months_3_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Display Plan</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_3_enable" data-width="84" checked>
                  </div>
                </div>
                <div id="6months" class="tab-pane fade">
                  <h4>6 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_6_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                    <div class="form-group">
                        <label>Sale Price</label>
                        <input type="number" step="0.01" name="months_6_sale_price" placeholder="Sale Price" min="0" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Sale Price From</label>
                            <input type="date" name="months_6_sale_price_from" placeholder="Sale Price From" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Sale Price To</label>
                            <input type="date" name="months_6_sale_price_to" placeholder="Sale Price To" class="form-control">
                        </div>
                    </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_6_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_6_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="months_6_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="months_6_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Display Plan</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_6_enable" data-width="84" checked>
                  </div>
                </div>
                <div id="12months" class="tab-pane fade">
                  <h4>12 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_12_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Sale Price</label>
                    <input type="number" step="0.01" name="months_12_sale_price" placeholder="Sale Price" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Sale Price From</label>
                      <input type="date" name="months_12_sale_price_from" placeholder="Sale Price From" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Sale Price To</label>
                      <input type="date" name="months_12_sale_price_to" placeholder="Sale Price To" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_12_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_12_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="months_12_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="months_12_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Display Plan</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_12_enable" data-width="84" checked>
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
              <div class="form-group">
                <label>Course Type</label>
                <select name="course_type"class="form-control" required>
                  <option value="" selected disabled>Select Course Type</option>
                  @foreach(\App\Http\AdminHelpers::courseType() as $courseType)
                    <option value="{{ $courseType['id'] }}"> {{ $courseType['option'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group disable-upgrade-container">
                <label>Disable Upgrade Price On (Date)</label>
                <input type="date" name="disable_upgrade_price_date" placeholder="Disable Upgrade Price (Date)" class="form-control">
              </div>
              <div class="form-group disable-upgrade-container">
                <label>Disable Upgrade Price</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="disable-upgrade-price-toggle" data-off="No"
                       name="disable_upgrade_price" data-width="84">
              </div>
              <div class="form-group">
                <label>Student Discount</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Enable"
                       class="for-sale-toggle" data-off="Disable"
                       name="has_student_discount" data-width="84">
              </div>

            </div>

            <div class="col-sm-7">
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#fullprice_edit">Full Payment</a></li>
                <li><a data-toggle="tab" href="#3months_edit">3 Months</a></li>
                <li><a data-toggle="tab" href="#6months_edit">6 Months</a></li>
                <li><a data-toggle="tab" href="#12months_edit">12 Months</a></li>
              </ul>
              <div class="tab-content">
                <div id="fullprice_edit" class="tab-pane fade in active">
                  <h4>Full Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="full_payment_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Sale Price</label>
                    <input type="number" step="0.01" name="full_payment_sale_price" placeholder="Sale Price" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Sale Price From</label>
                      <input type="date" name="full_payment_sale_price_from" placeholder="Sale Price From" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Sale Price To</label>
                      <input type="date" name="full_payment_sale_price_to" placeholder="Sale Price To" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="full_price_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="full_payment_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="full_payment_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                </div>
                <div id="3months_edit" class="tab-pane fade">
                  <h4>3 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_3_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Sale Price</label>
                    <input type="number" step="0.01" name="months_3_sale_price" placeholder="Sale Price" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Sale Price From</label>
                      <input type="date" name="months_3_sale_price_from" placeholder="Sale Price From" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Sale Price To</label>
                      <input type="date" name="months_3_sale_price_to" placeholder="Sale Price To" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_3_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_3_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="months_3_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="months_3_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Display Plan</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_3_enable" data-width="84">
                  </div>
                </div>
                <div id="6months_edit" class="tab-pane fade">
                  <h4>6 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_6_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Sale Price</label>
                    <input type="number" step="0.01" name="months_6_sale_price" placeholder="Sale Price" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Sale Price From</label>
                      <input type="date" name="months_6_sale_price_from" placeholder="Sale Price From" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Sale Price To</label>
                      <input type="date" name="months_6_sale_price_to" placeholder="Sale Price To" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_6_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_6_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="months_6_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="months_6_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Display Plan</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_6_enable" data-width="84">
                  </div>
                </div>
                <div id="12months_edit" class="tab-pane fade">
                  <h4>12 Months Payment Price</h4>
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="months_12_price" placeholder="Price" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Sale Price</label>
                    <input type="number" step="0.01" name="months_12_sale_price" placeholder="Sale Price" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Sale Price From</label>
                      <input type="date" name="months_12_sale_price_from" placeholder="Sale Price From" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Sale Price To</label>
                      <input type="date" name="months_12_sale_price_to" placeholder="Sale Price To" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_12_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Due Date (in days)</label>
                    <input type="number" name="months_12_due_date" placeholder="Due Date" min="0" required class="form-control">
                  </div>
                  <div class="form-group upgrade-price-container">
                    <label>Upgrade Price <span class="label-basic"></span></label>
                    <input type="number" step="0.01" name="months_12_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group upgrade-price-standard-container">
                    <label>Upgrade Price For Standard</label>
                    <input type="number" step="0.01" name="months_12_standard_upgrade_price" placeholder="Price" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Display Plan</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_12_enable" data-width="84">
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

<div id="includeCoachingModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          <p>
            Are you sure to <span></span> 1hr coaching session in this package?
          </p>
          <input type="hidden" name="has_coaching">
          <div class="text-right margin-top">
            <button type="submit" class="btn">Include Session</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@stop


@section('scripts')
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script>
$(document).ready(function(){

    $(".upgrade-price-container").hide();
    $(".upgrade-price-standard-container").hide();
    $(".disable-upgrade-container").hide();

    $("#addPackageModal").find('select[name=course_type]').change(function(){
        var selected_course = $(this).val();
        if (selected_course === '1') {
            $(".upgrade-price-container").hide();
            $(".upgrade-price-standard-container").hide();
            $(".disable-upgrade-container").hide();
        } else if (selected_course === '2') {
            $(".upgrade-price-container").show();
            $(".upgrade-price-standard-container").hide();
            $(".disable-upgrade-container").show();
            $(".label-basic").text('');
        } else {
            $(".upgrade-price-container").show();
            $(".upgrade-price-standard-container").show();
            $(".disable-upgrade-container").show();
            $(".label-basic").text('For Basic');
        }
    });

    $("#editPackageModal").find('select[name=course_type]').change(function(){
        var selected_course = $(this).val();
        if (selected_course === '1') {
            $(".upgrade-price-container").hide();
            $(".upgrade-price-standard-container").hide();
            $(".disable-upgrade-container").hide();
        } else if (selected_course === '2') {
            $(".upgrade-price-container").show();
            $(".upgrade-price-standard-container").hide();
            $(".disable-upgrade-container").show();
            $(".label-basic").text('');
        } else {
            $(".upgrade-price-container").show();
            $(".upgrade-price-standard-container").show();
            $(".disable-upgrade-container").show();
            $(".label-basic").text('For Basic');
        }
    });

    $(".btn-add-package").click(function(){
        $(".upgrade-price-container").hide();
        $(".upgrade-price-standard-container").hide();
        $(".disable-upgrade-container").hide();
    });

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

  $(".includeCoachingBtn").click(function(){
      let action        = $(this).data('action');
      let include_coaching  = parseInt($(this).data('include'));
      let modal         = $("#includeCoachingModal");
      let form          = modal.find('form');

      modal.find('.modal-title').text(include_coaching ? 'Include Coaching Session' : 'Remove Coaching Session');
      modal.find('span').text(include_coaching ? 'include' : 'remove');
      form.attr('action', action);
      form.find('[name=has_coaching]').val(include_coaching);
      form.find('[type=submit]').addClass(include_coaching ? 'btn-primary' : 'btn-danger').text(include_coaching ? 'Include Session' : 'Remove Session');
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
      var months_12_price = $(this).data('months_12_price');

      var full_price_product = $(this).data('full_price_product');
      var months_3_product = $(this).data('months_3_product');
      var months_6_product = $(this).data('months_6_product');
      var months_12_product = $(this).data('months_12_product');

      var full_price_due_date = $(this).data('full_price_due_date');
      var months_3_due_date = $(this).data('months_3_due_date');
      var months_6_due_date = $(this).data('months_6_due_date');
      var months_12_due_date = $(this).data('months_12_due_date');

      var full_payment_sale_price = $(this).data('full_payment_sale_price');
      var full_payment_sale_price_from = $(this).data('full_payment_sale_price_from');
      var full_payment_sale_price_to = $(this).data('full_payment_sale_price_to');

      var months_3_sale_price = $(this).data('months_3_sale_price');
      var months_3_sale_price_from = $(this).data('months_3_sale_price_from');
      var months_3_sale_price_to = $(this).data('months_3_sale_price_to');

      var months_6_sale_price = $(this).data('months_6_sale_price');
      var months_6_sale_price_from = $(this).data('months_6_sale_price_from');
      var months_6_sale_price_to = $(this).data('months_6_sale_price_to');

      var months_12_sale_price = $(this).data('months_12_sale_price');
      var months_12_sale_price_from = $(this).data('months_12_sale_price_from');
      var months_12_sale_price_to = $(this).data('months_12_sale_price_to');

      var months_3_enable = $(this).data('months_3_enable');
      var months_6_enable = $(this).data('months_6_enable');
      var months_12_enable = $(this).data('months_12_enable');

      var full_payment_upgrade_price = $(this).data('full_payment_upgrade_price');
      var months_3_upgrade_price = $(this).data('months_3_upgrade_price');
      var months_6_upgrade_price = $(this).data('months_6_upgrade_price');
      var months_12_upgrade_price = $(this).data('months_12_upgrade_price');

      var full_payment_standard_upgrade_price = $(this).data('full_payment_standard_upgrade_price');
      var months_3_standard_upgrade_price = $(this).data('months_3_standard_upgrade_price');
      var months_6_standard_upgrade_price = $(this).data('months_6_standard_upgrade_price');
      var months_12_standard_upgrade_price = $(this).data('months_12_standard_upgrade_price');

      var disable_upgrade_price_date = $(this).data('disable-upgrade-price-date');
      var disable_upgrade_price = $(this).data('disable-upgrade-price');

      var has_student_discount = $(this).data('has_student_discount');
      var selected_course = $(this).data('selected-course');
      var course_type = $(this).data('course-type');

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
      $('#editPackageModal input[name=months_12_price]').val(months_12_price);

      $('#editPackageModal input[name=full_price_product]').val(full_price_product);
      $('#editPackageModal input[name=months_3_product]').val(months_3_product);
      $('#editPackageModal input[name=months_6_product]').val(months_6_product);
      $('#editPackageModal input[name=months_12_product]').val(months_12_product);

      $('#editPackageModal input[name=full_price_due_date]').val(full_price_due_date);
      $('#editPackageModal input[name=months_3_due_date]').val(months_3_due_date);
      $('#editPackageModal input[name=months_6_due_date]').val(months_6_due_date);
      $('#editPackageModal input[name=months_12_due_date]').val(months_12_due_date);
      $('#editPackageModal input[name=workshops]').val(workshops);

      $('#editPackageModal input[name=full_payment_sale_price]').val(full_payment_sale_price > 0 ? full_payment_sale_price : '');
      $('#editPackageModal input[name=full_payment_sale_price_from]').val(full_payment_sale_price_from);
      $('#editPackageModal input[name=full_payment_sale_price_to]').val(full_payment_sale_price_to);

      $('#editPackageModal input[name=months_3_sale_price]').val(months_3_sale_price > 0 ? months_3_sale_price : '');
      $('#editPackageModal input[name=months_3_sale_price_from]').val(months_3_sale_price_from);
      $('#editPackageModal input[name=months_3_sale_price_to]').val(months_3_sale_price_to);

      $('#editPackageModal input[name=months_6_sale_price]').val(months_6_sale_price > 0 ? months_6_sale_price : '');
      $('#editPackageModal input[name=months_6_sale_price_from]').val(months_6_sale_price_from);
      $('#editPackageModal input[name=months_6_sale_price_to]').val(months_6_sale_price_to);

      $('#editPackageModal input[name=months_12_sale_price]').val(months_12_sale_price > 0 ? months_12_sale_price : '');
      $('#editPackageModal input[name=months_12_sale_price_from]').val(months_12_sale_price_from);
      $('#editPackageModal input[name=months_12_sale_price_to]').val(months_12_sale_price_to);

      if (has_student_discount) {
          $("#editPackageModal input[name=has_student_discount]").bootstrapToggle('on');
      }

      if (months_3_enable) {
          $("#editPackageModal input[name=months_3_enable]").bootstrapToggle('on');
      }

      if (months_6_enable) {
          $("#editPackageModal input[name=months_6_enable]").bootstrapToggle('on');
      }

      if (months_12_enable) {
          $("#editPackageModal input[name=months_12_enable]").bootstrapToggle('on');
      }

      if (disable_upgrade_price) {
          $("#editPackageModal input[name=disable_upgrade_price]").bootstrapToggle('on');
      } else {
          $("#editPackageModal input[name=disable_upgrade_price]").bootstrapToggle('off');
      }

      $('#editPackageModal input[name=full_payment_upgrade_price]').val(full_payment_upgrade_price > 0 ? full_payment_upgrade_price : '');
      $('#editPackageModal input[name=months_3_upgrade_price]').val(months_3_upgrade_price > 0 ? months_3_upgrade_price : '');
      $('#editPackageModal input[name=months_6_upgrade_price]').val(months_6_upgrade_price > 0 ? months_6_upgrade_price : '');
      $('#editPackageModal input[name=months_12_upgrade_price]').val(months_12_upgrade_price > 0 ? months_12_upgrade_price : '');

      $('#editPackageModal input[name=full_payment_standard_upgrade_price]').val(full_payment_standard_upgrade_price > 0 ? full_payment_standard_upgrade_price : '');
      $('#editPackageModal input[name=months_3_standard_upgrade_price]').val(months_3_standard_upgrade_price > 0 ? months_3_standard_upgrade_price : '');
      $('#editPackageModal input[name=months_6_standard_upgrade_price]').val(months_6_standard_upgrade_price > 0 ? months_6_standard_upgrade_price : '');
      $('#editPackageModal input[name=months_12_standard_upgrade_price]').val(months_12_standard_upgrade_price > 0 ? months_12_standard_upgrade_price : '');

      $('#editPackageModal select[name=course_type]').val(course_type ? course_type : '');
      $('#editPackageModal input[name=disable_upgrade_price_date]').val(disable_upgrade_price_date);

      $(".upgrade-price-container").hide();
      $(".upgrade-price-standard-container").hide();
      $(".label-basic").empty();

      if (selected_course === 1) {
          $(".upgrade-price-container").hide();
          $(".upgrade-price-standard-container").hide();
          $("#editPackageModal input[name=disable_upgrade_price]").bootstrapToggle('off');
          $(".disable-upgrade-container").hide();
      }
      if (selected_course === 2) {
          $(".upgrade-price-container").show();
          $(".upgrade-price-standard-container").hide();
          $(".disable-upgrade-container").show();
          $(".label-basic").text('');
      }
      if (selected_course === 3) {
          $(".upgrade-price-container").show();
          $(".upgrade-price-standard-container").show();
          $(".disable-upgrade-container").show();
          $(".label-basic").text('For Basic');
      }

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
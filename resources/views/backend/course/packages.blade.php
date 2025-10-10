@extends('backend.layout')

@section('styles')
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('title')
<title>Packages &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
		{{--<div class="col-sm-4">
			@if ( $errors->any() )
            <div class="alert alert-danger bottom-margin">
                <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
                </ul>
            </div>
            @endif
        </div>--}}
		<div class="col-sm-12">
                <button type="button" class="btn btn-primary margin-bottom btn-add-package" data-toggle="modal" data-target="#addPackageModal">+ {{ trans('site.add-package') }}</button>
                  <button type="button" class="btn btn-success margin-bottom btn-add-reward" data-toggle="modal" data-target="#rewardPackageModal"
                  data-action="{{route('admin.course.package.store', ['course_id' => $course->id])}}"
                  data-title="Add Reward Package for {{$course->title}}">+ Add Reward Package</button>
		</div>
		@foreach($course->allPackages as $k => $package)
		<div class="col-sm-12 col-md-12">
			<div class="panel panel-default panel-package">
				<div class="panel-heading">
					<div class="pull-right">
                        @if ($package->is_reward)
                        <button type="button" class="btn btn-info btn-xs btn-edit-reward" data-toggle="modal" data-target="#rewardPackageModal"
                                data-action="{{route('admin.course.package.update', ['course_id' => $course->id, 'package' => $package->id])}}"
                                data-title="Edit Reward Package for {{$course->title}}"
                        data-variation="{{ $package->variation }}" data-id="{{ $package->id }}"
                                data-description="{{ $package->description }}" ><i class="fa fa-pencil"></i></button>
                        @else
						<button type="button" data-target="#editPackageModal" data-toggle="modal" class="btn btn-info btn-xs btn-edit-package" 
            data-action="{{route('admin.course.package.update', ['course_id' => $course->id, 'package' => $package->id])}}"
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
            data-full_payment_other_sale_price="{{ number_format($package->full_payment_other_sale_price, 0, 0, '') }}"
            data-full_payment_other_sale_price_from="{{ $package->full_payment_other_sale_price_from }}"
            data-full_payment_other_sale_price_to="{{ $package->full_payment_other_sale_price_to }}"
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
                                data-sale_link = "{{ 'https://forfatterskolen.no/course/'.$package->course_id
                                .'/checkout?sp='.encrypt($package->id) }}"
                        data-has_student_discount="{{ $package->has_student_discount }}"
                                data-is_show="{{ $package->is_show }}"
                                data-is_upgradeable="{{ $package->is_upgradeable }}"
                                data-is_pay_later_allowed="{{ $package->is_pay_later_allowed }}"
                                data-is_standard="{{ $package->is_standard }}"
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
                                data-disable-upgrade-price="{{ $package->disable_upgrade_price }}"
                                data-issue_date="{{ $package->issue_date }}"
                        data-validity_period="{{ $package->validity_period }}"><i class="fa fa-pencil"></i></button>
                        @endif

						<button type="button" data-target="#deletePackageModal" data-toggle="modal" class="btn btn-danger btn-xs btn-delete-package" data-action="{{route('admin.course.package.destroy', ['course_id' => $course->id, 'package' => $package->id])}}" data-variation="{{$package->variation}}" data-id="{{$package->id}}"><i class="fa fa-trash"></i></button>
					</div>

					<h4>
                    {{$package->variation}}
                    @if ($package->is_standard)
                        <span class="label label-primary" style="font-size: 10px">Standard</span>
                    @endif
                    @if ($package->is_reward)
                        <span class="label label-success" style="font-size: 10px">Reward</span>
                    @endif
                    </h4>
				</div>
				<div class="panel-body row">
          <div class="col-sm-6"> 
  					{!! nl2br($package->description) !!}
            <div><em>{{ trans('site.maximum-manuscripts') }}: {{$package->manuscripts_count}}</em></div>
            <div><em>{{ trans_choice('site.workshops', 2) }}: {{$package->workshops}}</em></div>
            <div><em>Package ID: <b>{{$package->id}}</b></em></div>
            <div><em>Pay Later Allowed: <b>{{ $package->is_pay_later_allowed ? 'Yes' : 'No' }}</b></em></div>
            <div><em>Standard Package: <b>{{ $package->is_standard ? 'Yes' : 'No' }}</b></em></div>
                                        <div class="package-price">
              <div>
                <strong>{{ trans('site.full-payment') }}</strong><br />
                <span>{{ trans('site.price') }}: {{FrontendHelpers::currencyFormat($package->full_payment_price)}}</span><br />
                <span>Fiken Product ID: {{$package->full_price_product}}</span><br />
                <span>{{ trans('site.due-date') }}: {{$package->full_price_due_date}} days</span>
              </div>
              <div>
                <strong>3 {{ trans('site.months') }}</strong><br />
                <span>{{ trans('site.price') }}: {{FrontendHelpers::currencyFormat($package->months_3_price)}}</span><br />
                <span>Fiken Product ID: {{$package->months_3_product}}</span><br />
                <span>{{ trans('site.due-date') }}: {{$package->months_3_due_date}} days</span>
              </div>
              <div>
                <strong>6 {{ trans('site.months') }}</strong><br />
                <span>{{ trans('site.price') }}: {{FrontendHelpers::currencyFormat($package->months_6_price)}}</span><br />
                <span>Fiken Product ID: {{$package->months_6_product}}</span><br />
                <span>{{ trans('site.due-date') }}: {{$package->months_6_due_date}} days</span>
              </div>
                      <div>
                        <strong>12 {{ trans('site.months') }}</strong><br />
                        <span>{{ trans('site.price') }}: {{FrontendHelpers::currencyFormat($package->months_12_price)}}</span><br />
                        <span>Fiken Product ID: {{$package->months_12_product}}</span><br />
                        <span>{{ trans('site.due-date') }}: {{$package->months_12_due_date}} days</span>
                      </div>
  					</div>
          </div>
          <div class="col-sm-6">
            <h4>
              <button class="btn btn-primary btn-xs pull-right addShopManuscriptBtn" data-package_id="{{ $package->id }}" data-toggle="modal" data-target="#addShopManuscriptModal" data-action="{{ route('admin.package_shop_manuscript.store', $package->id) }}" data-shop_manuscripts_id="{{ json_encode($package->shop_manuscripts()->pluck('shop_manuscript_id')->toArray()) }}"><i class="fa fa-plus"></i></button>
              {{ trans_choice('site.shop-manuscripts', 2) }}
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
              {{ trans_choice('site.include-courses', 2) }}
            </h4>

            @if (!$package->has_coaching)
              <div class="clearfix"></div>

              <h4 style="margin-top:5px">
                <button class="btn btn-primary btn-xs pull-right includeCoachingBtn" data-toggle="modal" data-target="#includeCoachingModal" data-action="{{ route('admin.course.package.include-coaching', ['course_id' => $course->id, 'package_id' => $package->id]) }}"><i class="fa fa-plus"></i></button>
                {{ trans('site.include-coaching-session') }}
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
                        <button class="btn btn-danger btn-xs pull-right removeCoachingBtn" data-toggle="modal" data-target="#removeCoachingModal" data-action="{{ route('admin.course.package.include-coaching', ['course_id' => $course->id, 'package_id' => $package->id]) }}"
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
        <h4 class="modal-title">{{ trans('site.delete-shop-manuscript') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          {{ trans('site.delete-shop-manuscript-question') }}
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
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
        <h4 class="modal-title">{{ trans('site.add-shop-manuscript') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" onsubmit="disableSubmit(this)">
          {{csrf_field()}}
          <label>{{ trans_choice('site.shop-manuscripts', 1) }}</label>
          <select class="form-control" required="" name="shop_manuscript_id">
            <option value="" selected disabled>- Select shop manuscript -</option> 
            @foreach(App\ShopManuscript::orderBy('created_at', 'desc')->get() as $shopManuscript)
            <option value="{{ $shopManuscript->id }}">{{ $shopManuscript->title }}</option> 
            @endforeach
          </select>
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">{{ trans('site.add-shop-manuscript') }}</button>
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
        <h4 class="modal-title">{{ trans('site.add-package-to') }} {{$course->title}}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('admin.course.package.store', ['course_id' => $course->id])}}" 
          onsubmit="disableSubmit(this)">
      		{{csrf_field()}}
          <input type="hidden" name="variation_id">
          <div class="row">
            <div class="col-sm-5">
              <div class="form-group">
                <label>{{ trans('site.variation') }}</label>
                <input type="text" name="variation" placeholder="{{ trans('site.variation') }}" required class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans('site.description') }}</label>
                <textarea class="form-control" name="description" placeholder="{{ trans('site.description') }}" required rows="5"></textarea>
              </div>
              <div class="form-group">
                <label>{{ ucwords(trans('site.maximum-manuscripts')) }}</label>
                <input type="number" name="manuscripts_count" placeholder="{{ ucwords(trans('site.maximum-manuscripts')) }}" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans_choice('site.workshops', 2) }}</label>
                <input type="number" name="workshops" placeholder="{{ trans_choice('site.workshops', 2) }}" min="0" class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans('site.course-type') }}</label>
                <select name="course_type"class="form-control" required>
                  <option value="" selected disabled>Select Course Type</option>
                  @foreach(\App\Http\AdminHelpers::courseType() as $courseType)
                    <option value="{{ $courseType['id'] }}"> {{ $courseType['option'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group disable-upgrade-container">
                <label>{{ trans('site.disable-upgrade-price-on-date') }}</label>
                <input type="date" name="disable_upgrade_price_date" placeholder="{{ trans('site.disable-upgrade-price-on-date') }}" class="form-control">
              </div>
              <div class="form-group disable-upgrade-container">
                <label>{{ trans('site.disable-upgrade-price') }}</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="disable-upgrade-price-toggle" data-off="No"
                       name="disable_upgrade_price" data-width="84">
              </div>
            <div class="form-group">
                <label>Validity Period (in month)</label>
                <input type="number" class="form-control" name="validity_period" step="1">
            </div>
              <div class="form-group">
                <label>{{ trans('site.student-discount') }}</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Enable"
                       class="for-sale-toggle" data-off="Disable"
                       name="has_student_discount" data-width="84" checked>
              </div>
              <div class="form-group">
                <label>Set as Standard Package</label> <br>
                <input type="hidden" name="is_standard" value="0">
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="for-sale-toggle" data-off="No"
                       name="is_standard" value="1" data-width="84">
              </div>
              <div class="form-group">
                <label>Show Package</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="for-sale-toggle" data-off="No"
                       name="is_show" data-width="84" checked>
              </div>
              <div class="form-group">
                <label>Allow Upgrade</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                       name="is_upgradeable" data-width="84" checked>
              </div>
              <div class="form-group">
                <label>Allow Pay Later</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                       name="is_pay_later_allowed" data-width="84">
              </div>
            </div>

            <div class="col-sm-7">
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#fullprice">{{ trans('site.full-payment') }}</a></li>
                {{-- <li><a data-toggle="tab" href="#3months">3 {{ trans('site.months') }}</a></li>
                <li><a data-toggle="tab" href="#6months">6 {{ trans('site.months') }}</a></li>
                <li><a data-toggle="tab" href="#12months">12 {{ trans('site.months') }}</a></li> --}}
              </ul>
              <div class="tab-content">
                <div id="fullprice" class="tab-pane fade in active">
                  <h4>{{ trans('site.full-payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="full_payment_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
                  </div>
                    <div class="form-group">
                        <label>{{ trans('site.sale-price') }}</label>
                        <input type="number" step="0.01" name="full_payment_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>{{ trans('site.sale-price-from') }}</label>
                            <input type="date" name="full_payment_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>{{ trans('site.sale-price-to') }}</label>
                            <input type="date" name="full_payment_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                        </div>
                    </div>
                  <div class="form-group">
                      <label>Other Sale Price</label>
                      <input type="number" step="0.01" name="full_payment_other_sale_price" 
                      placeholder="Other Sale Price" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <label>Sale Price From</label>
                          <input type="date" name="full_payment_other_sale_price_from" 
                          placeholder="Sale Price From" class="form-control">
                      </div>
                      <div class="form-group col-md-6">
                          <label>Sale Price To</label>
                          <input type="date" name="full_payment_other_sale_price_to" 
                          placeholder="Sale Price To" class="form-control">
                      </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="full_price_due_date" placeholder="{{ trans('site.due-date') }}" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.payment-from') }}</label>
                    <input type="date" name="issue_date" placeholder="{{ trans('site.payment-from') }}" class="form-control">
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
                  <h4>3 {{ trans('site.months') }} {{ trans('site.payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="months_3_price" placeholder="{{ trans('site.price') }}" min="0" class="form-control">
                  </div>
                    <div class="form-group">
                        <label>{{ trans('site.sale-price') }}</label>
                        <input type="number" step="0.01" name="months_3_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>{{ trans('site.sale-price-from') }}</label>
                            <input type="date" name="months_3_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>{{ trans('site.sale-price-to') }}</label>
                            <input type="date" name="months_3_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                        </div>
                    </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_3_product" placeholder="Fiken Product ID" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="months_3_due_date" placeholder="{{ trans('site.due-date') }}" min="0" class="form-control">
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
                    <label>{{ trans('site.display-plan') }}</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_3_enable" data-width="84">
                  </div>
                </div>
                <div id="6months" class="tab-pane fade">
                  <h4>6 {{ trans('site.months') }} {{ trans('site.payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="months_6_price" placeholder="{{ trans('site.price') }}" min="0" class="form-control">
                  </div>
                    <div class="form-group">
                        <label>{{ trans('site.sale-price') }}</label>
                        <input type="number" step="0.01" name="months_6_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>{{ trans('site.sale-price-from') }}</label>
                            <input type="date" name="months_6_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>{{ trans('site.sale-price-to') }}</label>
                            <input type="date" name="months_6_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                        </div>
                    </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_6_product" placeholder="Fiken Product ID" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="months_6_due_date" placeholder="{{ trans('site.due-date') }}" min="0" class="form-control">
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
                    <label>{{ trans('site.display-plan') }}</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_6_enable" data-width="84">
                  </div>
                </div>
                <div id="12months" class="tab-pane fade">
                  <h4>12 {{ trans('site.months') }} {{ trans('site.payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="months_12_price" placeholder="{{ trans('site.price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.sale-price') }}</label>
                    <input type="number" step="0.01" name="months_12_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-from') }}</label>
                      <input type="date" name="months_12_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-to') }}</label>
                      <input type="date" name="months_12_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_12_product" placeholder="Fiken Product ID" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="months_12_due_date" placeholder="{{ trans('site.due-date') }}" min="0" class="form-control">
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
                    <label>{{ trans('site.display-plan') }}</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_12_enable" data-width="84">
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

<!-- add reward package modal -->
<div id="rewardPackageModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>

            <div class="modal-body">
                <form method="POST" action="" onsubmit="disableSubmit(this)">
                    {{csrf_field()}}
                    <input type="hidden" name="variation_id">
                    <input type="hidden" name="is_reward" value="1">
                    <input type="hidden" name="is_standard" value="0">

                    <?php
                        $required_fields = [
                            'manuscripts_count'         => 0,
                            'full_payment_price'        => 0,
                            'months_3_price'            => 0,
                            'months_6_price'            => 0,
                            'months_12_price'           => 0,
                            'full_price_product'        => 0,
                            'months_3_product'          => 0,
                            'months_6_product'          => 0,
                            'months_12_product'         => 0,
                            'full_price_due_date'       => 1,
                            'months_3_due_date'         => 1,
                            'months_6_due_date'         => 1,
                            'months_12_due_date'        => 1,
                            'course_type'               => 1];

                        foreach($required_fields as $field => $value) {
                            echo '<input type="hidden" name="'.$field.'" value="'.$value.'">';
                        }
                    ?>

                        <div class="form-group">
                            <label>{{ trans('site.variation') }}</label>
                            <input type="text" name="variation" placeholder="{{ trans('site.variation') }}" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea class="form-control" name="description" placeholder="{{ trans('site.description') }}" required rows="5"></textarea>
                        </div>

                    <button type="submit" class="btn btn-primary pull-right"></button>
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
        <h4 class="modal-title">{{ trans('site.edit-package') }} <span></span></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="" onsubmit="disableSubmit(this)">
      		{{csrf_field()}}
      		{{ method_field('PUT') }}
      		<input type="hidden" name="variation_id">
          <div class="row">
            <div class="col-sm-5">
          		<div class="form-group">
                <label>{{ trans('site.variation') }}</label>
          			<input type="text" name="variation" placeholder="{{ trans('site.variation') }}" required class="form-control">
          		</div>
          		<div class="form-group">
                <label>{{ trans('site.description') }}</label>
          			<textarea class="form-control" name="description" placeholder="{{ trans('site.description') }}" required rows="5"></textarea>
          		</div>
              <div class="form-group">
                <label>{{ ucwords(trans('site.maximum-manuscripts')) }}</label>
                <input type="number" name="manuscripts_count" placeholder="{{ ucwords(trans('site.maximum-manuscripts')) }}" min="0" required class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans_choice('site.workshops',2) }}</label>
                <input type="number" name="workshops" placeholder="{{ trans_choice('site.wokshops',2) }}" min="0" class="form-control">
              </div>
              <div class="form-group">
                <label>{{ trans('site.course-type') }}</label>
                <select name="course_type"class="form-control" required>
                  <option value="" selected disabled>Select Course Type</option>
                  @foreach(\App\Http\AdminHelpers::courseType() as $courseType)
                    <option value="{{ $courseType['id'] }}"> {{ $courseType['option'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group disable-upgrade-container">
                <label>{{ trans('site.disable-upgrade-price-on-date') }}</label>
                <input type="date" name="disable_upgrade_price_date" placeholder="{{ trans('site.disable-upgrade-price-on-date') }}" class="form-control">
              </div>
              <div class="form-group disable-upgrade-container">
                <label>{{ trans('site.disable-upgrade-price') }}</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="disable-upgrade-price-toggle" data-off="No"
                       name="disable_upgrade_price" data-width="84">
              </div>
                <div class="form-group">
                    <label>Validity Period (in month)</label>
                    <input type="number" class="form-control" name="validity_period" step="1">
                </div>
              <div class="form-group">
                <label>{{ trans('site.student-discount') }}</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Enable"
                       class="for-sale-toggle" data-off="Disable"
                       name="has_student_discount" data-width="84">
              </div>

              <div class="form-group">
                <label>Set as Standard Package</label> <br>
                <input type="hidden" name="is_standard" value="0">
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="for-sale-toggle" data-off="No"
                       name="is_standard" value="1" data-width="84">
              </div>

              <div class="form-group">
                <label>Show Package</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes"
                       class="for-sale-toggle" data-off="No"
                       name="is_show" data-width="84">
              </div>

              <div class="form-group">
                <label>Allow Upgrade</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                       name="is_upgradeable" data-width="84">
              </div>

              <div class="form-group">
                <label>Allow Pay Later</label> <br>
                <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No"
                       name="is_pay_later_allowed" data-width="84">
              </div>

              <div class="form-group sale-link-container">
                <label>Sale Link</label>
                <input type="text" class="form-control" disabled style="width: 90%; display: inline;">
                <input type="text" name="hidden_val" style="position: absolute; left: -10000px;">

                <button type="button" class="btn btn-success btn-xs copyToClipboard">
                  <i class="fa fa-clipboard"></i>
                </button>
              </div>

            </div>

            <div class="col-sm-7">
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#fullprice_edit">{{ trans('site.full-payment') }}</a></li>
                {{-- <li><a data-toggle="tab" href="#3months_edit">3 {{ trans('site.months') }}</a></li>
                <li><a data-toggle="tab" href="#6months_edit">6 {{ trans('site.months') }}</a></li>
                <li><a data-toggle="tab" href="#12months_edit">12 {{ trans('site.months') }}</a></li> --}}
              </ul>
              <div class="tab-content">
                <div id="fullprice_edit" class="tab-pane fade in active">
                  <h4>{{ trans('site.full-payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="full_payment_price" placeholder="{{ trans('site.price') }}" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.sale-price') }}</label>
                    <input type="number" step="0.01" name="full_payment_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-from') }}</label>
                      <input type="date" name="full_payment_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-to') }}</label>
                      <input type="date" name="full_payment_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                      <label>Other Sale Price</label>
                      <input type="number" step="0.01" name="full_payment_other_sale_price" 
                      placeholder="Other Sale Price" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <label>Sale Price From</label>
                          <input type="date" name="full_payment_other_sale_price_from" 
                          placeholder="Sale Price From" class="form-control">
                      </div>
                      <div class="form-group col-md-6">
                          <label>Sale Price To</label>
                          <input type="date" name="full_payment_other_sale_price_to" 
                          placeholder="Sale Price To" class="form-control">
                      </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="full_price_product" placeholder="Fiken Product ID" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="full_price_due_date" placeholder="{{ trans('site.due-date') }}" min="0" required class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.payment-from') }}</label>
                    <input type="date" name="issue_date" placeholder="{{ trans('site.payment-from') }}" class="form-control">
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
                  <h4>3 {{ trans('site.months') }} {{ trans('site.payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="months_3_price" placeholder="{{ trans('site.price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.sale-price') }}</label>
                    <input type="number" step="0.01" name="months_3_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-from') }}</label>
                      <input type="date" name="months_3_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-to') }}</label>
                      <input type="date" name="months_3_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_3_product" placeholder="Fiken Product ID" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="months_3_due_date" placeholder="{{ trans('site.due-date') }}" min="0" class="form-control">
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
                    <label>{{ trans('site.display-plan') }}</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_3_enable" data-width="84">
                  </div>
                </div>
                <div id="6months_edit" class="tab-pane fade">
                  <h4>6 {{ trans('site.months') }} {{ trans('site.payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="months_6_price" placeholder="{{ trans('site.price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.sale-price') }}</label>
                    <input type="number" step="0.01" name="months_6_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-from') }}</label>
                      <input type="date" name="months_6_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-to') }}</label>
                      <input type="date" name="months_6_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_6_product" placeholder="Fiken Product ID" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="months_6_due_date" placeholder="{{ trans('site.due-date') }}" min="0" class="form-control">
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
                    <label>{{ trans('site.display-plan') }}</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                           class="for-sale-toggle" data-off="No"
                           name="months_6_enable" data-width="84">
                  </div>
                </div>
                <div id="12months_edit" class="tab-pane fade">
                  <h4>12 {{ trans('site.months') }} {{ trans('site.payment-price') }}</h4>
                  <div class="form-group">
                    <label>{{ trans('site.price') }}</label>
                    <input type="number" step="0.01" name="months_12_price" placeholder="{{ trans('site.price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.sale-price') }}</label>
                    <input type="number" step="0.01" name="months_12_sale_price" placeholder="{{ trans('site.sale-price') }}" min="0" class="form-control">
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-from') }}</label>
                      <input type="date" name="months_12_sale_price_from" placeholder="{{ trans('site.sale-price-from') }}" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                      <label>{{ trans('site.sale-price-to') }}</label>
                      <input type="date" name="months_12_sale_price_to" placeholder="{{ trans('site.sale-price-to') }}" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Fiken Product ID</label>
                    <input type="text" name="months_12_product" placeholder="Fiken Product ID" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>{{ trans('site.due-date-in-days') }}</label>
                    <input type="number" name="months_12_due_date" placeholder="{{ trans('site.due-date') }}" min="0" class="form-control">
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
                    <label>{{ trans('site.display-plan') }}</label> <br>
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
        <h4 class="modal-title">{{ trans('site.delete-package') }} <span></span></h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="" onsubmit="disableSubmit(this)">
      		{{csrf_field()}}
      		{{ method_field('DELETE') }}
      		<input type="hidden" name="variation_id">
      		<p>
              {!! trans('site.delete-package-question') !!}
      		</p>
      		<button type="submit" class="btn btn-danger btn-block">{{ trans('site.delete-package') }}</button>
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
        <h4 class="modal-title">{{ ucfirst(strtolower(trans_choice('site.include-courses', 1))) }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" onsubmit="disableSubmit(this)">
          {{ csrf_field() }}
          <div class="form-group">
            <label>{{ trans_choice('site.courses', 1) }}</label>
            <select class="form-control" required id="related_course_select">
              <option value="" selected disabled>- Select course -</option> 
              @foreach(App\Course::where('id', '<>', $course->id)->orderBy('created_at', 'desc')->get() as $course)
              <option value="{{ $course->id }}" data-packages="{{ json_encode($course->packages()->pluck('variation', 'id')) }}">{{ $course->title }}</option> 
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>{{ trans_choice('site.packages', 1) }}</label>
            <select class="form-control" required name="include_package_id" id="related_package_select">
              <option value="" selected disabled>- Select package -</option> 
            </select>
          </div>
          <input type="hidden" name="package_id">
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">{{ ucfirst(strtolower(trans_choice('site.include-courses', 1))) }}</button>
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
        <h4 class="modal-title">{{ trans('site.delete-included-course') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          {{ trans('site.delete-included-course-question') }}
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
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
        <h4 class="modal-title">{{ trans('site.include-coaching-session') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="" onsubmit="disableSubmit(this)">
          {{csrf_field()}}
          <div class="form-group">
            <label>{{ trans('site.coaching-length') }}</label>
            <select name="has_coaching" class="form-control" required>
              <option value="" disabled selected> -- Select --</option>
              <option value="2">30min</option>
              <option value="1">1hr</option>
            </select>
          </div>
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-primary">{{ trans('site.include-session') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<div id="removeCoachingModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.remove-coaching-session') }}</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          <p>
            {{ trans('site.remove-coaching-session-question') }}
          </p>
          <input type="hidden" name="has_coaching">
          <div class="text-right margin-top">
            <button type="submit" class="btn btn-danger">{{ trans('site.remove-session') }}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@stop


@section('scripts')
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>

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
        $("#addPackageModal input[type=checkbox][name=is_standard]").bootstrapToggle('off');
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
      let modal         = $("#includeCoachingModal");
      let form          = modal.find('form');

      form.attr('action', action);
  });

    $(".removeCoachingBtn").click(function(){
        let action        = $(this).data('action');
        let include_coaching  = parseInt($(this).data('include'));
        let modal         = $("#removeCoachingModal");
        let form          = modal.find('form');

        form.attr('action', action);
        form.find('[name=has_coaching]').val(include_coaching);
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

      var full_payment_other_sale_price = $(this).data('full_payment_other_sale_price');
      var full_payment_other_sale_price_from = $(this).data('full_payment_other_sale_price_from');
      var full_payment_other_sale_price_to = $(this).data('full_payment_other_sale_price_to');

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
      let is_show = $(this).data('is_show');
      let is_upgradeable = $(this).data("is_upgradeable");
      let is_pay_later_allowed = $(this).data("is_pay_later_allowed");
      let is_standard = parseInt($(this).data('is_standard'), 10) === 1;

      let issue_date = $(this).data('issue_date');
      let validity_period = $(this).data('validity_period');

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

      $('#editPackageModal input[name=full_payment_other_sale_price]').val(full_payment_other_sale_price > 0 ? full_payment_other_sale_price : '');
      $('#editPackageModal input[name=full_payment_other_sale_price_from]').val(full_payment_other_sale_price_from);
      $('#editPackageModal input[name=full_payment_other_sale_price_to]').val(full_payment_other_sale_price_to);

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

      $(".sale-link-container").removeClass('hide');
      $(".sale-link-container input").val($(this).data('sale_link'));
      if (is_show) {
          $("#editPackageModal input[name=is_show]").bootstrapToggle('on');
          $(".sale-link-container").addClass('hide');
      }

      if (is_upgradeable) {
          $("#editPackageModal").find("input[name=is_upgradeable]").bootstrapToggle('on');
      }

      if (is_pay_later_allowed) {
          $("#editPackageModal").find("input[name=is_pay_later_allowed]").bootstrapToggle('on');
      }

      if (is_standard) {
          $("#editPackageModal input[type=checkbox][name=is_standard]").bootstrapToggle('on');
      } else {
          $("#editPackageModal input[type=checkbox][name=is_standard]").bootstrapToggle('off');
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

      $('#editPackageModal input[name=issue_date]').val(issue_date);
      $('#editPackageModal input[name=validity_period]').val(validity_period);

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

  let reward_modal = $("#rewardPackageModal");
  $(".btn-add-reward").click(function(){
      let action = $(this).data('action');
      let title = $(this).data('title');
      reward_modal.find('.modal-title').empty().text(title);
      reward_modal.find('form').attr('action', action);
      reward_modal.find('[name=_method]').remove();
      reward_modal.find('[type=submit]').text('Create Package');
  });

  $(".btn-edit-reward").click(function(){
      let action = $(this).data('action');
      let title = $(this).data('title');
      let id = $(this).data('id');
      let variation = $(this).data('variation');
      let description = $(this).data('description');
      reward_modal.find('.modal-title').empty().text(title);
      reward_modal.find('form').attr('action', action);
      reward_modal.find('form').prepend('<input type="hidden" name="_method" value="PUT">');
      reward_modal.find('[type=submit]').text('Update Package');
      reward_modal.find('[name=variation_id]').val(id);
      reward_modal.find('[name=variation]').val(variation);
      reward_modal.find('[name=description]').text(description);
  });

    // not working on hidden fields
    $(".copyToClipboard").click(function() {
        let copyText = $(this).closest('.sale-link-container').find('[name=hidden_val]');
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");

        toastr.success('Copied to clipboard.', "Success");
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }
    });
});
</script>
@stop
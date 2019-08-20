@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    {{ trans('site.learner.upgrades-text') }} {{$courseTaken->package->course->title}}
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <form action="{{ route('learner.upgrade-course', $courseTaken->id) }}" class="form-theme"
                  method="POST" onsubmit="disableSubmitOrigText(this)">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="font-barlow-regular mb-4">
                            @yield('heading')
                        </h1>
                    </div> <!-- end col-sm-12 -->

                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body p-5">
                                <div class="row">
                                    <div class="col-md-5">
                                        <h3 class="font-barlow-medium">{{ trans('site.front.course-text') }}</h3>
                                        <p>
                                            <b>{{$courseTaken->package->course->title}}</b>
                                        </p>
                                    </div> <!-- end col-md-5 -->
                                    <div class="col-md-7">
                                        <h3 class="font-barlow-medium">{{ trans('site.learner.current-package-text') }}</h3>
                                        <p>
                                            <b>{{$courseTaken->package->variation}}</b>
                                        </p>
                                        <div>
                                            {!! nl2br($courseTaken->package->description) !!}
                                        </div>
                                    </div> <!-- end col-md-7 -->
                                </div> <!-- end row -->
                            </div> <!-- end panel-body -->
                        </div> <!-- end panel -->

                        <div class="panel panel-default">
                            <div class="panel-body p-5">
                                <h3 class="font-barlow-medium">{{ trans('site.learner.upgrade-to-text') }}:</h3>
                                <p>
                                    <b>{{$currentPackage->variation}}</b>
                                </p>
                                <div>
                                    {!! nl2br($currentPackage->description) !!}
                                </div>
                            </div> <!-- end panel-body -->
                        </div> <!-- end panel -->
                    </div> <!-- end col-md-8 -->

                    <div class="col-md-4">
                        <div class="panel panel-default p-5">
                            <div class="panel-heading-underlined">{{ trans('site.front.form.course-package') }}</div>
                            <div class="panel-body px-0 pb-0">
                                <?php
                                $hasPaidCourse = false;
                                /*$packages = \App\Package::where('course_id', $courseTaken->package->course->id)
                                    ->where('id', '>', $courseTaken->package->id)
                                    ->get();*/
                                $packages = \App\Package::where('id', $package_id)
                                    ->get(); // this is the updated one the original is the one on the top
                                $currentCourseType = $courseTaken->package->course_type; // this is the current package that the learner have
                                foreach( Auth::user()->coursesTaken as $courseTaken ) :
                                    if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                                        //$hasPaidCourse = true;
                                        break;
                                    endif;
                                endforeach;
                                ?>
                                @foreach($packages as $k => $package)
                                    <?php
                                    //$currentCourseType = $package->course_type;
                                    $full_payment_price = $package->full_payment_upgrade_price;
                                    $months_3_price = $package->months_3_upgrade_price;
                                    $months_6_price = $package->months_6_upgrade_price;
                                    $months_12_price = $package->months_12_upgrade_price;

                                    if ($package->course_type == 3 && $currentCourseType == 2) {
                                        $full_payment_price = $package->full_payment_standard_upgrade_price;
                                        $months_3_price = $package->months_3_standard_upgrade_price;
                                        $months_6_price = $package->months_6_standard_upgrade_price;
                                        $months_12_price = $package->months_12_standard_upgrade_price;
                                    }

                                    $full_payment_sale_price = $package->full_payment_sale_price;
                                    $months_3_sale_price = $package->months_3_sale_price;
                                    $months_6_sale_price = $package->months_6_sale_price;
                                    $months_12_sale_price = $package->months_12_sale_price;

                                    $today 			= \Carbon\Carbon::today()->format('Y-m-d');
                                    $fromFull 		= \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
                                    $toFull 			= \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
                                    $isBetweenFull 	= (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

                                    $fromMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
                                    $toMonths3 			= \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
                                    $isBetweenMonths3 	= (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

                                    $fromMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
                                    $toMonths6 			= \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
                                    $isBetweenMonths6 	= (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

                                    $fromMonths12 		= \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
                                    $toMonths12 			= \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
                                    $isBetweenMonths12 	= (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

                                    ?>

                                    <div class="package-option custom-radio" id="package-option-{{ $package->id }}">
                                        <input type="radio" name="package_id"
                                               value="{{$package->id}}"
                                               data-full_payment_price="{{ FrontendHelpers::currencyFormat($full_payment_price) }}"
                                               data-months_3_price="{{ FrontendHelpers::currencyFormat($months_3_price) }}"
                                               data-months_6_price="{{ FrontendHelpers::currencyFormat($months_6_price) }}"
                                               data-months_12_price="{{ FrontendHelpers::currencyFormat($months_12_price) }}"
                                               data-full_payment_price_number="{{ $full_payment_price }}"
                                               data-months_3_price_number="{{ $months_3_price }}"
                                               data-months_6_price_number="{{ $months_6_price }}"
                                               data-months_12_price_number="{{ $months_12_price }}"
                                               data-variation="{{ $package->variation }}"
                                               data-description="{!! nl2br($package->description) !!}"
                                               id="{{$package->variation}}"

                                               required>
                                        <label for="{{$package->variation}}">{{$package->variation}} </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="panel-heading-underlined">{{ trans('site.front.form.payment-method') }}</div>
                            <div class="panel-body px-0 pb-0">
                                <select class="form-control" name="payment_mode_id" required data-size="15">
                                    @foreach(\App\Http\FrontendHelpers::paymentModes(true) as $paymentMode)
                                        <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                    @endforeach
                                </select>
                                <em>
                                    <small class="font-barlow-regular">
                                        {{ trans('site.learner.renew-course.payment-note') }}
                                    </small>
                                </em>
                            </div>

                            <div class="panel-heading-underlined">{{ trans('site.front.form.payment-plan') }}</div>
                            <div class="panel-body px-0 pb-0">
                                <div class="row">
                                    <div class="col-sm-12" id="paymentPlanContainer">

                                    </div>
                                    <div class="col-sm-12" style="margin-top: 8px" id="splitInvoiceContainer">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <span class="split-faktura">{{ trans('site.front.form.monthly-payment') }}?*</span>
                                            </div>
                                            <div class="payment-option custom-radio col-sm-6">
                                                <input type="radio" name="split_invoice" value="1" disabled required
                                                       id="yes_option">
                                                <label for="yes_option">{{ trans('site.front.yes') }}</label>
                                            </div>
                                            <div class="payment-option custom-radio col-sm-6">
                                                <input type="radio" name="split_invoice" value="0" disabled required
                                                       id="no_option">
                                                <label for="no_option">{{ trans('site.front.no') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <span class="col-sm-12 small-note">
										*{{ trans('site.front.form.invoice-note') }}
									</span>
                                </div>

                                <h3 class="font-barlow-regular font-weight-normal my-4">{{ trans('site.front.total') }}:

                                    <?php $standard_price = $courseTaken->package->course->packages->where('variation', 'Standard Kurs')->first(); ?>

                                    @if( $standard_price )
                                        <span class="theme-text font-barlow-regular">
                                            @if( $hasPaidCourse && $package->has_student_discount)
                                            {{--check if course is Webinar-pakke and apply 500 only--}}
                                            @if($courseTaken->package->course->type == "Single")
                                                {{ FrontendHelpers::currencyFormat($standard_price->full_payment_price - 500) }}
                                            @endif

                                            @if($courseTaken->package->course->type == "Group")
                                                {{ FrontendHelpers::currencyFormat($standard_price->full_payment_price - 1000) }}
                                            @endif
                                        @else
                                            {{ FrontendHelpers::currencyFormat($standard_price->full_payment_upgrade_price) }}
                                        @endif
                                        </span>
                                    @else
                                        <span class="theme-text font-barlow-regular">
                                            @if( $hasPaidCourse && $package->has_student_discount)
                                            @if($courseTaken->package->course->type == "Single")
                                                {{ FrontendHelpers::currencyFormat($courseTaken->package->course->packages[0]->full_payment_price - 500) }}
                                            @endif

                                            @if($courseTaken->package->course->type == "Group")
                                                {{ FrontendHelpers::currencyFormat($courseTaken->package->course->packages[0]->full_payment_price - 1000) }}
                                            @endif
                                        @else
                                            {{ FrontendHelpers::currencyFormat($courseTaken->package->course->packages[0]->full_payment_upgrade_price) }}
                                        @endif
                                        </span>
                                    @endif
                                </h3>

                                <button type="submit" class="btn site-btn-global-w-arrow mt-2 d-block">{{ trans('site.front.buy') }}</button>
                            </div>
                        </div> <!-- end panel-default -->
                    </div> <!-- end col-md-4 -->

                </div> <!-- end row -->

            </form> <!-- end form -->
        </div> <!-- end container -->
    </div> <!-- end learner-container -->
@stop

@section('scripts')
    <script>
        $(document).ready(function(){

            let course_id = '<?php echo $courseTaken->package->course->id?>';
            let current_package_id = '<?php echo $currentPackage->id?>';
            let count_package_change = 0; // used to determine the onload

            let translations = {
                upgrade_to : "{{ trans('site.learner.upgrade-to-text') }}"
            };

            setTimeout(function(){
                $("#package-option-"+current_package_id+"").find('input[name=package_id]').attr('checked', true).trigger('change');
                $('input:radio[name=payment_plan_id]:first').attr('checked', true).trigger('change');
            }, 100);

            $(".package-option").change(function(){
                let changePackage = $(this).find('input[name=package_id]');
                let selected_package_content = $("#selected-package-content"),
               newSelectedContent = '';
                selected_package_content.empty();

                newSelectedContent += '<h4>'+translations.upgrade_to+':</h4>';
                newSelectedContent += '<p class="margin-top"> <b>'+changePackage.data('variation')+'</b> </p>';
                newSelectedContent += '<div>'+changePackage.data('description')+'</div>';

                selected_package_content.append(newSelectedContent);
            });

            $('input[name=package_id]').on('change', function(){
                let checkout_total = $('.checkout-total');
                $('input:radio[name=split_invoice]').prop('disabled', true);
                $('input:radio[name=split_invoice]').prop('checked', false);
                generatePackagePaymentOption($(this).val());
                count_package_change++;

                let new_total = 0;

                if ($('input[name=payment_plan_id]:checked').length > 0) {
                    let plan = $('input[name=payment_plan_id]:checked').data('plan');
                    if( plan == 'Hele beløpet' ) {
                        let price = $(this).data('full_payment_price');
                        let price_value = $(this).attr('data-full_payment_sale_price_number')
                            ? $(this).data('full_payment_sale_price_number')
                            : $(this).data('full_payment_price_number');
                        new_total = price_value;

                        if ($("input[name=discount_value]").val()) {
                            let discount_value = $("input[name=discount_value]").val();
                            new_total = price_value - discount_value;
                        }
                    } else if( plan == '3 måneder' ) {
                        let price = $(this).data('months_3_price');
                        let price_value = $(this).attr('data-months_3_sale_price_number')
                            ? $(this).data('months_3_sale_price_number')
                            : $(this).data('months_3_price_number');
                        new_total = price_value;

                        if ($("input[name=discount_value]").val()) {
                            let discount_value = $("input[name=discount_value]").val();
                            new_total = price_value - discount_value;
                        }
                    } else if( plan == '6 måneder' ) {
                        let price = $(this).data('months_6_price');
                        let price_value = $(this).attr('data-months_6_sale_price_number')
                            ? $(this).data('months_6_sale_price_number')
                            : $(this).data('months_6_price_number');
                        new_total = price_value;

                        if ($("input[name=discount_value]").val()) {
                            let discount_value = $("input[name=discount_value]").val();
                            new_total = price_value - discount_value;
                        }
                    }
                } else {
                    new_total = $(this).data('full_payment_price_number');
                    if ($("input[name=discount_value]").val()) {
                        let discount_value = $("input[name=discount_value]").val();
                        let price_value = $(this).data('full_payment_price_number');
                        new_total = price_value - discount_value;
                    }
                }

                $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                    let checkout_total = $('.checkout-total');
                    checkout_total.find('span').text(data);
                });
            });



            $('select[name=payment_mode_id]').on('change', function(){
                let mode = $('option:selected', this).data('mode');
                if( mode === "Paypal" || mode === "Vipps") {
                    $('input:radio[name=payment_plan_id]').parent().addClass('disabled');
                    $('input:radio[name=payment_plan_id]').prop('disabled', true);
                    $('input:radio[name=payment_plan_id]').prop('checked', false);
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('checked', true);
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').parent().removeClass('disabled');
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('disabled', false);
                    let package = $('#package_select option:selected');
                    //$('#package_select option:selected').data('full_payment_price');
                    let price = $('input:radio[name=package_id]:checked').data('full_payment_price');
                    $('.checkout-total span').text(price);
                    $('input:radio[name=split_invoice]').prop('disabled', true);
                } else {
                    $('input:radio[name=payment_plan_id]').parent().removeClass('disabled');
                    $('input:radio[name=payment_plan_id]').prop('disabled', false);
                }
            });

            //setup before functions
            let typingTimer;                //timer identifier
            let doneTypingInterval = 1000;  //time in ms, 5 second for example
            let $coupon = $('input[name=coupon]');

            //on keyup, start the countdown
            $coupon.on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(checkDiscount, doneTypingInterval);
            });

            //on keydown, clear the countdown
            $coupon.on('keydown', function () {
                clearTimeout(typingTimer);
            });

            //user is "finished typing," do something
            function checkDiscount () {
                let data = {coupon: $coupon.val(), package_id: $('input[name=package_id]:checked').val()};
                $.get('/course/'+course_id+'/check_discount', data, function(){}, 'json')
                    .fail(function(){
                        $("#discount-wrapper").addClass('hide');
                        alert("Invalid Coupon Code.");

                        let new_total = 0;

                        if ($('input[name=payment_plan_id]:checked').length > 0) {

                            let plan = $('input[name=payment_plan_id]:checked').data('plan');

                            if( plan == 'Hele beløpet' ) {
                                //var price = $('#package_select option:selected').data('full_payment_price_number');
                                let price = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number');
                            } else if( plan == '3 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_3_price_number');
                                let price = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number');
                            } else if( plan == '6 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_6_price_number');
                                let price = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number');
                            }

                            new_total = price + $("input[name=discount_value]").val();
                        } else {
                            let price = $('#package_select option:selected').data('full_payment_price_number');

                            new_total = price + $("input[name=discount_value]").val();
                        }

                        $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                            let checkout_total = $('.checkout-total');
                            checkout_total.find('span').text(data);
                        });

                        $("input[name=discount_value]").val('');

                    })
                    .done(function(data){
                        $("#discount-wrapper").removeClass('hide');
                        $("#discount-display").text(data.discount_text);
                        $("input[name=discount_value]").val(data.discount);

                        let new_total = 0;

                        if ($('input[name=payment_plan_id]:checked').length > 0) {

                            let plan = $('input[name=payment_plan_id]:checked').data('plan');

                            if( plan == 'Hele beløpet' ) {
                                //var price = $('#package_select option:selected').data('full_payment_price_number');
                                let price = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number');
                            } else if( plan == '3 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_3_price_number');
                                let price = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number');
                            } else if( plan == '6 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_6_price_number');
                                let price = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number');
                            }

                            new_total = price - data.discount;
                        } else {
                            let price = $('#package_select option:selected').data('full_payment_price_number');

                            new_total = price - data.discount;
                        }

                        $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                            let checkout_total = $('.checkout-total');
                            checkout_total.find('span').text(data);
                        });
                    });
            }

            function generatePackagePaymentOption(package_id){
                let paymentPlanContainer = $("#paymentPlanContainer");
                paymentPlanContainer.empty();

                $.get('/payment-plan-options/'+package_id, {}, function(){}, 'json').done(function(data){
                    $.each(data, function (k, v) {
                        let checked = '';
                        if (k === 0/* && count_package_change === 1*/) {
                            checked = 'checked';
                        }

                        let paymentOptions = '<div class="payment-option custom-radio col-sm-6 px-0">';
                        paymentOptions += '<input type="radio" name="payment_plan_id" value="'+v.id+'" data-plan="'+v.plan+'" id="'+v.plan+'" '+checked+' required onchange="payment_plan_change(this)">';
                        paymentOptions += '<label for="'+v.plan+'">'+v.plan+'</label>';
                        paymentOptions += '</div>';
                        paymentPlanContainer.append(paymentOptions);
                    });
                });
            }
        });

        function payment_plan_change(t) {

            let checkout_total = $('.checkout-total');
            let plan = $(t).data('plan');
            let new_total = 0;
            $('input:radio[name=split_invoice]').prop('disabled', false);

            if( plan == 'Hele beløpet' ) {
                new_total = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
                    ? $('input[name=package_id]:checked').data('full_payment_sale_price_number')
                    : $('input[name=package_id]:checked').data('full_payment_price_number');

                let price_value = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
                    ? $('input[name=package_id]:checked').data('full_payment_sale_price_number')
                    : $('input[name=package_id]:checked').data('full_payment_price_number');

                if ($("input[name=discount_value]").val()) {
                    var discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }

                $('input:radio[name=split_invoice]').prop('disabled', true);
                $('input:radio[name=split_invoice]').prop('checked', false);
            } else if( plan == '3 måneder' ) {
                new_total = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_3_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_3_price_number');

                let price_value = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_3_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_3_price_number');

                if ($("input[name=discount_value]").val()) {
                    let discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }
            } else if( plan == '6 måneder' ) {
                new_total = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_6_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_6_price_number');

                let price_value = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_6_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_6_price_number');

                if ($("input[name=discount_value]").val()) {
                    var discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }
            } else if( plan == '12 måneder' ) {
                new_total = $('input[name=package_id]:checked').attr('data-months_12_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_12_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_12_price_number');

                let price_value = $('input[name=package_id]:checked').attr('data-months_12_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_12_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_12_price_number');

                if ($("input[name=discount_value]").val()) {
                    var discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }
            }
            $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                let checkout_total = $('.checkout-total');
                checkout_total.find('span').text(data);
            });
        }

        function disableSubmit(t) {
            let submit_btn = $(t).find('[type=submit]');
            submit_btn.text('');
            submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            submit_btn.attr('disabled', 'disabled');
        }
    </script>
@stop
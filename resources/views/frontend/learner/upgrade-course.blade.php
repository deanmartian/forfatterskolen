@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Oppgraderinger {{$courseTaken->package->course->title}}
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-4">
                        <h3 class="no-margin-top">@yield('heading')</h3>
                    </div>
                </div>

                <div class="col-sm-12 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-5">
                                <h4>Kurs</h4>
                                <p class="margin-top">
                                    <b>{{$courseTaken->package->course->title}}</b>
                                </p>
                            </div>
                            <div class="col-md-7">
                                <h4>Nåværende pakke</h4>
                                <p class="margin-top">
                                    <b>{{$courseTaken->package->variation}}</b>
                                </p>
                                <div>
                                    {!! nl2br($courseTaken->package->description) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body" id="selected-package-content">
                            <h4>Oppgrader til:</h4>
                            <p class="margin-top">
                                <b>{{$currentPackage->variation}}</b>
                            </p>
                            <div>
                                {!! nl2br($currentPackage->description) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('learner.upgrade-course', $courseTaken->id) }}" class="form-theme"
                method="POST" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <div class="col-sm-12 col-md-4">
                        <!-- Payment Details -->
                        <div class="panel panel-default no-margin-bottom">
                            <div class="panel-heading"><h4>Kurspakke</h4></div>
                            <div class="panel-body">
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

                                            <div class="package-option" id="package-option-{{ $package->id }}">
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

                                                       required>
                                                <label for="{{$package->variation}}">{{$package->variation}} </label>
                                            </div>
                                    @endforeach
                            </div> <!-- end of panel-body -->
                        </div><!-- end of panel panel-default no-margin-bottom -->

                        <div class="panel panel-default no-margin-bottom">
                            <div class="panel-heading"><h4>Betalingsmetode</h4></div>
                            <div class="panel-body">
                                <select class="form-control" name="payment_mode_id" required>
                                    @foreach(App\PaymentMode::get() as $paymentMode)
                                        <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                    @endforeach
                                </select>
                                <em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>
                            </div><!-- end of panel-body -->
                        </div><!-- end of panel panel-default no-margin-bottom -->

                        {{--<div class="panel panel-default no-margin-bottom">
                            <div class="panel-heading"><h4>Rabattkupong</h4></div>
                            <div class="panel-body">
                                <input type="text" name="coupon" class="form-control">
                            </div><!-- end of panel-body -->
                        </div>--}}<!-- end of panel panel-default no-margin-bottom -->

                        <div class="panel panel-default no-margin-bottom">
                            <div class="panel-heading"><h4>Betalingsplan</h4></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-6" id="paymentPlanContainer">
                                    </div>

                                    <div class="col-sm-6" style="margin-top: 8px" id="splitInvoiceContainer">
                                        <b>Månedlig faktura?*</b>
                                        <div class="payment-option">
                                            <input type="radio" name="split_invoice" value="1" disabled required>
                                            <label for="Yes">Ja</label>
                                        </div>
                                        <div class="payment-option">
                                            <input type="radio" name="split_invoice" value="0" disabled required>
                                            <label for="No">Nei</label>
                                        </div>
                                    </div>

                                </div><!-- end of row -->

                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        *Du kan velge om du vil ha faktura en gang i måneden, eller
                                        èn faktura der du kan betale inn ønsket beløp innen forfallsdatoen
                                    </div>
                                </div><!-- end of row -->

                                <hr>

                                <div class="text-center margin-bottom checkout-total">
                                    {{--@if( $hasPaidCourse && $package->has_student_discount)
                                        @if($courseTaken->package->course->type == "Single")
                                            <strong>Du har en rabatt som elev på Kr 500,00</strong> <br /><br />
                                        @endif

                                        @if($courseTaken->package->course->type == "Group")
                                            <strong>Du har en rabatt som elev på Kr 1000,00</strong> <br /><br />
                                        @endif
                                    @endif--}}

                                    <h4>Totalt</h4>

                                    <?php $standard_price = $courseTaken->package->course->packages->where('variation', 'Standard Kurs')->first(); ?>

                                        @if( $standard_price )
                                            <span>
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
                                            <span>
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

                                </div><!-- end of text-center margin-bottom checkout-total -->

                                <div id="discount-wrapper" class="hide text-center">
                                    <h4>Rabatt</h4>
                                    <span id="discount-display" style="font-size: 22px"></span>
                                </div>

                                <button type="submit" class="btn btn-theme btn-lg btn-block">Bestill</button>

                            </div><!-- end of panel-body -->
                        </div><!-- end of panel panel-default no-margin-bottom -->

                    </div><!-- end of col-sm-12 col-sm-4 -->
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function(){

            var course_id = '<?php echo $courseTaken->package->course->id?>';
            var current_package_id = '<?php echo $currentPackage->id?>';
            var count_package_change = 0; // used to determine the onload

            setTimeout(function(){
                $("#package-option-"+current_package_id+"").find('input[name=package_id]').attr('checked', true).trigger('change');
                $('input:radio[name=payment_plan_id]:first').attr('checked', true).trigger('change');
            }, 100);

            $(".package-option").change(function(){
               var changePackage = $(this).find('input[name=package_id]');
               var selected_package_content = $("#selected-package-content"),
               newSelectedContent = '';
                selected_package_content.empty();

                newSelectedContent += '<h4>Oppgrader til:</h4>';
                newSelectedContent += '<p class="margin-top"> <b>'+changePackage.data('variation')+'</b> </p>';
                newSelectedContent += '<div>'+changePackage.data('description')+'</div>';

                selected_package_content.append(newSelectedContent);
            });

            $('input[name=package_id]').on('change', function(){
                var checkout_total = $('.checkout-total');
                $('input:radio[name=split_invoice]').prop('disabled', true);
                $('input:radio[name=split_invoice]').prop('checked', false);
                generatePackagePaymentOption($(this).val());
                count_package_change++;

                var new_total = 0;

                if ($('input[name=payment_plan_id]:checked').length > 0) {
                    var plan = $('input[name=payment_plan_id]:checked').data('plan');
                    if( plan == 'Hele beløpet' ) {
                        var price = $(this).data('full_payment_price');
                        var price_value = $(this).attr('data-full_payment_sale_price_number')
                            ? $(this).data('full_payment_sale_price_number')
                            : $(this).data('full_payment_price_number');
                        new_total = price_value;

                        if ($("input[name=discount_value]").val()) {
                            var discount_value = $("input[name=discount_value]").val();
                            new_total = price_value - discount_value;
                        }
                    } else if( plan == '3 måneder' ) {
                        var price = $(this).data('months_3_price');
                        var price_value = $(this).attr('data-months_3_sale_price_number')
                            ? $(this).data('months_3_sale_price_number')
                            : $(this).data('months_3_price_number');
                        new_total = price_value;

                        if ($("input[name=discount_value]").val()) {
                            var discount_value = $("input[name=discount_value]").val();
                            new_total = price_value - discount_value;
                        }
                    } else if( plan == '6 måneder' ) {
                        var price = $(this).data('months_6_price');
                        var price_value = $(this).attr('data-months_6_sale_price_number')
                            ? $(this).data('months_6_sale_price_number')
                            : $(this).data('months_6_price_number');
                        new_total = price_value;

                        if ($("input[name=discount_value]").val()) {
                            var discount_value = $("input[name=discount_value]").val();
                            new_total = price_value - discount_value;
                        }
                    }
                } else {
                    new_total = $(this).data('full_payment_price_number');
                    if ($("input[name=discount_value]").val()) {
                        var discount_value = $("input[name=discount_value]").val();
                        var price_value = $(this).data('full_payment_price_number');
                        new_total = price_value - discount_value;
                    }
                }

                $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                    var checkout_total = $('.checkout-total');
                    checkout_total.find('span').text(data);
                });
            });



            $('select[name=payment_mode_id]').on('change', function(){
                var mode = $('option:selected', this).data('mode');
                if( mode == "Paypal" ) {
                    $('input:radio[name=payment_plan_id]').parent().addClass('disabled');
                    $('input:radio[name=payment_plan_id]').prop('disabled', true);
                    $('input:radio[name=payment_plan_id]').prop('checked', false);
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('checked', true);
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').parent().removeClass('disabled');
                    $('input:radio[name=payment_plan_id]').filter('[id="Hele beløpet"]').prop('disabled', false);
                    var package = $('#package_select option:selected');
                    //$('#package_select option:selected').data('full_payment_price');
                    var price = $('input:radio[name=package_id]:checked').data('full_payment_price');
                    $('.checkout-total span').text(price);
                    $('input:radio[name=split_invoice]').prop('disabled', true);
                } else {
                    $('input:radio[name=payment_plan_id]').parent().removeClass('disabled');
                    $('input:radio[name=payment_plan_id]').prop('disabled', false);
                }
            });

            //setup before functions
            var typingTimer;                //timer identifier
            var doneTypingInterval = 1000;  //time in ms, 5 second for example
            var $coupon = $('input[name=coupon]');

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
                var data = {coupon: $coupon.val(), package_id: $('input[name=package_id]:checked').val()};
                $.get('/course/'+course_id+'/check_discount', data, function(){}, 'json')
                    .fail(function(){
                        $("#discount-wrapper").addClass('hide');
                        alert("Invalid Coupon Code.");

                        var new_total = 0;

                        if ($('input[name=payment_plan_id]:checked').length > 0) {

                            var plan = $('input[name=payment_plan_id]:checked').data('plan');

                            if( plan == 'Hele beløpet' ) {
                                //var price = $('#package_select option:selected').data('full_payment_price_number');
                                var price = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number');
                            } else if( plan == '3 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_3_price_number');
                                var price = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number');
                            } else if( plan == '6 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_6_price_number');
                                var price = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number');
                            }

                            new_total = price + $("input[name=discount_value]").val();
                        } else {
                            var price = $('#package_select option:selected').data('full_payment_price_number');

                            new_total = price + $("input[name=discount_value]").val();
                        }

                        $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                            var checkout_total = $('.checkout-total');
                            checkout_total.find('span').text(data);
                        });

                        $("input[name=discount_value]").val('');

                    })
                    .done(function(data){
                        $("#discount-wrapper").removeClass('hide');
                        $("#discount-display").text(data.discount_text);
                        $("input[name=discount_value]").val(data.discount);

                        var new_total = 0;

                        if ($('input[name=payment_plan_id]:checked').length > 0) {

                            var plan = $('input[name=payment_plan_id]:checked').data('plan');

                            if( plan == 'Hele beløpet' ) {
                                //var price = $('#package_select option:selected').data('full_payment_price_number');
                                var price = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number');
                            } else if( plan == '3 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_3_price_number');
                                var price = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number');
                            } else if( plan == '6 måneder' ) {
                                //var price = $('#package_select option:selected').data('months_6_price_number');
                                var price = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number');
                            }

                            new_total = price - data.discount;
                        } else {
                            var price = $('#package_select option:selected').data('full_payment_price_number');

                            new_total = price - data.discount;
                        }

                        $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                            var checkout_total = $('.checkout-total');
                            checkout_total.find('span').text(data);
                        });
                    });
            }

            function generatePackagePaymentOption(package_id){
                var paymentPlanContainer = $("#paymentPlanContainer");
                paymentPlanContainer.empty();

                $.get('/payment-plan-options/'+package_id, {}, function(){}, 'json').done(function(data){
                    $.each(data, function (k, v) {
                        var checked = '';
                        if (k === 0/* && count_package_change === 1*/) {
                            checked = 'checked';
                        }

                        var paymentOptions = '<div class="payment-option">';
                        paymentOptions += '<input type="radio" name="payment_plan_id" value="'+v.id+'" data-plan="'+v.plan+'" id="'+v.plan+'" '+checked+' required onchange="payment_plan_change(this)">';
                        paymentOptions += '<label>'+v.plan+'</label>';
                        paymentOptions += '</div>';
                        paymentPlanContainer.append(paymentOptions);
                    });
                });
            }
        });

        function payment_plan_change(t) {

            var checkout_total = $('.checkout-total');
            var plan = $(t).data('plan');
            var new_total = 0;
            $('input:radio[name=split_invoice]').prop('disabled', false);

            if( plan == 'Hele beløpet' ) {
                new_total = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
                    ? $('input[name=package_id]:checked').data('full_payment_sale_price_number')
                    : $('input[name=package_id]:checked').data('full_payment_price_number');

                var price_value = $('input[name=package_id]:checked').attr('data-full_payment_sale_price_number')
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

                var price_value = $('input[name=package_id]:checked').attr('data-months_3_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_3_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_3_price_number');

                if ($("input[name=discount_value]").val()) {
                    var discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }
            } else if( plan == '6 måneder' ) {
                new_total = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_6_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_6_price_number');

                var price_value = $('input[name=package_id]:checked').attr('data-months_6_sale_price_number')
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

                var price_value = $('input[name=package_id]:checked').attr('data-months_12_sale_price_number')
                    ? $('input[name=package_id]:checked').data('months_12_sale_price_number')
                    : $('input[name=package_id]:checked').data('months_12_price_number');

                if ($("input[name=discount_value]").val()) {
                    var discount_value = $("input[name=discount_value]").val();
                    new_total = price_value - discount_value;
                }
            }
            $.get('/format_money/'+new_total, {}, function(){}, 'json').done(function(data){
                var checkout_total = $('.checkout-total');
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
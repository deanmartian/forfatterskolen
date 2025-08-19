{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('heading')
    Oppgraderinger
@stop

@section('content')
<div class="learner-container learner-upgrade">
    <div class="col-lg-12">
        <div class="col-lg-12">
            <div class="row">
                <h1>
                    {{ trans('site.learner.upgrades-text') }}
                </h1>
            </div>
        </div>

        <div class="row upgrade-wrapper">
            <div class="col-lg-8">
                <div class="card global-card">
                    <div class="card-body py-0">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ trans('site.front.course-text') }}</th>
                                <th>{{ trans('site.learner.current-package-text') }}</th>
                                <th>{{ trans('site.front.price') }}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($coursesTaken as $key => $courseTaken)
                                    @php
                                        $currentCourseType = $courseTaken->package->course_type;
                                    @endphp
                                    <tr>
                                        <td> {{$courseTaken->package->course->title}}</td>
                                        <td>
                                            <a href="#viewPackageDescriptionModal" data-toggle="modal" class="viewPackageDescriptionBtn"
                                                data-description="{{ $courseTaken->package->description }}">
                                                {{ $courseTaken->package->variation }}
                                            </a>
                                        </td>
                                        <td>
                                            @if (count($courseTaken->otherPackages))
                                                @foreach($courseTaken->otherPackages as $package)
                                                <?php
                                                    $upgradePrice = 0;
                                                    $displayBtn = true;
    
                                                    if (in_array($package->course_type, [3, 2])) {
                                                        $upgradePrice = ($package->course_type == 3 && $currentCourseType == 2) 
                                                            ? $package->full_payment_standard_upgrade_price 
                                                            : $package->full_payment_upgrade_price;
                                                    }
    
                                                    $today      = \Carbon\Carbon::today();
                                                    $disableUpgradeDate = \Carbon\Carbon::parse($package->disable_upgrade_price_date);
                                                    $orderDate =  \Carbon\Carbon::parse($courseTaken->created_at);
                                                    //$dateDiff = $orderDate->diffInDays(\CarbonCarbon::now());
                                                    $dateDiff = (int) round(\Carbon\Carbon::now()->diffInDays($orderDate, false));
    
                                                    if ($package->course->type == 'Single') {
                                                        $displayBtn = $dateDiff <= 14
                                                        ? !($package->disable_upgrade_price_date 
                                                            && $package->disable_upgrade_price == 1 
                                                            && $today->gte($disableUpgradeDate)) 
                                                            && !($package->disable_upgrade_price)
                                                        : false;
                                                    } else { // group package
                                                        $displayBtn = $package->disable_upgrade_price_date
                                                        ? !($package->disable_upgrade_price == 1 || $today->gte($disableUpgradeDate))
                                                        : !($package->disable_upgrade_price);
                                                    }
                                                ?>
                                                    @if($displayBtn && $courseTaken->package->is_upgradeable)
                                                        <span>{{ $package->variation }}:</span>
                                                        {{ FrontendHelpers::currencyFormat($upgradePrice) }}
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if (count($courseTaken->otherPackages))
                                                @foreach($courseTaken->otherPackages as $package)
                                                <?php
                                                    $upgradePrice = 0;
                                                    $displayBtn = true;
    
                                                    if (in_array($package->course_type, [3, 2])) {
                                                        $upgradePrice = ($package->course_type == 3 && $currentCourseType == 2) 
                                                            ? $package->full_payment_standard_upgrade_price 
                                                            : $package->full_payment_upgrade_price;
                                                    }
    
                                                    $today      = \Carbon\Carbon::today();
                                                    $disableUpgradeDate = \Carbon\Carbon::parse($package->disable_upgrade_price_date);
                                                    $orderDate =  \Carbon\Carbon::parse($courseTaken->created_at);
                                                    //$dateDiff = $orderDate->diffInDays(\Carbon\Carbon::now());
                                                    $dateDiff = (int) round(\Carbon\Carbon::now()->diffInDays($orderDate, false));
    
                                                    if ($package->course->type == 'Single') {
                                                        $displayBtn = $dateDiff <= 14
                                                        ? !($package->disable_upgrade_price_date 
                                                            && $package->disable_upgrade_price == 1 
                                                            && $today->gte($disableUpgradeDate)) 
                                                            && !($package->disable_upgrade_price)
                                                        : false;
                                                    } else { // group package
                                                        $displayBtn = $package->disable_upgrade_price_date
                                                        ? !($package->disable_upgrade_price == 1 || $today->gte($disableUpgradeDate))
                                                        : !($package->disable_upgrade_price);
                                                    }
                                                ?>
                                                    @if($displayBtn && $courseTaken->package->is_upgradeable && !Auth::user()->isDisabled)
                                                        <a href="{{ route('learner.get-upgrade-course',
                                                            ['course_taken_id' => $courseTaken->id, 'package_id' => $package->id]) }}"
                                                            class="btn btn-outline-primary">
                                                            {{ trans('site.learner.upgrade') }}
                                                        </a> 
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card-body -->
                </div> <!-- end assignment card -->
    
                <div class="card global-card w-100 mt-5">
                    <div class="card-header px-5">
                        <h2>
                            {{ trans('site.learner.script') }}
                        </h2>
                    </div>
                    <div class="card-body py-0">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ trans('site.learner.script') }}</th>
                                <th>{{ trans('site.learner.description-text') }}</th>
                                <th>{{ trans('site.learner.max-number-of-words-text') }}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach( Auth::user()->shopManuscriptsTaken as $shopManuscriptTaken )
                                    @if( $shopManuscriptTaken->status == 'Not started' )
                                        <tr>
                                            <td>
                                                {{ $shopManuscriptTaken->shop_manuscript->title }}
                                            </td>
                                            <td>
                                                {{ $shopManuscriptTaken->shop_manuscript->description }}
                                            </td>
                                            <td>
                                                {{ $shopManuscriptTaken->shop_manuscript->max_words }}
                                            </td>
                                            <td>
                                                @if (!Auth::user()->isDisabled)
                                                    <a class="btn btn-outline-primary"
                                                    href="{{ route('learner.get-upgrade-manuscript', $shopManuscriptTaken->id) }}">
                                                        {{-- {{ trans('site.learner.upgrade-script-development-text') }} --}}
                                                        {{ trans('site.learner.upgrade') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end manuscript card -->
    
                <div class="card global-card w-100 mt-5">
                    <div class="card-header px-5">
                        <h2>
                            {{ trans('site.learner.extra-writing-task-text') }}
                        </h2>
                    </div>
                    <div class="card-body py-0">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ trans('site.learner.assignment-single') }}</th>
                                <th>{{ trans('site.front.price') }}</th>
                                <th>{{ trans('site.learner.deadline') }}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($assignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->title }}</td>
                                    <td>{{ \App\Http\FrontendHelpers::formatCurrency($assignment->add_on_price) }}</td>
                                    <td>{{ $assignment->submission_date }}</td>
                                    <td>
                                        @if (!Auth::user()->isDisabled)
                                            <a href="{{ route('learner.get-upgrade-assignment', $assignment->id) }}"
                                            class="btn btn-outline-primary">
                                                Kjøp
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div>
            <div class="col-lg-4">
                <div class="right-container">
                    <p class="price">
                        Kroner 1490,- (ett år)
                    </p>
    
                    @php
                        $webinarPakke = AdminHelpers::getWebinarPakkeDetails(Auth::user()->id);
                    @endphp
    
                    @if ($webinarPakke)
                        <h2>
                            <i class="fa fa-wallet"></i>
                            {{ trans('site.learner.subscription-expires-text') }}
                        </h2>
                        <p>
                            {{ \Carbon\Carbon::parse($webinarPakke->end_date)->format('d F Y') }}
                        </p>
    
                        <h2>
                            {{ trans('site.learner.renew-automatically-text') }}
                            <input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.front.yes') }}"
                                class="webinar-auto-register-toggle" data-off="{{ trans('site.front.no') }}"
                                data-size="mini" id="auto-renew-toggle"
                                @if(Auth::user()->auto_renew_courses) {{ 'checked' }} @endif>
                        </h2>
    
                        <p>
                            @if (Auth::user()->auto_renew_courses)
                                <b>{{ trans('site.front.yes') }}</b>
                                ({{ \Carbon\Carbon::parse($webinarPakke->end_date)->subDays(7)->format('d.m.Y') }})
                            @else
                                <b>{{ trans('site.front.no') }}</b>
                            @endif
                        </p>
    
                        <button class="d-none" id="autoRenewBtn" data-toggle="modal" data-target="#autoRenewModal">
                        </button>
                        <button class="d-none" id="cancelAutoRenewBtn" data-toggle="modal" data-target="#cancelAutoRenewModal">
                        </button>
                        <button class="d-none" id="successAutoRenewBtn" data-toggle="modal" data-target="#successAutoRenewModal">
                        </button>
    
                        <?php
                            $coursesTaken = Auth::user()->coursesTaken;
                            $expiredDate = '';
                            foreach ($coursesTaken as $courseTaken) {
                                $package = \App\Package::find($courseTaken->package_id);
                                if ($package && $package->course_id == 17) {
                                    $expiredDate = $courseTaken->end_date;
                                }
                            }
    
                            $now = new DateTime();
                            $checkDate = date('m/Y', strtotime($expiredDate));
                            $input = DateTime::createFromFormat('m/Y', $checkDate);
                            $diff = $input->diff($now); // Returns DateInterval
    
                            // m is months
                            $withinAMonth = $diff->y === 0 && $diff->m <= 1;  // true
                            //display renew button when the webinar-pakke is going to expire within a month
                        ?>
                        @if($withinAMonth)
                            <button class="red-outline-btn" data-toggle="modal" data-target="#renewAllModal">
                                {{ trans('site.learner.renew-subscription-text') }}
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div id="autoRenewModal" class="modal fade new-global-modal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    {{ trans('site.learner.renew-automatically-text') }}
                </h3>
            </div>
            <div class="modal-body">
                <form action="{{ route('learner.upgrade-auto-renew') }}" method="POST" onsubmit="disableSubmitOrigText(this)">
                    {{ csrf_field() }}

                    <p>
                        {{ trans('site.learner.renew-subscription-automatically-question') }}?
                    </p>

                    <input type="hidden" name="auto_renew" value="1">
                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="width: 49%"
                            onclick="autoRenewToggleOption('off')">
                            {{ trans('site.front.no') }}
                        </button>
                        <button type="submit" class="btn btn-primary submit-btn pull-right" 
                        style="min-width: auto; padding: 3.75px 7.5px; width: 49%">
                            {{ trans('site.front.yes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="cancelAutoRenewModal" class="modal fade new-global-modal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Avbestille abonnementet
                </h3>
            </div>
            <div class="modal-body">
                <form action="{{ route('learner.upgrade-auto-renew') }}" method="POST" onsubmit="disableSubmitOrigText(this)">
                    {{ csrf_field() }}

                    <p>
                        ønsker du å si opp abonnementet?
                    </p>

                    <input type="hidden" name="auto_renew" value="0">
                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="width: 49%"
                            onclick="autoRenewToggleOption('on')">
                            {{ trans('site.front.no') }}
                        </button>
                        <button type="submit" class="btn btn-primary submit-btn pull-right" 
                        style="min-width: auto; padding: 3.75px 7.5px; width: 49%">
                            {{ trans('site.front.yes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="renewAllModal" class="modal fade new-global-modal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('site.learner.renew-all.title') }}</h3>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmitOrigText(this)">
                    {{ csrf_field() }}

                    <p>{{ trans('site.learner.renew-all.description') }}</p>
                    <div class="text-right margin-top">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="width: 49%">
                            {{ trans('site.front.no') }}
                        </button>
                        <button type="submit" class="btn btn-primary submit-btn pull-right" 
                        style="min-width: auto; padding: 3.75px 7.5px; width: 49%">
                            {{ trans('site.front.yes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="successAutoRenewModal" class="modal fade new-global-modal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title text-center">Success</h3>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('images-new/icon/big-green-check.png') }}" alt="">

                <h3>
                    You have successfully renewed!
                </h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default w-100" data-dismiss="modal">
                    Exit
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@if(session('success'))
<script>
    console.log("inside success");
    $("#successAutoRenewBtn").trigger('click');
</script>
@endif
<script>
    let autoRenewCourses = "{{ json_encode(Auth::user()->auto_renew_courses) }}"
    $("#auto-renew-toggle").change(function() {
        if ($(this).prop('checked') && autoRenewCourses == 0) {
            $("#autoRenewBtn").trigger('click');
        } 
        
        if (!$(this).prop('checked') && autoRenewCourses == 1){
            $("#cancelAutoRenewBtn").trigger('click');
        }
    });

    function autoRenewToggleOption(option) {
        $("#auto-renew-toggle").bootstrapToggle(option);
        //window.location.reload();
    }
</script>
@stop
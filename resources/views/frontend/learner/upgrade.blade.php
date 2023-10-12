{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Oppgraderinger
@stop

@section('content')
    <div class="learner-container learner-upgrade">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">
                        <h1 class="font-barlow-regular">
                            {{ trans('site.learner.upgrades-text') }}
                        </h1>
                    </div>
                </div>

                <div class="col-sm-6 button-container">
                    <div class="row">
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
                            <button class="btn site-btn-global mr-3" data-toggle="modal" data-target="#renewAllModal">
                                {{ trans('site.learner.renew-subscription-text') }}
                            </button>
                        @endif

                        <button class="btn site-btn-global" data-toggle="modal" data-target="#autoRenewModal">
                            {{ trans('site.learner.renew-automatically-text') }}
                        </button>
                    </div> <!-- end row justify-content-end -->
                </div> <!-- end col-md-6 -->
            </div> <!-- end row -->

            <div class="row mt-5">
                <div class="card global-card w-100">
                    <div class="card-header p-5">
                        <h4 class="font-weight-normal border d-inline-block p-3 mr-4">
                            <span class="theme-text font-barlow-regular">{{ trans('site.learner.subscription-expires-text') }}:</span>
                            {{ $expiredDate }}
                        </h4>

                        <h4 class="font-weight-normal border d-inline-block p-3 mr-4">
                            <span class="theme-text font-barlow-regular">Kroner 1490,- (ett år)</span>
                        </h4>

                        <h4 class="font-weight-normal border d-inline-block p-3">
                            <span class="theme-text font-barlow-regular">{{ trans('site.learner.renew-automatically-text') }}:</span>
                            {{ Auth::user()->auto_renew_courses ? 'Ja' : 'Nei'  }} ({{ \App\Http\FrontendHelpers::formatDate($expiredDate) }})
                        </h4>
                    </div>
                    <div class="card-body py-0">
                        <table class="table table-global">
                            <thead>
                            <tr>
                                <th>{{ trans('site.front.course-text') }}</th>
                                <th>{{ trans('site.learner.current-package-text') }}</th>
                                <th></th>
                                <th>{{ trans('site.front.price') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(Auth::user()->coursesTaken as $courseTaken)
                                <?php
                                $package = \App\Package::find($courseTaken->package_id);
                                // check if the course is not webinar pakke
                                ?>
                                @if ($package && $package->course_id != 17)
                                    <tr>
                                        <td> {{$courseTaken->package->course->title}}</td>
                                        <td>
                                            <a href="#viewPackageDescriptionModal" data-toggle="modal" class="viewPackageDescriptionBtn"
                                               data-description="{{ $package->description }}">
                                                {{ $courseTaken->package->variation }}
                                            </a>
                                        </td>
                                        <td>
                                            <?php
                                            $packages = \App\Package::where('course_id', $courseTaken->package->course->id)
                                                ->where('id', '>', $courseTaken->package->id)
                                                ->where('is_show', 1)
                                                ->get();
                                            $currentCourseType = $courseTaken->package->course_type;
                                            ?>
                                            @if (count($packages))
                                                @foreach($packages as $package)
                                                    <?php
                                                    $displayBtn = true;
                                                    $today      = \Carbon\Carbon::today();
                                                    $disableUpgradeDate = \Carbon\Carbon::parse($package->disable_upgrade_price_date);

                                                    $now = \Carbon\Carbon::now();
                                                    $orderDate =  \Carbon\Carbon::parse($courseTaken->created_at);
                                                    $dateDiff = $orderDate->diffInDays($now);

                                                    if ($package->course->type == 'Single') {

                                                        // check if the order date of he course is
                                                        // within 14 days
                                                        if ($dateDiff <= 14) {
                                                            if ($package->disable_upgrade_price_date) {
                                                                if ($package->disable_upgrade_price == 1) {
                                                                    $displayBtn = false;
                                                                } else {
                                                                    $displayBtn = true;
                                                                }

                                                                if ($today->gte($disableUpgradeDate)) {
                                                                    $displayBtn = false;
                                                                } else {
                                                                    $displayBtn = true;
                                                                }
                                                            } else {
                                                                if ($package->disable_upgrade_price) {
                                                                    $displayBtn = false;
                                                                }
                                                            }
                                                        } else {
                                                            // if the order date is not within 14 days
                                                            // hide the upgrade button
                                                            $displayBtn = false;
                                                        }
                                                    } else { // group package
                                                        if ($package->disable_upgrade_price_date) {
                                                            if ($package->disable_upgrade_price == 1) {
                                                                $displayBtn = false;
                                                            } else {
                                                                $displayBtn = true;
                                                            }

                                                            if ($today->gte($disableUpgradeDate)) {
                                                                $displayBtn = false;
                                                            } else {
                                                                $displayBtn = true;
                                                            }
                                                        } else {
                                                            if ($package->disable_upgrade_price) {
                                                                $displayBtn = false;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    @if ($displayBtn && $courseTaken->package->is_upgradeable)
                                                        <a href="{{ route('learner.get-upgrade-course',
                                                            ['course_taken_id' => $courseTaken->id, 'package_id' => $package->id]) }}"
                                                           class="btn site-btn-global site-btn-global-sm">
                                                            {{ trans('site.learner.upgrade') }} {{ $package->variation }}
                                                        </a> <div class="clearfix mt-1"></div>
                                                    @endif
                                                @endforeach
                                                {{--<button class="btn btn-primary btn-sm upgradePackageBtn"
                                                        data-toggle="modal" data-target="#upgradePackageModal"
                                                data-action="{{ route('learner.upgrade-course', $courseTaken->id) }}"
                                                data-fields="{{ json_encode($packages) }}">Oppgrader pakke</button>--}}
                                            @endif
                                        </td>
                                        <td>
                                            @if (count($packages))
                                                @foreach($packages as $package)
                                                    <?php
                                                    $upgradePrice = 0;

                                                    if ($package->course_type == 3 || $package->course_type == 2) {
                                                        $upgradePrice = $package->full_payment_upgrade_price;
                                                    }

                                                    if ($package->course_type == 3 && $currentCourseType == 2) {
                                                        $upgradePrice = $package->full_payment_standard_upgrade_price;
                                                    }

                                                    $displayBtn = true;
                                                    $today      = \Carbon\Carbon::today();
                                                    $disableUpgradeDate = \Carbon\Carbon::parse($package->disable_upgrade_price_date);

                                                    $now = \Carbon\Carbon::now();
                                                    $orderDate =  \Carbon\Carbon::parse($courseTaken->created_at);
                                                    $dateDiff = $orderDate->diffInDays($now);

                                                    if ($package->course->type == 'Single') {

                                                        if ($dateDiff <= 14) {
                                                            if ($package->disable_upgrade_price_date) {
                                                                if ($package->disable_upgrade_price == 1) {
                                                                    $displayBtn = false;
                                                                } else {
                                                                    $displayBtn = true;
                                                                }

                                                                if ($today->gte($disableUpgradeDate)) {
                                                                    $displayBtn = false;
                                                                } else {
                                                                    $displayBtn = true;
                                                                }
                                                            } else {
                                                                if ($package->disable_upgrade_price) {
                                                                    $displayBtn = false;
                                                                }
                                                            }
                                                        } else {
                                                            $displayBtn = false;
                                                        }
                                                    } else { // group package
                                                        if ($package->disable_upgrade_price_date) {
                                                            if ($package->disable_upgrade_price == 1) {
                                                                $displayBtn = false;
                                                            } else {
                                                                $displayBtn = true;
                                                            }

                                                            if ($today->gte($disableUpgradeDate)) {
                                                                $displayBtn = false;
                                                            } else {
                                                                $displayBtn = true;
                                                            }
                                                        } else {
                                                            if ($package->disable_upgrade_price) {
                                                                $displayBtn = false;
                                                            }
                                                        }
                                                    }
                                                    /*if ($package->disable_upgrade_price_date) {
                                                        if ($today->gte($disableUpgradeDate)) {
                                                            $displayBtn = false;
                                                        }
                                                    }

                                                    if ($package->disable_upgrade_price) {
                                                        $displayBtn = false;
                                                    } else {
                                                        $displayBtn = true;
                                                    }*/
                                                    ?>
                                                    @if($displayBtn && $courseTaken->package->is_upgradeable)
                                                        <b>{{ $package->variation }}:</b>
                                                        {{ FrontendHelpers::currencyFormat($upgradePrice) }}
                                                        <br>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card-->
                </div> <!-- end card-->

                <div class="card global-card w-100 mt-5">
                    <div class="card-header px-5">
                        <h1>
                            {{ trans('site.learner.script') }}
                        </h1>
                    </div>
                    <div class="card-body py-0">
                        <table class="table table-global">
                            <thead>
                            <tr>
                                <th>{{ trans('site.learner.script') }}</th>
                                <th width="550">{{ trans('site.learner.description-text') }}</th>
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
                                            <a class="btn site-btn-global site-btn-global-sm"
                                               href="{{ route('learner.get-upgrade-manuscript', $shopManuscriptTaken->id) }}">
                                                {{ trans('site.learner.upgrade-script-development-text') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card-body -->
                </div> <!-- end card for manus -->

                <div class="card global-card w-100 mt-5">
                    <div class="card-header px-5">
                        <h1>
                            {{ trans('site.learner.extra-writing-task-text') }}
                        </h1>
                    </div>
                    <div class="card-body py-0">
                        <table class="table table-global">
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
                                        <a href="{{ route('learner.get-upgrade-assignment', $assignment->id) }}"
                                           class="btn site-btn-global site-btn-global-sm">
                                            Kjøp
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div> <!-- end learner-container -->

    <div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{ trans('site.learner.renew-all.title') }}</h3>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <p>{{ trans('site.learner.renew-all.description') }}</p>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">{{ trans('site.front.yes') }}</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('site.front.no') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="coursesExpiresModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{ trans('site.learner.courses-expires-text') }}</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <?php
                    $coursesTaken = Auth::user()->coursesTaken;
                    $expiredDate = '';
                    foreach ($coursesTaken as $courseTaken) {
                        $package = \App\Package::find($courseTaken->package_id);
                        if ($package && $package->course_id == 17) {
                            $expiredDate = $courseTaken->end_date;
                        }
                    }
                    ?>
                    <p>
                        {{ trans('site.learner.webinar-package-expires-on-text') }} {{ $expiredDate }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div id="upgradePackageModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{ trans('site.learner.upgrade-package-text') }}</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary btn-sm">{{ trans('site.learner.upgrade-package-text') }}</button>
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">{{ trans('site.front.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="autoRenewModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{ trans('site.learner.renew-automatically-text') }}</h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('learner.upgrade-auto-renew') }}" method="POST" onsubmit="disableSubmitOrigText(this)">
                        {{ csrf_field() }}

                        <p>
                           {{ trans('site.learner.renew-subscription-automatically-question') }}?
                        </p>

                        <input type="hidden" name="auto_renew">
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary btn-sm" id="yesRenew">{{ strtoupper(trans('site.front.yes')) }}</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="noRenew">{{ strtoupper(trans('site.front.no')) }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="viewPackageDescriptionModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{ trans('site.learner.course-package-content-text') }}</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        $(".upgradePackageBtn").click(function(){
            let form = $('#upgradePackageModal').find('form');
            let fields = $(this).data('fields');
            let action = $(this).data('action');

            form.attr('action', action);
            form.find('.package-option').remove();

            let radioInput = '';
            $.each(fields,function(k, v) {
                radioInput += '<div class="package-option">' +
                    '<input type="radio" name="package_id" value="'+v.id+'" required>' +
                    ' <label for="'+v.variation+'">'+v.variation+'</label>' +
                    '</div>';
            });

            form.prepend(radioInput);
        });

        $("#yesRenew").click(function() {
            $("input[name=auto_renew]").val(1);
        });

        $("#noRenew").click(function() {
            $("input[name=auto_renew]").val(0);
        });

        $(".viewPackageDescriptionBtn").click(function(){
            let modal = $("#viewPackageDescriptionModal");
            modal.find('.modal-body').empty();

            let description = $(this).data('description');
            let test = '<pre>';
            test += description;
            test += '</pre>';
            modal.find('.modal-body').append(test);
        });

        function disableSubmit(t) {
            let submit_btn = $(t).find('[type=submit]');
            submit_btn.text('');
            submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            submit_btn.attr('disabled', 'disabled');
        }
    </script>
@stop
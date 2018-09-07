@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Oppgraderinger
@stop

@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <style>
        .table-users .table {
            margin-top: 12px;
            margin-bottom: 12px;
            background-color: #fff;
            border: solid 1px #ccc;
        }

        .table thead {
            background-color: #eee;
        }

        .margin-top-30 {
            margin-top: 30px;
        }

        .margin-bottom-30 {
            margin-bottom: 30px;
        }

        .margin-top-5 {
            margin-top: 5px;
        }

        .btn {
            border:none;
        }

        .sub-right-content {
            background-color: #d7e3e3;
            font-family: 'Lato', 'sans-serif';
        }

        .light-blue {
            background-color: #2da9e9;
            border-bottom: 2px solid #0277b3;
        }

        hr {
            border: 0.5px solid #252525;
        }

        .orange {
            background-color: #ffa83f;
            border-bottom: 2px solid #eb9b3b;
        }
    </style>
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

                <div class="col-sm-12 margin-top">
                    <div>
                        <button class="btn btn-primary light-blue" data-target="#renewAllModal" data-toggle="modal">
                            Forny abonnementet
                        </button>

                        <b>
                            Abonnementet utløper:
                        </b>

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

                        <em>{{ $expiredDate }}</em>

                        <button class="btn btn-primary pull-right light-blue" data-toggle="modal" data-target="#autoRenewModal">
                            Forny automatisk
                        </button>
                        <em class="pull-right" style="margin-right: 20px">
                            {{ Auth::user()->auto_renew_courses ? 'Ja' : 'Nei'  }} ({{ \App\Http\FrontendHelpers::formatDate($expiredDate) }})
                        </em>
                        <b class="pull-right">Forny automatisk: </b>
                    </div>
                    <b>
                        Kroner 1490,- (ett år)
                    </b>
                    <hr>
                    <div class="clearfix margin-top-30"></div>

                    <div class="col-sm-12">
                        <div class="table-users table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kurs</th>
                                        <th>Nåværende pakke</th>
                                        <th></th>
                                        <th>Pris</th>
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
                                                            @if ($displayBtn)
                                                            <a href="{{ route('learner.get-upgrade-course',
                                                            ['course_taken_id' => $courseTaken->id, 'package_id' => $package->id]) }}"
                                                            class="btn btn-primary btn-xs orange">
                                                                Oppgrader {{ $package->variation }}
                                                            </a> <div class="clearfix margin-top-5"></div>
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
                                                            @if($displayBtn)
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
                        </div>
                    </div>

                    <div class="clearfix margin-bottom-30"></div>
                    <hr>
                    <div class="clearfix margin-top-30"></div>

                    <div class="col-sm-12">

                        <h4>Manus</h4>

                        <div class="table-users table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Manus</th>
                                    <th>Beskrivelse</th>
                                    <th>Maks antall ord</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach( Auth::user()->shopManuscriptsTaken as $shopManuscriptTaken )
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
                                            <a class="btn btn-primary btn-xs orange" href="{{ route('learner.get-upgrade-manuscript', $shopManuscriptTaken->id) }}">
                                                Oppgrader Manusutvikling
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="clearfix margin-bottom-30"></div>
                    <hr>
                    <div class="clearfix margin-top-30"></div>

                    <div class="col-sm-12">

                        <h4>Ekstra skriveoppgave</h4>

                        <div class="table-users table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Oppgave</th>
                                    <th>Pris</th>
                                    <th>Frist</th>
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
                                               class="btn btn-primary btn-xs orange">
                                                Kjøp
                                            </a>
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

        <div class="clearfix"></div>
    </div>

    <div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Forny alle kursene for ett år</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.renew-all-courses') }}">
                        {{ csrf_field() }}

                        <p>Vil du fornye alle kursene dine for ett år ekstra for kroner 1490,?</p>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">ja</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Nei</button>
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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Courses Expires</h4>
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
                    Webinar pakke expires on {{ $expiredDate }}
                </div>
            </div>
        </div>
    </div>

    <div id="upgradePackageModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upgrade Package</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary btn-sm">Oppgrader pakke</button>
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
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
                    <h4 class="modal-title">Forny automatisk</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('learner.upgrade-auto-renew') }}" method="POST">
                        {{ csrf_field() }}

                        Er du sikker på at du vil fornye abonnementet ditt automatisk?

                        <input type="hidden" name="auto_renew">
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary btn-sm" id="yesRenew">JA</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="noRenew">NEI</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="viewPackageDescriptionModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Kurspakke innhold</h4>
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
            var form = $('#upgradePackageModal').find('form');
            var fields = $(this).data('fields');
            var action = $(this).data('action');

            form.attr('action', action);
            form.find('.package-option').remove();

            var radioInput = '';
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
            var modal = $("#viewPackageDescriptionModal");
            modal.find('.modal-body').empty();

            var description = $(this).data('description');
            var test = '<pre>';
            test += description;
            test += '</pre>';
            modal.find('.modal-body').append(test);
        });
    </script>
@stop
@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Oppgraderinger {{$shopManuscriptTaken->shop_manuscript->title}}
@stop

@section('styles')
    <style>
        form .form-group {
            margin-bottom: 0;
            margin-top: 12px;
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

                <form method="POST" action="{{ route('learner.upgrade-manuscript', $shopManuscriptTaken->id) }}"
                class="form-theme">
                    {{ csrf_field() }}

                <div class="col-sm-12 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-body" style="background: #fff;">
                            <div class="col-md-5">
                                <h4>Nåværende manus detaljer</h4>
                                <p class="margin-top">
                                    <b>Manus:</b> <br>
                                    {{$shopManuscriptTaken->shop_manuscript->title}}
                                </p>
                                <p>
                                    <b>Beskrivelse:</b> <br>
                                    {{$shopManuscriptTaken->shop_manuscript->description}}
                                </p>
                                <p>
                                    <b>Maks antall ord:</b> <br>
                                    {{$shopManuscriptTaken->shop_manuscript->max_words}} ords
                                </p>
                            </div>

                            <div class="col-md-7">
                                <h4>Oppgrader til</h4>
                                @foreach($shopManuscriptUpgrades as $shopManuscriptUpgrade)
                                    <div class="form-group">
                                        <input type="radio" name="manuscript_upgrade_id" value="{{ $shopManuscriptUpgrade->id }}">
                                        <label>{{ $shopManuscriptUpgrade->upgrade_manuscript->title }}
                                            - {{ $shopManuscriptUpgrade->upgrade_manuscript->max_words }} ord
                                            ({{ FrontendHelpers::currencyFormat($shopManuscriptUpgrade->price) }})</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div><!-- end col-sm-12 col-md-8 -->

                    <div class="col-sm-12 col-md-4">
                        <div class="panel panel-default no-margin-bottom">
                            <div class="panel-heading"><h4>Betalingsmetode</h4></div>
                            <div class="panel-body">
                                <select class="form-control" name="payment_mode_id" required>
                                    @foreach(App\PaymentMode::get() as $paymentMode)
                                        <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                    @endforeach
                                </select>
                                <em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>

                                <button type="submit" class="btn btn-theme btn-lg btn-block">Bestill</button>
                            </div><!-- end panel-body -->
                        </div><!-- end panel panel-default no-margin-bottom -->

                        <?php $hasPaidCourse = false; ?>
                        @if( !Auth::guest() )
                            <?php
                            foreach( Auth::user()->coursesTaken as $courseTaken ) :
                                if( $courseTaken->package->course->type != "Free" && $courseTaken->is_active ) :
                                    $hasPaidCourse = true;
                                    break;
                                endif;
                            endforeach;
                            ?>
                        @endif
                    </div><!-- end col-sm-12 col-md-4 -->

                </form>

            </div>
        </div>

        <div class="clearfix"></div>

    </div>
@stop
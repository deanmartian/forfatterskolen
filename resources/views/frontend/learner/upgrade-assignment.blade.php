@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Buy {{$assignment->title}}
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
                            <h4>{{ $assignment->title }}</h4>
                            <b>Beskrivelse:</b>
                            {{ $assignment->description }} <br>
                            <b>Frist:</b>
                            {{ \Carbon\Carbon::parse($assignment->submission_date)->format('d.m.Y') }}
                            Klokken
                            {{ \Carbon\Carbon::parse($assignment->submission_date)->format('H:i') }}
                            <br>
                            <b>Pris:</b>
                            {{ \App\Http\FrontendHelpers::currencyFormat($assignment->add_on_price) }} <br>
                            <b>Maks antall ord:</b>
                            {{ $assignment->max_words }} ord
                        </div>
                    </div>
                </div>

                <form action="{{ route('learner.upgrade-assignment', $assignment->id) }}" class="form-theme"
                      method="POST">
                {{ csrf_field() }}

                    <div class="col-sm-12 col-md-4">
                        <!-- Payment Details -->
                        <div class="panel panel-default no-margin-bottom">
                            <div class="panel-heading"><h4>Betalingsmetode</h4></div>
                            <div class="panel-body">
                                <select class="form-control" name="payment_mode_id" required>
                                    @foreach(App\PaymentMode::get() as $paymentMode)
                                        <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                    @endforeach
                                </select>
                                <em><small>Merk: Vi godtar kun full betaling på PAYPAL</small></em>

                                <hr>

                                <div class="text-center margin-bottom checkout-total">
                                    <h4>Totalt</h4>
                                    <span>{{ FrontendHelpers::currencyFormat($assignment->add_on_price) }}</span>
                                </div>

                                <button type="submit" class="btn btn-theme btn-lg btn-block">Bestill</button>

                            </div><!-- end of panel-body -->
                        </div><!-- end of panel panel-default no-margin-bottom -->
                    </div>

                </form>

            </div>
        </div>

        <div class="clearfix"></div>

    </div>
@stop
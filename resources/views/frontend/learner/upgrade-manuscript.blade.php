@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Oppgraderinger {{$shopManuscriptTaken->shop_manuscript->title}}
@stop

@section('content')

    <div class="learner-container">
        <div class="container">
            <form method="POST" action="{{ route('learner.upgrade-manuscript', $shopManuscriptTaken->id) }}"
                  class="form-theme" onsubmit="disableSubmitOrigText(this)">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="font-barlow-regular mb-4">
                            @yield('heading')
                        </h1>
                    </div>

                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body p-5">
                                <div class="row mt-4">
                                    <div class="col-md-5">
                                        <h3 class="mb-3">Nåværende manus detaljer</h3>
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
                                    </div> <!-- end col-md-5 -->

                                    <div class="col-md-7">
                                        <h3 class="mb-4">Oppgrader til</h3>
                                        @foreach($shopManuscriptUpgrades as $shopManuscriptUpgrade)
                                            <div class="custom-radio mb-1">
                                                <input type="radio" name="manuscript_upgrade_id" value="{{ $shopManuscriptUpgrade->id }}"
                                                id="{{ $shopManuscriptUpgrade->upgrade_manuscript->title }}">
                                                <label for="{{ $shopManuscriptUpgrade->upgrade_manuscript->title }}">
                                                    {{ $shopManuscriptUpgrade->upgrade_manuscript->title }}
                                                    - {{ $shopManuscriptUpgrade->upgrade_manuscript->max_words }} ord
                                                    ({{ \App\Http\FrontendHelpers::currencyFormat($shopManuscriptUpgrade->price) }})</label>
                                            </div>
                                        @endforeach
                                    </div> <!-- end col-md-7 -->
                                </div> <!-- end row -->
                            </div> <!-- end panel-body -->
                        </div> <!-- end panel-default -->
                    </div> <!-- end col-lg-8 -->

                    <div class="col-md-4">
                        <div class="panel panel-default p-5">
                            <div class="panel-heading-underlined">Betalingsmetode</div>
                            <div class="panel-body px-0 pb-0">
                                <select class="form-control" name="payment_mode_id" required data-size="15">
                                    @foreach(\App\Http\FrontendHelpers::paymentModes() as $paymentMode)
                                        <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                    @endforeach
                                </select>
                                <em>
                                    <small class="font-barlow-regular">
                                        Merk: Vi godtar kun full betaling på PAYPAL
                                    </small>
                                </em>

                                <button type="submit" class="btn site-btn-global-w-arrow mt-2 d-block">Bestill</button>
                            </div>
                        </div> <!-- end panel-default -->
                    </div> <!-- end col-lg-4 -->

                </div> <!-- end row -->

            </form> <!-- end form -->
        </div> <!-- end container -->
    </div> <!-- end learner-container -->

@stop
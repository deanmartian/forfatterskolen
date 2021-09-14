@extends('frontend.layout')

@section('title')
    <title>Upgrade &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    {{ trans('site.front.buy') }} {{$assignment->title}}
@stop

@section('content')
    <div class="learner-container">
        <div class="container">

            <form action="{{ route('learner.upgrade-assignment', $assignment->id) }}" class="form-theme"
                  method="POST">
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
                                <h3>{{ $assignment->title }}</h3>
                                <b>{{ trans('site.learner.description-text') }}:</b>
                                {{ $assignment->description }} <br>
                                <b>{{ trans('site.learner.deadline') }}:</b>
                                {{ ucwords(strtr(trans('site.learner.submission-date-value'), [
                                       '_date_' => \Carbon\Carbon::parse($assignment->submission_date)->format('d M Y'),
                                        '_time_' => \Carbon\Carbon::parse($assignment->submission_date)->format('H:i')
                                    ])) }}
                                <br>
                                <b>{{ trans('site.front.price') }}:</b>
                                {{ \App\Http\FrontendHelpers::currencyFormat($assignment->add_on_price) }} <br>
                                <b>{{ trans('site.learner.max-number-of-words-text') }}:</b>
                                {{ $assignment->max_words }} ord
                            </div> <!-- end panel-body -->
                        </div> <!-- end panel -->
                    </div> <!-- end col-lg-8-->

                    <div class="col-md-4">
                        <div class="panel panel-default p-5">
                            <div class="panel-heading-underlined">{{ trans('site.front.form.payment-method') }}</div>
                            <div class="panel-body px-0 pb-0">
                                <select class="form-control" name="payment_mode_id" required data-size="15">
                                    @foreach(\App\Http\FrontendHelpers::paymentModes() as $paymentMode)
                                        <option value="{{$paymentMode->id}}" data-mode="{{ $paymentMode->mode }}">{{$paymentMode->mode}}</option>
                                    @endforeach
                                </select>
                                <em>
                                    <small class="font-barlow-regular">
                                        {{ trans('site.learner.renew-course.payment-note') }}
                                    </small>
                                </em>

                                <h3 class="font-barlow-regular font-weight-normal my-4">{{ trans('site.front.total') }}:
                                    <span class="theme-text font-barlow-regular">
                                        {{ App\Http\FrontendHelpers::currencyFormat($assignment->add_on_price) }}
                                    </span>
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
@extends('frontend.layout')

@section('title')
    <title>Invitation Invalid &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h1 class="card-title font-weight-light with-border-b pb-2">Invalid Invitation Request</h1>
                                    <p class="font-weight-light">
                                        Sorry. This is an invalid invitation request. If you believe this is an error,
                                        please email us and leave your feedback on
                                        <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
@endsection

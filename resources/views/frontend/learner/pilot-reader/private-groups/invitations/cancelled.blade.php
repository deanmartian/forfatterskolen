@extends('frontend.layout')

@section('title')
    <title>Invitation Cancelled &rsaquo; Forfatterskolen</title>
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
                                        Sorry, this invitation has been cancelled. If you believe this was a mistake,
                             you'll need to contact <b>{{ $author->full_name }}</b> to ask for access
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

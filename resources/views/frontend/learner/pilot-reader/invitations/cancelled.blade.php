@extends('frontend.layout')

@section('title')
    <title>Invitation Declined &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Invitation Cancelled @stop

@section('content')

    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    <div class="row">
                        <header>
                            <h5 class="font-16">@yield('heading')</h5>
                            {{ "Sorry, this invitation has been cancelled. If you believe this was a mistake,
                             you'll need to contact $author->first_name  $author->last_name to ask for access"}}
                        </header>
                    </div>

                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content white-background -->

        <div class="clearfix"></div>

    </div> <!-- end account-container -->

@stop
@extends('frontend.layout')

@section('title')
    <title>About Reader Directory&rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content reader-directory-search white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    @include('frontend.learner.pilot-reader.partials.reader-directory-nav')

                </div><!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content reader-directory-search white-background -->

        <div class="clearfix"></div>
    </div>
@stop
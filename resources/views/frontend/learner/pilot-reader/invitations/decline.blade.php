@extends('frontend.layout')

@section('title')
    <title>Invitation Declined &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Invitation Declined @stop

@section('content')

    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    <div class="row">
                        <header>
                            {{--<h5 class="font-16">@yield('heading')</h5>
                            <p>
                                You've declined ely butabara's invitation to read testing.
                            </p>
                            <p>
                                Thanks for letting us know.
                            </p>--}}

                            <a href="{{ route('learner.book-author') }}">Back to Dashboard</a>
                            @if($isAlreadyDecline)
                                <h5 class="card-title font-weight-light with-border-b pb-2 font-16">Invitation Declined</h5>
                                <p class="font-weight-light">
                                    You already declined this invitation.
                                </p>
                            @else
                                <h5 class="card-title font-weight-light with-border-b pb-2 font-16">Invitation Declined</h5>
                                <p class="font-weight-light">
                                    {{ "You've declined " . $author->first_name . " " . $author->last_name ."'s" }} invitation to read <em>{{ $book->title }}</em>.
                                    Thanks for letting us know.
                                </p>
                            @endif
                        </header>
                    </div>

                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content white-background -->

        <div class="clearfix"></div>

    </div> <!-- end account-container -->

@stop
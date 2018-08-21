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
                            @if (Auth::check())
                                @if(Auth::user()->email !== $invitation->email)
                                    <p class="font-weight-light">{{ "This invitation was sent to $receiver_name, but you're already logged in to a different account."}}</p>
                                    <p class="font-weight-light">{{ "Book invitations are unique to each individual, so this link can't be shared between multiple people."}}</p>
                                @elseif(!$isAlreadyAccepted)
                                    <h1 class="card-title font-weight-light with-border-b pb-2">Invitation Accepted</h1>
                                    <p class="font-weight-light">
                                        You accepted the invitation for reading a book entitled "{{ $book->title }}".
                                        Click "Read Now" button to read otherwise click "Go to Dashboard" button
                                        to go to your dashboard.
                                    </p>
                                    <div class="form-group">
                                        @if($chapter)
                                            <a href="{{ route('learner.book-author-book-view-chapter', ['book_id' => $book->id,
                                            'chapter_id' => $chapter->id]) }}" class="btn btn-outline-success float-right">Read Now</a>
                                        @endif
                                        <a href="{{ route('learner.book-author-book-show', $book->id) }}" class="btn btn-outline-primary float-right mr-1">View Book</a>
                                        <a href="{{ route('learner.book-author') }}" class="btn btn-outline-info float-right mr-1">Go to Dashboard</a>
                                    </div>
                                @else
                                    <h1 class="card-title font-weight-light with-border-b pb-2">Invitation Accepted</h1>
                                    <p class="font-weight-light">You already accepted this invitation.</p>
                                @endif
                            @else
                                <p class="font-weight-light">
                                    {{ $author->first_name . " " . $author->last_name }} has invited you to read "{{ $book->title }}". To accept the invitation you need a BetaBooks account.
                                </p>
                                <p class="font-weight-light">
                                    Please click Register link above to create your account or click Login link if you already have an account.
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
@extends('frontend.layout')

@section('title')
    <title>Invitation Accepted &rsaquo; Forfatterskolen</title>
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
                                    @if(Auth::user()->email !== $email)
                                        <h1 class="card-title font-weight-light with-border-b pb-2">Invitation Accepted</h1>
                                        <p class="font-weight-light">{{ "This invitation was sent to $receiver_name, but you're already logged in to a different account."}}</p>
                                        <p class="font-weight-light">{{ "Group invitations are unique to each individual, so this link can't be shared between multiple people."}}</p>
                                    @elseif(!$isAlreadyAccepted)
                                        <h1 class="card-title font-weight-light with-border-b pb-2">Invitation Accepted</h1>
                                        <p class="font-weight-light">
                                            You accepted the invitation for joining a group entitled "{{ $group->name }}".
                                            Click "View Group" button to view the group details otherwise click "Go to Dashboard" button
                                            to go to your dashboard.
                                        </p>
                                        <div class="form-group">
                                            <a href="{{ route('learner.private-groups.show', $group->id) }}" class="btn btn-outline-primary float-right mr-1">View Group</a>
                                            <a href="{{ route('learner.book-author') }}" class="btn btn-outline-info float-right mr-1">Go to Dashboard</a>
                                        </div>
                                    @else
                                        <h1 class="card-title font-weight-light with-border-b pb-2">Invitation Accepted</h1>
                                        <p class="font-weight-light">You already accepted this invitation.</p>
                                    @endif
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

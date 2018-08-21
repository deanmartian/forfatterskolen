@extends('frontend.layout')

@section('title')
    <title>Invitation Declined &rsaquo; Forfatterskolen</title>
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
                                    @if($isAlreadyDecline)
                                        <h1 class="card-title font-weight-light with-border-b pb-2">Invitation Declined</h1>
                                        <p class="font-weight-light">
                                            You already declined this invitation.
                                        </p>
                                    @else
                                        <h1 class="card-title font-weight-light with-border-b pb-2">Invitation Declined</h1>
                                        <p class="font-weight-light">
                                            {{ "You've declined " . $author->first_name . " " . $author->last_name ."'s" }} invitation to join {{ $group->name }}.
                                            Thanks for letting us know.
                                        </p>
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
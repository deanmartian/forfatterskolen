@extends('frontend.layout')

@section('page_title', 'Invitasjonslenke &rsaquo; Forfatterskolen')
@section('styles')
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="container">
        <div class="row mt-4">
            <div class="col-sm-8 col-sm-offset-2 mb-3">
                <div class="global-card with-border">
                    <div class="card-body">
                        <h1 class="card-title font-weight-light with-border-b pb-2 mt-0">Invitation Link</h1>
                        <p class="font-weight-light margin-top">
                            @if(Auth::check())
                                @if($hasAccess || $user_author->id === $book->author_id)
                                    You already have an access to read <strong><i class="font-weight-bold">{{ $book->title }}</i></strong>
                                @else
                                    @if($send_count < 3)
                                        <div class="form-group font-weight-light display-none" id="resultDiv">
                                            New <i class="font-weight-bold">invitation</i> has been sent to your account. Please click
                                            <a href="{{ route('learner.book-author') }}">here</a> to view the invitation.
                                            Additionally, you will also received an <strong class="font-weight-bold">email</strong> containing the invitation details.
                                        </div>
                                        <div class="form-group font-weight-light" id="requestingDiv">
                                            <p class="lead mb-1">Sending Email <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></p>
                                            <small class="font-weight-light">Invitation email will be sent to your email. This might take a few minutes</small>
                                        </div>
                                    @else
                                        <div class="form-group font-weight-light">
                                            {{ "Sorry, you've reached the maximum sending of invitation to your email." }}
                                        </div>
                                    @endif
                                @endif
                            @else
                                {{ "Ready to read "}} <strong><i class="font-weight-bold">{{ $book->title }}</i></strong> by <strong>{{ $author->first_name . " " . $author->last_name }}</strong>{{ "? Just enter your email address below and we'll send you a link to get started. "}}
                                <form id="sentEmailForm">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-3">
                                            <input type="text" name="email" class="form-control" placeholder="Enter your email here">
                                            <button class="btn btn-info btn-sm mt-1 btn-block"><i class="fa fa-spinner fa-pulse fa-1x fa-fw display-none icon"></i> Submit</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script>
        let book_id = "{{ $book->id }}";
        let email = null;
        let invitation_link = "{{ route('book.invite.send') }}";
        @if(Auth::check())
                @if(!$hasAccess && $user_author->id !== $book->author_id && $send_count < 3)
                    email = "{{ Auth::user()->email }}";
                @endif
            invitation_link = "{{ route('account.book.invite.send') }}";
        @endif
    </script>
    <script src="{{ asset('/js/pilot-reader/invitation_link.js') }}"></script>
@endsection
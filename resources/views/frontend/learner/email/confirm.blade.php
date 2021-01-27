@extends('frontend.layout')

@section('content')
    <div class="container text-center" style="margin-bottom: 20px; padding-top: 20px">
        <div class="row">
            <div class="col-sm-12">
                <div class="global-card with-border" style="padding: 10px">
                    <div class="card-body">
                        <h1 class="card-title font-weight-light with-border-b pb-2" style="font-size: 36px">Email Confirmation</h1>
                        <p class="font-weight-light">
                            @if(Auth::user()->id === $data['user_id'])
                                {{ "You've confirmed" }} <strong class="font-italic">{{ $data['email'] }}</strong> {{" as one of you're email. If you want set this as your primary email or view your list of emails, click the \"Profil\" button below"}}
                                <div class="form-group">
                                    <a href="/account/profile" class="btn btn-info btn-sm float-right">Profil</a>
                                </div>
                            @else
                                {{ "Sorry. This confirmation email was sent to "}} <strong>{{ $data['user']->first_name . " " . $data['user']->last_name  }}</strong>{{", but you're already logged in to a different account." }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

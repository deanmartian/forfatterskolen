@extends('frontend.layout')

@section('title')
    <title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="container thankyou-container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 text-center">
                <div class="thankyou">
                    <div class="thankyou-header">
                        <div><img src="{{asset('images/thankyou.png')}}"></div>
                    </div>
                    <div class="thankyou-content">
                        <h3>Takk for at du meldte deg på!</h3>
                        <p>
                            Du vil nå få tilsendt en epost!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
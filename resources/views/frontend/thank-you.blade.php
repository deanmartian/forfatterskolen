@extends('frontend.layout')

@section('title')
    <title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="thank-you-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 left-container">
                    <img src="{{ asset('images-new/thumb-icon.png') }}" alt="" class="thumb">
                    <h1>Takk for at du meldte deg på!</h1>
                    <p>
                        Du vil nå få tilsendt en epost! <br>
                        <small class="redirect" style="display: inline-block; margin-bottom: 150px"><em>(Du blir sendt til forsiden om
                                <span>5</span> sekunder)</em></small>
                    </p>
                </div>

                <div class="col-sm-6 right-container">
                    <img src="{{ asset('images-new/thankyou-hero.jpg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        let time = 5;
        window.setInterval(
            function()
            {
                time--;
                console.log(time);
                if(time === 0){
                    window.location.href = '{{ url('/account/dashboard') }}';
                }
                jQuery('.redirect span').text(time);
            },
            1000);
    </script>
@stop
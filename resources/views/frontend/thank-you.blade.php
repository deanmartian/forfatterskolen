@extends('frontend.layout')

@section('title')
    <title>Thank You &rsaquo; Forfatterskolen</title>
@stop

@section('content')
{{-- data-bg="https://www.forfatterskolen.no/images-new/thankyou-bg.png" --}}
    <div class="thank-you-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 left-container">
                    {{-- <img data-src="https://www.forfatterskolen.no/images-new/thumb-icon.png" alt="" class="thumb"> --}}
                    <h1>{{ trans('site.front.thank-you.title') }}</h1>
                    <p>
                        {{ trans('site.front.thank-you.description') }} <br>

                        {!! str_replace(['_start_redirect_', '_end_redirect_', '_start_span_', '_end_span_'],
                        ['<small class="redirect" style="display: inline-block; margin-bottom: 150px"><em>',
                        '</em></small>', '<span>', '</span>'],
                        trans('site.front.thank-you.note')) !!}
                    </p>
                </div>

                <div class="col-sm-6 right-container">
                    <img data-src="https://www.forfatterskolen.no/images-new/thankyou-hero.png" alt="">
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
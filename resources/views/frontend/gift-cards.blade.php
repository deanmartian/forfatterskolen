@extends('frontend.layout')

@section('title')
    <title>Gift Cards &rsaquo; Forfatterskolen</title>
@stop

@section('content')

    <div class="gift-cards-page" data-bg="https://www.forfatterskolen.no/images-new/gift-cards/bg-image.jpg">
        <div class="container">

            <div class="card details-container">
                <div class="card-body">
                    <h1 class="page-title">
                        {{ trans('site.gift-card.title') }}
                    </h1>

                    <div class="col-sm-8 description-container">
                        <p>
                            {!! trans('site.gift-card.description') !!}
                        </p>

                        <div style="margin-top: 20px">
                            <a href="{{ route('front.gift.course') }}" class="btn site-btn-global" style="margin-right: 20px">
                                {{ trans('site.gift-card.buy-course') }}
                            </a>

                            <a href="{{ route('front.gift.shop-manuscript') }}" class="btn site-btn-global">
                                {{ trans('site.gift-card.buy-manuscript') }}
                            </a>
                        </div>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card -->

            <div class="card gift-cards-container">
                <div class="card-body">

                    <h3>
                        {{ trans('site.gift-card.select-gift-cards') }}
                    </h3>

                    @php

                    $cards = \App\Http\FrontendHelpers::gitCards();

                    @endphp

                    <div class="row">

                        @foreach($cards as $card)
                            <div class="col-sm-6 text-center" style="margin-top: 20px">
                                <label>
                                    <input type="radio" name="card" value="{{ $card['name'] }}" class="image-radio"
                                           onclick="setGiftCard(this)">
                                    <img src="{{ $card['image'] }}" alt="card image">
                                    <b> {{ $card['label'] }} </b>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ route('front.gift.course') }}" class="btn site-btn-global" style="margin-right: 20px">
                    {{ trans('site.gift-card.buy-course') }}
                </a>

                <a href="{{ route('front.gift.shop-manuscript') }}" class="btn site-btn-global">
                    Buy Manuscript
                </a>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        function setGiftCard(e) {

            $.ajax({
                type:'POST',
                url:'/set-gift-card',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { "card" : e.value },
                success: function(data){
                    console.log(data);
                }
            });
        }
    </script>
@stop
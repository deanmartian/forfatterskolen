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
                        Gift Cards
                    </h1>

                    <div class="col-sm-8 description-container">
                        <p>
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean
                            massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec
                            quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec
                            pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a,
                            venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.
                            Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu,
                            consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.
                            Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies
                            nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus,
                            tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam
                            quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt
                            tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget
                            eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna.
                            Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,
                        </p>
                    </div>
                </div> <!-- end card-body -->
            </div> <!-- end card -->

            <div class="card gift-cards-container">
                <div class="card-body">

                    <h3>
                        Select gift cards
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
                                    <img src="{{ $card['image'] }}">
                                    <b> {{ $card['label'] }} </b>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ route('front.gift.course') }}" class="btn site-btn-global" style="margin-right: 20px">
                    Buy Course
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
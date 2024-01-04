@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Coaching Timer</title>
@stop

@section('content')

    <div class="coaching-timer-page-new">
        <div class="header" style="background-image: url('https://www.forfatterskolen.no/images-new/red-header-new.png')">
            <div class="container">
                <h1>
                    {{ trans('site.front.coaching-timer.title') }}
                </h1>
            </div>
        </div>

        <div class="container position-relative">
            <div class="row details-wrapper">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="top-details">
                                <img src="{{ asset('images-new/icon/open-book-dark-red.png') }}" alt="">
                                <h2>
                                    1190 KR
                                </h2>
                                <p>
                                    {{ trans('site.front.30-mins') }}
                                </p>
                            </div>
                    
                            <h2>
                                {{ trans('site.front.coaching-timer.one-on-one-coaching') }}
                            </h2>
                            <p class="position-relative">
                                {{ trans('site.front.coaching-timer.one-on-one-coaching-description') }}
                            </p>
                            <a href="{{ route('front.coaching-timer-checkout', 2) }}" class="btn buy-btn">
                                {{ trans('site.front.buy') }}
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="top-details">
                                <img src="{{ asset('images-new/icon/open-book-dark-red.png') }}" alt="">
                                <h2>
                                    1690 KR
                                </h2>
                                <p>
                                    {{ trans('site.front.60-mins') }}
                                </p>
                            </div>
                    
                            <h2>
                                {{ trans('site.front.coaching-timer.one-on-one-coaching') }}
                            </h2>
                            <p class="position-relative">
                                {{ trans('site.front.coaching-timer.one-on-one-coaching-description') }}
                            </p>
                            <a href="{{ route('front.coaching-timer-checkout', 1) }}" class="btn buy-btn">
                                {{ trans('site.front.buy') }}
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('compute_manuscript'))
        <div id="computeManuscriptModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        {!! Session::get('compute_manuscript') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
    <script>
        $(document).ready(function(){

            @if(Session::has('compute_manuscript'))
            $('#computeManuscriptModal').modal('show');
            @endif

            let form = $('.form-container form');
            form.find('input[type=text]').click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=file]').on('change', function(){
                let file = $(this).val().split('\\').pop();
                form.find('input[type=text]').val(file);
            });
            form.on('submit', function(e){
                let file = form.find('input[type=file]').val().split('\\').pop();
                if( file === '' ){
                    alert('Please select a document file.');
                    e.preventDefault();
                }
            });
        });
    </script>
@stop
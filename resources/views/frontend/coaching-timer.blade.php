@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Coaching Timer</title>
@stop

@section('content')

    <div class="coaching-timer-page" data-bg="https://www.forfatterskolen.no/images-new/ctimer-bg.png">
        <div class="container">
            <h1 class="title text-center">
                {{ trans('site.front.coaching-timer.title') }}
            </h1>

            <div class="row details-container text-center">
                <div class="col-md-6">
                    <div class="left-column" data-bg="https://www.forfatterskolen.no/images-new/ctimer-left.jpg">
                        <div class="circle">
                            <div class="smaller-circle">
                                <div class="h1">1190 KR</div>
                                <h2 class="theme-text">
                                    {{ trans('site.front.30-mins') }}
                                </h2>
                            </div>
                        </div>

                        <div class="h1">
                            {{ trans('site.front.coaching-timer.one-on-one-coaching') }}
                        </div>
                        <p class="position-relative">
                            {{ trans('site.front.coaching-timer.one-on-one-coaching-description') }}
                        </p>
                        <a href="{{ route('front.coaching-timer-checkout', 2) }}" class="btn buy-btn">
                            {{ trans('site.front.buy') }}
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="right-column" data-bg="https://www.forfatterskolen.no/images-new/ctimer-right.jpg">
                        <div class="circle">
                            <div class="smaller-circle">
                                <div class="h1">1690 KR</div>
                                <h2 class="theme-text">
                                    {{ trans('site.front.60-mins') }}
                                </h2>
                            </div>
                        </div>

                        <div class="h1">
                            {{ trans('site.front.coaching-timer.one-on-one-coaching') }}
                        </div>
                        <p class="position-relative">
                            {{ trans('site.front.coaching-timer.one-on-one-coaching-description') }}
                        </p>
                        <a href="{{ route('front.coaching-timer-checkout', 1) }}" class="btn buy-btn">
                            {{ trans('site.front.buy') }}
                        </a>
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
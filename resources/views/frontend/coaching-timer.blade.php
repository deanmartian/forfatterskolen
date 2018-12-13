@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Coaching Timer</title>
@stop

@section('content')

    <div class="coaching-timer-page">
        <div class="container">
            <h1 class="title text-center">
                Coaching Timer
            </h1>

            <div class="row details-container text-center">
                <div class="col-md-6">
                    <div class="left-column">
                        <div class="circle">
                            <div class="smaller-circle">
                                <h1>1190 KR</h1>
                                <h2 class="theme-text">30 mins</h2>
                            </div>
                        </div>

                        <h1>En til en-coaching</h1>
                        <p>
                            Ønsker du personlig veiledning på manuset ditt? Her kan du bestille time hos en av våre
                            profesjonelle redaktører.
                        </p>
                        <a href="{{ route('front.coaching-timer-checkout', 2) }}" class="btn buy-btn">Bestill</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="right-column">
                        <div class="circle">
                            <div class="smaller-circle">
                                <h1>1690 KR</h1>
                                <h2 class="theme-text">60 mins</h2>
                            </div>
                        </div>

                        <h1>En til en-coaching</h1>
                        <p>
                            Ønsker du personlig veiledning på manuset ditt? Her kan du bestille time hos en av våre
                            profesjonelle redaktører.
                        </p>
                        <a href="{{ route('front.coaching-timer-checkout', 1) }}" class="btn buy-btn">Bestill</a>
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
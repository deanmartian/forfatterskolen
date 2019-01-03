@extends('frontend.layout')

@section('title')
<title>Forfatterskolen Copy Editing</title>
@stop

@section('content')

    <div class="other-service-page copy-editing-page">
        <div class="container">
            <h1 class="text-center">Språkvask</h1>
            <div class="col-sm-12 form-container">
                <div class="row align-items-center">
                    <div class="col-md-3 price-container">
                        <div class="circle">
                            <div class="circle-white">
                                <h2 class="word-count">1000 tegn</h2>
                                <h3>30 kroner</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 details-container">
                        <h1>Profesjonell språkvask
                            <a href="{{ route('front.other-service-checkout', ['plan' => 1, 'has_data' => 0]) }}"
                               class="btn buy-btn">Bestill språkvask</a>
                        </h1>

                        <p>
                            Vi tilbyr profesjonell språkvask av alle typer manus. Vi beregner 3 ukers behandlingstid,
                            før du får ditt språkvaskede manus tilbake. Beregn pris ved å laste opp manuset i boksen til venstre
                            (dette er kun en utregning, og innebærer ikke kjøp).
                        </p>

                        <form method="POST" enctype="multipart/form-data" action="{{ route('front.copy-editing') }}"
                              id="manuscript">
                            {{ csrf_field() }}
                            <div class="input-group mb-3">
                                <input type="file" class="hidden" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                <input type="text" readonly="" class="form-control disabled"
                                       placeholder="Velg et dokument å laste opp" required>
                                <div class="input-group-append">
                                    <button class="btn bg-site-red select-manuscript" type="submit">Beregn pris</button>
                                </div>
                            </div>
                        </form>

                        <span>Dette er bare en ordteller, og innebærer ikke kjøp. </span> <br>
                        <span>Merk: Godkjente filformater er docx.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('compute_manuscript'))
        <div id="computeManuscriptModal" class="modal fade" role="dialog">
            <div class="modal-dialog" style="width: 350px">
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

            let form = $('.details-container form');
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
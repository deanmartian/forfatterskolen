@extends('frontend.layout')

@section('title')
<title>Forfatterskolen Copy Editing</title>
@stop

@section('content')

    <div class="row copy-editing-container">
        <p class="title">
            <span class="highlight">KOR</span>REKTUR
        </p>

        <div class="pos">
            <form method="POST" enctype="multipart/form-data" action="{{ route('front.correction') }}"
            id="manuscript">
                {{ csrf_field() }}
                <p class="text">Dette er kun en ordteller - opplasting av manus innebærer ikke kjøp.</p>
                <b><p class="text1">Merk: Godkjente filformater er docx.</p></b>
                <!-- application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text -->
                <input type="file" class="hidden" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                <input type="text" readonly class="form-control" required="" placeholder="CHOOSE A DOCUMENT"
                id="select-document">
                <button type="submit" class="margin-top">BEREGN PRIS</button>
            </form>
        </div>

        <div class="image3 container"><img src="{{ asset('images/bgwhite.png') }}">
            <p class="sub">Profesjonell <br>korrektur
            <p class="link2">1000 tegn  -  25 kroner</p>
            <p class="disc2">
                Vi tilbyr profesjonell korrektur på alle typer manus. Beregnet behandlingstid fra bestilling er 3 uker.
                Du kan beregne pris på korrektur ved å laste opp manuset i boksen til venstre (dette er kun en utregning,
                og innebærer ikke kjøp).
            </p>
            <a href="{{ route('front.other-service-checkout', ['plan' => 2, 'has_data' => 0]) }}"><p class="book2">Bestill korrektur</p></a>
        </div>
        <div class="space">s</div>
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

            var form = $('.pos form');
            form.find('input[type=text]').click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=file]').on('change', function(){
                var file = $(this).val().split('\\').pop();
                form.find('input[type=text]').val(file);
            });
            form.on('submit', function(e){
                var file = form.find('input[type=file]').val().split('\\').pop();
                if( file == '' ){
                    alert('Please select a document file.');
                    e.preventDefault();
                }
            });
        });
    </script>
@stop
@extends('frontend.layout')

@section('title')
<title>Forfatterskolen Copy Editing</title>
@stop

@section('content')

    <div class="row copy-editing-container">
        <p class="title">
            <span class="highlight">SPR</span>ÅKVASK
        </p>

        <div class="pos">
            <form method="POST" enctype="multipart/form-data" action="{{ route('front.copy-editing') }}"
            id="manuscript">
                {{ csrf_field() }}
                <p class="text">Dette er bare en ordteller, og innebærer ikke kjøp. </p>
                <b><p class="text1">Merk: Godkjente filformater er docx.</p></b>
                <input type="file" class="hidden" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                <input type="text" readonly class="form-control" required="" placeholder="Velg et dokument å laste opp"
                id="select-document">
                <button type="submit" class="margin-top">Beregn pris</button>
            </form>
        </div>

        <div class="image3 container"><img src="{{ asset('images/bgwhite.png') }}">
            <p class="sub">Profesjonell <br>språkvask
            <p class="link2">1000 tegn  -  30 kroner</p>
            <p class="disc2">
                Vi tilbyr profesjonell språkvask av alle typer manus. Vi beregner 3 ukers behandlingstid,
                før du får ditt språkvaskede manus tilbake. Beregn pris ved å laste opp manuset i boksen til venstre
                (dette er kun en utregning, og innebærer ikke kjøp).
            </p>
            <a href="{{ route('front.other-service-checkout', ['plan' => 1, 'has_data' => 0]) }}"><p class="book2">Bestill språkvask</p></a>
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
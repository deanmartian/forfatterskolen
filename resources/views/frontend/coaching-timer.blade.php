@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Coaching Timer</title>
@stop

@section('content')
    <div class="row coaching-timer-container">
        <p class="title">
            <span class="highlight">Coa</span>ching <br>
            timer
        </p>

        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 left-content">
                <div class="image1"><img src="{{ asset('images/bgwhite.png') }}">
                    <p class="sub">One On One<br>Coaching</p>
                    <p class="link">30 min  |  1190 KR</p>
                    <p class="desc">Discribe your service here. What makes it great? Use short catchy text to tell ter people what you offer, and the benifits they will receive.</p>
                    <div class="button1 box"></div>
                    <a href="{{ route('front.coaching-timer-checkout', 2) }}"><p class="book">book it</p></a>
                </div>
            </div>

            <div class="col-md-6 col-sm-12 col-xs-12 right-content">
                <div class="image3"><img src="{{ asset('images/bgwhite.png') }}">
                    <p class="sub">One On One<br>Coaching
                    <p class="link">1 hr  |  1690 KR</p>
                    <p class="desc">Discribe your service here. What makes it great? Use short catchy text to tell ter people what you offer, and the benifits they will receive.</p>
                    <div class="button2 box"></div>
                    <a href="{{ route('front.coaching-timer-checkout', 1) }}"><p class="book">book it</p></a>
                </div>
            </div>
        </div>

        {{--<div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12"></div>
            <div class="col-md-6 col-sm-12 col-xs-12 form-container margin-top-16">
                <form method="POST" enctype="multipart/form-data" action="{{ route('front.coaching-timer') }}"
                      id="manuscript">
                    {{ csrf_field() }}
                    <p class="text">Compute add-on price. </p>
                    <b><p class="text1">Merk: Godkjente filformater er docx.</p></b>
                    <input type="file" class="hidden" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    <input type="text" readonly class="form-control" required="" placeholder="Velg et dokument å laste opp"
                           id="select-document">
                    <button type="submit" class="margin-top">Add-on</button>
                </form>
            </div>
        </div>--}}


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

            var form = $('.form-container form');
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
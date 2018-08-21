@extends('frontend.layout')

@section('title')
    <title>{{ $survey->title }} &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        .card {
            position: relative;
            margin: 8px 0 16px 0;
            background-color: #fff;
            transition: box-shadow .25s;
            border-radius: 2px;
            box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
            margin-top: 50px;
        }

        .card .card-title {
            font-size: 24px;
            font-weight: 300;
        }

        .card .card-content {
            padding: 20px;
            border-radius: 0 0 2px 2px;
        }

        .red-text {
            color: #f00
        }

        .divider {
            height: 1px;
            overflow: hidden;
            background-color: #e0e0e0;
        }

        .flow-text {
            font-size: 20px;
        }

        select.browser-default {
            display: block;
        }

        .card-content select {
            background-color: rgba(255,255,255,0.9);
            width: 100%;
            padding: 5px;
            border: 1px solid #f2f2f2;
            border-radius: 2px;
            height: 40px;
            text-transform: none;
            color: inherit;
            font: inherit;
            margin: 0;
        }

        .card-content select:focus {
            outline: 1px solid #c9f3ef;
        }

        .card-content .row .col.s12 {
            width: 100%;
            margin-left: auto;
            left: auto;
            right: auto;
        }

        .card-content .row .col {
            float: left;
            box-sizing: border-box;
            padding: 0 12px;
        }

        .card-content .input-field {
            position: relative;
            margin-top: 10px;
            font-size: 15px;
        }

        .card-content input[type=text], textarea.materialize-textarea {
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #9e9e9e;
            border-radius: 0;
            outline: none;
            height: 48px;
            width: 100%;
            font-size: 16px;
            margin: 0 0 15px 0;
            padding: 0;
            box-shadow: none;
            box-sizing: content-box;
            transition: all 0.3s;
        }

        .card-content .input-field label {
            color: #9e9e9e;
            position: absolute;
            top: 13px;
            left: 12px;
            font-size: 13px;
            cursor: text;
            transition: .2s ease-out;
        }

        .card-content .btn, .btn-large {
            text-decoration: none;
            color: #fff;
            background-color: #26a69a;
            text-align: center;
            letter-spacing: .5px;
            transition: .2s ease-out;
            cursor: pointer;
        }

        .card-content .waves-effect {
            position: relative;
            cursor: pointer;
            display: inline-block;
            overflow: hidden;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            vertical-align: middle;
            z-index: 1;
            will-change: opacity, transform;
            transition: all .3s ease-out;
        }

        .card-content .btn{
            border: none;
            border-radius: 2px;
            display: inline-block;
            height: 36px;
            line-height: 36px;
            outline: 0;
            padding: 0 32px;
            text-transform: uppercase;
            vertical-align: middle;
            -webkit-tap-highlight-color: transparent;
            box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
        }

        .card-content .btn:hover{
            background-color: #2bbbad;
            color:#fff;
        }

        .card-content .btn:hover{
            box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18), 0 4px 15px 0 rgba(0,0,0,0.15);
        }

        /*this section is responsible for changing the font and color of the label in text field*/
        input[type=text]:focus:not([readonly]){
            border-bottom: 1px solid #26a69a;
            box-shadow: 0 1px 0 0 #26a69a;
        }

        input[type=text]:focus:not([readonly])+label{
            color: #26a69a;
        }

        .input-field label.active {
            font-size: 12px;
            transform: translateY(-140%);
        }

        [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
            position: absolute;
            left: -9999px;
            opacity: 0;
        }

        input[type="checkbox"], input[type="radio"] {
            box-sizing: border-box;
            padding: 0;
        }

        [type="checkbox"]+label {
            position: relative;
            padding-left: 35px;
            cursor: pointer;
            display: inline-block;
            height: 25px;
            line-height: 20px;
            font-size: 16px;
            font-weight: normal;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        [type="checkbox"]+label:before, [type="checkbox"]:not(.filled-in)+label:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 14px;
            height: 14px;
            z-index: 0;
            border: 2px solid #5a5a5a;
            border-radius: 1px;
            margin-top: 2px;
            transition: .2s;
        }

        [type="checkbox"]:not(.filled-in)+label:after {
            border: 0;
            -webkit-transform: scale(0);
            transform: scale(0);
        }

        [type="checkbox"]:checked+label:before {
            top: -4px;
            left: -3px;
            width: 8px;
            height: 16px;
            border-top: 2px solid transparent;
            border-left: 2px solid transparent;
            border-right: 2px solid #26a69a;
            border-bottom: 2px solid #26a69a;
            -webkit-transform: rotate(40deg);
            transform: rotate(40deg);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transform-origin: 100% 100%;
            transform-origin: 100% 100%;
        }
    </style>
@stop

@section('content')
    <div class="account-container">
        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-md-6 col-md-offset-3">

                <div class="card">
                    <div class="card-content">
                        <span class="card-title"> Start taking Survey</span>
                        <p>
                        </p>
                        <p>
                            <span class="flow-text margin-top">{{ $survey->title }}</span> <br/>
                            {{ $survey->description }}
                        </p>

                        <div class="divider" style="margin:25px 0px;"></div>

                        <form action="{{ route('learner.take-survey', $survey->id) }}" method="POST">
                            {{ csrf_field() }}

                            @forelse($survey->questions as $question)
                                <p>
                                    <b>
                                        {{ $question->title }}
                                    </b>

                                </p>
                                @if($question->question_type === 'text')
                                    <div class="input-field col s12">
                                        <input id="answer" type="text" name="{{ $question->id }}[answer]">
                                        <label for="answer">Answer</label>
                                    </div>
                                @elseif($question->question_type === 'textarea')
                                    <div class="form-group">
                                        <span>Provide Answer</span>
                                        <textarea name="{{ $question->id }}[answer]" id="" cols="20" rows="10"
                                                  class="form-control"></textarea>
                                    </div>
                                @elseif($question->question_type === 'radio')
                                    <?php $radio_options = json_decode($question->option_name);?>
                                    @foreach($radio_options as $key=>$value)
                                        <p style="margin:0px; padding:0px;">
                                            <input name="{{ $question->id }}[answer][]" type="radio" id="{{ $key }}"
                                            value="{{ $value }}"/>
                                            <label for="{{ $key }}">{{ $value }}</label>
                                        </p>
                                    @endforeach
                                @elseif($question->question_type === 'checkbox')
                                    <?php $checkbox_options = json_decode($question->option_name);?>
                                    @foreach($checkbox_options as $key=>$value)
                                        <p style="margin:0px; padding:0px;">
                                            <input type="checkbox" id="{{ $question->id.'-'.$key }}" name="{{ $question->id }}[answer][]"
                                            value="{{ $value }}"/>
                                            <label for="{{$question->id.'-'.$key}}">{{ $value }}</label>
                                        </p>
                                    @endforeach
                                @endif
                                <div class="divider" style="margin:25px 0px;"></div>

                            @empty
                                <p class="text-center">
                                    <b >Nothing to show</b>
                                </p>
                            @endforelse

                            @if($survey->questions->count())
                            <button class="btn waves-effect waves-light">Submit Survey</button>
                            @endif

                        </form>
                    </div>
                </div>

            </div>
        </div>

        <div class="clearfix"></div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('js/custom-materialize.js') }}"></script>
@stop
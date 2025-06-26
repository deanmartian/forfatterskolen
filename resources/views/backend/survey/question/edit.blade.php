@extends('backend.layout')

@section('title')
    <title>{{ $surveyQuestion->title }}</title>
@stop

@section('styles')
    <style>
        /*1rem = 16px*/
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

        .margin-top-15 {
            margin-top: 15px !important;
        }

    </style>
@stop

@section('content')
    <div class="col-sm-10">
        <div class="col-md-6 col-md-offset-4">
            <div class="card">
                <div class="card-content">
                    <span class="card-title"> Edit Question</span>
                    <a href="#deleteQuestionModal" class="pull-right red-text"
                       data-toggle="modal" data-action="{{ route('admin.survey.question.destroy', ['survey_id' => $survey->id,
                       'question' => $surveyQuestion->id]) }}"
                       id="deleteQuestionBtn">Delete Question</a>

                    <form method="POST" action="{{ route('admin.survey.question.update',
                    ['survey_id' => $survey->id, 'question' => $surveyQuestion->id]) }}">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="row">

                            <div class="input-field col s12">
                                <select class="browser-default" name="question_type" id="question_type" required>
                                    <option value="" disabled selected>Choose your option</option>
                                    @foreach(\App\Http\AdminHelpers::question_type() as $type)
                                        <option value="{{ $type['id'] }}"
                                        @if($type['id'] == $surveyQuestion->question_type) selected @endif>
                                            {{ $type['option'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-field col s12 margin-top-15">
                                <input name="title" id="title" type="text" value="{{ $surveyQuestion->title }}">
                                <label for="title">Question</label>
                            </div>

                            <!-- this part will be chewed by script in init.js -->
                            <div class="form-g">

                                @if($surveyQuestion->question_type === 'checkbox' || $surveyQuestion->question_type === 'radio')
                                    <?php $options = json_decode($surveyQuestion->option_name);?>
                                        @foreach($options as $key=>$value)
                                            <div class="input-field col input-g s12">
                                                <input type="text" name="option_name[]" id="option_name[]"
                                                value="{{ $value }}">
                                                <span style="float:right; cursor:pointer;" class="delete-option">Delete</span>
                                                <label for="option_name">Options</label>
                                                <span class="add-option" style="cursor:pointer;">Add Another</span>
                                            </div>
                                        @endforeach
                                @endif

                            </div>

                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteQuestionModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Question</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <p>
                            Are you sure you want to delete this question? <br>
                        </p>
                        <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('/js/custom-materialize.js') }}"></script>

    <script>
        $(document).ready(function(){
            $("#deleteQuestionBtn").click(function(){
                var action = $(this).data('action'),
                    form    = $("#deleteQuestionModal").find('form');

                form.attr('action', action);
            });
        });
    </script>
@stop
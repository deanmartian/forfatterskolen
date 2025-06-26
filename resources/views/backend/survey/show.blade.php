@extends('backend.layout')

@section('title')
    <title>{{ $survey->title }}</title>
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

    </style>
@stop

@section('content')
    <div class="col-sm-10">
        <div class="col-md-6 col-md-offset-4">
            <div class="card">
                <div class="card-content">
                    <span class="card-title"> {{ $survey->title }}</span>

                    <p>
                        {{ $survey->description }}
                    </p>

                    <b>{{ trans('site.start-date') }}:</b> {{ \App\Http\FrontendHelpers::isDate($survey->start_date) ?
                    \Carbon\Carbon::parse($survey->start_date)->format('Y.m.d') : NULL }}
                    <br>
                    <b>{{ trans('site.end-date') }}:</b> {{ \App\Http\FrontendHelpers::isDate($survey->end_date) ?
                    \Carbon\Carbon::parse($survey->end_date)->format('Y.m.d') : NULL }}
                    <br> <br>


                    <a href="#editSurveyModal" data-toggle="modal" id="editSurveyBtn"
                    data-fields="{{json_encode($survey)}}" data-action="{{ route('admin.survey.update', $survey->id) }}">Edit Survey</a>
                    <?php
                        $answer = 0;
                    foreach($survey->questions as $question) {
                        $answer += $question->answers->count();
                    }
                    ?>

                    @if($answer)
                        | <a href="{{ route('admin.survey.download-answers', $survey->id) }}">Download Answers</a>
                    @endif
                    | <a href="#editDateModal" data-toggle="modal">Edit Date</a>
                    <a href="#deleteSurveyModal" class="pull-right red-text"
                    data-toggle="modal" data-action="{{ route('admin.survey.destroy', $survey->id) }}"
                    id="deleteSurveyBtn">Delete Survey</a>

                    <div class="divider" style="margin:20px 0;"></div>

                    <p class="flow-text text-center">Questions</p>

                    <div class="panel-group" id="accordion">
                        @forelse ($survey->questions as $question)
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion"
                                        href="#question-{{ $question->id }}"
                                        aria-expanded="false" class="collapsed">
                                            {{ $question->title }}
                                        </a>

                                        <a href="{{ route('admin.survey.question.edit', ['survey_id' => $survey->id,
                                        'question' => $question->id]) }}" class="pull-right"
                                        style="color: #039be5">Edit</a>
                                    </h4>
                                </div>

                                <div id="question-{{ $question->id }}" class="panel-collapse collapse"
                                aria-expanded="false">
                                    <div class="panel-body">
                                        <div style="margin:5px; padding:10px;">
                                            @if($question->question_type === 'text')
                                                <input type="text" class="form-control">
                                            @elseif($question->question_type === 'textarea')
                                                <div class="form-group">
                                                    <label for="">Provide Answer</label>
                                                    <textarea name="" id="" cols="30" rows="10"
                                                              class="form-control"></textarea>
                                                </div>
                                            @elseif($question->question_type === 'radio')
                                                <?php $radio_options = json_decode($question->option_name);?>
                                                    @foreach($radio_options as $key=>$value)
                                                        <p style="margin:0px; padding:0px;">
                                                            <input type="radio" id="{{ $key }}" name="optRadio"/>
                                                            <label for="{{$key}}">{{ $value }}</label>
                                                        </p>
                                                    @endforeach

                                            @elseif($question->question_type === 'checkbox')
                                                <?php $checkbox_options = json_decode($question->option_name);?>
                                                    @foreach($checkbox_options as $key=>$value)
                                                        <p style="margin:0px; padding:0px;">
                                                            <input type="checkbox" id="{{ $question->id.'-'.$key }}" />
                                                            <label for="{{$question->id.'-'.$key}}">{{ $value }}</label>
                                                        </p>
                                                    @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="panel panel-default">
                                <div class="panel-body text-center">
                                    <em style="padding:10px;">Nothing to show. Add questions below.</em>
                                </div>
                            </div>
                        @endforelse
                    </div>



                    <h2 class="flow-text">Add Question</h2>
                    <form method="POST" action="{{ route('admin.survey.question.store', $survey->id) }}" id="boolean">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="input-field col s12">
                                <select class="browser-default" name="question_type" id="question_type" required>
                                    <option value="" disabled selected>Choose your option</option>
                                    @foreach(\App\Http\AdminHelpers::question_type() as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['option'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-field col s12">
                                <input name="title" id="title" type="text">
                                <label for="title">Question</label>
                            </div>
                            <!-- this part will be chewed by script in init.js -->
                            <span class="form-g"></span>

                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <div id="editSurveyModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Survey</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="" cols="30" rows="10"
                                      class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <select class="form-control" name="course_id" required>
                                <option value="" disabled="disabled" selected>Select Course</option>
                                @foreach(\App\Course::all() as $course)
                                    <option value="{{ $course->id }}"> {{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">Update</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteSurveyModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Survey</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <p>
                            Are you sure you want to delete this survey? <br>

                            <em>Note: This would also delete the questions for this survey</em>
                        </p>
                        <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editDateModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Date</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.survey.update-date', $survey->id) }}"
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label>{{ trans('site.start-date') }}</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $survey->start_date }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.end-date') }}</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $survey->end_date }}"
                                   required>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.save') }}</button>
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
           $("#editSurveyBtn").click(function(){
             var fields = $(this).data('fields'),
                action  = $(this).data('action'),
                form    = $("#editSurveyModal").find('form');

             form.attr('action', action);
             form.find('input[name=title]').val(fields.title);
             form.find('textarea[name=description]').val(fields.description);
            form.find('select[name=course_id]').val(fields.course_id);
           });

           $("#deleteSurveyBtn").click(function(){
               var action = $(this).data('action'),
                   form    = $("#deleteSurveyModal").find('form');

               form.attr('action', action);
           });
        });
    </script>
@stop
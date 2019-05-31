@extends('backend.layout')

@section('title')
    <title>{{$survey->title}} Answers</title>
@stop

@section('styles')
    <style>
        .panel-group .panel {
            border: none;
            border-left: 3px solid #862736;
            border-radius: 0;
            box-shadow: 1px 1px 15.36px 0.64px hsla(0,0%,73.3%,.27);
        }

        .panel-group .panel>.panel-heading {
            background-color: #fff;
            border-radius: 0;
            padding: 20px 30px;
        }
    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> {{ $survey->title }} Answers</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <div class="panel-group margin-top" id="accordion">
            <?php $first = true; ?>
            @foreach( $questions as $question )
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $question->id }}" class="all-caps collapsed">
                                <i class="img-icon"></i> {{ $question->title }}
                            </a>
                        </h4>
                    </div>
                    <div id="collapse-{{ $question->id }}" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table dt-table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="500">{{ trans('site.learner.learner-text') }}</th>
                                        <th>{{ trans('site.answer-text') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($question->answers()->with('user')->get() as $answer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.learner.show', $answer->user->id) }}">
                                                {{ $answer->user->full_name }}
                                            </a>
                                        </td>
                                        <td>
                                            <?php
                                                $result = json_decode($answer->answer);
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    echo implode(", ", $result);
                                                } else {
                                                    echo $answer->answer;
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php $first = false; ?>
            @endforeach
        </div>
    </div>
@stop